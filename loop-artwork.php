
<?php /* Start loop */ ?>
<?php while (have_posts()) : the_post(); ?>
  <?php roots_post_before(); ?>
    <?php

      

      $author_id = get_the_author_meta('ID');

      $artist = eas_is_artist($author_id);
      $owner = eas_user_is_owner();
      $contest = get_post_meta($post->ID, 'contest', true);
      $contest = !empty($contest);

      $winner = get_post_meta($post->ID, 'winner', true);

      $juror = eas_user_is_juror();

      if ($contest) {
               $linkurl = trailingslashit(get_bloginfo('siteurl')).'crossingborders/submissions/'.$author_id;

      } else {
         $linkurl = eas_artist_page_url($author_id);
      }

    ?>
    <article <?php post_class() ?> id="post-<?php the_ID(); ?>">
    <?php roots_post_inside_before(); ?>
    <?php if ($artist || $owner || ($contest && $juror)) { ?>
      <?php
        if (eas_is_edit()) {
          $edit = true;
          $edit_class = ' edit';
        } else {
          $edit = false;
          $edit_class = '';
        }
      ?>
      <div class="container<?php echo $edit_class; ?>">
        <article <?php post_class('row'.$edit_class) ?> id="post-<?php the_ID(); ?>">
          <div class="span8">
            <?php
              $img = eas_artwork_img($post->ID, 'full');
              $width = $img[1];
              $height = $img[2];
              $src = $img[0];
            ?>
            <div class="artcontainer">
            <img class="singleart" data-width="<?php echo $width; ?>" data-height = "<?php echo $height; ?>" src="<?php echo $src; ?>">
            </div>
          </div>
          <div class="span4">

            <?php  $meta = get_post_meta($post->ID); ?>

            <?php if (!$edit) { ?>

              <div class="artworkinfo">
                <h3 class="artistname"><?php eas_display_avatar($author_id); ?><a href="<?php echo $linkurl ?>" title="<?php the_author(); ?>"><?php the_author(); ?></a></h3>
                <?php if (eas_user_is_juror() & !$contest) {
                  ?>
                    <p><a href="<?php echo trailingslashit(get_bloginfo('siteurl')).'crossingborders/submissions/'.$author_id ?>">View Crossing Borders Submissions</a></p>
                  <?php
                } ?>
                <h1 class="entry-title"><?php the_title(); ?></h1>

                <small>
                  <?php
                    eas_display_meta($meta, 'year', false);
                    eas_display_meta($meta, 'medium', false);
                    eas_display_meta($meta, 'size', false);
                  ?>

                   <?php the_content(); ?>
                   <?php if ($contest && eas_user_is_admin()) {
                      ?><p><input type="checkbox" class="winner" <?php if ($winner) echo "checked"; ?>> Include in contest</p><?php
                   } ?>
                  <?php if (!$artist && !$contest) { ?>
                    <div class="alert alert-warning">You have not yet been approved as an artist. Until then, your art is visible only to you.</div>
                  <?php } ?>

                    <?php
                      eas_who_favorited();
                    ?>
                </small>

              </div>

              <?php eas_favorite_button() ?><?php eas_homebg_button(); ?><?php eas_delete_button(); ?>
              <?php if (eas_user_is_owner()) eas_edit_button(); ?>
              <?php eas_where_tagged(); ?>
              <?php eas_artwork_thread($post->ID, false); ?>

            <?php } else if (eas_user_is_owner()) { ?>
              <form method="post" action="">
                <input type="hidden" name="ID" value="<?php echo $post->ID; ?>">
                <input type="text" name="title" placeholder="Title" value="<?php the_title(); ?>">
                <input type="text" name="artwork_year" placeholder="Year" value="<?php echo $meta['year'][0]; ?>">
                <input type="text" name="medium" placeholder="Medium" value="<?php echo $meta['medium'][0]; ?>">
                <input type="text" name="credits" placeholder="Artist Credits" value="<?php echo $meta['credits'][0]; ?>">
                <p>
                  <?php 
                  eas_editor(get_the_content(), 'moreinfo', 'more_info', false);
                  ?>
                </p>
                <?php eas_save_button(); ?>
              </form>
            <?php } else { ?>
              <?php eas_not_owner(); ?>
            <?php } ?>
          </div>
        </article>
      <?php } else { ?>

        This artwork is unavailable.

      <?php } ?>

    </div>


      <?php roots_post_inside_after(); ?>
    </article>
  <?php roots_post_after(); ?>
<?php endwhile; /* End loop */ ?>
<?php eas_display_cc(); ?>
