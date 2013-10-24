<?php
/*
Template Name: Forgot Password
*/
?>

<?php
$v = 0;

$new = false;
if (!empty($_GET['new'])) {
  $new = true;
}

if (isset($_POST['reset'])) {
  $email = trim($_POST['user_email']);
  $v = 2;
  if (is_email($email)) {
    $reset_user = get_user_by('email', $_POST['user_email']);

    if ($reset_user !== false) {
      $v_from_db = get_user_meta($reset_user->ID,'pwreset',true);

      if (trim($v_from_db) == trim($_POST['v'])) {

        wp_update_user(array(
          'ID' => $reset_user->ID,
          'user_pass' => $_POST['new_pass']
        ));

        delete_user_meta($reset_user->ID, 'pwreset');
        wp_set_auth_cookie($reset_user->ID);
        if ($new) {
          $action = 'register';
        } else {
          $action = 'passreset';
        }
        wp_redirect(get_bloginfo('siteurl').'?action='.$action);
        exit;

      } else {
        $_GET['action'] = 'invalidreset';
      }



    } else {
      $_GET['action'] = 'invalidreset';
    }

  } else {
    $_GET['action'] = 'invalidemail';
  }
} else if (isset($_POST['user_email'])) {
  if (is_email($_POST['user_email'])) {

    $reset_user = get_user_by('email',$_POST['user_email']);

    if ($reset_user !== false) {

      if (eas_do_pass_reset_email($reset_user->ID, $_POST['user_email'], true)) {
        $_GET['action'] = 'emailsent';
      } else {
        $_GET['action'] = 'couldnotreset';
      }

      

    }


  } else {
    $_GET['action'] = 'notemail';
  }
} else if (isset($_GET['v'])) {

  $v = 1;

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
           <?php if (!$v) { ?>
          <p>Enter the email address you used to register for this site and we'll email a link that will enable you to reset your password.</p>
          <form class="form-inline" method="post" action="">
            <input type="email" name="user_email">
            <input class="btn btn-primary" type="submit" value="Send Password Reset Email">
          </form>
          <?php } else if ($v == 1) { ?>
          <?php
          if (!$new) {
          ?>
            <p>Please enter your email address (just to double-check it's really you!) and your desired new password.</p>

          <?php
        } else {
          ?>

            <p>Welcome! Enter the email address this link was sent to and your desired password and you can start exploring.</p>

          <?php
        }

        ?>
            <form method="post" action="" autocomplete="off">
              <?php
               if ($new) {
                  ?>
                  <input type="hidden" name="new" value="1">
                  <?php
                }

              ?>
              <input type="hidden" name="reset" value="1">
              <input type="hidden" name="v" value="<?php echo urlencode($_GET['v']); ?>">
            <p><input type="email" name="user_email" placeholder="email address"></p>
            <p><input type="password" name="new_pass" placeholder="new password"></p>
            <p><input class="btn btn-primary" type="submit" value="Reset Password"></p>
          </form>
          <?php } else { ?>

            <p>You are already logged in.</p>

          <?php } ?>
        <?php } else {
          if ($new) {
            ?>
              <p>You must <a href="/login?action=logout&redirect=<?php echo $_SERVER['REQUEST_URI']; ?>" title="Log Out">log out</a> before activating your new account.</p>
            <?php
          } else {
            ?>

            <p>You must <a href="/login?action=logout&redirect=<?php echo $_SERVER['REQUEST_URI']; ?>">log out</a> to recover your password.</p>


            <?php
          }


        }

       ?>

      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>
<?php get_footer(); ?>
