<?php
/*
Template Name: Your Favorites
*/
get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'artpage'); ?>
        <?php roots_loop_after(); ?>

        <?php
        if (is_user_logged_in()) {
          get_currentuserinfo();
          $favs = eas_get_favorites($user_ID);
          if (count($favs)) {
            foreach ($favs as $f) {
              echo eas_get_figure_tag($f->id, 'large');
            }
          } else {
            echo '<p>This is where your favorite works of art from EAS will go! Just click the star (<a class="btn btn-mini">&#9733;</a>) next to any work of art on the site.</p>
            <p>Here are works that EAS users are looking at right now. If any of these catch your eye... Click the star next to them and they\'ll be here next time you visit!</p>
            ';
            echo '<div class="showfavbutton">';
            $recentlyviewed = eas_recently_viewed_works(0, true);
            foreach ($recentlyviewed as $a) {
              eas_figure_tag($a, 'large');
            }
            echo '</div>';
          }

        } else {
          eas_not_logged_in();
        }

        ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>