<?php
  if ($wp_query->query_vars['galleryview'] == 1) {
    $galleryview = true;
  } else {
    $galleryview = false;
  }
  if (eas_user_is_admin()) {

      if ($wp_query->query_vars['feature'] == "1") {
        eas_feature();
        $_GET['action'] = 'feature';
        wp_redirect(eas_forum_url($post->ID).'?action=feature');
        exit;
      } else if ($wp_query->query_vars['feature'] == "-1") {
        eas_unfeature();
        $_GET['action'] = 'unfeature';
        wp_redirect(eas_forum_url($post->ID).'?action=unfeature');
        exit;
      }
  }

  if ($wp_query->query_vars['delete'] == 1) {
    $post = wp_update_post(array('ID' => $post->ID, 'post_status' => 'trash'), false);
    if ($post !== 0) {
      wp_redirect('/forum?action=delete');
      exit();
    } else {
      _d('failed to delete');
    }
  }


  $reply = false;
  if ($wp_query->query_vars['reply'] == "1") {
    $wp_query->query_vars['posts_per_page'] = 1;
    $reply = true;
  }

  if (eas_user_is_owner() && isset($_POST['ID']) && intval($_POST['ID']) == $post->ID) {
    $updated_post = array(
      'ID' => $post->ID,
      'post_title' => $_POST['post_title'],
      'post_content' => $_POST['post_content']
    );
    $updated = wp_update_post($updated_post);
    wp_redirect(eas_forum_url($post->ID).'?action=updateforumpost');
    exit;

  }
?>

<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
          <?php
          $logged_in = is_user_logged_in();
          if ($reply && $logged_in) {
              eas_forum_form($post->ID, 0, true);
          }
          if ($logged_in || !$reply) {
            roots_loop_before();
            if ($galleryview) $loop_part = 'art';
            else $loop_part = 'forum';

            get_template_part('loop', $loop_part);

            roots_loop_after();
          } else {
            eas_not_logged_in();
          }
        ?>


      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>