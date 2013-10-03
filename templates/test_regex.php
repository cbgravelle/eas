<?php
/*
Template Name: test regex
*/

$url = eas_escape_url(get_bloginfo('siteurl'));
	$pattern = '/('.$url.'\/(art|a|artists|forum)\/([0-9]+)\/?,?){1,}/i';

	$matches = array();
	$string = 'http://beta.emergentartspace.org/a/123,http://beta.emergentartspace.org/a/456 blah blah blah http://beta.emergentartspace.org/a/789';
	preg_match_all($pattern, $string, $matches);
	_d($matches);