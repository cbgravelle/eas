<?php

$searchtype = eas_page_type();

?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <div class="page-header">
          <h3><?php _e('Search Results for', 'roots'); ?> <strong><?php echo get_search_query(); ?></strong></h3>
        </div>
				<div>
          <?php eas_artist_search_results(get_search_query()); ?>
				</div>
				<div>
					<?php eas_forum_search_results(get_search_query()); ?>
				</div>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>
