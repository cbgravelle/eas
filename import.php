<?php
/*
Template Name: Import
*/
include( ABSPATH . 'wp-admin/includes/image.php' );
echo '<!DOCTYPE html>';

$conn = new mysqli('localhost','emergentartspace','4BUcJ42csYKdy3Uz','emergentartspace');


if (!isset($_POST['ID'])) {
	$artists = $conn->query("select * from wp_posts where post_type = 'art' and post_status = 'publish' order by ID asc");
	?><form action="" method="post"><select name="ID"><?php
	while ($artist = $artists->fetch_assoc()) {
		?>
			<option value="<?php echo $artist['ID']; ?>"><?php echo $artist['post_title']; ?></option>
		<?php
	}
	?></select>
	<input type="submit" value="submit">
		</form>

	<?php
} else {
	$result = $conn->query('select * from wp_posts where ID = '.$_POST['ID']);

	while ($row = $result->fetch_assoc()) {
		$artist['display_name'] = utf8_encode($row['post_title']);
		$artist['user_registered'] = $row['post_date'];


		$submission = $conn->query("select s2.field_value as email from wp_cf7dbplugin_submits s1 join wp_cf7dbplugin_submits s2 on s1.submit_time = s2.submit_time where s1.field_name like 'name-333' and lower(s1.field_value) like '%".strtolower($artist['display_name'])."%' and s2.field_name like 'email-333'");

		while ($sub = $submission->fetch_assoc()) {
			$artist['email'] = $sub['email'];

			$olduserquery = $conn->query("select * from wp_users where lower(user_email) like '%".strtolower($artist['email'])."%'");

			while ($olduser = $olduserquery->fetch_assoc()) {
				$artist['user_login'] = $olduser['user_login'];
				$artist['user_url'] = $olduser['user_url'];
				$artist['user_registered'] = $olduser['user_registered'];

			}

		}


		$images = array();
		$post_content = $row['post_content'];

		$matches = array();
		$loc = 0;

		$pattern = '/\[artistdata](.+?)\[\/artistdata]/s';

		preg_match($pattern, substr($post_content, $loc), $matches, PREG_OFFSET_CAPTURE);

		$artist['data'] = preg_replace('/\[\/artistdataitem]\[artistdataitem]/', ';', $matches[1][0]);
		$artist['data'] = preg_replace('/\[\/?artistdataitem]/', '', $artist['data']);
		$artist['data'] = preg_replace('/\[\/?artistdatalabel]/', ' ', $artist['data']);


		$pattern = '/\/artistdata](.+?)\[/s';

		preg_match($pattern, substr($post_content, $loc), $matches, PREG_OFFSET_CAPTURE);

		$artist['description'] = utf8_encode($matches[1][0]);

		$pattern = '/alignleft.+src="(.+?)"/';
		if (preg_match($pattern, substr($post_content, $loc), $matches, PREG_OFFSET_CAPTURE) > 0) {
			$artist['biophoto'] = str_replace('-150x150', '', $matches[1][0]);
		}

		$orig_pattern = '/wp-image-([0-9]+?)[^0-9]/';
		$pattern = $orig_pattern;
		while (preg_match($pattern, substr($post_content, $loc), $matches, PREG_OFFSET_CAPTURE) > 0) {			
			
			$img = array();

			$i = 0;

			do {

				if ($i) preg_match($pattern, substr($post_content, $loc), $matches, PREG_OFFSET_CAPTURE);

				$img['old_ID'] = $matches[1][0];

				$loc += $matches[1][1] + strlen($matches[1][0]);

				$imgquery = $conn->query('select guid from wp_posts where ID = '.$img['old_ID']);

				while ($this_img = $imgquery->fetch_assoc()) {
					$img['src'] = $this_img['guid'];
				}
				$i++;
			} while ($img['src'] == $artist['biophoto']);

			$pattern = '/h3.+>(.+?)[^\[]\[/';
			preg_match($pattern, substr($post_content, $loc), $matches, PREG_OFFSET_CAPTURE);

			$img['title'] = utf8_encode(trim($matches[1][0]));
			$loc += $matches[1][1] + strlen($matches[1][0]);
			$pattern = '/medium\](.+)?\[/';

			preg_match($pattern, substr($post_content, $loc), $matches, PREG_OFFSET_CAPTURE);

			$img['medium'] = utf8_encode(trim($matches[1][0]));
			$loc += $matches[1][1] + strlen($matches[1][0]);
			array_push($images, $img);
			$pattern = $orig_pattern;
		}
		$artist['images'] = $images;
		$importantfields = array('email', 'user_login');
		if (!isset($_POST['adduser'])) {
			_d($artist);
			?>
				<form action="" method="post">
					<input type="hidden" name="ID" value="<?php echo $_POST['ID']; ?>">

					<?php



					foreach ($importantfields as $field) {
						if (!isset($artist[$field])) {
							?>
								<input type="text" name="<?php echo $field; ?>" placeholder="<?php echo $field; ?>">
							<?php
						}
					}

					?>

					<input type="hidden" name="adduser" value="true">
					<input type="submit" value="Import User">
				</form>

			<?php
		}
	}
}

if (isset($_POST['adduser'])) {

	if (!isset($artist['user_login'])) {
		$email_splode = explode('@', $artist['email']);
		$artist['user_login'] = $email_splode[0];
	}

	foreach ($importantfields as $field) {
		if (isset($_POST[$field])) {
			$artist[$field] = $_POST[$field];
		}
	}

	$row = $wpdb->insert('wp_users', array(
		'user_login' => $artist['user_login'],
		'user_nicename' => $artist['display_name'],
		'user_email' => $artist['email'],
		'user_registered' => $artist['user_registered'],
		'display_name' => $artist['display_name']
	));

	$userid = $wpdb->insert_id;

	$wp_user_object = new WP_User($userid);
	$wp_user_object->set_role('artist');

	if (isset($artist['biophoto'])) {
		
		_d(str_replace('emergentartspace.org','staging.emergentartspace.org',$artist['biophoto']));
		$avatarid = upload_file_from_url();

    update_user_meta($userid, 'avatar', $avatarid);

	}

	update_user_meta($userid, 'description', $artist['description']);
	update_user_meta($userid, 'nickname', $artist['display_name']);	

	foreach ($artist['images'] as $this_img) {

		$new_post = array(
			'post_title' => $this_img['title'],
			'post_date' => $artist['user_registered'],
			'post_status' => 'publish',
			'post_author' => $userid,
			'post_type' => 'artwork',
			'post_category' => array(0)
		);

		$artwork_id = wp_insert_post($new_post, false);

		update_post_meta($artwork_id, 'medium', $this_img['medium']);

		$img_id = upload_file_from_url(str_replace('emergentartspace.org','staging.emergentartspace.org',$this_img['src']), $artwork_id);

	}

	echo '<a href="'.eas_artist_page_url($userid).'" target="_blank">Clicky</a><p>';


		$artists = $conn->query("select * from wp_posts where post_type = 'art' and post_status = 'publish' order by ID asc");
		?><form action="" method="post"><select name="ID"><?php
		while ($artist = $artists->fetch_assoc()) {
			?>
				<option value="<?php echo $artist['ID']; ?>"><?php echo $artist['post_title']; ?></option>
			<?php
		}
		?></select>
		<input type="submit" value="submit">
			</form>

		<?php
}




