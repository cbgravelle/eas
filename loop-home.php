<?php /* Start loop */ ?>
<div class="homeinner container">
			<?php while (have_posts()) : the_post(); ?>
			  <?php roots_post_before(); ?>
			    <?php roots_post_inside_before(); ?>
			      <?php the_content(); ?>
			      <?php wp_link_pages(array('before' => '<nav class="pagination">', 'after' => '</nav>')); ?>
			    <?php roots_post_inside_after(); ?>
			  <?php roots_post_after(); ?>
			<?php endwhile; /* End loop */ ?>
</div>