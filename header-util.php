<?php
    $message = array();
  if (isset($_GET['action'])) {
    $action_str = $_GET['action'];

    $message['success'] = array();
    $message['info'] = array();
    $message['warning'] = array();
    $message['error'] = array();

    $action_array = explode(',', $action_str);

    foreach ($action_array as $action) {
      if ($action == 'login') {
        array_push($message['success'], 'You have successfully logged in');
      } else if ($action == 'logout') {
        array_push($message['info'], 'You have been logged out. See you soon!');
      } else if ($action == 'new') {
        array_push($message['success'],'Your message has been posted to the forum.');
      } else if ($action == 'upload') {
        if (eas_is_artist()) {
          array_push($message['success'], 'Your artwork has been uploaded! Check it out!');
        } else {
          array_push($message['info'], 'Your art has been uploaded, but your account still must be approved before it will be publicly available on the site.');
        }
      } else if ($action == 'updateprofile') {
        array_push($message['success'], 'Your information has been updated.');
      } else if ($action == 'updateartwork') {
        array_push($message['success'], 'This artwork has been updated.');
      }else if ($action == 'updateforumpost') {
        array_push($message['success'], 'This forum post has been updated.');
      } else if ($action == 'addfav') {
        array_push($message['success'], 'This artwork has been added as a favorite.'); 
      } else if ($action == 'alreadyfav') {
        array_push($message['info'], 'This is already a favorite of yours. To further show your support you can start a discussion! Why not!');
      } else if ($action == 'unfav') {
        array_push($message['info'], 'This is no longer among your favorites. Those were the days...');
      } else if ($action == 'artistpending') {
        array_push($message['warning'], 'This artist is pending approval.');
      } else if ($action == 'artistapproved') {
        array_push($message['success'], 'This user has been approved as an artist.');
      } else if ($action == 'decline') {
        array_push($message['info'], 'This user has been declined');
      } else if ($action == 'invalidlogin') {
        array_push($message['error'], 'Your username or password is invalid.');
      } else if ($action == 'invalidlogine') {
        array_push($message['error'], 'Your email address or password is invalid.');
      } else if ($action == 'emailinuse') {
        array_push($message['error'], 'That email address is already in use. Forgot your password?');
      } else if ($action == 'usernameinuse') {
        array_push($message['error'], 'That username is already in use');
      } else if ($action == 'register' && is_user_logged_in()) {
        $user = wp_get_current_user();
        eas_send_verification_email($user->ID);
        array_push($message['success'], 'You have successfully registered. Welcome to the Emergent Art Space website!');
      } else if ($action == 'feature') {
        array_push($message['success'], 'This post is now featured.');
      } else if ($action == 'unfeature') {
        array_push($message['info'], 'This post is no longer featured.');
      } else if ($action == 'notartist') {
        // $msg = 'After you have uploaded your art, your account must be approved.';
        if (!eas_user_verified()) {
          $msg .= '<p>Please ensure your email address is valid in <a href="/settings/email" title="Email Settings">your settings</a> so we can send you a confirmation email.';
        }
        array_push($message['warning'], $msg
          );
      } else if ($action == 'follow') {
        array_push($message['success'], 'You are now following this artist.');
      } else if ($action == 'unfollow') {
        array_push($message['info'], 'You are no longer following this artist.');
      } else if ($action == 'passreset') {
        array_push($message['success'], 'Your password has been reset and you have been logged in.');
      } else if ($action == 'invalidreset') {
        array_push($message['error'], 'Something went wrong resetting your password. Make sure you are using the same email address used on your account.');
      } else if ($action == 'verificationsent') {
        array_push($message['success'], 'An email with a verification link has been sent to the email-address you provided.');
      } else if ($action == 'emailverified') {
        array_push($message['success'], 'Your email has been verified. You can now adjust your notification settings.');
      } else if ($action == 'newartist') {
        array_push($message['success'], 'Welcome to your artist page! You can now upload as much art as you want without requiring approval.');
      } else if ($action == 'emailsent') {
        array_push($message['success'], 'Please check your email account for password recovery instructions.<p>Our messages may appear in the spam folder. Look for a message from no-reply@emergentartspace.org.');
      } else if ($action == 'delete') {
        array_push($message['success'], 'This post has been deleted.');
      } else if ($action == 'verificationfail') {
        array_push($message['success'], 'Sorry to report that verification has failed. Try re-sending the email and make sure you use the newest email—old verification emails won\'t work!');
      } else if ($action == 'updatepassword') {
        array_push($message['success'], 'Your password has been updated.');
      } else if ($action == 'noage') {
        array_push($message['error'], 'Please enter either your date of birth or your current school.');
      } else {
      }
    }


  }

if (count($message) > 0) { // TODO: gray box still showing for empty message?>

  <script> console.log(<?php echo max(max($message)) ?>); </script>


  <div class="actionwrapouter">
    <div class="container actionwrap">
        <?php foreach ($message as $key => $message_array) {
          foreach ($message_array as $m) {
            ?>
              <div class="alert alert-<?php echo $key; ?>">
                <?php echo $m; ?>
              </div>
            <?php
          }
        } ?>
    </div>
  </div>
<?php } ?>

<?php
/*if (eas_user_is_admin()) {
  _d($wp_query);
}*/
?>
