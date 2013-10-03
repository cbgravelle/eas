<?php
/*
Template Name: Import Home
*/
include( ABSPATH . 'wp-admin/includes/image.php' );
echo '<!DOCTYPE html>';

$conn = new mysqli('localhost','emergentartspace','4BUcJ42csYKdy3Uz');

$result = $conn->query("
	select ID, post_date, post_content, post_title, post_name from emergentartspace.wp_posts where post_type = 'post' and post_status = 'publish'
");



while ($p = $result->fetch_assoc()) {
	$query = $wpdb->prepare('select * from wp_posts p where post_title = %s', $p['post_title']);
	$equiv_post = $wpdb->get_row($query);

	import_child_comments($conn, $p['ID'], $equiv_post->ID);


}

function import_comment($db, $old_id, $email, $date, $content, $newparent, $op) {

	$new_user = get_user_by('email', $email);
	
	$new_id = wp_insert_post(array(
		'post_author' => $new_user->ID,
		'post_date' => $date,
		'post_content' => $content,
		'post_type' => 'forum',
		'post_parent' => $newparent,
		'post_status' => 'publish'
	));

	_d($new_id);


	eas_update_meta($new_id, 'op', $op);


	import_child_comments($db, $old_id, $new_id, $op);

}

function import_child_comments($db, $parent, $newparent, $op = 0) {
	if ($op == 0) $op = $newparent;


	$query = "
		select * from emergentartspace.wp_comments where comment_approved = 1 and comment_post_ID = ".$parent." or comment_parent = ".$parent."
	";


	$comm = $db->query($query);




	while ($c = $comm->fetch_assoc()) {
		import_comment($db, $c['comment_ID'], $c['comment_author_email'], $c['comment_date'], $c['comment_content'], $newparent, $op);
	}

}