<?php
/*
Template Name: Open Call Thanks
*/
?>

<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop','page'); ?>
        <?php roots_loop_after(); ?>
        <?php 
          if(is_user_logged_in()) {
            echo oc_display_user(get_current_user_id(), true, 'opencall', 'opencall/thanks');
          } else {
            wp_redirect('login?redirect=/opencall/thanks');
          }
        ?>


      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>

<?php get_footer(); ?>

