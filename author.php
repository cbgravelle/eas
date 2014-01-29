<?php
  
  $this_author = get_userdata($author);
  $this_meta = get_user_meta($author);

  $admin = eas_user_is_admin();
  $owner = eas_user_is_owner();

  if ($wp_query->query_vars['approve'] == 1 && $admin) {

    $temp_user = new WP_User($author);



    $temp_user->set_role('artist');

    delete_user_meta($author, 'unverified');
    eas_update_usermeta($author, 'verified', 1);

    eas_artist_approval_email($author);

    wp_redirect(eas_artist_page_url($author).'?action=artistapproved');
    exit;
  } else if ($wp_query->query_vars['approve'] == -1 && $admin) {
    eas_update_usermeta($author, 'declined', 1);
    wp_redirect('/artists/approve/?action=decline');
  }

  if ($wp_query->query_vars['follow'] == '1') {
    eas_follow($author);
    wp_redirect(eas_artist_page_url($author).'?action=follow');
    exit;
  } else if ($wp_query->query_vars['follow'] == '-1') {
    eas_unfollow($author);
    wp_redirect(eas_artist_page_url($author).'?action=unfollow');
    exit;
  }
  $contestview = false;
  $juror = eas_user_is_juror();
  if ($wp_query->query_vars['contestname'] == 'crossingborders' && $juror) {
    $contestview = true;
    $contestname = 'crossingborders';
  } else if ($wp_query->query_vars['contestname'] == 'crossingborders' && !$juror) {
    wp_redirect(eas_artist_page_url($author));
  }
  $artist = eas_is_artist($author);
  $artistpending = false;
  if (!$artist && !$contestview && !$admin) {
    $artistpending = true;
    $_GET["action"] = 'artistpending';
  }

?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php if ($artist || $owner || $contestview) { ?>
          <div class="page-header">
            <div id="artist_profile_header">
              <div class="artist_avatar float_left">
                <?php eas_display_avatar($author); ?>
              </div>
              <div id="artist_profile_info" class="float_left">
                <div class="artist_name"><?php echo $this_author->nickname; ?></div>
                <?php eas_follow_button(); ?>   

                <?php eas_display_meta($this_meta, 'location');?>
                <?php eas_display_meta($this_meta, 'school'); ?>
                <?php eas_display_email_for_admins($author); ?>
              </div>
              <br class="clear" />
            </div>  
          </div>
          <?php if ($artistpending && $admin) eas_approve_artist_button(); ?>
          <?php if (!$contestview) { ?>
          <small>
            <div class="userdescription">
              <?php eas_display_meta($this_meta, 'description', false); ?>
            </div>
          </small>
          <?php } ?>
          <div class="row">
            <div class= "span9" <?php /*echo MAIN_CLASSES; */?>>
              <?php

                if (!$contestview) {
                  $artworks = eas_artworks_by_user($author);
                } else {
                  $artworks = eas_artworks_by_user($author, -1, $contestname);
                  ?>
                    <p><a href="<?php echo trailingslashit(get_bloginfo('siteurl')).'crossingborders/submissions' ?>">View all Crossing Borders submissions</a>
                  <?php
                }
                  foreach ($artworks as $a) {
                    echo eas_get_figure_tag($a->ID, 'large');
                  }



              ?>
            </div>
            <?php if (!$contestview) { ?>
            <div class="span3">
              <?php $favs = eas_get_favorites($author); 
              if (count($favs)) { ?>
                   <h3><?php echo $this_author->nickname; ?>'s Favorites</h3>
                   <br />
                    <?php foreach ($favs as $f) {
                        $img = eas_artwork_img($f->id, 'large');
                        $src = $img[0];
                        $url = eas_artwork_url($f->id); 
                        echo "<a href= ".$url."><img src= ".$src."></a>";
                     } ?>
              <?php } ?>

            </div>
            <?php } ?>
          <?php } else {
            ?>

              This user has not yet been approved to be an artist. Once your art is submitted it should take no more than 48 hours for approval.

            <?php
          } ?>
        </div>
        
        
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>