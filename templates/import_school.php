<?php
/*
Template Name: Import School
*/
include( ABSPATH . 'wp-admin/includes/image.php' );
echo '<!DOCTYPE html>';

$conn = new mysqli('localhost','emergentartspace','4BUcJ42csYKdy3Uz','emergentartspace');

$result = $conn->query("select * from wp_posts p where post_status = 'publish' and post_type = 'art'");

while ($post = $result->fetch_assoc()) {
	$pattern =  '/artistdatalabel\]([^\[]+)\:\[\/artistdatalabel\]([^\[]+)\[/';
	$matches = array();
	preg_match_all($pattern, $post['post_content'], $matches);
	$query = $wpdb->prepare('select u.ID from wp_users u where display_name = %s limit 1', $post['post_title']);
	$newauthor = $wpdb->get_var($query);
	foreach ($matches[1] as $key => $val) {
		$matches[1][$key] = strtolower($matches[1][$key]);
		if ($matches[1][$key] == 'location' || $matches[1][$key] == 'school') {
			eas_update_usermeta($newauthor, $matches[1][$key], $matches[2][$key]);
		
		} else if ($matches[1][$key] == 'artist website') {
			eas_update_usermeta($newauthor, 'website', $matches[2][$key]);
		}
	}

}