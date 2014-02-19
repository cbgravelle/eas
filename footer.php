
    <?php roots_footer_before(); ?>
    <footer id="content-info" class="<?php echo WRAP_CLASSES; ?>" role="contentinfo">
      <?php // roots_footer_inside(); ?>
      <?php // eas_footer_menu(); ?>
      <p class="copy"><small>&copy; <?php echo date('Y'); ?> <?php bloginfo('name'); ?></small></p>
      <?php echo eas_get_cc_general(); ?>
    </footer>
  </div><!-- /#wrap -->
  <?php roots_footer_after(); ?>

  <?php wp_footer(); ?>
  <?php roots_footer(); ?>
</body>
</html>
