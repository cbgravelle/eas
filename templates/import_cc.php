<?php
/*
Template Name: Import CC
*/
include( ABSPATH . 'wp-admin/includes/image.php' );
echo '<!DOCTYPE html>';

$conn = new mysqli('localhost','emergentartspace','4BUcJ42csYKdy3Uz','emergentartspace');

$result = $conn->query("select * from wp_posts p where post_status = 'publish' and post_type = 'art'");

while ($post = $result->fetch_assoc()) {
	$pattern =  '/\[eascc[^\]]+\]/';
	$matches = array();
	preg_match($pattern, $post['post_content'], $matches);
	
	$str = $matches[0];

	$cc_array = explode(' ',$str);
	
	$cc_vals = array('nd' => 0, 'nc' => 0, 'sa' => 0);

	$cc_str = 'by';

	foreach ($cc_array as $c) {
		foreach ($cc_vals as $key => $val) {
			if (substr($c, 0, 2) == $key) {
				$cc_vals[$key] = 1;
			}
		}
	}

	foreach (array('nc','nd','sa') as $c) {
		if ($cc_vals[$c] == 1) {
			$cc_str.='-'.$c;
		}
	}

	$query = $wpdb->prepare('select u.ID from wp_users u where display_name = %s limit 1', $post['post_title']);
	$newauthor = $wpdb->get_var($query);

	eas_update_cc($newauthor, $cc_str);
	_d($newauthor);

	if (isset($newauthor)) {
		$artworks = eas_artworks_by_user($newauthor);
		foreach ($artworks as $a) {
			eas_update_cc($a->ID, $cc_str);
			_d($a->ID);
		}
	}
	

}