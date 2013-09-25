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

if (have_posts()) { ?>
<ul class="forum">
  <?php /* Start loop */ ?>
  <?php while (have_posts()) : the_post();  ?>
    <?php roots_post_before(); ?>
    <?php $assoc_id = get_post_meta($post->ID, 'assoc_id', true); ?>
              <?php
          
          if ($assoc_id && !$artwork) {
            if (is_single()) {
              $size = 'medium';
            } else {
              $size = 'thumbnail';
            }
            $img = eas_artwork_img($assoc_id, $size);
            $src = $img[0];
            ?>

            <figure class="forumattachment">
            <a href="<?php echo eas_artwork_url($assoc_id); ?>"><img src="<?php echo $src; ?>"></a>
            </figure>

         <?php } ?>
      <li class="forumpost<?php if ($assoc_id) echo ' forumpostattachment'; ?>">

        <article <?php post_class($edit_class) ?> id="post-<?php the_ID(); ?>">
        <a name="<?php echo $post->ID; ?>"></a>
        <?php roots_post_inside_before(); ?>
          <h5><?php eas_display_avatar($post->post_author); ?><?php the_author_meta('nickname'); ?></h5>
          <?php if (!$edit) { ?>
            <div class="entry-content">
              <?php the_content(); ?>
            </div>
            <footer>
              <?php wp_link_pages(array('before' => '<nav id="page-nav"><p>' . __('Pages:', 'roots'), 'after' => '</p></nav>')); ?>
              <?php $tags = get_the_tags(); if ($tags) { ?><p><?php the_tags(); ?></p><?php } ?>
              <?php eas_forum_meta(); ?>
              <?php eas_new_post_button(true); ?>
              <?php eas_edit_button(); ?>
              <?php eas_feature_button(); ?>
            </footer>
          <?php } else { ?>
            <form method="post" action="">
              <input type="hidden" name="ID" value="<?php echo $post->ID; ?>">
              <?php eas_editor(get_the_content(), 'postcontent', 'post_content', true); ?>
              <input type="submit" class="btn btn-primary" value="Save">
            </form>
          <?php } ?>
          <?php roots_post_inside_after(); ?>
        </article>
        <?php 
      // Get child posts if we're requesting more than one post
      if ($wp_query->query_vars['posts_per_page'] != 1) {
       
        eas_get_forum_posts($post->ID); 
      }
      
      ?>

      </li>

      
    <?php roots_post_after(); ?>
  <?php  endwhile; /* End loop */ ?>
</ul>
<?php } ?>
