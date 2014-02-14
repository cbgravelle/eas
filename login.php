<?php
/*
Template Name: Login
*/

/*
require_once('inc/recaptchalib.php');
*/

if (isset($_GET['action'])) {
  if ($_GET['action'] == 'logout') {

    wp_redirect(site_url($_GET['redirect'].'?action=logout'));
    wp_clear_auth_cookie();
    do_action('logout');
    exit;
  }
}




if (isset($_REQUEST['redirect'])) {
  if ($_REQUEST['redirect'] == '/forgotpassword/' ) {
    $redirecturl = false;
    $redirect = '';
  } else {
      $redirecturl = $_REQUEST['redirect'];
      $redirect = '?redirect='.$_REQUEST['redirect'];
  }

} else {
  $redirecturl = false;
  $redirect = '';
}


if (isset($_POST['register'])) {
 

  require_once('inc/recaptchalib.php');
  $privatekey = "6LfWge4SAAAAACq4gCFHoL1kOXR_krKlzPPFDa_n";
  $resp = recaptcha_check_answer ($privatekey,
                                $_SERVER["REMOTE_ADDR"],
                                $_POST["recaptcha_challenge_field"],
                                $_POST["recaptcha_response_field"]);

  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
		$_GET['action'] = $_GET['action'].'badcaptcha';
  } else {

  $email = $_POST['email'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $nickname = $_POST['nickname'];


  $new_user_id = wp_create_user($username, $password, $email);
  
  if (is_wp_error($new_user_id)) {
    $codes = $new_user_id->get_error_codes();
    foreach ($codes as $c) {
      if ($c == 'empty_user_login') {
        $_GET['action'] = $_GET['action'].'emptyusername';
      } else if ($c == 'existing_user_login') {
        $_GET['action'] = $_GET['action'].',usernameinuse';
        $_POST['username'] = '';
      } else if ($c == 'existing_user_email') {
        $_GET['action'] = $_GET['action'].',emailinuse';
      }
    }
  } else {
    if (isset($_POST['remember']) && $_POST['remember'] == 'Yes') {
      $remember = 1;
    } else {
      $remember = 0;
    }
    eas_update_usermeta($new_user_id, 'unverified', 1);
    if (!empty($nickname)) {
      wp_update_user(array(
        'ID' => $new_user_id, 
        'nickname' => $nickname, 
        'display_name' => $nickname, 
        'user_nicename' => $nickname
      ));
    }
    wp_set_auth_cookie($new_user_id, $remember);
    error_log($redirecturl);
    if ($redirecturl !== false) {
      wp_redirect($redirecturl.'?action=register');
      exit;

    } else {
      wp_redirect(get_bloginfo('siteurl').'?action=register');
      exit;
    }
  }
 } //end recaptcha success
} //end post[register]

  else if (isset($_POST['login'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $the_user = wp_authenticate($username, $password);
  $auth = false;
  if (is_wp_error($the_user)) {
    if (is_email($username)) {
      $maybe_user = get_user_by('email',$username);
      if (!$maybe_user) {
        $_GET['action'] = 'invalidlogine';

      } else {
        $username = $maybe_user->data->user_login;
        $the_user = wp_authenticate($username, $password);
        if (!is_wp_error($the_user)) {
          $auth = true;
        } else {
          $_GET['action'] = 'invalidlogine';
        }
      }

    } else {
      $_GET['action'] = 'invalidlogin';
    }

  } else {
    $auth = true;
  }


  

  if (!$auth) {
    $stmt = $wpdb->prepare('select ID, user_pass from wp_users where user_login like %s', $username);
    $user_result = $wpdb->get_results($stmt);
    if (empty($user_result[0]->user_pass)) {

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, "http://emergentartspace.org/?remoteauth=true&username=".$username."&password=".$password);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $output = curl_exec($ch);
      curl_close($ch);
      error_log($output);
      error_log(var_export($user_result, true));
      
      if ($output == '1') {

        if (!empty($user_result[0]->ID)) {
          wp_set_password($password, $user_result[0]->ID);
          $the_user = $user_result[0];
          $auth = true;
        } else {

          $conn = new mysqli('localhost','emergentartspace','4BUcJ42csYKdy3Uz','emergentartspace');
          $result = $conn->query("select * from wp_users where user_login like '".$wpdb->escape($username)."'");
          while ($row = $result->fetch_assoc()) {
            $new_user_id = wp_create_user($row['user_login'], $password, $row['user_email']);
            wp_update_user(array('ID' => $new_user_id, 'user_registered' => $row['user_registered'], 'user_nicename' => $row['display_name'], 'nickname' => $row['display_name'], 'display_name' => $row['display_name']));
            $uid = $new_user_id;
            $auth = true;
          }

        }

      } else {
        $_GET['action'] = 'invalidlogin';
      }
    }
  }



  if ($auth) {
      if (!empty($the_user->ID)) {
        $uid = $the_user->ID;
      }
      if (isset($_POST['remember']) && $_POST['remember'] == 'Yes') {
        $remember = 1;
      } else {
        $remember = 0;
      }
      wp_set_auth_cookie($uid, $remember);
      error_log($redirecturl);
      if ($redirecturl !== false) {
        wp_redirect($redirecturl.'?action=login');
        exit;

      } else {
        wp_redirect(get_bloginfo('siteurl').'?action=login');
        exit;
      }
  }
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
        
        <?php if (!is_user_logged_in()) { ?>
        <div class="container">
          <div class="row">
            <div class="span6">
              <h3>Log in to access your account</h3>
              <?php eas_login_form($redirecturl); ?>
            </div>
            <div class="span6">
              <h3>Don't have an account yet? Register in seconds!</h3>
              <p>You'll be able to post on the forum and submit your art to the site.</p>
              <?php eas_register_form($redirecturl); ?>

            </div>
          </div>
        </div>

        <?php } else { ?>
          You are already logged in.
        <?php } ?>

      </div><!-- /#main -->

    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>

  <?php /*
  $captcha_public = "6LcaD-4SAAAAAIAldWSXHRLNkGqQvgUGbXUBq0Zd";
  $capcha_private = "6LcaD-4SAAAAAHmAWf78-Ca_UovSYNVvzQtZVH2z";

  $resp = recaptcha_check_answer ($captcha_private,
                                  $_SERVER["REMOTE_ADDR"],
                                  $_POST["recaptcha_challenge_field"],
                                  $_POST["recaptcha_response_field"]);
  if (!$resp->is_valid) {
    // What happens when the CAPTCHA was entered incorrectly
    die ("The reCAPTCHA wasn't entered correctly. Go back and try it again." .
         "(reCAPTCHA said: " . $resp->error . ")");
  } else {
    console.log("recapcha SUCCESS!");
  }
  */ ?>