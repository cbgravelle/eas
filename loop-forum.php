<?php

if (get_post_type() != 'artwork') {
  $artwork = false;
} else {
  $artwork = true;
}

if (eas_is_edit()) {
  $edit = true;
  $edit_class = ' edit';
} else {
  $edit = false;
  $edit_class = '';
}


?>

    <ul class="forum">
    <?php /* Start loop */ ?>
    <?php while (have_posts()) : the_post();  ?>
      <?php roots_post_before(); ?>

        <?php eas_forum_post(0, $edit); ?>
        
      <?php roots_post_after(); ?>
    <?php  endwhile; /* End loop */ ?>
    </ul>
