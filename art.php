<?php
/*
Template Name: Art
*/
?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">

          <?php roots_loop_before(); ?>
          <?php get_template_part('loop', 'artpage'); ?>
          <?php roots_loop_after(); ?>
          <?php eas_page_links(); ?>

          <?php eas_get_gallery_forum_posts(get_query_var('paged'), 9); ?>
            

          <?php/*  ?>
          <div class="span3">
            <?php get_currentuserinfo(); ?>
            <?php $favs = eas_get_favorites($user_ID); 
              if (count($favs)) { 
                ?>
                <div class="page-header favorites-header">
                   <h3>Your Favorites</h3>
                </div>
                    <?php
                      foreach ($favs as $f) {
                        $img = eas_artwork_img($f->id, 'large');
                        $src = $img[0];
                        ?>
                          <img src="<?php echo $src; ?>">
                        <?php
                      }
                    ?>
                <?php 
              }
             ?>
          </div>  <?php */?>

      </div><!-- /#main -->

    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>