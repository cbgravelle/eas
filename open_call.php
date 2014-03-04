<?php
/*
Template Name: Open Call
*/
?>

<?php 

global $user_ID, $meta, $data, $alert_message, $contestname, $post_status, $do_it, $errors;

$current_user = wp_get_current_user();
$user_ID = $current_user->ID;
$user_meta = get_user_meta($user_ID);
$alert_message = "";
$contestname = 'opencall';
$post_status = 'private';
$do_it = false;

function con( $data ) {
  if ( is_array( $data ) ) 
    $output = "<script>console.log( 'DEBUG: " . implode( ',', $data) . "' );</script>";
  else $output = "<script>console.log( 'DEBUG: " . $data . "' );</script>";
  echo $output;
}


function oc_get_artwork_form($action = "/upload", $the_post = false, $the_meta = false, $disabled = false) {

  global $user_ID, $meta;

  get_currentuserinfo();
  $user_ID = get_current_user_id();
  $meta = get_user_meta($user_ID);
  $title = $year = $medium = $credits = $moreinfo = '';
  $elid = "opencall";
  $contestname = "opencall";
  $full_name = empty($user_meta['last_name']) ? $meta['first_name'][0] : $meta['first_name'][0].' '.$meta['last_name'][0];
  $name = $user_meta['nickname'][0] ?: $full_name;
  $last_name = $meta['last_name'][0];
  $school = $_POST['school'] ?: $meta['school'][0];
  $location = $_POST['location'] ?: $meta['location'][0];
  $birthday = $_POST['birthday'] ?: $meta['birthday'][0];
  $title = $_POST['title'];
  $year = $_POST['raey'];
  $medium = $_POST['medium'];
  $size = $_POST['size'];
  $moreinfo = $_POST['more_info'];

  $the_return = '<form ';

    $the_return.= ' enctype="multipart/form-data"'; 
    $the_return .='action="'.$action.'" method="post" id="'.$elid.'">';
  
    $the_return .='<div class="oc_inputs">';

      $nudge = array();
      foreach (array('School', 'Location', 'Birthday') as $m) {
        if (!(isset($meta[strtolower($m)]) && count($meta[strtolower($m)])) && !empty($meta[strtolower($m)][0])) { 
          $required = $n != 'School' ? ' <span class="required">*</span>' : '';
          $the_return.='<p><input type="text" name="'.strtolower($m).'" placeholder="'.$m.'" value="">'.$required.' </p>';
        } 
      }

      $the_return .= '
      <p><input type="text" name="title" placeholder="Title" value="'.$title.'"></p>
      <p><input type="text" name="raey" placeholder="Year" value="'.$year.'"></p>
      <p><input type="text" name="medium" placeholder="Medium" value="'.$medium.'"></p>
      <p><input type="text" name="size" placeholder="Size" value="'.$size.'"></p>
    </div>

    <div class="oc_right">';
      $the_return .=' <div class= "oc_art_in"><input type="file" name="artwork" class="oc_art_input"></div>';
      $the_return .= '<input type="hidden" name="contest" value="'.$contestname.'">
      <p>Brief Description</p>'.eas_get_editor($moreinfo, 'moreinfo', 'more_info', true, '150px').'</p>
    </div>';

    $the_return .='<input type="hidden" id="attachmentid" name="artwork_img_id" value="">';
    $the_return .='<input id="artworksubmit" type="submit" class="btn btn-primary" value="Submit">';

  $the_return.='</form>';

  echo $the_return;
}

get_currentuserinfo();

if(!$user_ID) {
  wp_redirect('login?redirect=/opencall');
}

if (isset($_FILES['artwork'])) {

  require_once(ABSPATH.'/wp-admin/includes/image.php'); 
  require_once(ABSPATH.'/wp-admin/includes/file.php'); 
  require_once(ABSPATH.'/wp-admin/includes/media.php'); 

  $do_it = true;

  /*///////// FORM ERROR CHECKING ////////////*/

  $errors = array();

  if($_FILES['artwork']['type'] != 'image/jpeg') {
    $errors[] = 'The artwork must be in jpeg format.';
  }
  if(!isset($_POST['title']) || empty($_POST['title'])) {
    $errors[] = "Title cannot be blank.";
  }
  if(!isset($_POST['raey']) || empty($_POST['raey'])) {
    $errors[] = "Year cannot be blank.";
  }
  if(!isset($_POST['medium']) || empty($_POST['medium'])) {
    $errors[] = "Medium cannot be blank.";
  }
  if(!isset($_POST['size']) || empty($_POST['size'])) {
    $errors[] = "Size cannot be blank.";
  }
  if(!isset($_POST['more_info']) || empty($_POST['more_info'])) {
    $errors[] = "Description cannot be blank.";
  }
  if (sizeof($errors) > 0) {
    $do_it = false;
  }

  /*//////////// POSTING ARTWORK ////////////////*/

  if ($do_it) {

    $new_post = array(
    'post_title' => $_POST['title'],
    'post_content' => $_POST['more_info'],
    'post_date' => date('Y-m-d H:i:s'),
    'post_author' => $user->ID,
    'post_type' => 'artwork',
    'post_category' => array(0), 
    'post_status' => $post_status
    );

    $artwork_id = wp_insert_post($new_post, true);

    if ($artwork_id < 1) {
      echo '<script>console.log("DEBUG: $do_it = TRUE but $artwork_id < 1")</script>';
    } else {
      $overrides = array( 'test_form' => false);
      $file = media_handle_upload('artwork', $artwork_id, $overrides);
      if (!empty($file)) {
        update_post_meta($artwork_id, 'year', $_POST['raey']);
        update_post_meta($artwork_id, 'medium', $_POST['medium']);
        update_post_meta($artwork_id, 'size', $_POST['size']);
        update_post_meta($artwork_id, 'contest', 'opencall');
          
        $redirect_url = '/opencall/thanks';

  			eas_mail('info@emergentartspace.org', 'Artwork has been submitted to Open Call', eas_artwork_url($artwork_id));

        wp_redirect($redirect_url);
        exit;
      }
    }
  }
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

				<?php if(count($errors)): ?>
				  <ul class="errorList">
				    <?php foreach($errors as $error): ?>
					  <li><?= $error ?></li>
				    <?php endforeach; ?>
				  </ul>
				<?php endif; ?>

        <?php if (is_user_logged_in()) { 
          oc_get_artwork_form(true, '/opencall', 'upload_art', false, false, false);
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

