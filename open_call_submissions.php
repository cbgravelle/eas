<?php
/*
Template Name: Open Call Submissions
*/
?>

<?php 

  /*////////////// PASSWORD ///////////////*/

  /*///////// GETTING SUBMISSIONS /////////*/

function oc_get_subs() {
  return "submissions go here";
}

function oc_submissions($page = 0, $per_page = 10, $contest = false, $sort = 'recent') {
  global $wpdb;
  $contestname = false;
  if ($contest !== false) {
    $contestname = $contest;
    $contest = true;
  } 
  if ($page == 0) $page = 1;
  $contest_query = '';
  if ($contest) {
    $contest_query = $wpdb->prepare(
      "
      join wp_postmeta pm
      on p.ID = pm.post_id
      and pm.meta_key = 'contest'
      and pm.meta_value = %s
      where p.post_status = 'draft' 
      ", $contestname
    );

    $user_type_query = "";
  } else {
    $contest_query = "where p.post_status = 'publish' ";
    $user_type_query = "              join (
                select distinct m2.*
                from wp_usermeta m2
                join wp_usermeta m3
                on m2.user_id = m3.user_id
                where m2.meta_key = 'wp_capabilities'
                and m2.meta_value like 'a:1:{s:6:\"artist\";s:1:\"1\";}'
                and m3.meta_key = 'verified'
                and m3.meta_value = 1
              ) as m
            on u.ID = m.user_id";
  }
    
    $query = $wpdb->prepare("
            select distinct u.ID
              from $wpdb->users u
              join wp_usermeta um ON um.user_id = u.ID
              join (
                select p.* 
                from wp_posts p
                ".$contest_query."
                and p.post_type = 'artwork'
                order by p.post_date desc  
              ) as a
              on u.ID = a.post_author
              ".$user_type_query."
          where um.meta_key = 'nickname'
          ORDER BY
          ".($sort == 'name' ? "um.meta_value ASC" : "a.post_date DESC")."
          limit %d offset %d
          ", $per_page, ($page-1)*$per_page);
      $author_ids = $wpdb->get_results($query);
      foreach ($author_ids as $author) {
        eas_display_user($author->ID, true, $contestname);
      }
}

?>

<?php get_header(); ?>
  <?php roots_content_before(); ?>
    <div id="content" class="<?php echo CONTAINER_CLASSES; ?>">
    <?php roots_main_before(); ?>
      <div id="main" class="<?php echo MAIN_CLASSES; ?>" role="main">
        <?php roots_loop_before(); ?>
        <?php get_template_part('loop','page'); ?>
        <?php roots_loop_after(); ?>

        <?php /*if (is_user_logged_in()) { 
          echo "test echo";  */?>

        <div class= "pg">
        Sort by: <?php if(!isset($_GET['sort'])): ?><strong>
                  <?php else: ?><a class="gray3" href="/artists/"><?php endif; ?>
                    Recently Updated 
                  <?php if(!isset($_GET['sort'])): ?></strong>
                 <?php else: ?></a><?php endif; ?>
                |
                 <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name'): ?><strong>
                    <?php else: ?><a class="gray3" href="?sort=name"><?php endif; ?>
                      Name
                    <?php if(isset($_GET['sort']) && $_GET['sort'] == 'name'): ?></strong>
                  <?php else: ?></a><?php endif; ?>
        </div>
        <?php eas_page_links(); ?>

          <div id="artists_grid">
            <?php // oc_recently_updated_artists(get_query_var('paged'), 10, 'opencall'); ?>
            <?php
              if(isset($_GET['sort']) && $_GET['sort'] == 'name') { 
                oc_submissions(get_query_var('paged'), 10, 'opencall', 'name');
              } else { 
                oc_submissions(get_query_var('paged'), 10, 'opencall', 'recent');
              }
            ?>
            <br class="clear" />
          </div>
        <?php /*
        } else {
          echo 'not logged in';
        }  */?>

        <?php eas_page_links(); ?>


      </div><!-- /#main -->
    <?php roots_main_after(); ?>
    </div><!-- /#content -->
  <?php roots_content_after(); ?>

<?php get_footer(); ?>

