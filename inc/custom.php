<?php

// Custom functions


add_action('init', 'forumpost_register');

function forumpost_register() {
	$labels = array(
		'name' => _x('Forum', 'post type general name'),
		'singular_name' => _x('Forum Post', 'post type singular name'),
		'add_new' => _x('Add New', 'portfolio item'),
		'add_new_item' => __('Add New Forum Post'),
		'edit_item' => __('Edit Forum Post'),
		'new_item' => __('New Forum Post'),
		'view_item' => __('View Forum Post'),
		'search_items' => __('Search Forum Posts'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => get_stylesheet_directory_uri() . '/article16.png',
		'capability_type' => 'post',
		'hierarchical' => true,
		'menu_position' => null,
		'supports' => array('title','editor','author','revisions','custom-fields')
	);

	register_post_type('forum', $args);

}

add_action('init', 'artwork_register');

function artwork_register() {
	$labels = array(
		'name' => _x('Art Works', 'post type general name'),
		'singular_name' => _x('Artwork', 'post type singular name'),
		'add_new' => _x('Add New', 'portfolio item'),
		'add_new_item' => __('Add New Artwork'),
		'edit_item' => __('Edit Artwork'),
		'new_item' => __('New Artwork'),
		'view_item' => __('View Artwork'),
		'search_items' => __('Search Artworks'),
		'not_found' =>  __('Nothing found'),
		'not_found_in_trash' => __('Nothing found in Trash'),
		'parent_item_colon' => ''
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'menu_icon' => get_stylesheet_directory_uri() . '/article16.png',
		'rewrite' => array('slug' => 'a'),
		'capability_type' => 'post',
		'hierarchical' => true,
		'menu_position' => null,
		'supports' => array('title','editor','author','revisions','custom-fields')
	);

	register_post_type('artwork', $args);

}

add_action('init', 'forum_taxonomy_register');

function forum_taxonomy_register() {
	$labels = array(
		'name' => 'Forum Categories',
		'singular_name' => 'Forum Category',
		'search_items' => 'Search Forum Categories',
		'all_items' => 'All Forum Categories',
		'parent_item' => 'Parent Forum Category',
		'parent_item_colon' => 'Parent Forum Category:',
		'edit_item' => 'Edit Forum Category',
		'update_item' => 'Update Forum Category',
		'add_new_item' => 'Add New Forum Category',
		'new_item_name' => 'New Forum Category Name',
		'menu_name' => 'Forum Categories'
	);

	register_taxonomy('forum_category', array('forum'), array(
		'hierarchical' => true,
		'labels' => $labels,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => false
	));
}

function _d($var, $label = '') {
	echo '<pre>';
	if (!empty($label)) echo stripslashes($label.': ');
	var_dump($var);
	echo '</pre>';
}

function _err($var) {
	error_log('HERE IS THE ERROR'.var_export($var, true));
}

function eas_re($s) {

	if (strtolower(substr($s, 0, 4)) != 're: ') {
		$s = 're: '.$s;
	}

	return $s;
}

function eas_how_many_more_submissions($contestname, $total = 2, $uid = 0) {
	global $user_ID, $wpdb;
	if ($uid == 0) {
		get_currentuserinfo();
		$uid = $user_ID;
	}
	$query = $wpdb->prepare("
		select count(*) from wp_posts p
		join wp_postmeta m
		on m.post_id = p.ID
		where p.post_author = %d
		and p.post_type = 'artwork'
		and m.meta_key = 'contest'
		and m.meta_value = %s

	", $uid, $contestname);



	$how_many = $total - $wpdb->get_var($query);

	if ($how_many) {
		if ($how_many-1) {
			$plural = 's';
		} else {
			$plural = '';
		}
		if ($how_many == $total) {
			$more = ' ';
		} else {
			$more = ' more ';
		}
		return 'You may submit '.$how_many.$more.'work'.$plural.' to this open call.';
	} else {
		return false;
	}
}

function eas_forum_post($the_post = 0, $edit = false) {
	global $wp_query, $post;

	if ($the_post === 0) $the_post = $post;
	if (gettype($the_post) == 'integer') $the_post = get_post($the_post);
	$compact = false;
	$slug = '';
	if (isset($wp_query->queried_object->taxonomy)) {
		$slug = $wp_query->queried_object->slug;
		if ($slug == 'gallery') {
			$compact = true;
		}
	}
	$artwork = eas_is_artwork();
	?>
		<?php $assoc_id = get_post_meta($the_post->ID, 'assoc_id', true); ?>
              <?php
          if ($assoc_id && !$artwork) {

            if (is_single()) {
              $size = 'medium';
            } else {
              $size = 'thumbnail';
            }
            $img = eas_artwork_img($assoc_id, $size);
            $src = $img[0];

            ?>

            <figure class="forumattachment">
            <a href="<?php echo eas_artwork_url($assoc_id); ?>"><img src="<?php echo $src; ?>"></a>
            </figure>

         <?php } ?>
      <li class="forumpost<?php if ($assoc_id && !$artwork) echo ' forumpostattachment'; if ($compact) echo ' compact'; ?>">

        <article <?php post_class($edit_class.' '.$slug) ?> id="post-<?php the_ID(); ?>">
        <a name="<?php echo $the_post->ID; ?>"></a>
        <?php roots_post_inside_before(); ?>
             <div class="entry-content">
            	 <h5><?php /* eas_display_avatar($the_post->post_author); */?>
            	 	<span class="subject"><?php the_title(); ?></span>
            	 </h5>
            	 <?php if ($the_post->post_parent != 0) { ?>
            	 	<a href="<?php get_site_url();?>/artists/<?php the_author_meta(ID);?>">
            	 			<?php the_author_link();?>
            	 	</a>
            	 	<?php echo '| &nbsp; ';} ?>
            	 <?php eas_forum_meta(); ?>
            	 <?php echo '| &nbsp; ' ?>
              	 <?php eas_comment_button(true, true); ?>
              	 <?php eas_feature_button(); ?> 
            <?php if (!$edit && !$compact) {
            	the_content();
            } ?>
          <?php if (!$edit) { ?>
          <?php if (!$compact) { ?>

            <footer> 
              <?php eas_edit_button(); ?>
            </footer>
          <?php } else { ?>
			<?php 	$url = eas_forum_url($post->ID); ?>

	          <a href="<?php echo $url; ?>" title="<?php the_title(); ?>">
	          	<div class="imagelist row">

	          		<?php 
	          			$artworks = eas_tagged_artworks($post->ID);

	          			foreach ($artworks as $a) {
							$img = eas_artwork_img($a, 'span2-crop');
						  $src = $img[0];

						  ?>
				          <figure class="span2"><img src="<?php echo $src; ?>">
				          </figure>
						  <?php
	          			}
	          		?>

	          	</div>
	          </a>


	          <?php } ?>
	         </div>

          <?php } else { ?>
            <form method="post" action="">
              <input type="hidden" name="ID" value="<?php echo $the_post->ID; ?>">
              <input type="text" name="post_title" value="<?php the_title(); ?>">
              <?php eas_editor(get_the_content(), 'postcontent', 'post_content', true); ?>
              <input type="submit" class="btn btn-primary" value="Save">
            	<?php eas_delete_button(); ?>
            </form>
          <?php } ?>
          <?php roots_post_inside_after(); ?>
        </article>
        <?php 
      // Get child posts if we're requesting more than one post
      if ($wp_query->query_vars['posts_per_page'] != 1 && !$edit && !$compact) {
        
        eas_get_forum_posts($the_post->ID); 
      }
      
      ?>

      </li>
	<?php
}

function eas_artwork_form($file = true, $action = "/upload", $elid="upload_art", $the_post = false, $the_meta = false, $disabled = false, $contestname = false, $contestform = false) {
	echo eas_get_artwork_form($file, $action, $elid, $the_post, $the_meta, $disabled, $contestname, $contestform);
}

function eas_get_artwork_form($file = true, $action = "/upload", $elid="upload_art", $the_post = false, $the_meta = false, $disabled = false, $contestname = false, $contestform = false) {
	global $user_ID;
	get_currentuserinfo();
	$meta = get_user_meta($user_ID);
	$title = $year = $medium = $credits = $moreinfo = '';
	if ($the_post !== false) {
		$meta = get_post_meta($the_post->ID);
		$title = $the_post['post_title'];
		$year = $meta['year'];
		$medium = $meta['medium'];
		$credits = $meta['credits'];
		$moreinfo = get_the_content($the_post->ID);
	}
	$the_return = '<form ';
		if ($file) $the_return.= ' enctype="multipart/form-data"'; 
		$the_return .='action="'.$action.'" method="post" id="'.$elid.'">';
         if ($file) $the_return .='	<p><input type="file" name="artwork"></p>';

         if ($contestname !== false) $the_return .= '<input type="hidden" name="contest" value="'.$contestname.'">';
         $the_return.='   <p><input type="text" name="title" placeholder="Title" value="'.$title.'"></p>
            <p><input type="text" name="raey" placeholder="Year" value="'.$year.'"></p>
            <p><input type="text" name="medium" placeholder="Medium" value="'.$medium.'"></p>
            <p><input type="text" name="size" placeholder="Size"></p>
            <p>Description
              
              '.eas_get_editor($moreinfo, 'moreinfo', 'more_info', true)
             .'
            </p>';
            
            $nudge = array();
            foreach (array('School', 'Location', 'Birthday') as $m) {

				if (!(isset($meta[strtolower($m)]) && count($meta[strtolower($m)]) && !empty($meta[strtolower($m)][0]))) { 
					array_push($nudge, $m);
            	}
            }


            foreach ($nudge as $n) {
							$required = $n != 'School' ?  ' <span class="required">*</span>' : '';
            	$the_return.='
            		<p><input type="text" name="'.strtolower($n).'" placeholder="Your '.$n.'" value="">'.$required.'</p>
            	';
            }              
						if(count($nudge))
							$the_return .= '<p><span class="required">*</span> required';

            // $the_return .= eas_get_cc_settings('Creative Commons License');

            if ($contestform) {
            	$howmany = eas_how_many_more_submissions($contestname);
            	if ($howmany !== false) {
            		$the_return .='<label class="checkbox" for="contestname"><input type="checkbox" checked name="contestname" value="'.$contestname.'">Submit this work to our current contest <em>Crossing Borders</em>? Work submitted for this contest will not be visible on the site until the contest is over.</label>';

            	}
            }

            $the_return .='<input type="hidden" id="attachmentid" name="artwork_img_id" value="">
              <input id="artworksubmit" type="submit" class="btn btn-primary';
              if ($disabled) $the_return.= ' disabled';
              $the_return.='"';
              if ($disabled) $the_return.= ' disabled ';
              $the_return.= 'value="Submit">';
        $the_return.='</form>';

        return $the_return;
}


function eas_artwork_form_v2($file = true, $action = "/upload", $elid="upload_art", $the_post = false, $the_meta = false, $disabled = false, $contestname = false, $contestform = false) {
	echo eas_get_artwork_form_v2($file, $action, $elid, $the_post, $the_meta, $disabled, $contestname, $contestform);
}

function eas_get_artwork_form_v2($file = true, $action = "/upload", $elid="upload_art", $the_post = false, $the_meta = false, $disabled = false, $contestname = false, $contestform = false) {
	global $user_ID;
	get_currentuserinfo();
	$meta = get_user_meta($user_ID);
	$title = $year = $medium = $credits = $moreinfo = '';
	if ($the_post !== false) {
		$meta = get_post_meta($the_post->ID);
		$title = $the_post['post_title'];
		$year = $meta['year'];
		$medium = $meta['medium'];
		$credits = $meta['credits'];
		$moreinfo = get_the_content($the_post->ID);
	}
	$the_return = '<form ';
		if ($file) $the_return.= ' enctype="multipart/form-data"'; 
		$the_return .='action="'.$action.'" method="post" id="'.$elid.'">';
         if ($file) $the_return .='	<p><input type="file" name="artwork"></p>';

         if ($contestname !== false) $the_return .= '<input type="hidden" name="contest" value="'.$contestname.'">';
         $the_return.='   <p><input type="text" name="title" placeholder="Title" value="'.$title.'"> &nbsp*</p>
            <p><input type="text" name="raey" placeholder="Year" value="'.$year.'"> &nbsp*</p>
            <p><input type="text" name="medium" placeholder="Medium" value="'.$medium.'"> &nbsp*</p>
            <p><input type="text" name="size" placeholder="Size"> &nbsp*</p>
            <p>Description</p>
            <p>'.eas_get_editor($moreinfo, 'moreinfo', 'more_info', true).'</p>';
            
            $nudge = array();
            foreach (array('School', 'Location', 'Birthday') as $m) {
				if (!(isset($meta[strtolower($m)]) && count($meta[strtolower($m)]) && !empty($meta[strtolower($m)][0]))) { 
					array_push($nudge, $m);
            	}
            }

            foreach ($nudge as $n) {
				$required = $n != 'School' ?  ' <span class="required">*</span>' : '';
            	$the_return.='<p><input type="text" name="'.strtolower($n).'" placeholder="Your '.$n.'" value="">'.$required.'</p>';
            } 
			if(count($nudge)) {
				$the_return .= '<p><span class="required">*</span> required';
			}

            if ($contestform) {
            	$howmany = eas_how_many_more_submissions($contestname);
            	if ($howmany !== false) {
            		$the_return .='<label class="checkbox" for="contestname"><input type="checkbox" checked name="contestname" value="'.$contestname.'">Submit this work to our current contest <em>Crossing Borders</em>? Work submitted for this contest will not be visible on the site until the contest is over.</label>';

            	}
            }

            $the_return .='<input type="hidden" id="attachmentid" name="artwork_img_id" value="">
              <input id="artworksubmit" type="submit" class="btn btn-primary';
              if ($disabled) $the_return.= ' disabled';
              $the_return.='"';
              if ($disabled) $the_return.= ' disabled ';
              $the_return.= 'value="Submit">';
        $the_return.='</form>';

        return $the_return;
}

/*
function new_eas_artwork_form($disabled = false) {
	global $user_ID;
	get_currentuserinfo();
	$meta = get_user_meta($user_ID);
	$title = $year = $medium = $credits = $moreinfo = '';

	$the_return = '<form action="" method="post" id="artworkform">
	<input type="hidden" name="fpurl" id="fpurl">
	<input type="hidden" name="fpname" id="fpname">
	<p><input type="text" name="title" placeholder="Title"></p>
    <p><input type="text" name="year" placeholder="Year"></p>
    <p><input type="text" name="medium" required placeholder="Medium (required)"></p>
    <p><input type="text" name="size" required placeholder="Size (required)"></p>
    <p>Description
      
      '.eas_get_editor($moreinfo, 'moreinfo', 'more_info', true)
     .'
    </p>
    ';

    $nudge = array();

    foreach (array('School', 'Birthday', 'Location') as $m) {

		if (!(isset($meta[strtolower($m)]) && count($meta[strtolower($m)]) && !empty($meta[strtolower($m)][0]))) { 
			array_push($nudge, $m);
    	}
    }

    if (count($nudge)) {
    	$the_return.='<p>Please include the following information to help us determine if your work is right for the site (it most likely is!). You can change this information in your settings.</p>';
    }            	

    foreach ($nudge as $n) {
    	if ($n == 'Location') {
    		$additional = ' (City, Country) ';
    	} else {
    		$additional = ' ';
    	}
    	$the_return.='
    		<p><input type="text" name="'.strtolower($n).'" required placeholder="Your '.$n.$additional.'(required)" value=""></p>
    	';
    }    

     $the_return .= eas_get_cc_settings('Creative Commons License'); 

    $the_return .= '<input id="artworksubmit" type="submit" class="btn btn-primary';

      if ($disabled) $the_return.= ' disabled';
              $the_return.='"';
              if ($disabled) $the_return.= ' disabled ';
              $the_return.= 'value="Submit">';
        $the_return.='</form>';
        return $the_return;
} */

function eas_cc_settings($heading='Creative Commons License', $explain = true) {

	echo eas_get_cc_settings($heading, $explain);


}

function eas_get_cc_settings($heading = 'Creative Commons License', $explain = true) {
			global $user_ID;
		get_currentuserinfo();
		$usercc = get_user_meta($user_ID, 'cc', true);
		$radios = array(
			'allowmods' => array(
				array('','Yes',false),
				array('-sa','Only if they use the same Creative Commons license',false),
				array('-nd','No',false)
			),
			'comm' => array(
				array('','Yes',false),
				array('-nc','No', false)
			)
		);

		if (empty($usercc)) {
			$usercc = 'by-nc-nd';
		}

		$cc_array = array_slice(explode('-', $usercc), 1);

		foreach ($cc_array as $c) {
			if ($c == 'nc') {
				$radios['comm'][1][2] = true;
			} else if ($c == 'nd') {
				$radios['allowmods'][2][2] = true;
			} else if ($c == 'sa') {
				$radios['allowmods'][1][2] = true;
			}
		}

		$radios['allowmods'][0][2] = !($radios['allowmods'][1][2] || $radios['allowmods'][2][2]);

		$radios['comm'][0][2] = !($radios['comm'][1][2]);

		$the_return = '
    	<h3>'.$heading.'</h3>';
    	if ($explain) {
    		$the_return .= '
    	<p>These settings protect your work from being improperly shared. You can read more about them on the <a href="http://creativecommons.org/licenses/" title="About the Licenses - Creative Commons">Creative Commons</a> site.</p>
    	<p>Creative Commons licenses are non-revocable - the first published CC license associated with a work always applies.</p>
    	<p>All licenses require anyone who uses your work to give you credit.</p>
    	<p>Do you give permission to others to modify/remix this work when they share it?
    	';
    	}

    	foreach ($radios['allowmods'] as $r) {

    			$the_return .= get_radio_button($r, 'allowmods');

    	}
    	if ($explain) {
      	$the_return .='</p>
      	<p>Do you give permission for your work to be used commercially?</p>';
    	}
    	foreach ($radios['comm'] as $r) {
 
    			$the_return .=get_radio_button($r, 'comm');

    	}
    	$the_return .='
          </p>';
          return $the_return;
}

function display_radio_button($r, $name) {
	echo get_radio_button($r, $name);
}

function get_radio_button($r, $name) {
	$the_return = '    			<label class="radio">
    				<input type="radio" 
    					name="'.$name.'" 
    					value="'.$r[0].'"';
    					if ($r[2] == true) $the_return .= ' checked="true"';
    				$the_return .='>
    				'.$r[1].'
    			</label>';

    return $the_return;
}

function eas_save_cc($id, $user = false) {
	$ccstr = 'by'.$_POST['comm'].$_POST['allowmods'];
	eas_update_cc($id, $ccstr, $user);

}

function eas_update_cc($id, $ccstr, $user) {
	if (!$user)
		return update_post_meta($id, 'cc', $ccstr);
	else
		return eas_update_usermeta($id, 'cc', $ccstr);
}

function eas_display_cc($post_id = 0, $text = true) {
	echo eas_get_cc($post_id, $text);

}

function eas_get_cc($post_id = 0, $text = true) {
		global $post;
	if ($post_id == 0) {
		$post_id = $post->ID;
		$the_post = $post;
	} else {
		$the_post = get_post($post_id);
	}
	$the_user = get_userdata($the_post->post_author);

	$ccstr = get_post_meta($post_id,'cc', true);
	if (!empty($ccstr)) {
	$the_return = '
		 <div class="cclicense">
		 	<a rel="license" href="http://creativecommons.org/licenses/'.$ccstr.'/3.0/">
		 		<img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/'.$ccstr.'/3.0/88x31.png" />
		 	</a>';
	if ($text) $the_return .= '<span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">
		 		'.$post->post_title.'
		 	</span>
		 	 by 
		 	<a xmlns:cc="http://creativecommons.org/ns#" href="'.eas_artwork_url($post->ID).'" property="cc:attributionName" rel="cc:attributionURL">
		 		'.$the_user->display_name.'
		 	</a>
	 		 is licensed under a 
	 		 <a rel="license" href="http://creativecommons.org/licenses/'.$ccstr.'/3.0/">
	 		 	Creative Commons 
	 		 	'.eas_cc_words($ccstr).' 
	 		 	3.0 License
	 		 </a>.';
	$the_return.=	'</div>
	';

	return $the_return;
	}
}

function eas_display_cc_general($post_id = 0, $text = true) {
  echo eas_get_cc_general($post_id, $text);
}


function eas_get_cc_general($text = true) {
  
  $ccstr = 'by-nc-nd';
  $the_return = '<div class="cclicense">
    All works are licensed under a 
    <a rel="license" href="http://creativecommons.org/licenses/'.$ccstr.'/3.0/">
    Creative Commons Attribution-NonCommercial-NoDerivs 3.0 License</a></div>';
  return $the_return;
}

function eas_cc_words($str) {
	return str_replace('by','Attribution',
		str_replace('nd','NoDerivs', 
			str_replace('sa', 'ShareAlike', 
				str_replace('nc', 'NonCommercial', $str)
			)
		)
	);
}


function eas_forum_form($parent_id, $assoc_id = 0, $header = false, $header_text = 'Add Response', $autofocus = true) {
	$title = '';
	if ($parent_id) {
		$title = eas_re(get_the_title($parent_id));
	} else if ($assoc_id) {
		$title = eas_re(get_the_title($assoc_id));
	} ?>

	<div class="forum_form">
	
		<?php if ($header == true) { ?>
	    	<h3> <?php echo $header_text; ?> </h3>
		<?php } ?>

		<?php if (eas_is_forum()) echo '<div class="row">'; ?>

		<form <?php if (eas_is_forum()) echo 'class="span8"'; ?> method="post" action="/post" id="forum_reply">
			
            <input type="hidden" name="parent" value="<?php echo $parent_id; ?>">
        	<?php if ($assoc_id > 0) { ?>
        		<input type="hidden" name="assoc_id" value="<?php echo $assoc_id; ?>">
        	<?php } ?>
            <p><input type="text" name="post_title" placeholder="Title (Optional)" value="<?php echo $title; ?>"></p>
            	<?php 
            	if (eas_is_forum() && !$parent_id) {
            		if (isset($_GET["cat"])) {
            			$cat = $_GET["cat"];
            		} else {
            			$cat = 8;
            		}
            		echo '<span class="catfieldlabel">Category:</span>';
            		wp_dropdown_categories(array(
            		'taxonomy' => 'forum_category',
            		'selected' => $cat,
            		'hide_empty' => 0,
            		'exclude' => '7'
            	));
            	} else if (!eas_is_forum()) {
            		?>
            			<input type="hidden" name="cat" value="7">
            		<?php
            	}

            	?>
            <p>

              <?php 
              eas_editor('', 'postcontent', 'post_content', $autofocus);
              ?>
            </p>
            <p>
              <input type="submit" class="btn btn-primary" value="Submit">
            </p>
          </form>
         <?php /* if (eas_is_forum()) { ?>
			< div class="forumextras span4">
	          	<div class="favorites">
	          		<h3>Add Artwork</h3>
	          		<p>Click a work to add it to the content of your post.</p>
	          		<p>You can also add works by pasting the web address to the work into your post.</p>
	          		<h4>Your Favorites</h4>
		          	<?php $favs = eas_get_favorites(); 

		          		?><ul><?php
		          		foreach ($favs as $f) {
		          			?><li><?php
		          			eas_forumextra_tag($f->id);
		          			?></li><?php
		          		}
		          		?></ul><?php

		          	?>
		         </div>
			</div>
		<?php  } */ ?>
          
    	<?php if (eas_is_forum()) echo '  </div>'; ?>
    </div>
	<?php
}

function eas_forum_cat_menu() {
	?>
    <nav id="nav-forum" role="navigation">
      <?php wp_nav_menu(array(
        'theme_location' => 'forum_categories', 
        'walker' => new eas_forum_walker,
        'menu_class' => 'nav-pills'
      )); ?>
    </nav>
	<?php
}

function eas_art_pages_menu() {
	?>
		<nav id="nav-art" role="navigation">
			<?php
				wp_nav_menu(array(
					'theme_location' =>'art_menu',
					'menu_class' => 'nav-pills'
				));
			?>
		</nav>
	<?php
}

function eas_settings_pages_menu() {
	?>
		<nav id="nav-settings" role="navigation">
			<?php
				wp_nav_menu(array(
					'theme_location' =>'settings_menu',
					'menu_class' => 'nav-pills'
				));
			?>
		</nav>
	<?php
}

function eas_footer_menu() {
	?>

		<nav id="nav-footer" role="navigation">
			<?php
				wp_nav_menu(array(
					'theme_location' =>'footer_menu'
				));
			?>
		</nav>

	<?php
}


function eas_new_post_button($mini = false, $reply = false) {
	global $wp_query;
	global $post;

	if ($mini) {
		$class_suffix = ' btn-mini';
	} else {
		$class_suffix = '';
	}
	$post_type = $wp_query->query_vars['post_type'];
	if (!$reply && isset($wp_query->queried_object->taxonomy)) {
		$url = '/post?cat='.$wp_query->queried_object->term_id;
		$text = 'New Post';
	} else if ($post_type == 'forum') {
		$url = eas_forum_url($post->ID).'/reply';
		$text = 'Comment';
	} else {
		$url = '/post';
		$text = 'New Post';
	}
	?>
		<a href="<?php echo $url; ?>" class="eas_new_post_button"> <!-- class="btn<?php echo $class_suffix; ?>" -->
			<?php echo $text; ?>
		</a>
	<?php 
}

function eas_comment_button($mini = false, $reply = false) {
	global $wp_query;
	global $post;

	if ($mini) {
		$class_suffix = ' btn-mini';
	} else {
		$class_suffix = '';
	}
	$post_type = $wp_query->query_vars['post_type'];
	$url = eas_forum_url($post->ID).'/reply';
	$text = 'Comment';
	?>
		<a href="<?php echo $url; ?>" class="eas_comment_button"> <!-- class="btn<?php echo $class_suffix; ?>" -->
			<?php echo $text; ?>
		</a>
	<?php 
}

function eas_artwork_thread($artwork_id, $autofocus = true) {
	global $wp_query;
	if (is_user_logged_in()) {
		eas_forum_form(0, $artwork_id, true, 'Add Response', $autofocus);
	}
	else
	{
		echo '<form action="/login" method="GET"><input type="hidden" name="redirect" value="'. eas_relative_page_url() .'"/><input type="submit" value="Comment" class="btn btn-primary"/></form>';
	}
	


	$temp_query = $wp_query;
	$wp_query = null;

	$wp_query = new WP_Query(array(
		'meta_key' => 'assoc_id',
		'meta_value' => $artwork_id,
		'post_type' => 'forum'
	));

	get_template_part('loop','forum');

	$wp_query = null;
	$wp_query = $temp_query;
	wp_reset_postdata();

}

function eas_display_avatar($id) {
	echo eas_get_avatar_display($id);
}

function eas_get_avatar_display($id) {
		$avatar_meta = get_user_meta($id, 'avatar');
	if (count($avatar_meta) > 0) {
		$avatar_img = wp_get_attachment_image_src($avatar_meta[0]);
		$avatar_src = $avatar_img[0];
		return '<img class="avatar" src="'.$avatar_src.'">';
	} else {
		return false;
	}
}

function eas_avatar_src($id) {
	$avatar = get_user_meta($id, 'avatar');
	return wp_get_attachment_image_src($avatar[0]);
}

function eas_get_featured_forum_posts() {
	global $wp_query;
	$paged = $wp_query->query_vars['paged'];
	$temp_query = $wp_query;
	$wp_query = null;

	$wp_query = new WP_Query(
		array(
			'post_type' => 'forum',
			'meta_key' => 'feat',
			'meta_value' => '1',
			'paged' => $paged,
			'tax_query' => array(
				array(
					'taxonomy' => 'forum_category',
					'field' => 'slug',
					'terms' => 'gallery',
					'operator' => 'NOT IN'
			))
		)
	);

	get_template_part('loop','forum');
	$wp_query = null;
	$wp_query = $temp_query;
	wp_reset_postdata();
}

function eas_get_gallery_forum_posts($paged = 0, $perpage = 10) {
	global $wp_query;
	$temp_query = $wp_query;
	$wp_query = null;
	$wp_query = new WP_Query(
		array(
			'post_parent' => 0,
			'post_type' => 'forum',
			'posts_per_page' => $perpage,
			'paged' => $paged,
			'meta_key' => 'feat',
			'meta_value' => 1,
			'galleryview' => 1,
			'tax_query' => array(
				array(
					'taxonomy' => 'forum_category',
					'field' => 'slug',
					'terms' => 'gallery'
				)
			)
		)
	);

	get_template_part('loop','art_home');
	$wp_query = null;
	$wp_query = $temp_query;
	wp_reset_postdata();
}

function eas_get_forum_posts($parent = 0, $single = false) {
	global $wp_query;


	$temp_query = $wp_query;
	$wp_query = null;

	if (!$single) {

		$wp_query = new WP_Query(
			array(
		    	'post_parent' => $parent,
		    	'post_type' => 'forum',
		    	'posts_per_page' => 10,
		    	'paged' => $wp_query->query_vars['paged']
		  	)
		);

	} else {

		$wp_query = new WP_Query(
			array(
				'p' => $parent,
				'posts_per_page' => 1,
				'post_type' => 'forum'
			)
		);

	}



	get_template_part('loop','forum');

	$wp_query = null;
	$wp_query = $temp_query;
	wp_reset_postdata();

}

function eas_artwork_span($the_post_id, $id, $title, $width = 6, $img = 'span6xspan4') {
	  $img_ob = eas_artwork_img($id, $img);
  		$src = $img_ob[0];
  		$posturl = eas_art_url($the_post_id);
	  ?>

      <figure class="gallerybox span<?php echo $width; ?>">
  	    <a  href="<?php echo $posturl; ?>">
	      	<div class="relative">
		        <img class="<?php echo $img; ?>" src="<?php echo $src; ?>">
		        <figcaption><div class="inner"><h2><?php echo $title; ?></h2><span class="numresponses"><?php
		        	$numresponses = eas_num_responses($the_post_id);
		         	

		         	if ($numresponses) {
		         		echo $numresponses.' response';
		         		if ($numresponses-1) echo 's';
		         	}


		         ?></span></div></figcaption>
		    </div>
	    </a>
      </figure>
  <?php
}

function eas_num_responses($id) {
	global $wpdb;
	$query = $wpdb->prepare("
			select count(p.ID) from wp_postmeta m
			join wp_posts p
			on m.post_id = p.ID
			where m.meta_key = 'op'
			and m.meta_value = %d
			and p.ID != %d
			and p.post_status = 'publish'
			", $id, $id
	);


	$numresponses = $wpdb->get_var($query);

	return $numresponses;
}

function eas_display_meta($meta, $key, $display_key = true) {

	echo eas_get_meta_display($meta, $key, $display_key);

}

function eas_get_meta_display($meta, $key, $display_key = true) {
		if (isset($meta[$key]) && !empty($meta[$key][0])) {
		if ($key == 'location') {
			$label = 'Location:&nbsp&nbsp';
			$text = $meta[$key][0];
		} else if ($key == 'year') {
			$label = 'Year:&nbsp&nbsp';
			$text = $meta[$key][0];
		} else if ($key == 'medium') {
			$label = '';
			$text = $meta[$key][0];
		} else if ($key == 'description') {
			$label = 'Description:&nbsp&nbsp';
			$text = $meta[$key][0];
		} else if ($key == 'school') {
			$label = 'School:&nbsp&nbsp';
			$text = $meta[$key][0];
		} else if ($key == 'prize') {
			$label = 'Prize:&nbsp&nbsp';
			$text = $meta[$key][0];
		}
	}

	$the_return = '<div class="artistmeta">';

	if (!empty($text)) {
		if ($display_key) {
			$the_return.='<div class="artistmetaname">'.$label.'</div>';
		}
			$the_return.='<div class="artistmetaval inline">'.$text.'</div>';
	}

	$the_return .= '</div>';

	return $the_return;
}

function eas_forum_meta() {
	global $post;
	global $wp_query;
	if ($wp_query->query_vars['p'] != $post->ID) {
		$link_before = '<a href="'.eas_forum_url($post->ID).'">';
		$link_after = '</a>';
	} else {
		$link_before = $link_after = '';
	}
	/* $time = date_i18n("l, F j, Y g:i a", strtotime($post->post_date)); */

	$time = date_i18n("F j, Y", strtotime($post->post_date));
	echo '<time class="updated" datetime="'. get_the_time('c') 
	.'" pubdate>'. sprintf($link_before.'%s'.$link_after, $time) .'</time>';
	eas_forum_cat();

}

function eas_forum_cat() {
	global $post;
	$terms = get_the_terms($post->ID, 'forum_category');

	if ($terms !== false) {
		foreach ($terms[0] as $this_term) {
			/* echo '<span> in <a href="'.eas_forum_cat_url($this_term->term_id).'" title="'.$this_term->name.'">'.$this_term->name.'</span>'; */
		}
		/* foreach ($terms as $this_term) {
			echo '<span> in <a href="'.eas_forum_cat_url($this_term->term_id).'" title="'.$this_term->name.'">'.$this_term->name.'</span>';
		} */
	}
}

function eas_artwork_img($id, $size = 'thumbnail') {
    $args = array('post_type' => 'attachment', 'numberposts' => 1, 'post_status' => 'inherit', 'post_parent' => $id);
    $attachments = get_posts($args);
    $img = wp_get_attachment_image_src($attachments[0]->ID, $size);
    if (preg_match('/\.gif$/', $img[0]) && $size == 'large') {
    	$size = 'full';
    	$img = wp_get_attachment_image_src($attachments[0]->ID, $size);
    }
    return $img;
}

function eas_not_logged_in() {
	echo 'you are not logged in.';
}

function eas_not_owner() {
	echo 'you are not the owner of this post.';
}



function eas_artworks_by_user($id, $perpage = -1, $contest = false) {
		$args = array('author' => $id, 'post_type' => 'artwork', 'posts_per_page' => $perpage);
	if ($contest !== false) {
		$args['meta_key'] = 'contest';
		$args['meta_value'] = $contest;
		$args['post_status'] = 'draft';
    if ($contest == 'opencall') {
      $args['post_status'] = 'draft';
    } 
	}
  return get_posts($args);
}

function eas_artist_page_url($id) {
	return get_bloginfo('siteurl').'/artists/'.$id;
}


function eas_artwork_url($id) {
	return get_bloginfo('siteurl').'/a/'.$id;
}

function eas_forum_url($id) {
	$terms = get_the_terms($id, 'forum_category');
	$feat = get_post_meta($id, 'feat', true);
	if ($terms !== false) {
		foreach ($terms as $t) {
			if (empty($feat) || $t->slug != 'gallery') {
				return get_bloginfo('siteurl').'/forum/'.$id;
			} else if ($feat == '1') {
				return eas_art_url($id);
			}
		}
	} else {
		return get_bloginfo('siteurl').'/forum/'.$id;
	}


	
}

function eas_dndupload($contestname = false) {
	if (is_user_logged_in()) {
						$is_contest = false;
				$contest_query = '';
		if ($contestname !== false) {
			$is_contest = true;
			$contest_query = '?contestname='.$contestname;
		} 
		
		$the_return = '<p>If you\'re having problems with this page you can try our <a href="/upload'.$contest_query.'" title="Basic Uploader">basic uploader</a>.</p>';

		$show_it = true;
		if ($is_contest) {
			$how_many = eas_how_many_more_submissions($contestname);
			if ($how_many !== false) {
				$the_return .= '<p class="howmany">'.$how_many.'</p>';

			} else {
				$the_return .='<p class="howmany">Thank you for your submissions!</p>';
				$show_it = false;
			}
		}

		if ($show_it) {
			$the_return .=	'<div id="browser-warning">
			<p>It looks like your web browser might not work with this page. Try our <a title="Basic Uploader" href="/upload/basic/">basic uploader</a> instead!</p>
		</div>

		<!--dierct upload input ("fakeinput")-->
		<input type="file" id="filepicker-input" multiple="true"/>  	
		


		<div id="uploadwelcome">
		Drag and Drop an image or <span class="btn" id="direct-upload-text">click here to select one.</span>
		</div>
		
		<div id="dropbox" src="'.trailingslashit(get_bloginfo('siteurl')).'js/dnduploader/public_html/static/dropbox.png" alt="">
            <div class="relative">
            <div id="upload-problems"></div>
            <div class="dropboxarrow">&#x25BD;</div>
            <div class="dropboxtext"><span class="dragtext">Drag and </span>Drop your art here</div>
            <div class="uploadedimg">
                <img id="uploaded">
                <div id="fileinfo">
                    <div class="progress progress-striped active">
                        <div id="fileprogress" class="bar" style="width: 0%"></div>
                    </div>
                    <div id="preview">
                        <ul id="dropped-files">         
                        </ul>
                    </div>
                    <img id="ajax-loader" src="'.trailingslashit(get_bloginfo('siteurl')).'js/dnduploader/public_html/static/ajax-loader.gif" alt=""/>

                </div>
            </div>
        </div>
        </div>
        <div id="uploadform">
            <p>You can edit the details of this artwork while it uploads.</p>';
            if ($is_contest) {
                $action = '/'.$contestname;
            } else {
                $action = '/upload';
            }
            $the_return .=eas_get_artwork_form(false, $action, 'upload_art', false, false, true, $contestname).'
            <div id="hasntuploaded">You can save this info once your artwork loads.</div>
        </div>
		


<div id="drop-box-overlay">
</div>


<script src="'.trailingslashit(get_bloginfo('siteurl')).'js/dnduploader/public_html/static/jquery-1.7.2.min.js"></script>
<script src="'.trailingslashit(get_bloginfo('siteurl')).'js/dnduploader/public_html/static/jquery-ui-1.8.19.custom.min.js"></script>

<script src="'.trailingslashit(get_bloginfo('siteurl')).'js/dnduploader/public_html/static/jsUpload.js"></script>
<script src="'.trailingslashit(get_bloginfo('siteurl')).'js/dnduploader/public_html/static/main.js"></script>


	';


		}

			return $the_return;

		} else {
			return '<p class="howmany"><a href="/login?redirect='.eas_relative_page_url().'" title="Log In">Log in</a> or <a href="/register?redirect='.eas_relative_page_url().'" title="Register">register</a> to upload art! It only takes a moment!</p>';
		}

		
}

function eas_register_form($redirect = false) {

	?>
		      <form action="/login<?php eas_redirect_str($redirect); ?>" method="POST" autocomplete="off">

                <input type="hidden" name="register" value="1">
                <p>
                  <input type="text" name="nickname" placeholder="your name" value="<?php echo $_POST['nickname']; ?>">
                </p>
                <p>
                  <input type="email" name="email" placeholder="email address" value="<?php echo $_POST['email']; ?>">
                </p>
                <p>
                  <input type="text" name="username" placeholder="username" value="<?php echo $_POST['username']; ?>">
                </p>
                <p>
                  <input type="password" name="password" placeholder="password">
                </p>
                <p>
                  <label for="remember" class="checkbox"><input type="checkbox" name="remember">Remember Me</label>
                </p>
                
                <?php
                  require_once('recaptchalib.php');
  				  $publickey = "6LfWge4SAAAAAMQHTPPeFhfUQTMhzKCaBG_KCYP4"; 
                  echo recaptcha_get_html($publickey);
                ?>

               <!--
 <script type="text/javascript"
                   src="http://www.google.com/recaptcha/api/challenge?k=6LcaD-4SAAAAAIAldWSXHRLNkGqQvgUGbXUBq0Zd">
                </script>
                <noscript>
                   <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LcaD-4SAAAAAIAldWSXHRLNkGqQvgUGbXUBq0Zd"
                       height="300" width="500" frameborder="0"></iframe><br>
                   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
                   </textarea>
                   <input type="hidden" name="recaptcha_response_field"
                       value="manual_challenge">
                </noscript>
-->

                <p>
                  <input type="submit" class="btn btn-primary" value="Register">
                </p>
              </form>
	<?php
}

function eas_redirect_field($redirect = false) {



	if ($redirect !== false) {
		?>
			<input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
		<?php
	}


}

function eas_redirect_str($redirect = false) {
	if ($redirect !== false) {
		echo '?redirect='.$redirect;
	}
}

function eas_login_form($redirect = false) {
	?>
              <p>
              <form action="/login<?php eas_redirect_str($redirect); ?>" method="post">
              	
                <input type="hidden" name="login" value="1">
                <p>
                  <input type="text" name="username" placeholder="username or email address">
                </p>
                <p>
                  <input type="password" name="password" placeholder="password">
                </p>
                <p>
                  <label for="remember" class="checkbox"><input type="checkbox" name="remember">Remember Me</label>
                </p>

                <!--script type="text/javascript"
                   src="http://www.google.com/recaptcha/api/challenge?k=6LcaD-4SAAAAAIAldWSXHRLNkGqQvgUGbXUBq0Zd">
                </script>
                <noscript>
                   <iframe src="http://www.google.com/recaptcha/api/noscript?k=6LcaD-4SAAAAAIAldWSXHRLNkGqQvgUGbXUBq0Zd"
                       height="300" width="500" frameborder="0"></iframe><br>
                   <textarea name="recaptcha_challenge_field" rows="3" cols="40">
                   </textarea>
                   <input type="hidden" name="recaptcha_response_field"
                       value="manual_challenge">
                </noscript-->


                <p>
                  <input type="submit" class="btn btn-primary" value="Login">
                </p>
                <p>
                  <a href="/forgotpassword" title="Password Recovery">Forgot your Password?</a>
                </p>
              </form>

	<?php
}

function eas_art_url($id) {
	return get_bloginfo('siteurl').'/art/'.$id;
}

function eas_forum_cat_url($id) {
	$term = get_term($id, 'forum_category');
	return get_bloginfo('siteurl').'/forum/'.$term->slug.'/';
}

function eas_forum_cat_from_slug($id) {

}

function eas_is_artist($the_user_id = 0) {
	global $user_ID;
	if ($the_user_id == 0) {
		get_currentuserinfo();
		$the_user_id = $user_ID;
	}
	$user = get_userdata($the_user_id);
	return $user->roles[0] == 'artist';
}

function eas_user_is_admin() {
	return current_user_can('manage_options');
}

function eas_user_is_juror() {
	return current_user_can('view_subs') || eas_user_is_admin();
}



function eas_artists_grid($page = 0, $per_page = 20, $contest = false, $sort = 'recent') {
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
		$user_type_query = "    	        join (
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
	      // eas_display_user($author->ID, true, $contestname);
	      eas_artists_cell($author->ID);
	    }
}

function eas_artists_cell($id, $contest = false) {


  $this_author = get_userdata($id);
  $artworks = eas_artworks_by_user($id, 1, $contest);
  $meta = get_user_meta($id);

  if ($contest !== false) {
  	$linkurl = trailingslashit(get_bloginfo('siteurl')).$contest.'/submissions/'.$id;
  } else {
  	$linkurl = eas_artist_page_url($id);
  }

  $the_return = '';
  
  $the_return.='
    <div class="artists_cell">
	    <a class="userblock" href="'.$linkurl.'">
	      	'./*eas_get_avatar_display($id).*/'
	  		<div class="nickname">'.$this_author->nickname.'</div>
	      	'.eas_get_follow_button($id, true).'
		</a>
      <!-- div class="imagelist row" -->
  ';

    foreach ($artworks as $a) {

		  $img = eas_artwork_img($a->ID, 'span2-crop');
		  $src = $img[0];

		  /* formerly a link to the artwork: eas_artwork_url($a->ID) */
		  $the_return.='
          <a href="'.$linkurl.'">
	          <!--figure class="span2"-->
	          <img src="'.$src.'">
	          <!--/figure-->
          </a>
		  ';
		}

	$the_return.='
		<div class="usermeta">
			'.eas_get_meta_display($meta, 'location', false).'
		</div>
		<div class="usermeta">
			'.eas_get_meta_display($meta, 'school', false).'
		</div>
		<div class="usermeta">'.eas_get_birthday_display_for_admins($id).'</div>
		<div class="usermeta">
			'.eas_get_email_display_for_admins($id).'
		</div>
		</div>';

   echo $the_return;

}

/* 
function eas_show_usermeta($var, $meta, $id) {
	switch ($var) {
		case 'location':
			str = eas_get_meta_display($meta, 'location', false);
			break;
		case 'school':
			str = eas_get_meta_display($meta, 'school', false);
			break;
		case 'birthday':
			str = eas_get_birthday_display_for_admins($id);
			break;
		case 'email':
			str = eas_get_email_display_for_admins($id);
			break;
	}

	/*
	if(empty(str)) { 
		return;
	} else {  
		return '<div class="usermeta">'.str.'</div></br>';
	}


}
*/


function eas_recently_updated_artists($page = 0, $per_page = 10, $contest = false, $sort = 'recent') {
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
		$user_type_query = "    	        join (
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
	      // eas_artists_cell($author->ID);
	    }
}

function eas_unapproved_artists() {
	global $wpdb;
	$query = $wpdb->prepare("
		select distinct u.ID
			from wp_users u
			join wp_posts p
			on p.post_author = u.ID
			join wp_usermeta m
			on m.user_id = u.ID
			left join wp_usermeta m2
			on u.id = m2.user_id
			and m2.meta_key = 'declined'
			where p.post_type = 'artwork'
			and m2.meta_value is null
			and m.meta_key = 'wp_capabilities'
			and m.meta_value != 'a:1:{s:6:\"artist\";s:1:\"1\";}'
			order by p.post_date desc
	");

	$author_ids = $wpdb->get_results($query);
	foreach ($author_ids as $author) {
		eas_display_user($author->ID);
	}
}

function eas_display_user($id, $li = true, $contest = false) {
	echo eas_get_user_display($id, $li, $contest);
}

function eas_get_user_display($id, $li, $contest = false) {
	$this_author = get_userdata($id);
  $artworks = eas_artworks_by_user($id, 6, $contest);
  $meta = get_user_meta($id);

  if ($contest !== false) {
  	$linkurl = trailingslashit(get_bloginfo('siteurl')).$contest.'/submissions/'.$id;
  } else {
  	$linkurl = eas_artist_page_url($id);
  }

  $the_return = '';
  
  $the_return.='
    <li>
	    <a class="userblock" href="'.$linkurl.'">
	      <h3>
	      	'.eas_get_avatar_display($id).'
	  		<span class="nickname">'.$this_author->nickname.'</span>
	      	'.eas_get_follow_button($id, true);
	      if (!eas_is_artist($id) and eas_user_is_admin() and $contest === false) {
	      	$the_return.= eas_get_approve_artist_button($id);
	      	$the_return.= eas_get_decline_artist_button($id);
	      }
	      $the_return.='</h3>
	      <span class="usermeta">
	      	'.eas_get_meta_display($meta, 'location', false).'

	      </span>
	      <span class="usermeta">'.eas_get_birthday_display_for_admins($id).'</span>
	      <span class="usermeta">
	      	'.eas_get_email_display_for_admins($id).'
	      </span>
		</a>
      <div class="imagelist row">
  ';

    foreach ($artworks as $a) {

		  $img = eas_artwork_img($a->ID, 'span2-crop');
		  $src = $img[0];

		  $the_return.='
          <a href="'.eas_artwork_url($a->ID).'">
	          <figure class="span2"><img src="'.$src.'">
	          </figure>
          </a>
		  ';
		}

	$the_return.='</div>';

   /*if ($li) $the_return = '<li>'.$the_return.'</li>';*/
   return $the_return;
}

function eas_random_cb_works() {

	global $wpdb;

	$query = 'select p.id from wp_posts p 
	join wp_postmeta contest on contest.post_id = p.ID
	join wp_postmeta winner on winner.post_id = p.ID
	and p.ID != 4797 and p.ID != 4810 and p.ID != 4789 and p.ID != 4807
	where contest.meta_value = "crossingborders"
	and winner.meta_value = 1';


	$posts = $wpdb->get_col($query);

	shuffle($posts);

	return array_slice($posts, 0, 10);

}



function eas_relative_page_url() {
	$exploded = explode('?',$_SERVER['REQUEST_URI']);
	return $exploded[0];
}

function eas_is_edit() {
	global $wp_query;
	return $wp_query->query_vars['edit'] == '1';
}

function eas_page_type() {
	if (eas_is_artists()) return 'artists';
	if (eas_is_forum()) return 'forum';
	else return '';
}

function eas_is_artists() {
	global $wp_query;
	return is_author() || is_page('artists') || $wp_query->query_vars['searchtype'] == 'artists';
}

function eas_is_forum() {
	global $wp_query;
	return is_page('forum') || is_page('post')|| $wp_query->query_vars['post_type'] == 'forum' || $wp_query->query_vars['searchtype'] == 'forum';
}

function eas_is_artwork() {
	return (get_query_var('post_type') == 'artwork') || (get_query_var('meta_key') == 'assoc_id');
}

function query_to_words($query) {
	return array_map('strtolower',explode(' ', $query));
}

function eas_forum_search_results($search_query) {
	global $wpdb;
	global $wp_query;
	$words = explode(' ', $search_query);
	$result_lists = array();

	foreach ($words as $w) {
		$the_query = $wpdb->prepare("
			select distinct p.id from wp_posts p
			where p.post_type = 'forum'
			and p.post_content like '%%%s%%'
		", $w);

		$the_posts = $wpdb->get_results($the_query);
		array_push($result_lists, $the_posts);
	}

	/* if (sizeof($result_lists)>0) { */
		echo "<h2>Forum</h2>";
	/* } */

	$search_results = array();

	foreach ($result_lists as $list) {
		foreach ($list as $r) {
			if (!isset($search_results[$r->id])) {
				$search_results[$r->id] = 1;
			} else {
				$search_results[$r->id]++;
			}
		}
	}
	echo '<ul class="forum">';
	$search_q = new EasSearchResults($search_results);
	for (; $search_q->valid(); $search_q->next()) {
		setup_postdata(get_post($search_q->current()));
		eas_forum_post(get_post($search_q->current()));
	}
	echo '</ul>';

}


function eas_artist_search_results($search_query) {
	global $wpdb;
	$words = query_to_words($search_query);

	$result_lists = array();
	foreach ($words as $w) {

			$the_query = $wpdb->prepare("
				select distinct u.id from wp_users u 
				join (select * from wp_usermeta m3
					where m3.meta_key = 'wp_capabilities'
				) as m2
				on u.id = m2.user_id
				right join wp_usermeta m4
				on m4.user_id = u.id
				and m4.meta_key = 'verified'
				and m4.meta_value = 1
				join wp_usermeta m
				on u.id = m.user_id
				where m2.meta_value like '%%artist%%'
				and (
					lower(m.meta_value) like %s
					or lower(m.meta_value) like concat('%% ', %s, ' %%')
					or lower(m.meta_value) like concat(%s, ' %%')
					or lower(m.meta_value) like concat('%% ', %s, '.%%')
					or lower(m.meta_value) like concat('%% ', %s)
				)
			", $w, $w, $w, $w, $w);

			$the_posts = $wpdb->get_results($the_query);

			array_push($result_lists, $the_posts);
			
	}

	/* if (sizeof($result_lists)>1) { */
		echo "<h2>Portfolio</h2>";
	/* } */

	$search_results = array();


	foreach ($result_lists as $list) {
		foreach ($list as $r) {
			if (!isset($search_results[$r->id])) {
				$search_results[$r->id] = 1;
			} else {
				$search_results[$r->id]++;
			}
		}
	}

	$search_q = new EasSearchResults($search_results);
	for (; $search_q->valid(); $search_q->next()) {
		$user_info = get_userdata($search_q->current());
		?>
			<ul class="users">
				<?php eas_display_user($user_info->ID); ?>
			</ul>

		<?php
	}

}

function eas_user_gets_notified($notif, $uid = 0) {
	global $user_ID;
	if ($uid == 0) {
		get_currentuserinfo();
		$uid = $user_ID;
	}



	$settings = get_user_meta($uid, 'emailsettings', true);

	error_log(var_export($settings));

	return $settings[$notif] == true;
}

function eas_update_usermeta($id, $key, $value, $unique = true) {
	return eas_meta_helper('user', $id, $key, $value, $unique);
}

function eas_update_meta($id, $key, $value, $unique = true) {
	return eas_meta_helper('post', $id, $key, $value, $unique);
}

function eas_meta_helper($type, $id, $key, $value, $unique) {
	if ($type == 'post') return add_post_meta( $id, $key, $value, $unique) or ($unique && update_post_meta( $id, $key, $value ));
	else if ($type == 'user') return add_user_meta($id, $key, $value, $unique) or ($unique && update_user_meta($id, $key, $value));
}

function eas_figure_tag($id, $size = 'medium', $contest = false) {
	echo eas_get_figure_tag($id, $size, $contest);
}

function eas_get_figure_tag($id, $size = 'medium', $contest = false) {
	if ($size == 'thumb') {
		$size = 'thumbnail';
		$sizeclass = 'thumb';
	} else {
		$sizeclass = $size;
	}
	
	$img = eas_artwork_img($id, $size);
	$src = $img[0];
	$figure_post = get_post($id);
	$author = get_userdata($figure_post->post_author);
	$the_meta = get_post_meta($id);
	if ($size == 'large' || $size == 'thumbnail' || $size == 'slider-thumb') {
		$well = ' ';
	} else {
		$well = ' well ';
	}

	if ($contest !== false) {
		$slideno = get_post_meta($id, 'cborder', true) + 1;
		$url = trailingslashit(get_bloginfo('siteurl')).'crossingborders#&panel=1-'.$slideno;
	} else {
		$url = eas_artwork_url($id);
	}

	return '
		<figure class="artwork'.$well.$sizeclass.'">
			<a class="imglink" href="'.$url.'" title="'.$figure_post->post_title.'">
				<img data-width="'.$img[1].'" data-height="'.$img[2].'" class="art '.$sizeclass.'" src="'.$src.'" title="'.$figure_post->post_title.'">
			</a>
			<figcaption>
				<div class="captioninner">
					<div class="relative">
					<div class="artinfo">
						<p class="name">'.$author->nickname.'</p>
						<h3 class="title">'.$figure_post->post_title.'</h3>
						<p class="medium">'.eas_get_meta_display($the_meta, 'medium', false).'</p>
						<p class="year">'.eas_get_meta_display($the_meta, 'year', false).'</p>
					</div>
					<div class="hoverbuttons">
						<div class="relative">
							<div class="ccinfo">
								'.eas_get_cc($id, false).'
							</div>
							<div class="buttons">
								'.eas_get_favorite_button($id).'
							</div>
						</div>
					</div>
					</div>
				</div>
			</figcaption>
		</figure>
	';
}

function eas_forumextra_tag($id, $active = false) {
	$url = eas_artwork_url($id);
	$img = eas_artwork_img($id, 'medium');
	$src = $img[0];
	$figure_post = get_post($id);
	$activestr = '';
	if ($active) $activestr = 'active';
	?>
		<figure class="<?php echo $activestr; ?>forumextra" data-extra-id="<?php echo $id; ?>"><img src="<?php echo $src; ?>"></figure>
	<?php
}

function eas_feature_button() {
	global $post;

	if (eas_user_is_admin()) {
		if (eas_is_featured()) {
			$aclass = ' btn-success';
			$un = 'un';
		} else {
			$aclass = ' btn-inverse';
			$un = '';
		}
		$id = $post->ID;
		$url = eas_forum_url($id);

		?>
			<a class="btn btn-mini<?php echo $aclass; ?>" href="<?php echo $url; ?>/<?php echo $un; ?>feature"><i class="icon-ok icon-white"></i></a>
		<?php



	}
}

function eas_get_homebg() {
	/*MODIFIED FOR CB*/

	global $wpdb;
	$bgs = $wpdb->get_col("
		select p.ID from wp_postmeta m
		join wp_posts p
		on p.ID = m.post_id	

		where m.meta_key = 'homebg'
		and m.meta_value = 1
		/* and p.post_status = 'draft' */

    "
		);

	return $bgs;



}

function eas_homebg_button($id = 0) {
	global $post;
	if ($id == 0) $id = $post->ID;

	if (eas_user_is_admin()) {
		if (eas_is_homebg()) {
			$checked = ' checked ';
		} else {
			$checked = ' ';
		}

		?>
			<form class="form-inline">
				<label for="homebg" class="checkbox">
					<input type="checkbox" name="homebg"<?php echo $checked; ?>class="homebg" value="<?php echo $id; ?>">
					Use in homepage background
				</label>
			</form>
		<?php
	}
}

function eas_is_homebg($id = 0) {
	global $post;
	if ($id == 0) $id = $post->ID;
	$meta = get_post_meta($id, 'homebg', true);
	return (!empty($meta) && $meta == '1');
}

function eas_user_verified($id = 0) {
	global $user_ID;
	if ($id == 0) {
		get_currentuserinfo();
		$id = $user_ID;
	}

	$users = get_users(array(
		'include' => array($id),
		'meta_key' => 'verified',
		'meta_value' => '1'
	));


	return (bool) count($users);

}

function eas_follow($id, $user = 0) {
	global $user_ID;
	if ($user == 0) {
		get_currentuserinfo();
		$user = $user_ID;
	}

	eas_update_usermeta($user, 'follows', $id, false);

}

function eas_unfollow($id, $user = 0) {
	global $user_ID;
	if ($user == 0) {
		get_currentuserinfo();
		$user = $user_ID;
	}

	delete_user_meta($user, 'follows', $id);
}

function eas_follow_button($id = 0, $mini = false, $redirect = '') {
	
	echo eas_get_follow_button($id, $mini, $redirect);

}

function eas_get_follow_button($id = 0, $mini = false, $redirect = '') {
	// follow button coming soon
	/*global $author;
	if ($id == 0) $id = $author;
	if ($mini) $ministr = ' btn-mini';
	else $ministr = '';
	if (eas_is_following($id)) {
			return '<a class="followbtn btn btn-success'.$ministr.'" href="'.eas_artist_page_url($id).'/unfollow" title="Unfollow">Following</a>';
	} else {
			return '<a class="followbtn btn'.$ministr.'" href="'.eas_artist_page_url($id).'/follow" title="Follow">Follow Artist</a>';
	}*/
}

function eas_is_following($id, $user = 0) {
	global $user_ID;
	if ($user == 0) {
		get_currentuserinfo();
		$user = $user_ID;
	}
	$users = get_users(array(
		'include' => array($user),
		'meta_key' => 'follows',
		'meta_value' => $id
	));
	return (bool) count($users);
}

function eas_display_email_for_admins($uid) {
	echo eas_get_email_display_for_admins();

}

function eas_get_email_display_for_admins($uid) {
		
	   if (eas_user_is_admin())  {
	   	$the_user = get_user_by('id', $uid);
	   	return $the_user->user_email;
	   }
}

function eas_get_birthday_display_for_admins($uid) {
	if (eas_user_Is_admin()) {
		$birthday = get_user_meta($uid, 'birthday', true);
		return $birthday;
	}
}
function eas_approve_artist_button($id = 0) {

	echo eas_get_approve_artist_button($id);
}

function eas_get_approve_artist_button($id = 0) {
	global $post;
	$contest = get_post_meta($post->ID, 'contest', $true);
	if (empty($contest)) {
		if ($id == 0) {
		$href = 'approve';
	} else {
		$href = trailingslashit(eas_artist_page_url($id)).'approve';
	}
	return '<a class="btn" href="'.$href.'">Approve this Artist</a>';
	}

}

function eas_get_decline_artist_button($id = 0) {
	global $post;
	$href = trailingslashit(eas_artist_page_url($id)).'decline';
	return '<a class="btn" href="'.$href.'">Decline</a>';
}

function eas_get_favorites($the_user_id = 0, $type = 'artwork') {
	global $user_ID;
	if ($the_user_id == 0) {
		get_currentuserinfo();
		$the_user_id = $user_ID;
	}
	global $wpdb;
	$q = "
		select distinct p.id
		from (select * from wp_usermeta
where meta_key = 'fav'
and user_id = %d
order by umeta_id desc) as m
		join wp_posts p
			on m.meta_value= p.id
			where p.post_type = '%s'
			and p.post_author != m.user_id
	";
	$query = $wpdb->prepare($q, $the_user_id, $type);
	return $wpdb->get_results($query);
}

function eas_where_tagged($post_id = 0) {
	global $post;
	global $wpdb;
	if ($post_id == 0) $post_id = $post->ID;

	$query = $wpdb->prepare("
		select distinct p.*, u.display_name
		from (select * from wp_postmeta m2
			where m2.meta_key = 'tagspost'
			and m2.meta_value = %s) as m
		join wp_posts p
		on m.post_id = p.ID
		and p.post_status = 'publish'
		join wp_users u
		on u.ID = p.post_author
		order by p.post_date desc
	", $post_id);


	$posts = $wpdb->get_results($query);
	$p = count($posts);

	if ($p) {
	echo '<div class="wheretaggedwrapper">Featured by: ';
	echo '<ul class="wheretagged">';
	for ($i = 0; $i < count($posts); $i++) {

		$link_title = $posts[$i]->post_title;

		$user = get_userdata($posts[$i]->post_author);
		$name = $posts[$i]->display_name; //$user->data->display_name;

		echo '<li>';
		echo '<a href="'.eas_forum_url($posts[$i]->ID).'" title="'.$link_title.'">'.$name.', '.wp_days_ago($posts[$i]->post_date).'</a>';
		echo '</li>';
	} 
	echo '</ul></div>';
	}

}

function eas_who_favorited($post_id = 0) {
	global $post;
	global $wpdb;
	global $user_ID;
	get_currentuserinfo();
	if ($post_id == 0) $post_id = $post->ID;

	$query = $wpdb->prepare("
		select distinct *
		from (select * from wp_usermeta m2
				where m2.meta_key = 'fav'
				and m2.meta_value = %s) as m
			join wp_users u
					on m.user_id = u.id
			order by m.umeta_id asc
	", $post_id);

	$users = $wpdb->get_results($query);
	$u = count($users);
	$also = '';
	$newusers = array();
	$firstfan = false;
	$postfix = ' favorited this.';
	foreach ($users as $this_u) {
		if (intval($this_u->ID) != $user_ID) {
			array_push($newusers, $this_u);
		} else if ($u == 1) {
			$firstfan = true;
			$postfix = 'You are the first to favorite this.';
		} else {
			$postfix = ' also favorited this.';
		}

	}

	$users = $newusers;
	$nu = count($newusers);

echo '<ul class="whofavorited">';


	for ($i = 0; $i < $nu; $i++) {
		echo '<li>';
		if ($i && $nu > 2) {
			echo '<span class="grammar">,';
		} 
		if (($nu - 1) && $i == $nu - 1) {
			echo ' and </span>';
		} else {
			echo ' </span>';
		}

		$closing_tag = '';
		if (eas_is_artist($user[$i]->id)) {
			echo '<a href="'; 
				echo eas_artist_page_url($users[$i]->id);
			echo '" title="';
				echo $users[$i]->display_name;
			echo '">';
			$closing_tag = '</a>';
		}
		

		echo $users[$i]->display_name.$closing_tag;


		

		echo '</li>';

	}
	if ($i || $firstfan)
			echo '<li>'.$postfix.'</li>';

	echo '</ul>';


}

function eas_has_favorite($id = 0) {
	// Determines whether the current user has this
	// as a favorite
	global $user_ID;
	global $post;
	if ($id == 0) $id = $post->ID;
	get_currentuserinfo();
	$users = get_users(array(
		'include' => array($user_ID),
		'meta_key' => 'fav',
		'meta_value' => $id
	));

	return (boolean) count($users);
}


function eas_is_featured($id = 0) {
	global $post;
	if ($id == 0) $id = $post->ID;
	$meta = get_post_meta($id, 'feat', true);
	return (!empty($meta));

}

function eas_add_featured($post_id = 0) {
	global $post;
}

function eas_feature($id = 0) {
	global $post;
	if ($id == 0) $id = $post->ID;
	return eas_update_meta($id, 'feat', '1', true);
}

function eas_unfeature($id = 0) {
	global $post;
	if ($post_id == 0) $id = $post->ID;
	return delete_post_meta($id, 'feat');
}

function eas_add_favorite($post_id = 0, $the_user_id = 0) {
	global $post;
	global $user_ID;
	if ($the_user_id == 0) {
		get_currentuserinfo();
		$the_user_id = $user_ID;
	}
	if ($post_id == 0) {
		$post_id = $post->ID;
	}
	return eas_update_usermeta($the_user_id, 'fav', $post_id, false);
}

function eas_remove_favorite($post_id = 0, $the_user_id = 0) {
	global $post;
	global $user_ID;

	if (!$post_id) $post_id = $post->ID;
	if (!$the_user_id) {
		get_currentuserinfo();
		$the_user_id = $user_ID;
	}
	return delete_user_meta($the_user_id, 'fav', $post_id);
}

function eas_favorite_button($id = 0) {
	echo eas_get_favorite_button($id);
}

function eas_recently_viewed_works($id = 0, $all = false, $perpage = 10, $paged = 1) {
	global $user_ID;
	global $wpdb;

	if (!$all) {
		if ($id == 0) {
			get_currentuserinfo();
			$id = $user_ID;
		}
		$userstr = $wpdb->prepare(' and v.user_id = %d', $id);
	} else {
		$userstr = '';
	}

	$query = $wpdb->prepare("
		select distinct p.ID from (select * from eas_views order by time desc) as v
		join wp_posts p
		on v.post_id = p.ID
		join wp_usermeta m
		on m.user_id = p.post_author
		where p.post_type like 'artwork'
		and p.post_status like 'publish'
		and m.meta_key = 'wp_capabilities'
		and m.meta_value like 'a:1:{s:6:\"artist\";s:1:\"1\";}'"
		.$userstr."
		limit %d, %d
	", (($paged-1)*$perpage), $perpage);
	return $wpdb->get_col($query);
}

function eas_get_favorite_button($id = 0) {
	global $post;
	$prefix = '';
	$un = '';
	if ($id > 0) {
		$prefix = trailingslashit(eas_artwork_url($id));
	} else {
		$id = $post->ID;
	}
	$class = ' favbutton';
	if (eas_has_favorite($id)) {
		$has_favorite = true;
		$class .= ' btn-success';
		$title = 'Remove from Favorites';
		$un = 'un';
	} else {
		$has_favorite = false;
		$title = 'Add as Favorite';
	}
	if (!is_user_logged_in()) {
		$class.=' decoy';
	}

	
	return	'<a class="btn btn-mini'.$class.'" title="'.$title.'" href="'.$prefix.$un.'favorite">&#9733;</a>';
	
}


function eas_edit_button() {
	global $post;
	if (eas_user_is_owner()) {

		if ($post->post_type == 'forum') {
			$url_prefix = eas_forum_url($post->ID);
		} else if ($post->post_type == 'artwork') {
			$url_prefix = eas_artwork_url($post->ID);
		}

		?>
			<a class="btn btn-inverse btn-mini" title="Edit" href="<?php echo $url_prefix; ?>/edit">Edit</a>
		<?php
	}
}

function eas_delete_button($id = 0) {
	global $post;
	if ($id == 0) {
		$id = $post->ID;
	}
	if (eas_user_is_owner()) {

		if ($post->post_type == 'forum') {
			$url_prefix = eas_forum_url($post->ID);
		} else if ($post->post_type == 'artwork') {
			$url_prefix = eas_artwork_url($post->ID);
		}

		?>
			<a class="btn deletebutton btn-inverse btn-mini" title="Delete Post" href="<?php echo $url_prefix; ?>/delete"><i class="icon-trash icon-white"></i></a>
		<?php
	}


}


function eas_save_button() {
	?>
		<input type="submit" class="btn btn-primary" value="Save">
	<?php
}

function eas_user_is_owner() {
	global $post;
	global $user_ID;

	get_currentuserinfo();
	return eas_user_is_admin() || $user_ID == intval($post->post_author);
  // return true; doesn't fix author draft view problem - March'14 CG
}

function eas_cb_show_works() {
	global $wpdb;

	$posts = $wpdb->get_col("
		SELECT p.ID 
		FROM wp_posts p
		JOIN wp_postmeta m ON p.ID = m.post_id
		JOIN wp_postmeta m2 ON p.ID = m2.post_id
		LEFT JOIN wp_postmeta m3 ON p.ID = m3.post_id AND m3.meta_key = 'cborder'
		WHERE p.post_status =  'draft'
		AND m.meta_key =  'contest'
		AND m.meta_value =  'crossingborders'
		AND m2.meta_key = 'winner'
		AND m2.meta_value = 1
		ORDER BY m3.meta_value + 0 ASC
	");

	return $posts;

}

function eas_editor($content, $name, $t_name, $autofocus = false, $height = '200') {
	/*$tinymce = array(
		'height' => $height,
		'content_css' => '/css/editor-style.css',
		'theme_advanced_buttons1' => 'bold,italic,underline'
		);
	if ($autofocus) $tinymce['auto_focus'] = $name;

	wp_editor($content, $name, array(
		'media_buttons' => false,
		'textarea_name' => $t_name,
		'tinymce' => $tinymce
	));*/

	/*$imgext = array('jpg','png','gif','jpeg');

	foreach ($imgext as $ext) {
		$content = preg_replace('/<img src="[^>"].'.$ext.'[^"]"/', '.'.$ext, $content);
	}*/

	echo eas_get_editor($content, $name, $t_name, $autofocus, $height);
	/*?>
		<textarea class="easeditor" <?php echo $autofocus_str; ?> name="<?php echo $t_name; ?>" ><?php echo esc_textarea($content); ?></textarea>
	<?php*/
}

function eas_get_editor($content, $name, $t_name, $autofocus = false, $height = '200') {
		if ($autofocus) $autofocus_str = ' autofocus = "false" ';
	else $autofocus_str = '';
		return '
		<div class="editorwrap editorwrap-'.$name.'">
			<textarea class="easeditor easeditor-'.$name.'" name="'.$t_name.'">'.$content.'</textarea>
			<div contenteditable name="'.$name.'" class="inactive easeditor easeditor-'.$name.'">'.$content.'</div>
			<script type="text/javascript">
				$(\'textarea.easeditor-'.$name.'\').remove();
				$editor = $(\'.easeditor-'.$name.'\');
				$editor.removeClass(\'inactive\');
				$hiddenInput = $(\'<input type="hidden" name="'.$t_name.'" class="easeditorinput-'.$name.'">\');
				$(\'.editorwrap-'.$name.'\').append($hiddenInput);
				
				$editor.bind(\'change\', function() {
					updateHiddenInput($editor, $hiddenInput);
				});
				$editor.parents(\'form\').bind(\'submit\',function() {
					updateHiddenInput($editor, $hiddenInput);
				});

		</script>
		</div>';
}


class EasSearchResults extends SplPriorityQueue 
{
	public function __construct($array) {
		foreach ($array as $id => $weight) {
		$this->insert($id, $weight);
	}
	}
    /**
     * We modify the abstract method compare so we can sort our
     * rankings using the values of a given array
     */
    public function compare($array1, $array2)
    {
        $values1 = array_values($array1);
        $values2 = array_values($array2);
        if ($values1[0] === $values2[0]) return 0;
        return $values1[0] < $values2[0] ? -1 : 1;
    }
}

function eas_first_tagged_artwork($id) {
	$the_meta = get_post_meta($id, 'tagspost', true);
	return $the_meta;
}

function eas_tagged_artworks($id) {
	$the_meta = get_post_meta($id, 'tagspost');
	return $the_meta;
}


function eas_escape_url($str) {
	$str = str_replace(':','\:', $str);
	$str = str_replace('/','\/', $str);
	$str = str_replace('.','\.', $str);

	return $str;

}

function eas_add_url_tags($matches) {
	return eas_process_urls($matches);
}

function eas_add_url_dressing($matches) {
	return eas_process_urls($matches, true);
}

function eas_process_urls($matches, $dress = false) {
	global $wp_query;
	global $post;
	
	if ($matches[2] == 'a') {
		$the_post = get_post($matches[3]);
		if ($the_post->post_type == 'artwork') {

			if ($dress) {
				if ($wp_query->query_vars['galleryview'] == '1') {
					$size = 'large';
				} else {
					$size = 'medium';
				}
				return $matches[1].eas_get_figure_tag($matches[3], $size).$matches[4];
			} else {
				$meta = eas_update_meta($post->ID, 'tagspost', $matches[3], false);
				return $matches[0];
			}
			
		} else {
			if ($dress) return '<figure class="well">Invalid URL</figure>';
		}
	} else {
		$the_post = get_post($matches[1]);
		if ($the_post->post_type == 'artwork') {
			if ($dress) {
				if ($wp_query->query_vars['galleryview'] == '1') {
					$size = 'large';
				} else {
					$size = 'medium';
				}
				return eas_get_figure_tag($matches[1], $size);
			} else {
				eas_update_meta($post->ID, 'tagspost', $matches[1], false);
			}
		}
	}



	return $matches[0];
}

remove_filter('the_content','wptexturize');

add_filter('the_content', 'eas_smart_urls');
add_action('save_post', 'eas_tag_urls');


function eas_tag_urls($post_id) {
	global $post;
	$post->ID = $post_id;
	$the_post = get_post($post->ID);
	eas_process_content($the_post->post_content, false);
}


function eas_smart_urls($content) {
	return eas_process_content($content, true);

}

function eas_process_content($content, $dress = false) {
	global $post;
	if ($dress) {
		$fn = 'eas_add_url_dressing';
	} 
	else {
		$fn = 'eas_add_url_tags';
		delete_post_meta($post->ID, 'tagspost');
		delete_post_meta($post->ID, 'tagsuser');
	} 
	$matches = array();
	$url = eas_escape_url(get_bloginfo('siteurl'));

	$pattern = '/([^"]?)'.$url.'\/(art|a|artists|forum)\/([0-9]+)\/?([^"]?)/i';
	$content = preg_replace_callback($pattern, $fn, $content);

	$pattern = '/<figure class="activeforumextra" data-extra-id="([0-9]+?)"><img[^>]+><\/figure>/i';
	$content = preg_replace_callback($pattern, $fn, $content);

	if ($dress) return $content;
}

function eas_page_links($pagename = "0") {	
	$paged = get_query_var('paged');
	$pagename = $pagename == "0" ? get_query_var('pagename') : $pagename;
	if(!$pagename)
		$pagename = get_query_var('post_type'); 
	$perpage = 10;
	global $epl_total, $epl_calc;
	if(!$epl_calc)
	{
		switch($pagename)
		{
			case 'submissions':
				global $wpdb;
				$sql = "select count(distinct u.ID) from wp_users u join wp_usermeta um ON um.user_id = u.ID join ( select p.* from wp_posts p join wp_postmeta pm on p.ID = pm.post_id and pm.meta_key = 'contest' and pm.meta_value = 'crossingborders' where p.post_status = 'draft' and p.post_type = 'artwork' order by p.post_date desc ) as a on u.ID = a.post_author where um.meta_key = 'nickname'";
				$epl_total = ceil($wpdb->get_var($sql)/$perpage);
			break;

      case 'opencall/submissions':
        global $wpdb;
        $sql = "select count(distinct u.ID) from wp_users u join wp_usermeta um ON um.user_id = u.ID join ( select p.* from wp_posts p join wp_postmeta pm on p.ID = pm.post_id and pm.meta_key = 'contest' and pm.meta_value = 'opencall' where p.post_status = 'draft' and p.post_type = 'artwork' order by p.post_date desc ) as a on u.ID = a.post_author where um.meta_key = 'nickname'";
        $epl_total = ceil($wpdb->get_var($sql)/$perpage);
      break;

			case 'approve':
				global $wpdb;
			  $query = $wpdb->prepare("
			    select count(distinct u.ID) as c
		      from wp_users u
		      join wp_posts p
		      on p.post_author = u.ID
		      join wp_usermeta m
		      on m.user_id = u.ID
		      left join wp_usermeta m2
		      on u.id = m2.user_id
		      and m2.meta_key = 'declined'
		      where p.post_type = 'artwork'
		      and m2.meta_value is null
		      and m.meta_key = 'wp_capabilities'
		      and m.meta_value != 'a:1:{s:6:\"artist\";s:1:\"1\";}'
		      order by p.post_date desc
			  ");
		
			  $epl_total = ceil($wpdb->get_var($query) / $perpage);		
			break;

			case 'forum':
				$category = get_query_var('forum_category');
				if($category)
				{
					global $wp_query;			
				}
				else
				{
					$wp_query = new WP_Query(
			    	array(
					  	'post_type' => 'forum',
					    'meta_key' => 'feat',
					    'meta_value' => '1',
					    'paged' => $paged,
				      'tax_query' => array(
				      	array(
				        	'taxonomy' => 'forum_category',
				          'field' => 'slug',
				          'terms' => 'gallery',
				          'operator' => 'NOT IN'
					      )
							)
				    )
		  		);
				}
				$epl_total = $wp_query->max_num_pages;
			break;

			case 'art':
				$wp_query = new WP_Query(
			    array(
			      'post_parent' => 0,
			      'post_type' => 'forum',
			      'posts_per_page' => 10,
			      'paged' => $paged,
			      'meta_key' => 'feat',
			      'meta_value' => 1,
			      'galleryview' => 1,
			      'tax_query' => array(
			        array(
		  	        'taxonomy' => 'forum_category',
			          'field' => 'slug',
			          'terms' => 'gallery'
			        )
			      )
			    )
			  );			
				$epl_total = $wp_query->max_num_pages;
			break;

			case 'artists':
				$perpage = 20;
				global $wpdb;
				$sql = "select count(distinct u.ID) as c "
						."FROM wp_users u "
						."	JOIN wp_usermeta um" 
						."	ON um.user_id = u.ID "
						."	JOIN ( 	select p.* "
						."			from wp_posts p "
						."			where p.post_status = 'publish' and p.post_type = 'artwork' "
						."			order by p.post_date desc ) as a "
						."	ON u.ID = a.post_author "
						."	JOIN ( 	select distinct m2.* "
						."			from wp_usermeta m2 "
						."				join wp_usermeta m3 "
						."				on m2.user_id = m3.user_id "
						."			where m2.meta_key = 'wp_capabilities' "
						."			  and m2.meta_value like 'a:1:{s:6:\"artist\";s:1:\"1\";}' "
						."			  and m3.meta_key = 'verified' and m3.meta_value = 1 ) as m "
						."	ON u.ID = m.user_id "
						."WHERE um.meta_key = 'nickname' "
						."ORDER BY um.meta_value";
				$epl_total = ceil($wpdb->get_var($sql)/$perpage);
			break;
		}
	}
	$epl_calc = true;
	$big = 999999999; // need an unlikely integer

  echo '<div id="eas_forum_page_nums" class="clear">';
  echo paginate_links( array(
    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
    'format' => '?paged=%#%',
    'current' => max( 1, get_query_var('paged') ),
    'total' => $epl_total,
  ) );
  echo '</div>';
	return;


	$prev = $next = true;
	if ($paged == 0) {
		$prev = false;
		$next_url = 'page/2';
	} else {
		$next_url = '../'.($paged+1);
	}

	if ($paged == 2) {
		$prev_url = '../../';
	} else {
		$prev_url = '../'.($paged-1);
	}

	echo '<div class="row">
			<div class="span6 prevpage">';
	if ($prev) echo '<a href="'.$prev_url.'" title="Previous Page">Previous Page</a>';
		echo '&nbsp;</div>
			<div class="span6 nextpage">
				<a href="'.$next_url.'" title="Next Page">Next Page</a>	
			</div>
		</div>';

}

function eas_do_pass_reset_email($user_id = 0, $user_email = '', $forgot = true) {
		$reset_user = new WP_User($user_id);
	  $verification = create_random_string(32);
      delete_user_meta($reset_user->ID, 'pwreset');
      eas_update_usermeta($reset_user->ID, 'pwreset', $verification, true);
      
      
      if ($forgot) {
      	$subject = 'Password Reset';
      	$message = "Hi! Click this link and we\'ll have you back on the site in no time:

	      ".trailingslashit(get_bloginfo('siteurl'))."forgotpassword/?v=".$verification."

	      Thanks!
	      -Emergent Art Space";
      } else {
      	$subject = 'Activate your Emergent Art Space Account!';
      	$message = "Hi! An account has been created for you at Emergent Art Space.

You can log in using this email address. Click this link to set your password.

	      ".trailingslashit(get_bloginfo('siteurl'))."setpassword/?v=".$verification."&new=1

	      Thanks!
	      -Emergent Art Space";
      }
      
      $result = eas_mail($user_email, $subject, stripslashes($message));
      return $result;

}




function eas_send_verification_email($id = 0) {
	global $user_ID;
	if ($id == 0) {
		get_currentuserinfo();
		$id = $user_ID;
	}

	if (eas_user_verified($id)) {
		eas_update_usermeta($id, 'verified', '');
	}

	$verification = create_random_string(32);
	eas_update_usermeta($id, 'verify_str', $verification);
	if (!eas_is_artist($id)) {
		$subject = 'Email Verification';
		$userdata = get_userdata($id);
		$space = '';
		if (!empty($userdata->display_name)) $space = ' ';
		$message = 
			'Hi'.$space.$userdata->display_name.'!
'.			'Thanks for verifying your email!
'.			'You can complete the process by clicking here: '.trailingslashit(get_bloginfo('siteurl')).'settings/email/?id='.$id.'&v='.$verification.'

'.			'-Emergent Art Space
		';
	} else {
		$subject = 'Your art is almost ready!';
		$userdata = get_userdata($id);
		$space = '';
		if (!empty($userdata->display_name)) $space = ' ';
		$message = 'Hi'.$space.$userdata->display_name.'!
'.			'Thanks so much for submitting your art! It\'s almost ready to be displayed.
'.			'You can complete the process by clicking here: '.trailingslashit(get_bloginfo('siteurl')).'settings/email/?id='.$id.'&v='.$verification.'

'.			'-Emergent Art Space
		';
	}


	eas_mail($userdata->user_email, $subject, stripslashes($message));

}

function verify_user($id, $v) {
	$users = get_users(array(
		'include' => array($id),
		'meta_key' => 'verify_str',
		'meta_value' => $v
	));

	if (count($users)) {
		if(!get_user_meta($id, 'emailsettings', true))
		{
			$email_settings = array(
				'forumemail' => 1,
				'artemail' => 1,
			);
			eas_update_usermeta($id, 'emailsettings', $email_settings);
		}
		eas_update_usermeta($id, 'unverified', 0);		
		return eas_update_usermeta($id, 'verified', '1');
	} else {
		return false;
	}
}



function eas_artist_approval_email($id) {
	$userdata = get_userdata($id);
	$subject = 'You have been approved!';
	$space = '';
	if (!empty($userdata->display_name)) $space = ' ';
	if (eas_user_verified($id)) {
		$message = 'Hi'.$space.$userdata->display_name.'!
'.			'Thank you for submitting your art!
'.			'It is now available for viewing at emergentartspace.org.
'.			'You can check it out here: '.eas_artist_page_url($id).'?action=newartist
		
'.			'Thanks again!
'.			'-Emergent Art Space
		';
		eas_mail($userdata->user_email, $subject, $message);
	} else {
		eas_send_verification_email($id);
	}
		

	
}

function eas_notify($uid, $id, $orig_id, $type='forumemail') {

	if (eas_user_gets_notified($type, $uid)) {
		error_log('gets notified');

		$new_post = get_post($id);
		$old_post = get_post($orig_id);

		if ($uid != $new_post->post_author) {
			$responder = get_userdata($new_post->post_author);
			$originator = get_userdata($uid);



			if ($type == 'forumemail') {
							if (!empty($old_post->post_title)) {
				$title_disp = '"'.$old_post->post_title.'"';
			} else {
				$title_disp = 'your post';
			}
				$msg = 'Hi!

'.			'There is a response to your post on Emergent Art Space

'.			'You can view your post and its replies at '.eas_forum_url($orig_id).'

'.			'Thanks!
'.			'-Emergent Art Space
				';
			} else if ($type == 'artemail') {
							if (!empty($old_post->post_title)) {
				$title_disp = '"'.$old_post->post_title.'"';
			} else {
				$title_disp = 'your work';
			}
				$msg = 'Hi!
'.			'There is a new comment on your artwork on Emergent Art Space

'.			'You can view your artwork and its replies at '.eas_artwork_url($orig_id).'

'.			'Thanks!
'.			'-Emergent Art Space
				';
			}

			if (!empty($msg)) {
				eas_mail(
					$originator->user_email,
					'You have a new response on EAS',
					$msg
				);
			}

		}





	}


}



function eas_mail($addr, $subject, $content, $from='no-reply@emergentartspace.org') {
	$from = 'From: Emergent Art Space <'.$from.'>

';
	return wp_mail(
		$addr, 
		$subject, 
		$content, 
		$from
	);
}

function create_random_string($len) { 

    $chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
    srand((double)microtime()*1000000); 
    $i = 0; 
    $pass = '' ; 

    while ($i <= $len) { 
        $num = rand() % 33; 
        $tmp = substr($chars, $num, 1); 
        $pass = $pass . $tmp; 
        $i++; 
    } 

    return $pass; 

}

class eas_forum_walker extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth, $args) {
   global $wp_query;

   $forum_cat_button = true;
   if ($item->object != 'forum_category') {
   	$forum_cat_button = false;
   }

   $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
   $class_names = $value = '';

   $classes = empty( $item->classes ) ? array() : (array) $item->classes;
   if (isset($wp_query->query_vars['forum_category'])) {
   	$term = get_term_by('slug',$wp_query->query_vars['forum_category'], 'forum_category');
   }


   $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
   $class_names = ' class="'. esc_attr( $class_names ) . '"';

   $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
   if ($forum_cat_button) {
   	$url = eas_forum_cat_url($item->object_id);
   } else {
   	$url = $item->url;
   }

   $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
   $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
   $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
   $attributes .= ' href="'.esc_attr($url).'"';

   $prepend = '<strong>';
   $append = '</strong>';
   $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

   if($depth != 0)
   {
             $description = $append = $prepend = "";
   }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }		
}

class eas_art_walker extends Walker_Nav_Menu {
	function start_el(&$output, $item, $depth, $args) {
   global $wp_query;

   $forum_cat_button = true;
   if ($item->object != 'forum_category') {
   	$forum_cat_button = false;
   }

   $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
   $class_names = $value = '';

   $classes = empty( $item->classes ) ? array() : (array) $item->classes;
   if (isset($wp_query->query_vars['forum_category'])) {
   	$term = get_term_by('slug',$wp_query->query_vars['forum_category'], 'forum_category');
   }


   $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
   $class_names = ' class="'. esc_attr( $class_names ) . '"';

   $output .= $indent . '<li id="menu-item-'. $item->ID . '"' . $value . $class_names .'>';
   $url = $item->url;

   $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
   $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
   $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
   $attributes .= ' href="'.esc_attr($url).'"';

   $prepend = '<strong>';
   $append = '</strong>';
   $description  = ! empty( $item->description ) ? '<span>'.esc_attr( $item->description ).'</span>' : '';

   if($depth != 0)
   {
             $description = $append = $prepend = "";
   }

    $item_output = $args->before;
    $item_output .= '<a'. $attributes .'>';
    $item_output .= $args->link_before .$prepend.apply_filters( 'the_title', $item->title, $item->ID ).$append;
    $item_output .= '</a>';
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }		
}

if (function_exists('add_theme_support')) {
	add_theme_support('post-thumbnails');
}

if (function_exists( 'add_image_size' ) ) { 
	add_image_size( 'span2-crop', '170', '170', true );
}

function eas_siteurl_shortcode($atts){
 return trailingslashit(get_bloginfo('siteurl'));
}
add_shortcode( 'siteurl', 'eas_siteurl_shortcode' );

add_action('init','eas_register_menus');

function eas_register_menus() {
	register_nav_menus(array(
		'forum_categories' => 'Forum Categories',
		'art_menu' => 'Art Menu',
		'settings_menu' => 'Settings Menu',
		'footer_menu' => 'Footer Menu'
	));
}


add_filter('query_vars', 'eas_queryvars' );

function eas_queryvars($qvars)
{
  array_push($qvars, 
  	'searchtype', 
  	'edit',
  	'delete',
  	'addfav', 
  	'removefav', 
  	'approve', 
  	'feature', 
  	'follow', 
  	'reply', 
  	'galleryview', 
  	'homebg',
  	'old_title',
  	'contestname'
  );

  return $qvars;
}


add_filter('rewrite_rules_array', 'eas_rewrite_rules');
function eas_rewrite_rules($rules) {
	$newrules["forum/([0-9]+)?/?$"] = 'index.php?p=$matches[1]&paged=$matches[2]&post_type=forum';
	$newrules["forum/([0-9]+)?/edit/?$"] = 'index.php?p=$matches[1]&paged=$matches[2]&post_type=forum&edit=1';
	$newrules["forum/([0-9]+)?/feature/?$"] = 'index.php?p=$matches[1]&paged=$matches[2]&post_type=forum&feature=1';
	$newrules["forum/([0-9]+)?/unfeature/?$"] = 'index.php?p=$matches[1]&paged=$matches[2]&post_type=forum&feature=-1';
	$newrules["forum/([0-9]+)?/reply/?$"] = 'index.php?p=$matches[1]&reply=1&post_type=forum';
	$newrules["forum/([0-9]+)?/delete/?$"] = 'index.php?p=$matches[1]&post_type=forum&delete=1';	
	$newrules["forum/([^/]+)?(/page/?([0-9]{1,})?){0,}/?$"] = 'index.php?forum_category=$matches[1]&post_type=forum&paged=$matches[3]';


	$newrules["user/([0-9]+)?/?"] = 'index.php?author=$matches[1]&forum_author=true';

	$newrules["art/([0-9]+)?/?$"] = 'index.php?p=$matches[1]&post_type=forum&forum_category=gallery&galleryview=1';
	$newrules["art/([0-9]+)?/edit/?$"] = 'index.php?p=$matches[1]&post_type=forum&forum_category=gallery&galleryview=1&edit=1';
	$newrules["art/([0-9]+)?/feature/?$"] = 'index.php?p=$matches[1]&post_type=forum&forum_category=gallery&galleryview=1&feature=1';
	$newrules["art/([0-9]+)?/unfeature/?$"] = 'index.php?p=$matches[1]&post_type=forum&forum_category=gallery&galleryview=1&feature=-1';
	$newrules["art/([0-9]+)?/reply/?$"] = 'index.php?p=$matches[1]&reply=1&post_type=forum&forum_category=gallery&galleryview=1';
	$newrules["art/([0-9]+)?/delete/?$"] = 'index.php?p=$matches[1]&post_type=forum&delete=1';	
	$newrules["art/page/([0-9]{1,}?)/?$"] = 'index.php?pagename=art&paged=$matches[1]&galleryview=1';
	$newrules["art(/page)?/?$"] = 'index.php?pagename=art&galleryview=1';
	$newrules["(art/favorites)?/?$"] = 'index.php?pagename=$matches[1]';
	$newrules["art/(.+)?/?$"] = 'index.php?old_title=$matches[1]';

	$newrules["artists/([0-9]+)?/page/?([0-9]{1,})/?$"] = 'index.php?author=$matches[1]&paged=$matches[2]';
	$newrules["artists/([0-9]+)?(/[0-9]+)?/?$"] = 'index.php?author=$matches[1]&paged=$matches[2]';
	$newrules["artists/([0-9]+)?/approve/?$"] = 'index.php?author=$matches[1]&approve=1';
	$newrules["artists/([0-9]+)?/decline/?$"] = 'index.php?author=$matches[1]&approve=-1';	
	$newrules["artists/([0-9]+)?/follow/?$"] = 'index.php?author=$matches[1]&follow=1';
	$newrules["artists/([0-9]+)?/unfollow/?$"] = 'index.php?author=$matches[1]&follow=-1';
	$newrules["artists(/page)?/?$"] = 'index.php?pagename=artists';

	$newrules["a/([0-9]+)?$"] = 'index.php?p=$matches[1]&post_type=artwork';
	$newrules["a/([0-9]+)?/edit/?$"] = 'index.php?p=$matches[1]&post_type=artwork&edit=1';
	$newrules["a/([0-9]+)?/favorite/?$"] = 'index.php?p=$matches[1]&post_type=artwork&addfav=1';
	$newrules["a/([0-9]+)?/unfavorite/?$"] = 'index.php?p=$matches[1]&post_type=artwork&removefav=1';
	$newrules["a/([0-9]+)?/homebg"] = 'index.php?p=$matches[1]&post_type=artwork&homebg=1';
	$newrules["a/([0-9]+)?/delete/?$"] = 'index.php?p=$matches[1]&post_type=artwork&delete=1';	

	$newrules["(.+)/submissions/([0-9]+)/?$"] = 'index.php?author=$matches[2]&contestname=$matches[1]';

	$newrules["setpassword$"] = 'index.php?pagename=forgotpassword';

	$newrules["search/(.+)/(.+)/?$"] = 'index.php?s=$matches[2]&searchtype=$matches[1]';

	$rules = $newrules + $rules;

	unset($rules["forum/(.+?)(/[0-9]+)?/?$"]);


 	

	_d($rules);
	return $rules;
}

function eas_mcsubscribe_shortcode( $atts ){
 	return file_get_contents(locate_template("form-mc.php"));
}
add_shortcode( 'mailinglist', 'eas_mcsubscribe_shortcode' );


if (run_once('add_roles_3')) {
			$result = add_role('artist', 'Artist', array(
	    'read' => true, // True allows that capability
	));
	remove_role('juror');
	$result = add_role('juror', 'Juror', array(
	    'read' => true, 
	    'view_subs' => true,
	    'read_private_posts' => true,
	    'edit_others_posts' => true
	));
}

function eas_get_image_sizes() {

	global $_wp_additional_image_sizes;


	$image_sizes = array();

	foreach (get_intermediate_image_sizes() as $s) {
		if (isset($_wp_additional_image_sizes[$s])) {
			$width = intval($_wp_additional_image_sizes[$s]['width']);
			$height = intval($_wp_additional_image_sizes[$s]['height']);
			$crop = $_wp_additional_image_sizes[$s]['crop'];
		} else {
			$width = get_option($s.'_size_w');
			$height = get_option($s.'_size_h');
			$crop = false;
		}

		$image_sizes[$s] = array('width' => $width, 'height' => $height, 'crop' => $crop);
	}

	return $image_sizes;

}




function run_once($key){
    $test_case = get_option('run_once');
    if (isset($test_case[$key]) && $test_case[$key]){
        return false;
    }else{
        $test_case[$key] = true;
        update_option('run_once',$test_case);
        return true;
    }
}


if (!current_user_can('manage_options')) {
	add_filter('show_admin_bar', '__return_false');
}

if (isset($_GET['rebuildimgmeta'])) {
	if (isset($_GET['paged'])) $paged = $_GET['paged'];
	else $paged = 1;
	error_log('chuggin, page '.$paged);
	if (eas_user_is_admin()) {
			include( ABSPATH . 'wp-admin/includes/image.php' );
			$posts = get_posts(array('post_type' => 'attachment', 'posts_per_page' => 100, 'paged' => $paged));
			foreach ($posts as $p) {
				$path = get_attached_file($p->ID);
				$md = wp_generate_attachment_metadata($p->ID, $path);
				_d($md);
				eas_update_meta($p->ID, '_wp_attachment_metadata', $md);
				_d(get_post_meta($p->ID), '_wp_attachment_metadata', $md);
			}

	}

}

if (!function_exists('upload_file_from_url')) {

function upload_file_from_url($url, $parent = 0) {
	require_once(ABSPATH . 'wp-admin/includes/image.php');
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$data = curl_exec($ch);
	curl_close($ch);

	$imgname = basename($url);

	$filetype = wp_check_filetype($imgname, null);

	$the_file = wp_upload_bits($imgname, null, $data);

	$file_id = wp_insert_attachment(
		array(
			'post_title' => $imgname,
			'post_content' => '',
			'post_status' => 'inherit',
			'post_mime_type' => $filetype['type'],
			'post_parent' => $parent
		),
			$the_file['file']
	);

	$attach_data = wp_generate_attachment_metadata($file_id, $the_file['file']);
  	wp_update_attachment_metadata($file_id, $attach_data);

	return $file_id;

}

}

function copy_file_from_url($url, $newpath, $delete = false) {

	error_log('delete is set to: ' . $delete);
	error_log('curl init');
	$ch = curl_init ($url);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
  $raw=curl_exec($ch);
  curl_close ($ch);
  
  	$fp = fopen($newpath,'x');
	fwrite($fp, $raw);
	error_log('curl close');
	fclose($fp);

	if ($delete) {
		$ch = curl_init($url .'?key='.eas_fp_api_key());
    	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
    	$data = curl_exec($ch);
    	curl_close($ch);
    	error_log('deleted: ' . $url);
	}
}

function eas_fp_api_key() {
	return 'AJPzx3eR3O3aSJc3EYCJwz';
}

function is_ajax_req() {
	return (isset($_GET['ajax'])) || (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}

function eas_record_view() {
	global $post;
	global $user_ID;
	get_currentuserinfo();
	if (!isset($user_ID)) $user_ID = 0;
    ?>
    	<script language="javascript">
    		$.post('<?php echo trailingslashit(get_bloginfo('siteurl')); ?>view.php', { pid: <?php echo $post->ID; ?>, uid: <?php echo $user_ID; ?>});
    	</script>
    <?php
}
add_action('wp_head', 'eas_record_view');
function eas_404() {
	global $wp_query;
	  $wp_query->is_404 = true;
	  $wp_query->is_single = false;
	  $wp_query->is_page = false;

	  include( get_query_template( '404' ) );
	  exit();
}

function eas_redirect_old_page() {
	global $wp_query;
	if (isset($wp_query->query_vars['old_title'])) {
		$old_title = $wp_query->query_vars['old_title'];
		$title_ids = array(
			'wiebe' => 974,
			'allison-brooke' => 975,
			'madeleine-blake' => 976,
			'emma-gavin' => 977,
			'jason-katzenstein' => 978,
			'jonathan-meade' => 979,
			'maria-ashkin' => 980,
			'kevin-rouff' => 982,
			'rajah-maximilien-bose' => 983,
			'austen-weymueller' => 984,
			'molly-gavin' => 985,
			'zihan-karim' => 991,
			'gladys-kalichini' => 987,
			'isabel-gibson' => 986,
			'malcom-hecht' => 990,
			'pearl-hesselden' => 988,
			'asma-ghanem' => 992,
			'asem-naser' => 981,
			'olivia-tierk' => 989,
			'megan-finley' => 993,
			'nelmarie-de-preez' => 994,
			'uji-venkat' => 995,
			'ruben-oliva-ros' => 996,
			'franco-carcamo' => 997,
			'einat-moglad' => 999,
			'daniel-sonnino-2' => 1000,
			'eleanor-leonne-bennett' => 1569,
			'mihaela-kamenova' => 1001,
			'masha-yakov' => 1003,
			'kristina-kucerov' => 1002,
			'katarzyna-pieczynska' => 1570,
			'isha-bawiskar' => 1004,
			'maria-crean' => 1005,
			'chaitanya-ingle' => 1006,
			'ali-asgar' => 1007,
			'filip-kos' => 1008,
			'stefano-adamo-mayakasa' => 1587,
			'retina-zygi' => 1010,
			'paul-anmy' => 1011,
			'rachel-peterson-schmerge' => 1012,
			'karen-argus' => 1013,
			'abdullrahman-r-aman' => 1604
		);

		if (isset($title_ids[$old_title])) {
			wp_redirect(eas_forum_url($title_ids[$old_title]));
		}
	}
}
add_action('wp_head', 'eas_redirect_old_page');

add_action('edit_user_profile', 'add_extra_profile_fields');

function add_extra_profile_fields() {
    ?>
    <?php $uid = $_GET['user_id'] ?>
    <h3>Additional Info</h3>

	<table class="form-table">

		<tr>
			<th><label for="school">Location</label></th>

			<td>
				<input type="text" name="location" id="location" value="<?php echo esc_attr( get_user_meta( $uid, 'location', true ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>

		<tr>
			<th><label for="school">School</label></th>

			<td>
				<input type="text" name="school" id="school" value="<?php echo esc_attr( get_user_meta( $uid, 'school', true  ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>

		<tr>
			<th><label for="birthday">Birthday</label></th>

			<td>
				<input type="text" name="birthday" id="birthday" value="<?php echo esc_attr( get_user_meta( $uid, 'birthday', true  ) ); ?>" class="regular-text" /><br />
			</td>
		</tr>

	</table>

	<?php $unverified = get_user_meta($uid,  'unverified' ,true ); ?>
	<input name="verified" value="1" type="checkbox" <?php if (!$unverified) echo ' checked'; ?>> User verified?


  	<?php
}

add_action( 'personal_options_update', 'my_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'my_save_extra_profile_fields' );

function my_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_user_meta( $user_id, 'location', $_POST['location'] );
	update_user_meta( $user_id, 'school', $_POST['school'] );
	update_user_meta( $user_id, 'birthday', $_POST['birthday'] );

	if (isset($_POST['verified']) && $_POST['verified'] == 1) {
		delete_user_meta($user_id, 'unverified');
		add_user_meta($user_id, 'verified', '1');
	}

}

function oc_display_user($id, $li, $contest = 'opencall', $redir = 'opencall') {
  $this_author = get_userdata($id);
  $artworks = eas_artworks_by_user($id, 6, $contest);
  $meta = get_user_meta($id);

  get_currentuserinfo();
  if(!is_user_logged_in()) {
    wp_redirect('login?redirect=/'+$redir);
  }

  if ($contest !== false) {
    $linkurl = trailingslashit(get_bloginfo('siteurl')).$contest.'/submissions/'.$id;
  } else {
    $linkurl = eas_artist_page_url($id);
  }

  $the_return = '';
  
  $the_return.='
    <div class="row">
      <a class="oc_userblock" href="'.$linkurl.'">
        <h3>
        <span class="nickname">'.$this_author->nickname.'</span>
          '.eas_get_follow_button($id, true);
        if (!eas_is_artist($id) and eas_user_is_admin() and $contest === false) {
          $the_return.= eas_get_approve_artist_button($id);
          $the_return.= eas_get_decline_artist_button($id);
        }
        $the_return.='</h3>
        <span class="usermeta">
          '.eas_get_meta_display($meta, 'location', false).'
        </span>
        <span class="usermeta">'.eas_get_birthday_display_for_admins($id).'</span>
        <span class="usermeta">
          '.eas_get_email_display_for_admins($id).'
        </span>
      </a>
      <div class="imagelist">
  ';

    foreach ($artworks as $a) {

      $img = eas_artwork_img($a->ID, 'span2-crop');
      $src = $img[0];

      $the_return.='
        <a href="'.eas_artwork_url($a->ID).'">
          <figure class="span2"><img src="'.$src.'">
          </figure>
        </a>
      ';
    }

  $the_return.='</div></div>' /* .row and .imagelist */;

  return $the_return;
}
