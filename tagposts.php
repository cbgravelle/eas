<?php
/*
Template Name: tag posts
*/

$temp_query = $wp_query;
	$wp_query = null;

	$wp_query = new WP_Query(array(
		'post_type' => 'forum',
		'posts_per_page' => '-1'
	));

	_d($wp_query);

	while (have_posts()) { the_post();
		_d($post);
		eas_tag_urls($post->post_content);
	}

	$wp_query = null;
	$wp_query = $temp_query;
	wp_reset_postdata();