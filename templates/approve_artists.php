<?php
/*
Template Name: Approve Artists
*/
?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
       <?php  if (eas_user_is_admin()) {
        ?>
 <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'page'); ?>
        <?php roots_loop_after(); ?>
        
        <?php eas_page_links(); ?>
        <ul class="users">

          <?php
            eas_unapproved_artists(get_query_var('paged'));
          ?>
        </ul>
        <?php eas_page_links(); ?>
        <?php

      }
      ?>
       



      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>