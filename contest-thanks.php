<?php
/*
Template Name: Contest Thanks
*/
get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'page'); ?>
        <?php roots_loop_after(); ?>
        <?php 

          $how_many = eas_how_many_more_submissions(get_post_meta($post->post_parent, 'contest', true));

          if ($how_many === false) {
            echo '<p class="howmany">Thank you for your submissions!</p>';
          } else {
            echo '<p class="howmany">'.$how_many.'   <a href="../" class="btn" title="Upload Another Work">Upload more art!</a></p>';
          }

        ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>