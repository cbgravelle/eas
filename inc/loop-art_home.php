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

$post_count = $wp_query->post_count;

?>
<div class="artcontainer">
<?php
if (have_posts()) {
  ?>
  <div class="row"><?php
  for (; $i < 2 && $i < $post_count; $i++) { the_post();
    eas_artwork_span($post->ID, eas_first_tagged_artwork($id), get_the_title());
  }

  ?>

    </div>
    <div class="row">

  <?php

  for (; $i < 5 && $i < $post_count; $i++) { the_post();
    eas_artwork_span($post->ID, eas_first_tagged_artwork($id), get_the_title(), 4, 'span4xspan3');

  }

  ?>
  </div>
  <div class="row">

  <?php

  for (; $i < 9 && $i < $post_count; $i++) { the_post();
    eas_artwork_span($post->ID, eas_first_tagged_artwork($id), get_the_title(), 3, 'span3-crop');

  }

  ?>
  </div>

  <?php
}

?>
</div>
