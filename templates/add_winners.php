<?php
/*
Template Name: add winners
*/
get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">

        <?php $winners = array(3324,342,315,3664,4317,2799,3315,447,4175,384,4277,3607,3458,3902,3778,275,3575,370, 2796,3790,4336,2262,3954,3603,4449,425,3949,3619,4273,4205,424,3713,3655,3407,3068,4415,2230,4365,3469);

        foreach ($winners as $w) {
          $result = eas_update_meta($w, 'winner', 1);
          _d($w);
          _d($result);

        }

        ?>
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'page'); ?>
        <?php roots_loop_after(); ?>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>