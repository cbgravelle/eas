<?php
/*
Template Name: Open Call Thanks
*/
?>

<?php 


/*///////// GETTING SUBMISSIONS /////////*/

/*
function oc_display_user($li, $contest = false) {
  $id = get_current_user_id();
  $this_author = get_userdata($id);
  $artworks = eas_artworks_by_user($id, 6, $contest);
  $meta = get_user_meta($id);

  if ($contest !== false) {
    $linkurl = trailingslashit(get_bloginfo('siteurl')).$contest.'/submissions/'.$id;
  } else {
    $linkurl = eas_artist_page_url($id);
  }

  $the_return = '';
  
  $the_return.='
      <a class="oc_userblock" href="'.$linkurl.'">
        <h3>
        <span class="nickname">'.$this_author->nickname.'</span>
          '.eas_get_follow_button($id, true).'</h3>
        <span class="usermeta">
          '.oc_get_meta_display($meta, 'location', false).'
        </span>
        <span class="usermeta">'.eas_get_birthday_display_for_admins($id).'</span>
        <span class="usermeta">
          '.eas_get_email_display_for_admins($id).'
        </span>
    </a>
      <div class="imagelist row">
  ';

    foreach ($artworks as $a) {

      $img = eas_artwork_img($a->ID, 'span2-crop');
      $src = $img[0];

      $the_return.='
          <a href="'.eas_artwork_url($a->ID).'">
            <figure class="span2"><img src="'.$src.'">
            </figure>
          </a>
      ';
    }

  $the_return.='</div>';
  return $the_return;
}

/*
function eas_get_meta_display($meta, $key, $display_key = true) {

  if (isset($meta[$key]) && !empty($meta[$key][0])) {
    if ($key == 'location') {
      $label = 'Location:&nbsp&nbsp';
      $text = $meta[$key][0];
    } else if ($key == 'year') {
      $label = 'Year:&nbsp&nbsp';
      $text = $meta[$key][0];
    } else if ($key == 'medium') {
      $label = '';
      $text = $meta[$key][0];
    } else if ($key == 'description') {
      $label = 'Description:&nbsp&nbsp';
      $text = $meta[$key][0];
    } else if ($key == 'school') {
      $label = 'School:&nbsp&nbsp';
      $text = $meta[$key][0];
    } else if ($key == 'prize') {
      $label = 'Prize:&nbsp&nbsp';
      $text = $meta[$key][0];
    }
  }

  $the_return = '<div class="artistmeta">';

  if (!empty($text)) {
    if ($display_key) {
      $the_return.='<div class="artistmetaname">'.$label.'</div>';
    }
      $the_return.='<div class="artistmetaval inline">'.$text.'</div>';
  }
  $the_return .= '</div>';

  return $the_return;
} */
?>

<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop','page'); ?>
        <?php roots_loop_after(); ?>
        <?php echo oc_display_user(get_current_user_id(), true, 'opencall'); ?>
        <br class="clear" />
        <?php /*
        } else {
          echo 'not logged in';
        }  */?>

        <?php eas_page_links(); ?>


      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>

<?php get_footer(); ?>

