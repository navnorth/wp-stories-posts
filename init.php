<?php
// hook into the init action and call create_managment_taxonomies when it fires
add_action( 'after_setup_theme', 'create_managment_taxonomies');

// create taxonomies, for the post type "ask_question"
function create_managment_taxonomies()
{
	$args = array(
		'labels'             => array('name' =>  _x( 'Stories', 'post type general name', 'your-plugin-textdomain' )),
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
						'program'	 => 'Program',
						'state'	 => 'State',
						'grade_level'=> 'Grade Level');
	
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
	$story_highlight 	= get_post_meta($post->ID, "story_highlight", true);
	$story_district 	= get_post_meta($post->ID, "story_district", true);
	$story_school 		= get_post_meta($post->ID, "story_school", true);
	$story_mapaddress 	= get_post_meta($post->ID, "story_mapaddress", true);
	$story_zipcode 		= get_post_meta($post->ID, "story_zipcode", true);
	$story_districtsize	= get_post_meta($post->ID, "story_districtsize", true);
	$story_characteristic = unserialize(get_post_meta($post->ID, "story_characteristic", true));
	$story_sidebar_content = get_post_meta($post->ID, "story_sidebar_content", true);
	
	$return = '';
		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Video</div>';
			$return .= '<div class="wrprfld"><input type="text" name="story_video" value="'.$story_video.'" /></div>';
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
			$return .= '<div class="wrprtext">District Size</div>';
			$return .= '<div class="wrprfld">';
				$return .= '<select name="story_districtsize">';
					$return  .= '<option value="">Select Distict Size</option>';	
					foreach($districtsize as $size)
					{
						if(isset($story_districtsize) && !empty($story_districtsize))
						{
							if($size == $story_districtsize)
							{
								$check = 'selected="selected"';
							}
							else
							{
								$check = '';
							}
						}
						$return  .= '<option value="'.$size.'" '.$check .'/>'.$size.'</option>';
					}
				$return .= '</select>';	
			$return .= '</div>';
		$return .= '</div>';
		
		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Characteristic</div>';
			$return .= '<div class="wrprfld">';
					foreach($characteristics as $characteristic)
					{
						if(isset($story_characteristic) && is_array($story_characteristic))
						{
							if(in_array($characteristic, $story_characteristic))
							{
								$check = 'checked="checked"';
							}
						}
						$return  .= '<div>
										<input type="checkbox" name="story_characteristic[]" value="'.$characteristic.'" '.$check .'/>
										<label>'.$characteristic.'</label>
									</div>';
					}
			$return .= '</div>';
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
	
	if(isset($_POST['story_video']) && !empty($_POST['story_video']))
	{
		update_post_meta($post->ID, "story_video", $_POST['story_video']);
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
function get_story_search($searchtext=NULL, $taxonomy_state=NULL, $taxonomy_program=NULL, $taxonomy_grade_level=NULL, $district_location=NULL, $district_size=NULL)
{
	global $wpdb, $characteristics, $districtsize;
	
	$args = array('orderby'   => 'name', 
				  'order'     => 'ASC',
				  'hide_empty'=> false); 
	$states = get_terms('state', $args);
	$programs = get_terms('program', $args);
	$grades = get_terms('grade_level', $args);
	
	$stateoption = '<option value="">Select State</option>';
	if(isset($states) && !empty($states))
	{
		foreach($states as $state)
		{
			if($taxonomy_state == $state->slug): $check = 'selected="selected"'; else: $check = ''; endif; 
			$stateoption .= '<option '.$check.' value="'.$state->slug.'">'.$state->name.'</option>';
		}
	}
	$programoption = '<option value="">Select program</option>';
	if(isset($programs) && !empty($programs))
	{
		foreach($programs as $program)
		{
			if($taxonomy_program == $program->slug): $check = 'selected="selected"'; else: $check = ''; endif; 
			$programoption .= '<option '.$check.' value="'.$program->slug.'">'.$program->name.'</option>';
		}
	}
	$gradeoption = '<option value="">Select Grade</option>';
	if(isset($grades) && !empty($grades))
	{
		foreach($grades as $grade)
		{
			if($taxonomy_grade_level == $grade->slug): $check = 'selected="selected"'; else: $check = ''; endif; 
			$gradeoption .= '<option '.$check.' value="'.$grade->slug.'">'.$grade->name.'</option>';
		}
	}
	$district_locationoption = '<option value="">Select District Location</option>';
	if(isset($characteristics) && !empty($characteristics))
	{
		foreach($characteristics as $characteristic)
		{
			if($district_location == $characteristic): $check = 'selected="selected"'; else: $check = ''; endif; 
			$district_locationoption .= '<option '.$check.' value="'.$characteristic.'">'.$characteristic.'</option>';
		}
	}
	$district_sizeoption = '<option value="">Select District Size</option>';
	if(isset($districtsize) && !empty($districtsize))
	{
		foreach($districtsize as $size)
		{
			if($district_size == $size): $check = 'selected="selected"'; else: $check = ''; endif; 
			$district_sizeoption .= '<option '.$check.' value="'.$size.'">'.$size.'</option>';
		}
	}
	?>
    	<aside class="search_widget stry_srch_frm">
            <h3><?php if(isset($searchtext)) { echo "Refine Search"; }else { echo "EdTech Story"; }?></h3>
            <form method="get">
                <input type="text" name="searchtext" value="<?php echo $searchtext; ?>" />
                <select name="taxonomy_state">
                    <?php echo $stateoption; ?>
                </select>
                <select name="taxonomy_program">
                    <?php echo $programoption; ?>
                </select>
                <select name="taxonomy_grade_level">
                    <?php echo $gradeoption; ?>
                </select>
                <select name="district_location">
                    <?php echo $district_locationoption; ?>
                </select>
                <select name="district_size">
                    <?php echo $district_sizeoption; ?>
                </select>
                <div class="showallstories">
                    <a href="<?php echo site_url();?>/stories/?action=showall"> Show All Stories</a>
                </div>
                <input type="submit" name="action" value="Search" />
            </form>
        </aside>
    <?php
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
?>