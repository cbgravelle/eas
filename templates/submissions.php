<?php
/*
Template Name: submissions
*/
?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'page'); ?>
        <?php roots_loop_after(); ?>
        
        <?php eas_page_links(); ?>
        <ul class="users">

          <?php
          $contest = get_post_meta($post->ID, 'contest', true);
            if (eas_user_is_juror()) {
                          eas_recently_updated_artists(get_query_var('paged'), 10, $contest);

            }
          ?>
        </ul>
        <?php eas_page_links(); ?>



      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>