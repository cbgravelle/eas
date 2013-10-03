<?php
/*
Template Name: Profile
*/
?>
<?php

get_currentuserinfo();

if (isset($_POST['updateprofile']) && $_POST['updateprofile'] == 1) {
  $nickname = $_POST['display_name'];
  $user = array(
    'ID' => $user_ID,
    'nickname' => $nickname,
    'display_name' => $nickname,
    'user_nicename' => $nickname
  );

  wp_update_user($user);


  update_user_meta($user['ID'], 'school', $_POST['school']);
  update_user_meta($user['ID'], 'description', $_POST['description']);
  update_user_meta($user['ID'], 'location', $_POST['location']);
  update_user_meta($user['ID'], 'birthday', $_POST['birthday']);
  eas_save_cc($user['ID'], true);
  if (isset($_POST['removeavatar']) && $_POST['removeavatar'] == 'on') {
    delete_user_meta($user['ID'], 'avatar');
  } else if (isset($_FILES['biophoto'])) {
    error_log(isset($_FILES['biophoto']));
    require_once(ABSPATH.'/wp-admin/includes/image.php'); 
    require_once(ABSPATH.'/wp-admin/includes/file.php'); 
    require_once(ABSPATH.'/wp-admin/includes/media.php'); 
    $overrides = array( 'test_form' => false);
    $file = media_handle_upload('biophoto',0, $overrides);
    if (!is_wp_error($file)) {
      update_user_meta($user['ID'], 'avatar', $file);  

    }
  }

  $_GET['action'] = 'updateprofile';

}

?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>

          <?php get_template_part('loop','settings'); ?>

        <?php roots_loop_after(); ?>


        <?php // The form for updating your profile ?>
        <?php if (is_user_logged_in()) {
          get_currentuserinfo();
          $current_userdata = get_userdata($user_ID);
          $current_usermeta = get_user_meta($user_ID);
          ?>

          <form enctype="multipart/form-data" action="/settings" method="post" id="update_profile">
            <input type="hidden" name="updateprofile" value="1">
            <p><label for="display_name">Display Name As <span class="required">*</span></label><input type="text" name="display_name" value="<?php echo $current_userdata->data->display_name; ?>"></p>
            <p><label for="location">Location <span class="required">*</span></label><input type="text" name="location" value="<?php echo $current_usermeta['location'][0]; ?>"></p>
            <p><label for="school">School (if applicable)</label><input type="text" name="school" value="<?php echo $current_usermeta['school'][0]; ?>"></p>
            <p><label for="birthday">Birthday (for internal use only) <span class="required">*</span></label><input type="text" name="birthday" value="<?php echo $current_usermeta['birthday'][0]; ?>"></p>
						<p><span class="required">*</span> required</p>

            <h3>Profile Picture</h3>
            <?php
              eas_display_avatar($user_ID);
            ?>
            <p><input type="file" name="biophoto"></p>
            <p><label for="removeavatar" class="checkbox"><input type="checkbox" name="removeavatar">Remove Profile Picture?</label></p>
            <p>Artist Statement</p>
            <p>
              <?php 
              eas_editor($current_usermeta['description'][0], 'description', 'description')
              ?>
            </p>
            <?php if (count(eas_artworks_by_user($user_ID))) { 
              eas_cc_settings('Default Creative Commons License');
             } ?>
            <p>
              <input type="submit" class="btn btn-primary" value="Save Settings">
            </p>
          </form>

        <?php } else {?>
          You must log in to edit your profile.
        <?php } ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<script type="text/javascript">
$(document).ready(function() {
  $('input[name=birthday]').datepicker({changeYear: true, yearRange: "1950:", changeMonth: true });
});
</script>
<?php get_footer(); ?>
