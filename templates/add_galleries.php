<?php
/*
Template Name: Add Galleries
*/

$results = get_users(array('role' => 'artist', 'orderby' => 'registered'));

foreach ($results as $r) {
	$these_posts = new WP_Query(array('author' => $r->ID, 'post_type' => 'forum'));
if ($these_posts->post_count == 0) {
	$artworks = eas_artworks_by_user($r->ID);

	$content = '';

	foreach ($artworks as $a) {
		$content.='
			'.eas_artwork_url($a->ID).'
		';
	}

	$new_post = array(
		'post_type' => 'forum',
		'post_date' => $r->user_registered,
		'post_title' => $r->display_name,
		'post_status' => 'publish',
		'post_author' => $r->ID,
		'post_content' => $content
	);

	$new_id = wp_insert_post($new_post);

	wp_set_object_terms($new_id, 'gallery', 'forum_category');

	eas_feature($new_id);

	echo '<a href="'.trailingslashit(get_bloginfo('siteurl')).'art/'.$new_id.'">'.$r->display_name.'</a><br>';

}	
/*
	*/
}