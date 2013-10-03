<?php
/*
Template Name: New Forum Post
*/
?>
<?php

  if (isset($_POST['post_content'])) {
    $post_content = $_POST['post_content'];
    $post_parent = $_POST['parent'];
    $post_title = $_POST['post_title'];
    $cat = intval($_POST['cat']);
    



    global $user_ID;
    $new_post = array(
      'post_title' => $post_title,
      'post_content' => $post_content,
      'post_status' => 'publish',
      'post_date' => date('Y-m-d H:i:s'),
      'post_author' => $user_ID,
      'post_type' => 'forum',
      'post_parent' => $post_parent,
      'post_category' => array(0)
    );

    $post_id = wp_insert_post($new_post, false);

    wp_set_object_terms($post_id, $cat, 'forum_category');

    if ($post_parent > 0) {

      $parent_post = get_post($post_parent);


      error_log('about to notify');
      eas_notify($parent_post->post_author, $post_id, $post_parent, 'forumemail');


      $op = get_post_custom_values('op', $post_parent);
      $op = $op[0];
    } else {
      $op = $post_id;
    }

    if (isset($_POST['assoc_id'])) {
      update_post_meta($post_id, 'assoc_id', $_POST['assoc_id']);
      $assoc_post = get_post($_POST['assoc_id']);

      eas_notify($assoc_post->post_author, $post_id, $_POST['assoc_id'], 'artemail');

    }

    update_post_meta($post_id, 'op', $op);

    if ($post_id > 0) {
      header('Location: '.eas_forum_url($op).'?action=new'.'#'.$post_id);
      exit;
    }
  }

  if (isset($wp_query->query_vars['reply'])) {
    $parent_id = $wp_query->query_vars['reply'];
  } else {
    $parent_id = 0;
  }

?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>

        <?php /* Start loop */ ?>

        <?php while (have_posts()) : the_post(); ?>
          <?php roots_post_before(); ?>
            <?php roots_post_inside_before(); ?>
              <div class="page-header">
                <h1>
                  <?php
                    if ($parent_id > 0) {
                      echo 'New Reply';
                    } else {
                      echo 'New Post';
                    }
                  ?>
                </h1>
              </div>
              <?php the_content(); ?>
            <?php roots_post_inside_after(); ?>
          <?php roots_post_after(); ?>
        <?php endwhile; /* End loop */ ?>

        <?php roots_loop_after(); ?>
        <?php

          if ($parent_id > 0) {
            eas_get_forum_posts($parent_id, true);
          }

        ?>

        <?php // The form for making a new post ?>
        <?php if (is_user_logged_in()) { ?>

          <?php eas_forum_form($parent_id); ?>

        <?php } else {
          ?>
            <p>You have to log in to do that!</p>
          <?php
        } ?>

      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>