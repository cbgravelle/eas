<?php
/*
Template Name: Forum
*/
?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php while(have_posts() ) { the_post(); ?>


                <div class="page-header forumcats">
                  <div>
                    <h1><?php the_title(); ?></h1>
                    <span class="catdescription">
                      <?php the_content(); ?>
                    </span>
                    <?php eas_new_post_button(); ?>
                  </div>
                  <?php eas_forum_cat_menu(); ?>
                </div>

        <?php } ?>
                  <?php eas_page_links(); ?>

        <?php eas_get_featured_forum_posts(); ?>

                  <?php eas_page_links(); ?>



      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>