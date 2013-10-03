<?php
/*
Template Name: DND Upload
*/

include( ABSPATH . 'wp-admin/includes/image.php' );

$db = 'dnduploader';
$host = 'localhost';
$user = 'root';
$pass = 'EASmysql';

mysql_connect($host, $user, $pass) or die('Could not connect to MySQL. Error:' . mysql_error());
mysql_select_db($db) or die('Could not find Database. Please check if the database information is correct.');       
define('FILE_PATH', ABSPATH.'assets/');
define('PACKET_SIZE', 512 * 512); // bytes, need to be same as in JavaScript
define('STORE_FILES', true); //whether store files or not


$is_artist = true;
$is_contest = false;


if (isset($_POST['fileId']) && isset($_POST['token'])) {
    mergeFiles();
} else if (isset($_GET['fileId']) && isset($_GET['token']) && isset($_GET['packet'])) {
    error_log('GET PACKET PHP');
    getPacket();
} else if (isset($_POST['totalSize']) && isset($_POST['fileName']) && isset($_POST['type'])) {
    newUpload();
}


$contestname = false;
$meta = get_post_meta($post->ID);
if (isset($meta['contest'])) {
    $is_contest = true;
    $contestname = $meta['contest'][0];
}

$post_status = 'publish';

if ($is_contest) {
    $post_status = 'draft';
}

if (!eas_is_artist()) {
    $is_artist = false;
    if (!$is_contest) {
         $_GET['action'] = 'notartist';
    }
   
}



if (isset($_POST['artwork_img_id'])) {
    $do_it = true;
    $how_many = false;
    if ($is_contest) {
        $how_many = eas_how_many_more_submissions($_POST['contest']);
        if ($how_many === false) {
            $do_it = false;
        }
    }


    if ($is_contest && empty($_POST['birthday']) && empty($_POST['school'])) {
        $do_it = false;
        $_GET['action'] = 'noage';
    }

   if ($do_it) {
    $new_post = array(
        'post_title' => $_POST['title'],
        'post_content' => $_POST['more_info'],
        'post_status' => $post_status,
        'post_date' => date('Y-m-d H:i:s'),
        'post_author' => $user->ID,
        'post_type' => 'artwork',
        'post_category' => array(0)
      );

      $artwork_id = wp_insert_post($new_post, false);

      if ($artwork_id > 0) {
        $overrides = array( 'test_form' => false);
        error_log($_POST['artwork_img_id']);
        wp_update_post(array(
            'ID' => $_POST['artwork_img_id'],
            'post_parent' => $artwork_id
        ));

        update_post_meta($artwork_id, 'year', $_POST['year']);
        update_post_meta($artwork_id, 'medium', $_POST['medium']);
        update_post_meta($artwork_id, 'size', $_POST['size']);
        if ($is_contest) update_post_meta($artwork_id, 'contest', $contestname);


        eas_save_cc($artwork_id);

        get_currentuserinfo();

        foreach (array('school', 'location', 'birthday') as $m) {

            if( isset($_POST[$m])) {
                $m = eas_update_usermeta($user_ID, $m, $_POST[$m]);
            }
            
        }

        if (!$is_contest) {
            $redirect = eas_artwork_url($artwork_id).'?action=upload';
        } else {
            $redirect = trailingslashit(get_bloginfo('siteurl')).$contestname.'/thanks';
        }

        error_log($redirect);

        wp_redirect($redirect);
        exit;
      
      }
   }
}



function throwError($error)
{
    echo json_encode(array(
        "error" => $error
    ));
    exit;
}

function sendAsJSON($array)
{
	echo json_encode($array);
	exit;
}



function newUpload() 
{	

    $new_name = wp_unique_filename(FILE_PATH, $_POST['fileName']);

    $fileData = $_POST['totalSize'] . "|" . preg_replace('/[^A-Za-z0-9\/]/', '', $_POST['type']) . "|" . $new_name;
    $originalFileName = $_POST['fileName'];
    $token 	  = md5($fileData);
	
	do {
		//the probability of this being unique is good enough in most cases
		//2^31 - 1 is the max int on 32 bit systems
		$fileid   = time() . mt_rand(5, pow(2, 31) - 1); 		
		
		$query = sprintf("INSERT INTO files (id, fileData, fileid, token, original_filename, upload_date) VALUES(NULL, '%s', ". $fileid . ", '" . $token . "', '%s', 0)",
		mysql_real_escape_string($fileData),
		mysql_real_escape_string($originalFileName)
		);
		

		mysql_query($query);
		
		define("MYSQL_CODE_DUPLICATE_KEY", 1062); // @see http://dev.mysql.com/doc/refman/5.1/en/error-messages-server.html
	} while (mysql_errno() == MYSQL_CODE_DUPLICATE_KEY); //we dont like  duplicate keys
    
    sendAsJSON(array(
        "action" => "new_upload",
        "fileId" => $fileid,
        "token"  => $token
    ));	
}


function mergeFiles() 
{

	$sql = mysql_query("SELECT fileData, original_filename FROM files WHERE fileid = '" . $_POST['fileId'] . "' AND token = '" . $_POST['token']."'");
	$row = mysql_fetch_assoc($sql);
	
    if ($row === FALSE) {
        throwError("No file found in the database for the provided ID / token");
    }

    // check if we the file has already been uploaded, merged and completed
    if (!file_exists(FILE_PATH . $_POST['fileId'])) {
		
        list($fileSize, $fileType, $fileName) = explode("|", $row['fileData']);

        $totalPackages = ceil($fileSize / PACKET_SIZE);

        // check that all packages exist
        for ($package = 0; $package < $totalPackages; $package++) {
            if (!file_exists(FILE_PATH . $_POST['fileId'] . "-" . $package)) {
                throwError("Missing package #" . $package);
            }
        }

        $orig_name = $row['original_filename'];
        $new_name = wp_unique_filename(FILE_PATH, $orig_name);
        $fullpath = FILE_PATH.$new_name;

        // open file to create final file
        if (!$handle = fopen($fullpath, 'w')) {
            throwError("Unable to create new file for merging");
        }

        // write each package to the file
        for ($package = 0; $package < $totalPackages; $package++) {

            $contents = @file_get_contents(FILE_PATH . $_POST['fileId'] . "-" . $package);
            if (!$contents) {
                unlink($fullpath);
                throwError("Unable to read contents of package #" . $package);
            }

            if (fwrite($handle, $contents) === FALSE) {
                unlink($fullpath);
                throwError("Unable to write package #" . $package . " to merge");
            }
        }

        // remove the packages
        for ($package = 0; $package < $totalPackages; $package++) {
            if (!unlink(FILE_PATH . $_POST['fileId'] . "-" . $package)) {
                throwError("Unable to remove package #" . $package);
            }
        }
    }
	
	//on success, update the uploaded date in mysql
	mysql_query("UPDATE files SET upload_date = " . time() .",  WHERE fileid = '".$_POST['fileId']."'");
	
    // add attachment

    $filetype = wp_check_filetype($fullpath, null);

    $file_id = wp_insert_attachment(
        array(
            'post_title' => '',
            'post_content' => '',
            'post_status' => 'inherit',
            'post_mime_type' => $filetype['type'],
            'post_parent' => $parent
        ),
            $fullpath
    );

    $attach_data = wp_generate_attachment_metadata($file_id, $fullpath);
    wp_update_attachment_metadata($file_id, $attach_data);

    $img = wp_get_attachment_image_src($file_id, 'medium');

    sendAsJSON(array(
        "action" => "complete",
        "file" => $_POST['fileId'],
        "attach_id" => $file_id,
        "preview_img" => $img
    ));
}


/**
 * After initialized the upload, we can start receiving the packets (or 'slices')
 */
function getPacket()
{



	$sql = mysql_query("SELECT fileid FROM files WHERE fileid = '" . $_GET['fileId'] . "' AND token = '" . $_GET['token']."'");
	$rowExists = is_resource($sql) && (mysql_num_rows($sql) > 0);

	//die (var_dump($_GET). 'rows: '.mysql_num_rows($sql));
    if ($rowExists) {

        if (STORE_FILES) {
            if (!$handle = fopen(FILE_PATH . $_GET['fileId'] . "-" . $_GET['packet'], 'w')) {
                throwError("Unable to open package handle");
            }

            if (fwrite($handle, $GLOBALS['HTTP_RAW_POST_DATA']) === FALSE) {

                throwError("Unable to write to package #" . $_GET['packet']);
            }
            fclose($handle);
        }
        

        sendAsJSON(array(
            "action" => "new_packet",
            "result" => "success",
            "packet" => $_GET['packet']
        ));
    } 
}




get_header(); ?>
  <?php roots_content_before(); ?>
  	<link rel="stylesheet" type="text/css" href="<?php echo trailingslashit(get_bloginfo('siteurl')); ?>js/dnduploader/public_html/static/main.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="<?php echo trailingslashit(get_bloginfo('siteurl')); ?>js/dnduploader/public_html/static/button.css" media="screen"/>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <div class="row">
          <div class="span12">
            <?php roots_loop_before(); ?>	
            <?php get_template_part('loop','page'); ?>
            <?php roots_loop_after(); ?>


            <?php echo eas_dndupload($contestname); ?>
		</div>

      </div><!-- /#main -->

    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>