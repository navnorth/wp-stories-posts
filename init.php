<?php
// hook into the init action and call create_managment_taxonomies when it fires
add_action( 'init', 'create_managment_taxonomies');

// create taxonomies, for the post type "ask_question"
function create_managment_taxonomies()
{
	$args = array(
		'labels'             => array('name' =>  _x( 'Stories', 'post type general name', SCP_SLUG )),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'stories' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail' ),
		'register_meta_box_cb' => 'story_custom_metaboxes'
	);
	register_post_type( 'stories', $args );

	$texonomy_array = array(
						'program' => 'Program',
						'state' => 'State',
						'grade_level' => 'Grade Level',
						'characteristics' => 'Characteristics',
						'districtsize' => 'District Size');

	foreach($texonomy_array as $texonomy_key => $texonomy_value)
	{
			// Add new taxonomy, hierarchical (like Category)
			$labels = array(
				'name'                       => _x( $texonomy_value, 'taxonomy general name' ),
				'singular_name'              => _x( $texonomy_value, 'taxonomy singular name' ),
				'search_items'               => __( 'Search '.$texonomy_value ),
				'all_items'                  => __( 'All '.$texonomy_value ),
				'parent_item'                => __( 'Parent '.$texonomy_value ),
				'parent_item_colon'          => __( 'Parent '.$texonomy_value ),
				'edit_item'                  => __( 'Edit '.$texonomy_value ),
				'update_item'                => __( 'Update '.$texonomy_value ),
				'add_new_item'               => __( 'Add New '.$texonomy_value ),
				'new_item_name'              => __( 'New '.$texonomy_value.' Name' ),
				'menu_name'                  => __( $texonomy_value ),
			);
			$args = array(
				'hierarchical'          => true,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'query_var'             => true,
				'rewrite'               => array( 'slug' => $texonomy_key ),
			);
			register_taxonomy( $texonomy_key, array('stories'), $args );
	}

	$labels = array(
				'name'                       => _x( 'Tags', 'taxonomy general name' ),
				'singular_name'              => _x( 'Tag', 'taxonomy singular name' ),
				'search_items'               => __( 'Search Tags' ),
				'popular_items'              => __( 'Popular Tags' ),
				'all_items'                  => __( 'All Tags' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => __( 'Edit Tag' ),
				'update_item'                => __( 'Update Tag' ),
				'add_new_item'               => __( 'Add New Tag' ),
				'new_item_name'              => __( 'New Tag Name' ),
				'separate_items_with_commas' => __( 'Separate Tag with commas' ),
				'add_or_remove_items'        => __( 'Add or remove Tag' ),
				'choose_from_most_used'      => __( 'Choose from the most used Tag' ),
				'not_found'                  => __( 'No found.' ),
				'menu_name'                  => __( 'Tags' ),
			);

			$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'story_tag' ),
			);

			register_taxonomy( 'story_tag', 'stories', $args );
}

function story_custom_metaboxes()
{
	add_meta_box('story_metaid','Additional Fields','create_stories_metabox','stories','advanced');
}

function create_stories_metabox()
{
	global $post, $characteristics, $districtsize;
	$story_video 		= get_post_meta($post->ID, "story_video", true);
	$story_video_host 	= get_post_meta($post->ID, "story_video_host", true);
	$story_highlight 	= get_post_meta($post->ID, "story_highlight", true);
	$story_district 	= get_post_meta($post->ID, "story_district", true);
	$story_school 		= get_post_meta($post->ID, "story_school", true);
	$story_mapaddress 	= get_post_meta($post->ID, "story_mapaddress", true);
	$story_zipcode 		= get_post_meta($post->ID, "story_zipcode", true);
	$story_sidebar_content = get_post_meta($post->ID, "story_sidebar_content", true);
	
	$return = '';
		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Video ID</div>';
			$return .= '<div class="wrprfld"><input type="text" name="story_video" value="'.$story_video.'" /></div>';
		$return .= '</div>';
		
		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Video Host</div>';
			$return .= '<div class="wrprfld">
					<select name="story_video_host">
						<option value="0">Select Video Host</option>';
			$status = ($story_video_host=="1")?"selected":"";
			$return .=		'<option value="1" '.$status.'>YouTube</option>';
			$status = ($story_video_host=="2")?"selected":"";
			$return .=		'<option value="2" '.$status.'>Vimeo</option>';
			$return .=	'</select>
					</div>';
		$return .= '</div>';

		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Highlight</div>';
			$return .= '<div class="wrprfld">';
					$return .= '<div>';
						if($story_highlight == "true"){ $status = 'checked="checked"';}else{ $status = '';}
						$return .= '<input type="radio" name="story_highlight" value="true" '. $status .' >';
						$return .= '<label>True</label>';
					$return .= '</div>';
					$return .= '<div>';
						if($story_highlight == "false"){ $status = 'checked="checked"';}else{ $status = '';}
						$return .= '<input type="radio" name="story_highlight" value="false" '. $status .' >';
						$return .= '<label>False</label>';
					$return .= '</div>';
			$return .= '</div>';
		$return .= '</div>';

		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Location</div>';
			$return .= '<div class="wrprfld">
							<span>District</span>
							<input type="text" name="story_district" value="'. $story_district .'" />
							<span>School</span>
							<input type="text" name="story_school" value="'. $story_school .'" />
							<span>Map Address</span>
							<input type="text" name="story_mapaddress" value="'. $story_mapaddress .'" />
							<span>Zipcode</span>
							<input type="text" name="story_zipcode" value="'. $story_zipcode .'" />
						</div>';
		$return .= '</div>';

		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Additional Sidebar Content</div>';
			$return .= '<div class="wrprfld">
							<textarea name="story_sidebar_content">'.$story_sidebar_content.'</textarea>
						</div>';
		$return .= '</div>';

	echo $return;
}

add_action('save_post', 'save_askquestion_metabox');
function save_askquestion_metabox()
{
	global $post;
	
	if(isset($_POST['story_video']))
	{
		update_post_meta($post->ID, "story_video", $_POST['story_video']);
	}
	
	if(isset($_POST['story_video_host']) && !empty($_POST['story_video_host']))
	{
		update_post_meta($post->ID, "story_video_host", $_POST['story_video_host']);
	}

	if(isset($_POST['story_highlight']) && !empty($_POST['story_highlight']))
	{
		update_post_meta($post->ID, "story_highlight", $_POST['story_highlight']);
	}

	if(isset($_POST['story_district']) && !empty($_POST['story_district']))
	{
		update_post_meta($post->ID, "story_district", $_POST['story_district']);
	}

	if(isset($_POST['story_school']) && !empty($_POST['story_school']))
	{
		update_post_meta($post->ID, "story_school", $_POST['story_school']);
	}

	if(isset($_POST['story_zipcode']) && !empty($_POST['story_zipcode']))
	{
		update_post_meta($post->ID, "story_zipcode", $_POST['story_zipcode']);
	}

	if(isset($_POST['story_districtsize']) && !empty($_POST['story_districtsize']))
	{
		update_post_meta($post->ID, "story_districtsize", $_POST['story_districtsize']);
	}

	if(isset($_POST['story_mapaddress']) && !empty($_POST['story_mapaddress']))
	{
		update_post_meta($post->ID, "story_mapaddress", $_POST['story_mapaddress']);
		$latlong = get_latitude_longitude($_POST['story_mapaddress']);
		if($latlong)
		{
        	$map = explode(',' ,$latlong);
        	$mapLatitude = $map[0];
        	$mapLongitude = $map[1];
			save_metadata($post->ID, $mapLatitude, $mapLongitude);
		}
	}

	if(isset($_POST['story_characteristic']) && !empty($_POST['story_characteristic']))
	{
		$story_characteristic = serialize($_POST['story_characteristic']);
		update_post_meta($post->ID, "story_characteristic", $story_characteristic);
	}
	else
	{
		$story_characteristic = serialize(array());
		update_post_meta($post->ID, "story_characteristic", $story_characteristic);
	}

	if(isset($_POST['story_sidebar_content']) && !empty($_POST['story_sidebar_content']))
	{
		update_post_meta($post->ID, "story_sidebar_content", $_POST['story_sidebar_content']);
	}

}
//Save Data
function save_metadata($postid, $mapLatitude, $mapLongitude)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "scp_stories";

	$post = get_post($postid);
	$title= $post->post_title;
	$content =  substr(preg_replace('/[^A-Za-z0-9\-\(\) ]/', '', strip_tags($post->post_content)), 0, 100);
	$image = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );

	if($wpdb->get_results("select id from $table_name where postid=$postid"))
	{
		$wpdb->get_results("UPDATE $table_name SET title='$title', content='$content', image='$image', longitude='$mapLongitude', latitude='$mapLatitude' where postid=$postid");
	}
	else
	{
		$wpdb->get_results("INSERT into $table_name (postid, title, content, image, longitude, latitude) VALUES ($postid, '$title', '$content', '$image', '$mapLongitude', '$mapLatitude')");
	}
}
//function for get longitude and latitude
function get_latitude_longitude($address)
{

    $address = str_replace(" ", "+", $address);

    $json = file_get_contents("http://maps.google.com/maps/api/geocode/json?address=$address&sensor=false");
    $json = json_decode($json);
	if(!empty($json))
	{
    	$lat = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
    	$long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
    	return $lat.','.$long;
	}
	else
	{
		return false;
	}
}

//Story Search
function get_stories_side_nav($taxonomy=NULL, $taxonomy_name=NULL)
{
	global $wpdb;

	$args = array('orderby'   => 'term_order',
				  'order'     => 'ASC',
				  'hide_empty'=> false);

	$states = get_terms('state', $args);
	$grades = get_terms('grade_level', $args);
	$characteristics = get_terms('characteristics', $args);
	$districtsize = get_terms('districtsize', $args);
	$tags = get_terms('story_tag', $args);

	if(isset($states) && !empty($states))
	{
		/*if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'state'): $display = 'block'; else: $display = 'none'; endif;
		$stateoption = '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($states as $state)
		{
			if(isset($taxonomy_name) && !empty($taxonomy_name) && $state->slug == $taxonomy_name):
				$check = 'checked';
			else:
				$check = '';
			endif;
			$stateoption .= '<li class="'.$check.'">
								<a href="'.site_url().'/stories/state/'.$state->slug.'">'.$state->name.' ('.$state->count.')</a>
							</li>';
		}
		$stateoption .= '</div>';*/
		if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'state'): $display = 'block'; else: $display = 'none'; endif;
		$stateoption = '<div class="tglelemnt" style="display:'. $display.'">';
		$stateoption .= '<select name="state" id="statedropdown">';
		$stateoption .= '<option value="">Browse by State</option>';
		foreach($states as $state)
		{
			if(isset($taxonomy_name) && !empty($taxonomy_name) && $state->slug == $taxonomy_name):
				$check = 'selected="selected"';
			else:
				$check = '';
			endif;
			$stateoption .= '<option '.$check.' value="'.site_url().'/stories/state/'.$state->slug.'">'.$state->name.' ('.$state->count.')</option>';
		}
		$stateoption .= '</select></div>';
	}

	if(isset($grades) && !empty($grades))
	{
		if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'grade_level'): $display = 'block'; else: $display = 'none'; endif;
		$gradeoption = '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($grades as $grade)
		{
			if(isset($taxonomy_name) && !empty($taxonomy_name) && $grade->slug == $taxonomy_name):
				$check = 'checked';
			else:
				$check = '';
			endif;
			$gradeoption .= '<li class="'.$check.'">
								<a href="'.site_url().'/stories/grade_level/'.$grade->slug.'">'.$grade->name.' ('.$grade->count.')</a>
							</li>';
		}
		$gradeoption .= '</div>';
	}

	if(isset($characteristics) && !empty($characteristics))
	{
		if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'characteristics'): $display = 'block'; else: $display = 'none'; endif;
		$district_locationoption = '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($characteristics as $characteristic)
		{
			if(isset($taxonomy_name) && !empty($taxonomy_name) && $characteristic->slug == $taxonomy_name):
				$check = 'checked';
			else:
				$check = '';
			endif;
			$district_locationoption .= '<li class="'.$check.'">
											<a href="'.site_url().'/stories/characteristics/'.$characteristic->slug.'">
												'.$characteristic->name.' ('.$characteristic->count.')
											</a>
										</li>';
		}
		$district_locationoption .= '</div>';
	}

	if(isset($districtsize) && !empty($districtsize))
	{
		if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'districtsize'): $display = 'block'; else: $display = 'none'; endif;
		$district_sizeoption = '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($districtsize as $size)
		{
			if(isset($taxonomy_name) && !empty($taxonomy_name) && $size->slug == $taxonomy_name):
				$check = 'checked';
			else:
				$check = '';
			endif;
			$district_sizeoption .= '<li class="'.$check.'">
										<a href="'.site_url().'/stories/districtsize/'.$size->slug.'">'.$size->name.' ('.$size->count.')</a>
									</li>';
		}
		$district_sizeoption .= '</div>';
	}

	$stories_home_URL = site_url().'/stories/';
	?>
    	<aside class="search_widget stry_srch_frm">
            <h3>
            	<?php if($_SERVER["REQUEST_URI"] != $stories_home_URL) { echo '<a href="'.$stories_home_URL.'">'; } ?>
            	Stories of EdTech Innovation
            	<?php if($_SERVER["REQUEST_URI"] != $stories_home_URL) { echo '</a>'; } ?>
            </h3>
            <p class="stry_srch_desc">
            	Use this tool to browse stories of innovation happening in schools across the nation. By sharing these stories, we hope to connect districts, schools, and educators trying similar things so that they can learn from each other's experiences.
            </p>

            <h5 class="hdng_mtr brdr_mrgn_none stry_browse_header">Browse Stories</h5>
            <div class="srchtrmbxs">
                <ul class="cstmaccordian">
                	<div class="cstmaccordiandv">
                        <?php
							if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'state'):
								$class = 'fa-caret-down';
								$accordian_title = 'Collapse';
							else:
								$class = 'fa-caret-right';
								$accordian_title = 'Expand';
							endif;
						?>
                        <i class="fa <?php echo $class; ?>"></i>
                        <a tabindex="0" title="<?php echo $accordian_title; ?> State Menu" class="accordian_section_title">State</a>
                    </div>
                    <?php echo $stateoption; ?>
                </ul>
            </div>
            <div class="srchtrmbxs">
                <ul class="cstmaccordian">
                	<div class="cstmaccordiandv">
                        <?php
							if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'grade_level'):
								$class = 'fa-caret-down';
								$accordian_title = 'Collapse';
							else:
								$class = 'fa-caret-right';
								$accordian_title = 'Expand';
							endif;
						?>
                        <i class="fa <?php echo $class; ?>"></i>
                        <a tabindex="0" title="<?php echo $accordian_title; ?> Grade Menu" class="accordian_section_title">Grade</a>
                    </div>
                    <?php echo $gradeoption; ?>
                </ul>
            </div>
            <div class="srchtrmbxs">
                <ul class="cstmaccordian">
                	<div class="cstmaccordiandv">
                        <?php
							if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'characteristics'):
								$class = 'fa-caret-down';
								$accordian_title = 'Collapse';
							else:
								$class = 'fa-caret-right';
								$accordian_title = 'Expand';
							endif;
						?>
                        <i class="fa <?php echo $class; ?>"></i>
                        <a tabindex="0" title="<?php echo $accordian_title; ?> Community Type Menu" class="accordian_section_title">Community Type</a>
                    </div>
                    <?php echo $district_locationoption; ?>
                </ul>
            </div>
            <div class="srchtrmbxs">
                <ul class="cstmaccordian">
                    <div class="cstmaccordiandv">
                        <?php
							if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'districtsize'):
								$class = 'fa-caret-down';
								$accordian_title = 'Collapse';
							else:
								$class = 'fa-caret-right';
								$accordian_title = 'Expand';
							endif;
						?>
                        <i class="fa <?php echo $class; ?>"></i>
                        <a tabindex="0" title="<?php echo $accordian_title; ?> District Size Menu" class="accordian_section_title">District Size</a>
                    </div>
                    <?php echo $district_sizeoption; ?>
                </ul>
            </div>

			<?php echo get_top_topics_nav($taxonomy, $taxonomy_name) ?>

            <div class="showallstories">
                <a class="btn_dwnld" href="<?php echo site_url();?>/stories/?action=showall">Browse All Stories</a>
            </div>

        </aside>
    <?php
}

function get_top_topics_nav($taxonomy=NULL, $taxonomy_name=NULL)
{
	global $wpdb;
	$args = array('orderby' => 'count', 'order' => 'DESC', 'number' => 10);
	$tags = get_terms('story_tag', $args);
	$topic_nav = '';

	$topic_nav .= '<div class="topic_sidebar"><h5>Topics :</h5><ul>';

	foreach($tags as $tag)
	{
		if(isset($taxonomy_name) && !empty($taxonomy_name) && $taxonomy_name == $tag->slug):
			$class = "checkedtag";
		else:
			$class = '';
		endif;
		$topic_nav .= '<li class="'.$class.'">
			  			 <a href="'.site_url().'/stories/story_tag/'.$tag->slug.'">'.ucfirst($tag->name).'
			  			 <span>('.$tag->count.')</span></a>
		  			   </li>';
	}

	$topic_nav .= '</ul></div>';
	return $topic_nav;
}

function get_counts($termid, $searchresult)
{
	global $wpdb;
	$taxon_tablename = $wpdb->prefix."term_taxonomy";
	$term_rel_tablename = $wpdb->prefix."term_relationships";
	$query = "SELECT object_id from $term_rel_tablename where term_taxonomy_id=(SELECT term_taxonomy_id from $taxon_tablename where term_id=$termid)";
	$data = $wpdb->get_results($query, OBJECT_K);
	if(!empty($data) && !empty($searchresult))
	{
		$data = array_keys($data);
		$result = array_intersect($searchresult, $data);
		$count = count($result);
	}
	else
	{
		$count = 0;
	}
	return $count;
}

function get_metacounts($meta, $searchresult)
{
	global $wpdb;
	$tablename = $wpdb->prefix."postmeta";
	$query = "SELECT post_id from $tablename where meta_value LIKE '%$meta%'";
	$data = $wpdb->get_results($query, OBJECT_K);
	if(!empty($data) && !empty($searchresult))
	{
		$result = array_intersect_key($searchresult, $data);
		$count = count($result);
	}
	else
	{
		$count = 0;
	}
	return $count;
}

//Functions for load template
function get_story_template_part( $slug, $name = null )
{
	do_action( "get_story_template_part_{$slug}", $slug, $name );

	$templates = array();
	$name = (string) $name;
	if ( '' !== $name )
		$templates[] = "{$slug}-{$name}.php";

	$templates[] = "{$slug}.php";

	locate_story_template($templates, true, false);
}
function locate_story_template($template_names, $load = false, $require_once = true )
{
	$located = '';
	foreach ( (array) $template_names as $template_name )
	{
		if ( !$template_name )
			continue;
		if ( file_exists(SCP_PATH . 'templates/' . $template_name))
		{
			$located = SCP_PATH . 'templates/' . $template_name;
			break;
		}
	}

	if ( $load && '' != $located )
		load_story_template( $located, $require_once );

	return $located;
}
function load_story_template( $_template_file, $require_once = true )
{
	global $posts, $post, $wp_did_header, $wp_query, $wp_rewrite, $wpdb, $wp_version, $wp, $id, $comment, $user_ID;

	if ( is_array( $wp_query->query_vars ) )
		extract( $wp_query->query_vars, EXTR_SKIP );

	if ( $require_once )
		require_once( $_template_file );
	else
		require( $_template_file );
}

//simply return Story or Stories depending on count
function story_plural( $count = null )
{
	if (!empty($count) && ($count > 1))
		return 'Stories';
	else
		return 'Story';
}

//Story Search
/* disabling search for now. Just going to use browse Navigation
function get_story_search($searchresult=NULL, $searchtext=NULL, $taxonomy_state=NULL, $taxonomy_program=NULL, $taxonomy_grade_level=NULL, $district_location=NULL, $district_size=NULL,$taxonomy_tags=NULL)
{
	global $wpdb, $characteristics, $districtsize;

	$args = array('orderby'   => 'term_order',
				  'order'     => 'ASC',
				  'hide_empty'=> false);

	$states = get_terms('state', $args);
	//$programs = get_terms('program', $args);
	$grades = get_terms('grade_level', $args);
	$tags = get_terms('story_tag', $args);

	if(isset($states) && !empty($states))
	{
		if(isset($taxonomy_state) && !empty($taxonomy_state)): $display = 'block'; else: $display = 'none'; endif;
		$stateoption .= '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($states as $state)
		{
			$count = get_counts($state->term_id, $searchresult);
			if(in_array($state->slug, $taxonomy_state)): $check = 'checked="checked"'; else: $check = ''; endif;
			$stateoption .= '<li>
								<input type="checkbox" name="taxonomy_state[]" '.$check.' value="'.$state->slug.'" id="state'.$state->term_id.'">
								<label for="state'.$state->term_id.'">'.$state->name.'</label>
								<span>('. $count.')</span>
							</li>';
		}
		$stateoption .= '</div>';
	}
	// removing programs from search for now
	if(isset($programs) && !empty($programs))
	{
		if(isset($taxonomy_program) && !empty($taxonomy_program)): $display = 'block'; else: $display = 'none'; endif;
		$programoption .= '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($programs as $program)
		{
			$count = get_counts($program->term_id, $searchresult);
			if(in_array($program->slug, $taxonomy_program)): $check = 'checked="checked"'; else: $check = ''; endif;
			$programoption .= '<li>
								<input type="checkbox" name="taxonomy_program[]" '.$check.' value="'.$program->slug.'" id="prog'.$program->term_id.'">
								<label for="prog'.$program->term_id.'">'.$program->name.'</label>
								<span>('. $count.')</span>
							  </li>';
		}
		$programoption .= '</div>';
	}

	if(isset($grades) && !empty($grades))
	{
		if(isset($taxonomy_grade_level) && !empty($taxonomy_grade_level)): $display = 'block'; else: $display = 'none'; endif;
		$gradeoption .= '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($grades as $grade)
		{
			$count = get_counts($grade->term_id, $searchresult);
			if(in_array($grade->slug, $taxonomy_grade_level)): $check = 'checked="checked"'; else: $check = ''; endif;
			$gradeoption .= '<li>
								<input type="checkbox" name="taxonomy_grade_level[]" '.$check.' value="'.$grade->slug.'" id="grade'.$grade->term_id.'">
								<label for="grade'.$grade->term_id.'">'.$grade->name.'</label>
								<span>('. $count.')</span>
							</li>';
		}
		$gradeoption .= '</div>';
	}
	if(isset($characteristics) && !empty($characteristics))
	{
		if(isset($district_location) && !empty($district_location)): $display = 'block'; else: $display = 'none'; endif;
		$district_locationoption .= '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($characteristics as $characteristic)
		{
			$count = get_metacounts($characteristic, $searchresult);
			if(in_array($characteristic, $district_location)): $check = 'checked="checked"'; else: $check = ''; endif;
			$district_locationoption .= '<li>
											<input type="checkbox" name="district_location[]" '.$check.' value="'.$characteristic.'" id="char'.$characteristic.'">
											<label for="char'.$characteristic.'">'.$characteristic.'</label>
											<span>('. $count.')</span>
										</li>';
		}
		$district_locationoption .= '</div>';
	}
	if(isset($districtsize) && !empty($districtsize))
	{
		if(isset($district_size) && !empty($district_size)): $display = 'block'; else: $display = 'none'; endif;
		$district_sizeoption .= '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($districtsize as $size)
		{
			$count = get_metacounts($size, $searchresult);
			if(in_array($size, $district_size)): $check = 'checked="checked"'; else: $check = ''; endif;
			$district_sizeoption .= '<li>
										<input type="checkbox" name="district_size[]" '.$check.' value="'.$size.'" id="size'.$size.'">
										<label for="size'.$size.'">'.$size.'</label>
										<span>('. $count.')</span>
									</li>';
		}
		$district_sizeoption .= '</div>';
	}

	?>
    	<aside class="search_widget stry_srch_frm">
            <h3>Stories of EdTech Innovation</h3>
            <p class="stry_srch_desc">Use this tool to browse stories of innovation happening in schools across the nation. By sharing these stories, we hope to connect districts, schools, and educators trying similar things so that they can learn from each other's experiences.</p>
            <form method="get">
                <input type="text" name="searchtext" value="<?php echo $searchtext; ?>" />
                <div class="srchtrmbxs">
                    <ul class="cstmaccordian">
                        <div class="cstmaccordiandv">
                            <i class="fa fa-caret-right"></i>
                            <h5>State</h5>
                        </div>
                        <?php echo $stateoption; ?>
                    </ul>
                </div>
                <div class="srchtrmbxs">
                    <ul class="cstmaccordian">
                    	<div class="cstmaccordiandv">
                            <i class="fa fa-caret-right"></i>
                            <h5>Grade</h5>
                        </div>
                        <?php echo $gradeoption; ?>
                    </ul>
                </div>
                <div class="srchtrmbxs">
                    <ul class="cstmaccordian">
                    	<div class="cstmaccordiandv">
                            <i class="fa fa-caret-right"></i>
                            <h5>Community Type</h5>
                        </div>
                        <?php echo $district_locationoption; ?>
                    </ul>
                </div>
                <div class="srchtrmbxs">
                    <ul class="cstmaccordian">
                        <div class="cstmaccordiandv">
                            <i class="fa fa-caret-right"></i>
                            <h5>District Size</h5>
                        </div>
                        <?php echo $district_sizeoption; ?>
                    </ul>
                </div>
                <!--<select name="taxonomy_program">
                    <?php //echo $programoption; ?>
                </select>-->
				<?php if( isset($searchtext) ): ?>
	            <div class="pplrstorytags">
                	<h5>Topics</h5>
                    <ul>
						<?php
							foreach($tags as $tag)
							{
								$count = get_counts($tag->term_id, $searchresult);
								if(in_array( $tag->slug, $taxonomy_tags )): $check = 'checked="checked"'; else: $check = ''; endif;
								echo '<li>
										<input type="checkbox" '.$check.' name="story_tags[]" value="'.$tag->slug.'">
										<label>'.$tag->name.'</label>
										<span>('. $count.')</span>
									 </li>';
							}
                        ?>
                    </ul>
                </div>
                <?php endif; ?>
                <div class="showallstories">
                    <a href="<?php echo site_url();?>/stories/?action=showall">Show All Examples</a>
                </div>
                <input type="submit" name="action" value="Search" />
            </form>
        </aside>
    <?php
}
*/
?>