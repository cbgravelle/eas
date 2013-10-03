<?php
/*
Template Name: Import Comments
*/

$results = get_users(array('role' => 'artist', 'orderby' => 'registered'));

$conn = new mysqli('localhost','emergentartspace','4BUcJ42csYKdy3Uz','emergentartspace');

$result = $conn->query("select * from wp_posts where post_type = 'art' and post_status = 'publish'");

while ($thispost = $result->fetch_assoc()) {
	$new_post = $wpdb->get_results("select * from wp_posts where post_title ='".$thispost['post_title']."' and post_status = 'publish'");
	if (count($new_post) > 0) {
			$new_id = $new_post[0]->ID;
			$comments = $conn->query("select * from wp_comments where comment_post_ID = ".$thispost['ID']." and comment_parent = 0");
			while ($c = $comments->fetch_assoc()) {
				add_comment($c, 0, $new_id);
			}
	}

	

}

function add_comment($c, $parent, $post_id) {
	global $conn;
	$the_user = get_user_by('email',$c['comment_author_email']);
	$user_id = $the_user->ID;

	if ($parent == 0) $parent = $post_id;
	
	$new_post = array(
		'post_type' => 'forum',
		'post_date' => $c['comment_date'],
		'post_title' => '',
		'post_status' => 'publish',
		'post_author' => $user_id,
		'post_content' => $c['comment_content'],
		'post_parent' => $parent
	);



	$new_id = wp_insert_post($new_post);

	_d($new_id);

	if ($parent == 0) wp_set_object_terms($new_id, 'artwork', 'forum_category');

	eas_update_meta($new_id, 'op', $post_id);
	$the_query = "select * from wp_comments where comment_parent = ".$c['comment_ID'];
	$comments = $conn->query($the_query);

	while ($comm = $comments->fetch_assoc()) {
		add_comment($comm, $new_id, $post_id);
	}


}