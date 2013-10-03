<?php
/*
Template Name: Register
*/

$redirecturl = false;
if (isset($_REQUEST['redirect'])) {
  $redirecturl = $_REQUEST['redirect'];
}

?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'page'); ?>
        <?php roots_loop_after(); ?>
  
        <?php if (!is_user_logged_in()) { ?>
          <h3>Create an account in seconds!</h3>
          <p>You'll be able to post on the forum and submit your art to the site.</p>
          <?php eas_register_form($redirecturl); ?>
          <p><a href="/login" title="Login">Already have an account?</a></p>
          <p><a href="/forgotpassword" title="Forgot Password">Forgot your password?</a></p>
        <?php } else { ?>

          You are already logged in.

        <?php } ?>



      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>