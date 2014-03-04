<?php
/*
Template Name: Basic Upload
*/
?>
<?php
global $user_ID;
get_currentuserinfo();

if(!$user_ID)
	wp_redirect('login?redirect=/upload');

$contestform = false;

if (isset($_REQUEST['contestname'])) {
  $contestname = $_REQUEST['contestname'];
  $contestform = true;
}






if (isset($_FILES['artwork'])) {
require_once(ABSPATH.'/wp-admin/includes/image.php'); 
require_once(ABSPATH.'/wp-admin/includes/file.php'); 
require_once(ABSPATH.'/wp-admin/includes/media.php'); 

    $do_it = true;
    if ($contestform && empty($_POST['birthday']) && empty($_POST['school'])) {
        $do_it = false;
        $_GET['action'] = 'noage';
    }

$errors = array();
$check = array('location' => 1, 'birthday' => 1);
$meta = get_user_meta($user_ID);
foreach($check as $key => $value)
{
	if(isset($meta[$key]) && count($meta[$key]) && !empty($meta[$key][0]))
	{
		unset($check[$key]);
	}
}
if(isset($check['birthday']))
{
	$error = false;
	if(!isset($_POST['birthday']) || empty($_POST['birthday']))
	{
		$do_it = false;
		$error = true;
		$errors[] = "You must enter in your birthday.";
	}
	if(strtotime($_POST['birthday']) < strtotime("-30 years"))
	{
		$do_it = false;
		$error = true;
		$errors[] = "You must be less than 30 years old to submit artwork.";
	}
	if(!$error)
		update_user_meta($user_ID, 'birthday', $_POST['birthday']);
}
if(isset($check['school']))
{
	$error = false;
	if(!isset($_POST['school']) || empty($_POST['school']))
	{
		$do_it = false;
		$error = true;
		$errors[] = "You must be enrolled in school to submit artwork.";
	}
	if(!$error)
		update_user_meta($user_ID, 'school', $_POST['school']);
}
if(isset($check['location']))
{	
	$error = false;
	if(!isset($_POST['location']) || empty($_POST['location']))
	{
		$do_it = false;
		$error = true;
		$errors[] = "You must enter your location to submit artwork.";
	}
	if(!$error)
		update_user_meta($user_ID, 'location', $_POST['location']);
}
// check the file type
if($_FILES['artwork']['type'] != 'image/jpeg')
{
	$do_it = false;
	$errors[] = 'The artwork must be in jpeg format.';
}

  if ($contestname) {
    $post_status = 'draft';
  } else {
    $post_status = 'publish';
  }

  if ($do_it) {
    $new_post = array(
    'post_title' => $_POST['title'],
    'post_content' => $_POST['more_info'],
    'post_status' => 'publish',
    'post_date' => date('Y-m-d H:i:s'),
    'post_author' => $user->ID,
    'post_type' => 'artwork',
    'post_category' => array(0), 
    'post_status' => $post_status
  );

  $artwork_id = wp_insert_post($new_post, false);
  if ($artwork_id > 0) {
    $overrides = array( 'test_form' => false);
    $file = media_handle_upload('artwork', $artwork_id, $overrides);
      if (!empty($file)) {
          update_post_meta($artwork_id, 'year', $_POST['raey']);
      update_post_meta($artwork_id, 'medium', $_POST['medium']);
      update_post_meta($artwork_id, 'size', $_POST['size']);

      eas_save_cc($artwork_id);

      if ($contestform) {
        update_post_meta($artwork_id, 'contest', $_POST['contestname']);
        $redirect_url = '/'.$_POST['contestname'].'/thanks';
      } else {
        $redirect_url = eas_artwork_url($artwork_id).'?action=upload';
      }
			// mail			
			eas_mail('info@emergentartspace.org', 'Artwork has been submitted', eas_artwork_url($artwork_id));

      wp_redirect($redirect_url);
      exit;
    }

  
  }

  }
  

}

if (!eas_is_artist() && !$contestform) {
  $_GET['action'] = 'notartist';
} 

?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>

        <?php get_template_part('loop','page'); ?>

        <?php roots_loop_after(); ?>


        <?php // The form for uploading art ?>
				<?php if(count($errors)): ?>
				<ul class="errorList">
				<?php foreach($errors as $error): ?>
					<li><?= $error ?></li>
				<?php endforeach; ?>
				</ul>
				<?php endif; ?>
        <?php if (is_user_logged_in()) { 
          eas_artwork_form(true, '/upload', 'upload_art', false, false, false, $contestname, $contestform);
         } else {
          eas_not_logged_in();
         } ?>

      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<script type="text/javascript">
$(document).ready(function() {
	$('input[name=birthday]').datepicker({changeYear: true, yearRange: "1950:", changeMonth: true});
});
</script>
<?php get_footer(); ?>

