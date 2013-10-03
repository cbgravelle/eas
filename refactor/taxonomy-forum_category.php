<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
          <div class="page-header forumcats">
            <h1>
              <?php echo $wp_query->queried_object->name; ?>
            </h1>
            <span class="catdescription">
              <?php echo $wp_query->queried_object->description; ?>
            </span>
            <?php if ($wp_query->queried_object->term_id != 7) eas_new_post_button(); ?>
            <?php eas_forum_cat_menu(); ?>

          </div>
          <?php eas_page_links(); ?>
          <?php roots_loop_before(); ?>
            <?php get_template_part('loop', 'forum'); ?>
          <?php roots_loop_after(); ?>
          <?php eas_page_links(); ?>


      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>