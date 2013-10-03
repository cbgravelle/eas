<?php
/*
* Template name: CB Show
*/
?>
<?php get_header(); ?>
        <?php

          if (1) {
            $posts = eas_cb_show_works();

            ?>
            <script type="text/javascript" src="<?php echo trailingslashit(get_bloginfo('siteurl')); ?>js/jquery.anythingslider.js"></script>
            <script type="text/javascript" src="<?php echo trailingslashit(get_bloginfo('siteurl')); ?>js/cbSlider.js"></script>
            <link rel="stylesheet" type="text/css" href="<?php echo trailingslashit(get_bloginfo('siteurl')); ?>css/anythingslider.css">
            <div class="cbsliderfiller"></div>
            <div class="cbsliderwrap">
            <div class="thumbnails">
              <div class="thumbnailinner">
                <?php 
                $i = 0;
                  foreach ($posts as $p) {
                    $img = eas_artwork_img($p, 'span2-crop');
                    $src = $img[0];
                    ?><a class="cbthumb" href="#<?php echo ($i + 1); ?>"><figure class="span2"><img src="<?php echo $src; ?>"></figure></a><?php
                    $i++;
                  }
                ?>
              </div>
            </div>
            <ul id="slider" class="cbSlider"><?php
            foreach ($posts as $p) {

              ?>

                <li>
                  <?php

                  $img = eas_artwork_img($p, 'large');
                  $src = $img[0];
                  $figure_post = get_post($p);
                  $author = get_userdata($figure_post->post_author);
                  $author_meta = get_user_meta($figure_post->post_author);
                  $the_meta = get_post_meta($p);
                  ?>
                  <div class="cbworkwrapper">
                    <figure class="cbwork cbwork-<?php echo $p; ?>w">
                      <img data-width="<?php echo $img[1]; ?>" data-height="<?php echo $img[2]; ?>" class="art large" src="<?php echo $src; ?>" title="<?php echo $figure_post->post_title; ?>">
                      <figcaption>
                        <div class="inlineels"><p class="name"><strong><?php echo $author->nickname; ?></strong></p><p class="location"><?php echo eas_get_meta_display($author_meta, 'location', false); ?></p></div>
                        <div class="inlineels"><h3 class="title"><?php echo $figure_post->post_title; ?></h3><p class="prize"><?php echo eas_get_meta_display($the_meta, 'prize', false); ?></p></div>
                        <p class="medium"><?php echo eas_get_meta_display($the_meta, 'medium', false); ?></p>
                        <p class="description"><?php echo $figure_post->post_content; ?></p>
                        <p class="showall"><a href="#" class="showalllink">Show Thumbnails</a></p>
                      </figcaption>
                    </figure>
                  </div>

                </li>

              <?php
            }
            ?>
            </ul>
            </div>
            <div class="thumbnails"></div><?php
          }

          

        ?>
<?php get_footer(); ?>
