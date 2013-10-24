<?php
/*
Template Name: Change Password
*/

get_currentuserinfo();

if (isset($_POST['newpass'])) {
  wp_update_user(array(
    'ID' => $user_ID,
    'user_pass' => $_POST['newpass']
  ));

  $_GET['action'] = 'updatepassword';
}



get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'settings'); ?>
        <?php if (is_user_logged_in()) { ?>
        <form class="form-inline" method="post" action="">
          <p>Enter your desired new password here to change it.</p>
          <input name="newpass" type="password" placeholder="New Password">
          <input class="btn" type="submit" value="Save">
        </form>
        <?php } ?>
        <?php roots_loop_after(); ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>