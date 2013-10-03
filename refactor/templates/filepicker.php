<?php
/*
* Template name: filepicker
*/
?>
<?php

global $user_ID;


include( ABSPATH . 'wp-admin/includes/image.php' );

define('FILE_PATH', ABSPATH.'assets/');


if (isset($_POST['fpurl'])) {
  error_log('creating post');
  $new_post = array(
    'post_title' => $_POST['title'],
    'post_content' => $_POST['more_info'],
    'post_status' => 'publish',
    'post_date' => date('Y-m-d H:i:s'),
    'post_author' => $user->ID,
    'post_type' => 'artwork',
    'post_category' => array(0)
  );  

  foreach (array('School', 'Birthday', 'Location') as $n) {
    if (!empty($_POST[strtolower($n)])) {
      eas_update_usermeta($user_ID, strtolower($n), $_POST[strtolower($n)]);
    }
  }

  $artwork_id = wp_insert_post($new_post, false);
  eas_save_cc($artwork_id);


  $newname = wp_unique_filename(FILE_PATH, $_POST['fpname']);

  $fullpath = FILE_PATH.$newname;

  error_log('copying original');

  copy_file_from_url($_POST['fpurl'], $fullpath, true);

  $thumbs_uploaded = 0;



  while (isset($_POST['t_' . $thumbs_uploaded . '_url'])) {


    $url = $_POST['t_' . $thumbs_uploaded . '_url'];
    $width = $_POST['t_' . $thumbs_uploaded . '_width'];
    $height = $_POST['t_' . $thumbs_uploaded . '_height'];



    error_log('url: ' . $url);
    error_log('width: ' . $width);
     error_log('height: ' . $height);

    $exploded_name = explode('.', $newname);
    $exploded_name[0] .= '-' . $width . 'x' . $height;
    $thumb_name = implode('.', $exploded_name);


    $thumb_path = FILE_PATH.$thumb_name;


    error_log('copying thumb');
    copy_file_from_url($url, $thumb_path, true);

    $thumbs_uploaded += 1;

  }
  
  error_log('creating attachment');

    $filetype = wp_check_filetype($fullpath, null);

    $file_id = wp_insert_attachment(
        array(
            'post_title' => '',
            'post_content' => '',
            'post_status' => 'inherit',
            'post_mime_type' => $filetype['type'],
            'post_parent' => $artwork_id
        ),
            $fullpath
    );

    error_log('generating metadata');

    $attach_data = wp_generate_attachment_metadata($file_id, $fullpath);
    wp_update_attachment_metadata($file_id, $attach_data);


    error_log('redirecting...');
    wp_redirect(eas_artwork_url($artwork_id));
    error_log(eas_artwork_url($artwork_id));
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
        <script type="text/javascript" src="//api.filepicker.io/v1/filepicker.js"></script>
            <script type="text/javascript">
      //Seting up Filepicker.io with your api key
      imgSizes = <?php echo json_encode(eas_get_image_sizes()); ?>;
      filepicker.setKey('AJPzx3eR3O3aSJc3EYCJwz');

          var count = 0;
          var converted = 0;

      filepicker.pick({
        container: 'fpwindow',

        services: ['COMPUTER','URL','DROPBOX','GOOGLE_DRIVE','GMAIL','FLICKR','PICASA','FACEBOOK','BOX','EVERNOTE','FTP','WEBDAV'],
        maxSize: 50*1024*1024
      }, function(fpFile) {

        $('.notyet').html('Processing your artwork...');

        $('#fpurl').val(fpFile.url);
        $('#fpname').val(fpFile.filename);

        filepicker.stat(fpFile, {width: true, height: true}, function(data) {
          for (i in imgSizes) {

              if (parseInt(imgSizes[i]['width']) < data.width || parseInt(imgSizes[i]['height']) < data.height) {
                opts = {width: parseInt(imgSizes[i]['width']), height: parseInt(imgSizes[i]['height']), align: 'faces', quality: 80};

                if (imgSizes[i]['crop'] == false) {
                  opts['crop'] = 'max';
                } else {
                  opts['crop'] = 'crop';
                }

                filepicker.convert(fpFile, opts, function(newFile) {
                  filepicker.stat(newFile, {width: true, height: true}, function(data) {

                    $('#artworkform').prepend('<input type="hidden" name="t_' + converted + '_url" value="' + newFile.url + '"><input type="hidden" name="t_' + converted + '_width" value="' + data.width + '"><input type="hidden" name="t_' + converted + '_height" value="' + data.height + '">');

                    if (++converted == count) {
                              $('#artworksubmit').removeAttr('disabled').removeClass('disabled');
                              $('.notyet').html('Done! Ready to submit!');
                    } else {
                      $('.notyet').html('Processing your artwork... (' + (converted/count)*100 + '% complete)');
                    }


                  });
                });


                count++;
              }

            

          }
        });


      });

    $('#artworkform').live('submit', function() {
      $('.notyet').html('Finishing up...');
    })

  </script>
  
  <div class="row">
    <div class="span8">
        <iframe id="fpwindow"></iframe>
      </div>
      <div class="span4">
        <h2>About your Work</h2>
        <?php
          echo new_eas_artwork_form(true);
        ?>
        <div class="notyet">You save these details once your artwork finishes uploading and processing.</div>
      </div>
    </div>
      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>