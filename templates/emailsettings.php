<?php
/*
Template Name: Email Settings
*/
get_currentuserinfo();
$ajax = is_ajax_req();

if (isset($_GET['mailinglist'])) {
  require_once('inc/MCAPI.class.php');

  $api = new MCAPI('b3728dbc1e9e90b3785df31d74e17c10-us4');
  if (is_email($_GET['mailinglist'])) {

      /*$listId = $api->lists($api, array('list_name' => 'Emergent Art Space Mailing List'));
      _d($listId);*/
      $vals = $api->listSubscribe('0663054632', $_GET['mailinglist'], NULL, 'html', false, true, false, true);
      if ($api->errorCode){
        $action = 'subscribefailed';
        $error = $api->errorCode;
        error_log('ERROR IS: '.$error);
      } else {
        $action = 'mailsubscribe';
        $error = '';
      }
  }

  if ($ajax) {
    echo json_encode(array('action' => $action, 'error' => $error));
    exit;
  } else {
    $_GET['action'] = $action;
  }
}



if (isset($_POST["email"])) {
  if (is_email($_POST["email"])) {
    wp_update_user(array('ID' => $user_ID, 'user_email' => $_POST["email"]));
    

    eas_send_verification_email($user_ID);
    $_GET['action'] = 'verificationsent';
  }
}

if (isset($_GET["id"]) && isset($_GET["v"])) {
  $verify = verify_user($_GET["id"], $_GET["v"]);
  if ($verify !== false) {
    if (eas_is_artist($_GET["id"])) {
      wp_redirect(eas_artist_page_url($_GET["id"]).'?action=newartist');
      exit;
    } else {
      $_GET['action'] = 'emailverified';
    }
    
  } else {
    $_GET['action'] = 'verificationfail';
  }
}


  $email_settings = array(
    'forumemail' => null,
    'artemail' => null
  );

if (isset($_POST['emailsettings'])) {



  foreach ($email_settings as $s => $v) {
    if (isset($_POST[$s]) && $_POST[$s] == 'on') {
      $email_settings[$s] = true;
    }
  }

  eas_update_usermeta($user_ID, 'emailsettings', $email_settings);

  $_GET['action'] = 'updateprofile';

}
?>
<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo FULLWIDTH_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop', 'settings'); ?>
        <?php roots_loop_after(); ?>

        <?php if (is_user_logged_in()) { 

          $current_userdata = get_userdata($user_ID);
          if (!eas_user_verified()) { ?>
            <p>
              Use this form to verify your email address so you can receive notifications.</p>
            </p>
            <form class="form-inline" action="/settings/email" method="post">
              <input type="email" name="email" value="<?php echo $current_userdata->data->user_email; ?>">
              <input class="btn" type="submit" value="Send Verification Email">
            </form>
          <?php } else { ?>


                                      <p>
                            Use this form to change your email address. You will be sent an email at the new address shortly for verification.
                          </p>
                          <?php if (eas_is_artist()) { ?>
                            <p>Your work will not be visible under "Artists" until your email address is verified.</p>
                          <?php } ?>
                          <form class="form-inline" action="/settings/email" method="post">
                            <input type="email" name="email" value="<?php echo $current_userdata->data->user_email; ?>">
                            <input class="btn" type="submit" value="Change Email">
                          </form>
            <form method="post" action="">
              <input type="hidden" name="emailsettings" value="1">

              <p>
                Send me an email when...
              </p>
                        <?php
            $email_settings = get_user_meta($user_ID, 'emailsettings', true);

            $regular_settings = array(
              'forumemail' => 'Someone responds to one of my posts'
            );
            $artist_settings = array('artemail' => 'Someone responds to my artwork');


              foreach ($regular_settings as $s => $l) {
                ?>
                  <label class="checkbox"><input type="checkbox" name="<?php echo $s; ?>"<?php if ($email_settings[$s] == true) echo ' checked'; ?>><?php echo $l; ?></label>
                <?php
              }

              ?>
              <?php if (eas_is_artist()) {
                
              foreach ($artist_settings as $s => $l) {
                ?>
                  <label class="checkbox"><input type="checkbox" name="<?php echo $s; ?>"<?php if ($email_settings[$s] == true) echo ' checked'; ?>><?php echo $l; ?></label>
                <?php
              }
              } ?>
              <input class="btn" type="submit" value="Save">
            </form>

          <?php } ?>
        <?php } ?>


      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>
