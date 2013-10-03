<?php

$ajax = is_ajax_req();

if (eas_user_is_admin() && isset($wp_query->query_vars['homebg'])) {
  if (eas_is_homebg()) {
    delete_post_meta($post->ID, 'homebg');
    $action = 'nohomebg';
  } else {
    eas_update_meta($post->ID, 'homebg', '1');
    $action = 'homebg';
  }
  if ($ajax) {
    $result = array('action' => $action);
    echo json_encode($result);
  }
  exit;
} 

if (eas_user_is_owner() && isset($_POST['ID']) && intval($_POST['ID']) == $post->ID) {
  $artwork_id = intval($_POST['ID']);
  $updated_post = array(
    'ID' => $artwork_id,
    'post_title' => $_POST['title'],
    'post_content' => $_POST['more_info']
  );
  wp_update_post($updated_post);

  eas_update_meta($artwork_id, 'medium', $_POST['medium']);
  eas_update_meta($artwork_id, 'year', $_POST['artwork_year']);
  eas_update_meta($artwork_id, 'credits', $_POST['artwork_credits']);


  wp_redirect(eas_artwork_url($artwork_id).'?action=updateartwork');
  exit;
} else if (!eas_user_is_owner() && isset($_POST['ID'])) {
  $_GET['action'] = 'sneaky';
}

if ($wp_query->query_vars['addfav'] == '1') {
  $fav = eas_add_favorite();
  $action = 'addfav';
  if (!is_bool($fav)) {
    $action = 'addfav';
  } else if (!is_user_logged_in())  {
    $action = 'notloggedin';
  } else if (!$fav) {
    $action = 'alreadyfav';
  }

  if ($ajax) {
    $result = array('action' => $action);
    echo json_encode($result);
  } else {
      wp_redirect(eas_artwork_url($post->ID).'?action='.$action);
  }
      exit;


} else if ($wp_query->query_vars['removefav'] == '1') {
  $unfav = eas_remove_favorite();

  if ($unfav) {
    $action = 'unfav';
  } else {
    $action = 'unfaverror';
  }

  if ($ajax) {
    $result = array('action' => $action);
    echo json_encode($result);
  } else {
      wp_redirect(eas_artwork_url($post->ID).'?action='.$action);
  }

  exit;
}

  if ($wp_query->query_vars['delete'] == 1) {
    $the_post = wp_update_post(array('ID' => $post->ID, 'post_status' => 'trash'), false);
    if ($the_post !== 0) {
      wp_redirect('/artists?action=delete');
      exit();
    } else {
      _d('failed to delete');
    }
  }


  if (isset($_REQUEST["winner"])) {
      if ($_REQUEST["winner"] == 1) {
        $winval = 1;
        $result = eas_update_meta($post->ID, "winner", $winval);
      } else {
        $winval = 0;
        $result = delete_metadata('post', $post->ID, 'winner');
      }
        echo json_encode(array('val' => $winval, 'result' => $result));
        exit;
  }



?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'artwork'); ?>
        <?php roots_loop_after(); ?>

      </div><!-- /#main -->
    <?php roots_main_after(); ?>

    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>