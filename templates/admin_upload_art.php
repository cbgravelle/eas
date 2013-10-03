<?php
/*
Template Name: Admin Upload Art
*/
?>
<?php



if (isset($_FILES['artwork'])) {
require_once(ABSPATH.'/wp-admin/includes/image.php'); 
require_once(ABSPATH.'/wp-admin/includes/file.php'); 
require_once(ABSPATH.'/wp-admin/includes/media.php'); 


if (!empty($_POST['newuseremail'])) {
  $email_arr = explode('@',$_POST['newuseremail']);
  $user_login = $email_arr[0];
  $new_id = wp_insert_user(array(
    'user_email' => $_POST['newuseremail'],
    'user_login' => $user_login
    )

  );

  eas_update_usermeta($new_id, 'verified', '1');

  eas_update_usermeta($new_id, 'location', $_POST['newuserlocation']);
  eas_update_usermeta($new_id, 'school', $_POST['newuserschool']);
  eas_do_pass_reset_email($new_id, $_POST['newuseremail'], false);
  $_POST['user_id'] = $new_id;
}


$contestname = false;
if (!empty($_POST['contest'])) {
    $is_contest = true;
    $contestname = $_POST['contest'];
}

$post_status = 'publish';

if ($is_contest) {
    $post_status = 'draft';
}

$wp_user_object = new WP_User($_POST['user_id']);
$wp_user_object->set_role('artist');

  $new_post = array(
    'post_title' => $_POST['title'],
    'post_content' => $_POST['more_info'],
    'post_status' => $post_status,
    'post_date' => date('Y-m-d H:i:s'),
    'post_author' => $_POST['user_id'],
    'post_type' => 'artwork',
    'post_category' => array(0)
  );

  $artwork_id = wp_insert_post($new_post, false);

  if ($artwork_id > 0) {
    $overrides = array( 'test_form' => false);
    $file = media_handle_upload('artwork', $artwork_id, $overrides);
    
    update_post_meta($artwork_id, 'year', $_POST['year']);
    update_post_meta($artwork_id, 'medium', $_POST['medium']);
    update_post_meta($artwork_id, 'credits', $_POST['credits']);
    error_log($contestname);
    if ($contestname !== false) {
      update_post_meta($artwork_id, 'contest', $contestname);
    }

    wp_redirect(eas_artwork_url($artwork_id).'?action=upload');
    exit;
  
  }


}

if (eas_user_is_admin()) {

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
        <?php if (is_user_logged_in()) { ?>


          <form enctype="multipart/form-data" action="/adminupload" method="post" id="upload_art">
            <?php
              $args = array('orderby' => 'display_name', 'order' => 'asc');

              $users = get_users($args);
              echo '<select name="user_id">';
              foreach ($users as $u) {
                  ?>
                    <option value="<?php echo $u->ID; ?>"><?php echo $u->display_name; ?></option>
                  <?php
              }
              echo '</select>';


            ?>
            <h3>or create a new artist:</h3>
            <p><input type="text" name="newuseremail" placeholder="New Artist's Email"></p>
            <p><input type="text" name="newusername" placeholder="New Artist's Username"></p>
            <p><input type="text" name="newlocation" placeholder="New Artist's Location"></p>
            <p><input type="text" name="newschool" placeholder="New Artist's School"></p>

            <p>&nbsp;</p>


            <p><input type="file" name="artwork"></p>
            <p><input type="text" name="title" placeholder="Title"></p>
            <p><input type="text" name="year" placeholder="Year"></p>
            <p><input type="text" name="medium" placeholder="Medium"></p>
            <p><input type="text" name="credits" placeholder="Artist Credits"></p>
            <p>
              <select name="contest">
                <option value="" selected>No Contest</option>
                <option value="crossingborders">Crossing Borders</option>
              </select>
            </p>
            <p>
              <?php 
              eas_editor(get_the_content(), 'moreinfo', 'more_info', true);
              ?>
            </p>
            <p>
              <input type="submit" class="btn btn-primary" value="Submit">
            </p>
          </form>

        <?php } ?>

      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer();

}

