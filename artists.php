<?php
/*
Template Name: Artists
*/
?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'artists'); ?>
        <?php roots_loop_after(); ?>
        
				<div>
					Sort by: <?php if(!isset($_GET['sort'])): ?><strong>
                    <?php else: ?><a class="gray3" href="/artists/"><?php endif; ?>
                      Recently Updated 
                    <?php if(!isset($_GET['sort'])): ?></strong>
                   <?php else: ?></a><?php endif; ?>
                  |
                   <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name'): ?><strong>
                      <?php else: ?><a class="gray3" href="?sort=name"><?php endif; ?>
                        Name
                      <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name'): ?></strong>
                    <?php else: ?></a><?php endif; ?>
        </div>
        <?php eas_page_links(); ?>	


        <div id="artists_grid">
          <?php
						if(isset($_GET['sort']) && $_GET['sort'] == 'name')
						{ 
              eas_artists_grid(get_query_var('paged'), 20, false, 'name');
              //eas_recently_updated_artists(get_query_var('paged'), 10, false, 'name');
            }						
						else
						{ 
              eas_artists_grid(get_query_var('paged'), 20, false, 'recent');
              //eas_recently_updated_artists(get_query_var('paged')); 
            }
          ?>
          <br class="clear" />
        </div>

        <?php eas_page_links(); ?>
        
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>
