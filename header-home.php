<!DOCTYPE html>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 7]>    <html class="no-js lt-ie9 lt-ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 8]>    <html class="no-js lt-ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" <?php language_attributes(); ?>> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <title><?php wp_title('|', true, 'right'); bloginfo('name'); ?></title>

  <?php if (current_theme_supports('bootstrap-responsive')) { ?><meta name="viewport" content="width=device-width, initial-scale=1.0"><?php } ?>

  <script src="<?php echo get_template_directory_uri(); ?>/js/vendor/modernizr-2.5.3.min.js"></script>

  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
	<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script>window.jQuery || document.write('<script src="<?php echo get_template_directory_uri(); ?>/js/vendor/jquery-1.7.2.min.js"><\/script>')</script>

  <script type="text/javascript">
 <?php
if ( is_front_page() /*is_page('home')*/ ) {
  $home = true;
  $bgs = eas_get_homebg();
  $all_bgs = array();
  foreach ($bgs as $bgid) {
      $bgpost = get_post($bgid);
  $bgartist = get_userdata($bgpost->post_author);
  $img = eas_artwork_img($bgpost->ID, 'homebg');
  $src = $img[0];
    array_push($all_bgs, array(
      'artisturl' => eas_artwork_url($bgid),
      'artistname' => $bgartist->display_name,
      'title' => $bgpost->post_title,
      'src' => $src,
    ));
  }

  echo 'var homebgs = '.json_encode($all_bgs).';';
  ?>

      var bg = homebgs[Math.floor(Math.random()*homebgs.length)];

      $(function() {
        $('body')
          .css('background-image','url(' + bg.src + ')')
          .prepend('<div class="bgartist">background by <a href="' + bg.artisturl + '" title="' + bg.title + '">' + bg.artistname + '</a></div>');
      })

  <?php


} else {
  $home = false;
}

?>
</script>
 <?php roots_head(); ?>

  <?php wp_head(); ?>



</head>

<body <?php body_class(eas_page_type()); ?>>

  <!--[if lt IE 7]><div class="alert">Your browser is <em>ancient!</em> <a href="http://browsehappy.com/">Upgrade to a different browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">install Google Chrome Frame</a> to experience this site.</div><![endif]-->

  <?php roots_header_before(); ?>
  <?php
    if (current_theme_supports('bootstrap-top-navbar')) {
      get_template_part('templates/header', 'top-navbar');
    } else {
      get_template_part('templates/header', 'default');
    }

    get_template_part('header','util');


?>


  <?php roots_header_after(); ?>

  <?php roots_wrap_before(); ?>

  <div id="wrap" class="<?php echo WRAP_CLASSES; ?>" role="document">


