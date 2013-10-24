<?php
/*
Template Name: CB Order
*/


if (!empty($_REQUEST['order'])) {
  $works = explode(',', $_REQUEST['order']);

  _d($works);


  foreach ($works as $order => $id) {
    delete_post_meta($id, 'cborder');
    $result = eas_update_meta($id, 'cborder', $order);
  }

  exit;
}


?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'page'); ?>
        <?php roots_loop_after(); ?>
        <script type="text/javascript" src="<?php echo trailingslashit(get_bloginfo('siteurl')); ?>js/jquery-ui-1.9.2.custom.js"></script>
        <?
          $works = eas_cb_show_works();

          ?><ul id="cbworks"><?php

          foreach ($works as $w) {

            $p = get_post($w);

            $img = eas_artwork_img($w, 'thumbnail');
            $src = $img[0];

            ?><li id="<?php echo $p->ID; ?>"><img src="<?php echo $src; ?>"><?php echo $p->post_title; ?></li><?
          
          }

          ?></ul><script type="text/javascript">
            $('#cbworks').sortable().on('sortupdate', function(e) {
              ids = $(this).sortable('toArray');
              idStr = ids.join(',');
              $.get('',{order: idStr}, function(data) {
                console.log(data);
              })
            });




          </script> <?php

        ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>