<?php
$searchtype = eas_page_type();
if ($searchtype == 'artists') {
	$suffix = ' Artists';
} else if ($searchtype == 'forum') {
	$suffix = ' Forum';
}

$stylesheet = get_stylesheet();
$theme_root = get_theme_root_uri();

$template_directory_uri = $theme_root.'/'.$stylesheet.'/';


?>
<form role="search" method="post" id="searchform" class="navbar-search pull-right" action="<?php echo $template_directory_uri; ?>/searchredirect.php">
  <input type="search" value="" name="s" id="s" class="search-query" placeholder="Search<?php echo $suffix; ?>">
  <input type="hidden" name="searchtype" value="<?php echo $searchtype; ?>">
</form>