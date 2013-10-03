<header id="banner" class="navbar navbar-fixed-top" role="banner">
  <?php roots_header_inside(); ?>
  <div class="navbar-inner">
    <div class="<?php echo WRAP_CLASSES; ?>">
     <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>

      <a class="brand" href="<?php echo home_url(); ?>/">
        <?php bloginfo('name'); ?>
      </a>
      <nav id="nav-main" class="nav-collapse" role="navigation">
        <?php wp_nav_menu(array('theme_location' => 'primary_navigation', 'walker' => new Roots_Navbar_Nav_Walker(), 'menu_class' => 'nav')); ?>
      </nav>

      <?php get_template_part('searchform'); ?>
            <span class="pull-right">
      <?php if (!is_user_logged_in()) { ?>
        <a class="btn" href="/login?redirect=<?php echo eas_relative_page_url(); ?>">Log In / Register</a>
      <?php } else {
        get_currentuserinfo();
        $current_userdata = get_userdata($user_ID);
        $exploded = explode($_SERVER['REQUEST_URI'],'?');
        $currenturl = $exploded[0];
       ?>
        <a class="btn" href="/upload">Upload Art</a>
        <a class="btn" href="/settings">Settings</a>
        <a class="btn" href="/login?action=logout&redirect=<?php echo eas_relative_page_url(); ?>">Log Out (<?php echo $current_userdata->data->display_name; ?>)</a>
      <?php } ?>
    </span>
    </div>
  </div>

</header>
 <hr class="measuring">
