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
						'grade_level' => 'Level',
						'characteristics' => 'Community Type',
						'districtsize' => 'District Enrollment',
						'institutionenrollment' => 'Institution Enrollment',
						'institutiontype' => 'Institution Type');

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
	global $post, $characteristics, $districtsize, $wpdb;

	$story_address_latitude = "";
	$story_address_longitude = "";
	$story_zipcode = "";
	$story_mapaddress = "";
	$table_name = $wpdb->prefix . "scp_stories";

	$story_video 		= get_post_meta($post->ID, "story_video", true);
	$story_video_host 	= get_post_meta($post->ID, "story_video_host", true);
	$story_highlight 	= get_post_meta($post->ID, "story_highlight", true);
	$story_district 	= get_post_meta($post->ID, "story_district", true);
	$story_school 		= get_post_meta($post->ID, "story_school", true);
	$story_institution 	= get_post_meta($post->ID, "story_institution", true);
	$story_mapaddress 	= get_post_meta($post->ID, "story_mapaddress", true);
	$story_zipcode 		= get_post_meta($post->ID, "story_zipcode", true);
	$story_sidebar_content = get_post_meta($post->ID, "story_sidebar_content", true);
	$refresh_img = SCP_URL . 'images/refresh.png';

	$stories = scp_get_coordinates($post->ID);
	if (count($stories)>0){
		$story_address_latitude = $stories[0]->latitude;
		$story_address_longitude = $stories[0]->longitude;
	}

	$return = '';

		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Video ID</div>';
			$return .= '<div class="wrprfld"><input type="text" class="half-field-width" name="story_video" value="'.$story_video.'" /></div>';
		$return .= '</div>';

		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Video Host</div>';
			$return .= '<div class="wrprfld">
					<select name="story_video_host" class="half-field-width">
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
							<span>Institution</span>
							<input type="text" name="story_institution" value="'. $story_institution .'" />
						</div>
						<div class="wrprfld gmap-address">
							<span>Map Address</span>
							<input type="text" id="story-mapaddress" name="story_mapaddress" value="'. $story_mapaddress .'" />
							<span class="gmap-label">Zipcode</span>
							<input type="text" id="story-zipcode" name="story_zipcode" value="'. $story_zipcode .'" />
						</div>';
		$return .= '</div>';

		$return .= '<div class="scp_adtnalflds gmap-coordinates">';
			$return .= '<div class="wrprtext">Coordinates</div>';
			$return .= '<div class="wrprfld">
							<span class="map-coord-label">Latitude</span>
							<input type="text" id="map-latitude" class="map-coord-field"  name="story_address_latitude" value="'. $story_address_latitude .'" />
							<span class="map-coord-label coord-label-2">Longitude</span>
							<input type="text" id="map-longitude"  class="map-coord-field" name="story_address_longitude" value="'. $story_address_longitude .'" />
							<button type="button" class="map-refresh-btn button" title="Refresh Map"><img src="'.$refresh_img.'" height="24" width="24" /></button>
						</div>
					</div>';
		$return .= '<div id="map-error-msg" class="map-error-msg"></div>';
		$return .= '<div class="map-display"><div id="map"></div><div id="infowindow-content">
      					<span id="place-name" class="title"></span><br />
      					<span id="place-address"></span></div></div>';

		$return .= '<div class="scp_adtnalflds">';
			$return .= '<div class="wrprtext">Additional Sidebar Content</div>';
			$return .= '<div class="wrprfld">
							<textarea name="story_sidebar_content">'.$story_sidebar_content.'</textarea>
						</div>';
		$return .= '</div>';

		$return .= '<script type="text/javascript">
					let map;
					/* Initialize Map */
					function initMap() {
  						map = new google.maps.Map(document.getElementById("map"), {
    							center: { lat: 40.715618, lng: -74.011133 },
    							zoom: 8,
  								});
  						const input = document.getElementById("story-mapaddress");
  						const zip = document.getElementById("story-zipcode");
  						const lat = document.getElementById("map-latitude");
  						const lng = document.getElementById("map-longitude");
  						const err = document.getElementById("map-error-msg");
  						const options = {
				          componentRestrictions: { country: "us" },
				          fields: ["formatted_address", "geometry", "name"],
				          origin: map.getCenter(),
				          strictBounds: false,
				          types: ["address"],
				        };

				        /* Autocomplete binding */
				        const autocomplete = new google.maps.places.Autocomplete(
          					input,
          					options
        				);
        				autocomplete.bindTo("bounds", map);';

        if (!empty($story_address_latitude) && !empty($story_address_longitude)) {
        	$return .= 'const marker = new google.maps.Marker({
						    map,
						    anchorPoint: new google.maps.Point(0, -29),
						});
						marker.setVisible(false);
						var loc = new google.maps.LatLng('.$story_address_latitude.', '.$story_address_longitude.');
						map.setCenter(loc);
						map.setZoom(15);
						marker.setPosition(loc);
						marker.setVisible(true);
						';
        } else {
        	$return .= 'const marker = new google.maps.Marker({
						    map,
						    anchorPoint: new google.maps.Point(0, -29),
						});';
        }

		$return .= 'autocomplete.addListener("place_changed", () => {
    						marker.setVisible(false);
    						const place = autocomplete.getPlace();
    						if (!place.geometry) {
    							err.textContent = "No details available for input: " + place.name + "";
      							return;
    						}
    						if (place.geometry.viewport) {
						      map.fitBounds(place.geometry.viewport);
						    } else {
						      map.setCenter(place.geometry.location);
						      map.setZoom(15);
						    }

						    const geocoder = new google.maps.Geocoder();
						    const address = input.value;
						    geocoder.geocode({ address: address }, (results, status) => {
							    if (status === "OK") {
							    	console.log(results);
							      if (results.length>0){
							      	let address_components = results[0].address_components;
							      	address_components.map(function(component){
							      		if (component.types.indexOf("postal_code")!==-1){
							      			zip.value = component.long_name;
							      		}
							      	});
							      }
							    } else {
							      err.textContent = "Geocode was not successful for the following reason: " + status;
							    }
							  });

						    marker.setPosition(place.geometry.location);
						    marker.setVisible(true);
						    input.textContent = place.formatted_address;
						    let xlat = place.geometry.location.lat();
						    let xlng = place.geometry.location.lng();
						    lat.value = xlat;
						    lng.value = xlng;
						});';
		$return .= 'map.addListener("click", (mapsMouseEvent) => {
						console.log(mapsMouseEvent.latLng);
						let xlat = mapsMouseEvent.latLng.lat();
				    	let xlng = mapsMouseEvent.latLng.lng();
						const latlng = {
						    lat: parseFloat(xlat),
						    lng: parseFloat(xlng)
					    };
					    marker.setVisible(false);

					    const geocoder = new google.maps.Geocoder();
					    const address = input.value;
					    geocoder.geocode({ location: latlng }, (results, status) => {
					    if (status === "OK") {
					      if (results[0]) {
					      	input.value = results[0].formatted_address;
					      	let address_components = results[0].address_components;
					      	address_components.map(function(component){
					      		if (component.types.indexOf("postal_code")!==-1){
					      			zip.value = component.long_name;
					      		}
					      	});
					        /*const marker = new google.maps.Marker({
					          anchorPoint: new google.maps.Point(0, -29),
					          map: map,
					        });*/
					        marker.setVisible(false);
					        var loc = new google.maps.LatLng(latlng.lat, latlng.lng);
					        map.setCenter(loc);
					        map.setZoom(15);
					        marker.setPosition(loc);
					        marker.setVisible(true);
					      } else {
					        jQuery("#map-error-msg").html("No results found").show();
					        window.setTimeout(function(){ jQuery("#map-error-msg").hide(); },5000)
					      }
					    } else {
					      jQuery("#map-error-msg").html("Geocoder failed due to: " + status).show();
					      window.setTimeout(function(){ jQuery("#map-error-msg").hide(); },5000)
					    }
					  });

				    //input.textContent = place.formatted_address;
				    
				    lat.value = xlat;
				    lng.value = xlng;
					});';
		$return .=	'}
				</script>';
		$return .= initialize_stories_map();
		$return .= '<style>#map { height:100%; min-height:400px; }</style>';

	echo $return;
}

function initialize_stories_map(){
	global $_googleapikey;
	global $post;
	if (isset($_googleapikey) && $post->post_type=='stories')
		return '<script src="https://maps.googleapis.com/maps/api/js?key='.$_googleapikey.'&callback=initMap&libraries=places"></script>';
}

// Add map JS reference
add_action('admin_enqueue_scripts','scp_add_map_js');
function scp_add_map_js(){
	global $post;
	global $_googleapikey;
	if (is_object($post)){
		if ($post->post_type=="stories"){
			wp_enqueue_script('scp-map-script', SCP_URL.'js/scp_map.js', array('jquery'));
			wp_localize_script('scp-map-script', 'googlemap', array( 'apikey' => $_googleapikey ));
		}
	}
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
	
	if(isset($_POST['story_institution']) && !empty($_POST['story_institution']))
	{
		update_post_meta($post->ID, "story_institution", $_POST['story_institution']);
	}

	if(isset($_POST['story_zipcode']) && !empty($_POST['story_zipcode']))
	{
		update_post_meta($post->ID, "story_zipcode", $_POST['story_zipcode']);
	} else {
		if (is_object($post) && get_post_meta($post->ID, "story_zipcode"))
			update_post_meta($post->ID, "story_zipcode", "");
	}

	if(isset($_POST['story_districtsize']) && !empty($_POST['story_districtsize']))
	{
		update_post_meta($post->ID, "story_districtsize", $_POST['story_districtsize']);
	}

	//update_post_meta($post->ID, "story_mapaddress", $_POST['story_mapaddress']);
	if(isset($_POST['story_mapaddress']) && !empty($_POST['story_mapaddress']))
	{
		update_post_meta($post->ID, "story_mapaddress", $_POST['story_mapaddress']);
		/*$latlong = get_latitude_longitude($_POST['story_mapaddress']);
		if($latlong)
		{
        	$map = explode(',' ,$latlong);
        	$mapLatitude = $map[0];
        	$mapLongitude = $map[1];
			save_metadata($post->ID, $mapLatitude, $mapLongitude);
		}*/
	} else {
		if (is_object($post) && get_post_meta($post->ID, "story_mapaddress"))
			update_post_meta($post->ID, "story_mapaddress", "");
	}

	if (isset($_POST['story_address_latitude']) && isset($_POST['story_address_longitude'])){
		$mapLatitude = $_POST['story_address_latitude'];
        $mapLongitude = $_POST['story_address_longitude'];
		save_metadata($post->ID, $mapLatitude, $mapLongitude);
	}

	if(isset($_POST['story_sidebar_content']))
	{
		$story_characteristic = serialize($_POST['story_characteristic']);
		update_post_meta($post->ID, "story_characteristic", $story_characteristic);
	}
	else
	{
		if (is_object($post)){
			$story_characteristic = serialize(array());
			update_post_meta($post->ID, "story_characteristic", $story_characteristic);
		}
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

//Get Stories Coordinates
function scp_get_coordinates($postid)
{
	global $wpdb;
	$table_name = $wpdb->prefix . "scp_stories";

	$results = $wpdb->get_results("select * from $table_name where postid=$postid");
	
	return $results;
}

//function for get longitude and latitude
function get_latitude_longitude($address)
{
	global $_googleapikey;

    $address = str_replace(" ", "+", $address);

    $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false&key=".$_googleapikey);

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

function generate_state_dropdown($id, $taxonomy, $taxonomy_name, $level = null) {
	//Get State IDs
	$args = array(
			'orderby'   	=> 	'name',
			'order'     	=> 	'ASC',
			'fields'	=>	'ids',
			'hide_empty'	=> 	false);
	$state_ids = get_terms('state', $args);
	$stateoption = "";
	
	if ($level=="P-12")
		$grade_level = array("Early Childhood Education","P-12");
	else
		$grade_level = array("Higher Education","Postsecondary", "Higher & Adult Ed");
	
	//Get story ids based on state and grade_level
	$args2 = array(
		'post_type' => 'stories',
		'posts_per_page' => -1,
		'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'grade_level',
					'field' => 'name',
					'terms' => $grade_level
				),
				array(
					'taxonomy' => 'state',
					'field' => 'id',
					'terms' => $state_ids
				)
			),
		'fields' => 'ids'
		);
	$sposts = get_posts($args2);
	
	$states = wp_get_object_terms($sposts, 'state');
	
	//Enable State
	if(isset($states) && !empty($states))
	{
		if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'state'): $display = 'block'; else: $display = 'none'; endif;
		$stateoption = '<div class="tglelemnt" style="display:'. $display.'">';
		$stateoption .= '<select name="state" id="'.$id.'" data-post-ids="'.json_encode($sposts).'">';
		$stateoption .= '<option value="">Browse by State</option>';
		foreach($states as $state)
		{
			$count = get_count_by_state_level($grade_level, $state->term_id, $sposts);
			if(isset($taxonomy_name) && !empty($taxonomy_name) && $state->slug == $taxonomy_name):
				$check = 'selected="selected"';
			else:
				$check = '';
			endif;
			$stateoption .= '<option '.$check.' value="'.site_url().'/stories/state/'.$state->slug.'">'.$state->name.' ('.$count.')</option>';
		}
		$stateoption .= '</select></div>';
	}
	return $stateoption;
}

//Get state count by level
function get_count_by_state_level($level, $state_id, $object_ids) {
	$count = 0;
	
	//Get story ids based on state and grade_level
	$args = array(
		'post_type' => 'stories',
		'posts_per_page' => -1,
		'posts__in' => $object_ids,
		'tax_query' => array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'grade_level',
					'field' => 'name',
					'terms' => $level
				),
				array(
					'taxonomy' => 'state',
					'field' => 'term_id',
					'terms' => $state_id
				)
			),
		'fields' => 'id'
		);
	$results = get_posts($args);
	
	if ($results)
		$count = count($results);
		
	return $count;
}

//Story Search
function get_stories_side_nav($taxonomy=NULL, $taxonomy_name=NULL, $search_text=NULL, $active_level = "all")
{
	global $wpdb, $_filters;

	$args = array( 'orderby'   => 'term_order',
				  'order'     => 'ASC',
				  'hide_empty'=> false);

	$state_args = array( 'orderby'   => 'name',
				  'order'     => 'ASC',
				  'hide_empty'=> false);
				  
	$states = get_terms('state', $state_args);
	$grades = get_terms('grade_level', $args);
	$characteristics = get_terms('characteristics', $args);
	$districtsize = get_terms('districtsize', $args);
	$institutionenrollment = get_terms('institutionenrollment', $args);
	$institutiontype = get_terms('institutiontype', $args);
	$tags = get_terms('story_tag', $args);	
	
	//Enable State
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
	
	/** Institution Enrollment **/
	if(isset($institutionenrollment) && !empty($institutionenrollment))
	{
		if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'institutionenrollment'): $display = 'block'; else: $display = 'none'; endif;
		$institution_option = '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($institutionenrollment as $institution)
		{
			if(isset($taxonomy_name) && !empty($taxonomy_name) && $institution->slug == $taxonomy_name):
				$check = 'checked';
			else:
				$check = '';
			endif;
			$institution_option .= '<li class="'.$check.'">
							<a href="'.site_url().'/stories/institutionenrollment/'.$institution->slug.'">'.$institution->name.' ('.$institution->count.')</a>
						</li>';
		}
		$institution_option .= '</div>';
	}
	
	/** Institution Type **/
	if(isset($institutiontype) && !empty($institutiontype))
	{
		if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'institutiontype'): $display = 'block'; else: $display = 'none'; endif;
		$institutiontype_option = '<div class="tglelemnt" style="display:'. $display.'">';
		foreach($institutiontype as $type)
		{
			if(isset($taxonomy_name) && !empty($taxonomy_name) && $type->slug == $taxonomy_name):
				$check = 'checked';
			else:
				$check = '';
			endif;
			$institutiontype_option .= '<li class="'.$check.'">
							<a href="'.site_url().'/stories/institutiontype/'.$type->slug.'">'.$type->name.' ('.$type->count.')</a>
						</li>';
		}
		$institutiontype_option .= '</div>';
	}

	$stories_home_URL = site_url().'/stories/';
	?>
    	<aside class="search_widget stry_srch_frm">
	    <?php if (!(title_can_be_hidden())): ?>
            <h3>
            	<?php if($_SERVER["REQUEST_URI"] != $stories_home_URL) { echo '<a href="'.$stories_home_URL.'">'; } ?>
            	<?php _e( "Stories of EdTech Innovation", SCP_SLUG); ?>
            	<?php if($_SERVER["REQUEST_URI"] != $stories_home_URL) { echo '</a>'; } ?>
            </h3>
	    <?php endif; ?>
            <p class="stry_srch_desc">
            	<?php _e( "Use this tool to browse stories of innovation happening in schools across the nation. By sharing these stories, we hope to connect districts, schools, and educators trying similar things so that they can learn from each other's experiences.", SCP_SLUG); ?>
            </p>

        <?php 	$archive_notice = get_option('enable_archive_notice');
            	$archive_notice_content = get_option('archive_notice_content');
    		if ( ($archive_notice=="1" || $archive_notice=="on" ) & $archive_notice_content!=="" ): ?>
            	<div class="archived-disclaimer"><?php echo $archive_notice_content; ?></div>
        	<?php endif; ?>
            <h4 class="hdng_mtr brdr_mrgn_none stry_browse_header">Browse Stories</h4>
	    <div id="story-tabs">
	<?php
		//Define array $tabs
		$tabs = array(
			array( "name" => _x( "All" , SCP_SLUG ), "anchor" => "all" ),
			array( "name" => _x( "P-12" , SCP_SLUG ) , "anchor" => "p12") ,
			array( "name" => _x( "Higher & Adult Ed", SCP_SLUG ), "anchor" => "higheradulted" )
			);
		
		if (!empty($tabs)) {
			echo '<ul class="tabs">';
			foreach ($tabs as $tab) {
				if(in_array($taxonomy,array('state','grade_level')) && $tab['anchor']=="all") {
					$active = 'class="active"';
				} elseif (in_array($taxonomy,array('characteristics','districtsize')) && $tab['anchor']=="p12"){
					$active = 'class="active"';
				}  elseif (in_array($taxonomy,array('institutionenrollment','institutiontype')) && $tab['anchor']=="higheradulted"){
					$active = 'class="active"';
				} else {
					$active = "";
				}
				
				echo '<li class="'.$tab['anchor'].'"><a href="#'.$tab['anchor'].'" '.$active.'>'.$tab['name'].'</a></li>';
			}
			echo '</ul>';
		}
	?>
		<!-- All Tab -->
		<div id="all" class="story-tab">
			<?php if ($_filters['state']==1): ?>
			<div class="srchtrmbxs">
			    <div class="cstmaccordian">
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
			    </div>
			</div>
			<?php endif; ?>
			<?php if ($_filters['grade_level']==1): ?>
			<div class="srchtrmbxs">
			    <div class="cstmaccordian">
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
				    <a tabindex="0" title="<?php echo $accordian_title; ?> Grade Menu" class="accordian_section_title">Level</a>
				</div>
				<?php echo $gradeoption; ?>
			    </div>
			</div>
			<?php endif; ?>
		</div>
	    
		<!-- P-12 Tab -->
		<div id="p12" class="story-tab">
			<?php if ($_filters['state']==1): ?>
			<?php $state2option = generate_state_dropdown('statedropdown2', $taxonomy, $taxonomy_name, "P-12"); ?>
			<div class="srchtrmbxs">
			    <div class="cstmaccordian">
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
				<?php echo $state2option; ?>
			    </div>
			</div>
			<?php endif; ?>
			
			<?php if ($_filters['characteristics']==1): ?>
			<div class="srchtrmbxs">
				<div class="cstmaccordian">
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
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ($_filters['district_size']==1): ?>
			<div class="srchtrmbxs">
				<div class="cstmaccordian">
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
					<a tabindex="0" title="<?php echo $accordian_title; ?> District Enrollment Menu" class="accordian_section_title">District Enrollment</a>
				    </div>
				    <?php echo $district_sizeoption; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		
		<!-- Post Secondary Tab -->
		<div id="higheradulted" class="story-tab">
			<?php if ($_filters['state']==1): ?>
			<?php $state3option = generate_state_dropdown('statedropdown3', $taxonomy, $taxonomy_name, "Higher & Adult Ed"); ?>
			<div class="srchtrmbxs">
			    <div class="cstmaccordian">
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
				<?php echo $state3option; ?>
			    </div>
			</div>
			<?php endif; ?>
			
			
			<?php if ($_filters['institutiontype']==1): ?>
			<div class="srchtrmbxs">
				<div class="cstmaccordian">
				    <div class="cstmaccordiandv">
					<?php
						if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'institutiontype'):
							$class = 'fa-caret-down';
							$accordian_title = 'Collapse';
						else:
							$class = 'fa-caret-right';
							$accordian_title = 'Expand';
						endif;
					?>
					<i class="fa <?php echo $class; ?>"></i>
					<a tabindex="0" title="<?php echo $accordian_title; ?> Institution Type Menu" class="accordian_section_title">Institution Type</a>
				    </div>
				    <?php echo $institutiontype_option; ?>
				</div>
			</div>
			<?php endif; ?>
			
			<?php if ($_filters['institutionenrollment']==1): ?>
			<div class="srchtrmbxs">
				<div class="cstmaccordian">
				    <div class="cstmaccordiandv">
					<?php
						if(isset($taxonomy) && !empty($taxonomy) && $taxonomy == 'institutionenrollment'):
							$class = 'fa-caret-down';
							$accordian_title = 'Collapse';
						else:
							$class = 'fa-caret-right';
							$accordian_title = 'Expand';
						endif;
					?>
					<i class="fa <?php echo $class; ?>"></i>
					<a tabindex="0" title="<?php echo $accordian_title; ?> Institution Enrollment Menu" class="accordian_section_title">Institution Enrollment</a>
				    </div>
				    <?php echo $institution_option; ?>
				</div>
			</div>
			<?php endif; ?>
		</div>
		<input type="hidden" name="active_level" id="active_level" value="<?php echo $active_level; ?>" />
		</div>    
		
		<?php echo get_story_search($search_text, $taxonomy, $taxonomy_name); ?>
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

	$topic_nav .= '<div class="topic_sidebar"><h4 class="hdng_mtr brdr_mrgn_none stry_topics_header">Topics :</h4><ul>';

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
	$posts_tablename = $wpdb->prefx."posts";
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

function display_story_content($post_id, $limit = 200) {
	$story = "";

	$post = get_post($post_id);

	if ($post->post_excerpt) {
		$story =  $post->post_excerpt;
	} else {
		$content = strip_tags(get_the_content($post_id));
		$story = substr($content, 0, $limit)."...";
	}
	return $story;
}

/** Text-based search on the sidebar **/
function get_story_search($search_text=NULL, $taxonomy=NULL, $taxonomy_name=NULL) {
	$search_value="";

	if (isset($search_text))
		$search_value=' value="'.oet_sanitize($search_text).'"';

	$search_form = '<div class="srchtrmbxs">
				<form action="'.site_url().'/stories/" class="search-form searchform clearfix" method="get" _lpchecked="1">
					<div class="search-wrap">
						<input type="hidden" name="action" value="search">
						<input type="text" placeholder="Search stories..." class="s field" aria-label="search stories" name="search_text"'. $search_value .'>
						<button class="search-icon" type="submit" aria-label="Search"></button>
					</div>
				</form><!-- .searchform -->
			</div>';
	return $search_form;
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

/* not needed for OET. Should pull the Spacious parts out (or use function_exists) before enabling again

function story_entry_meta() {
   if ( 'stories' == get_post_type() ) :
      echo '<footer class="entry-meta-bar clearfix">';
      echo '<div class="entry-meta clearfix">';
      ?>

      <span class="by-author author vcard"><a class="url fn n" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a></span>

      <?php
      $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';
      if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
         $time_string .= '<time class="updated" datetime="%3$s">%4$s</time>';
      }
      $time_string = sprintf( $time_string,
         esc_attr( get_the_date( 'c' ) ),
         esc_html( get_the_date() ),
         esc_attr( get_the_modified_date( 'c' ) ),
         esc_html( get_the_modified_date() )
      );
      printf( __( '<span class="date"><a href="%1$s" title="%2$s" rel="bookmark">%3$s</a></span>', 'spacious' ),
         esc_url( get_permalink() ),
         esc_attr( get_the_time() ),
         $time_string
      ); ?>

      <?php if( has_category() ) { ?>
         <span class="category"><?php the_category(', '); ?></span>
      <?php } ?>

      <?php if ( comments_open() ) { ?>
         <span class="comments"><?php comments_popup_link( __( 'No Comments', 'spacious' ), __( '1 Comment', 'spacious' ), __( '% Comments', 'spacious' ), '', __( 'Comments Off', 'spacious' ) ); ?></span>
      <?php } ?>

      <?php edit_post_link( __( 'Edit', 'spacious' ), '<span class="edit-link">', '</span>' ); ?>

      <?php if ( ( spacious_options( 'spacious_archive_display_type', 'blog_large' ) != 'blog_full_content' ) && !is_single() ) { ?>
         <span class="read-more-link"><a class="read-more" href="<?php the_permalink(); ?>"><?php _e( 'Read more', 'spacious' ); ?></a></span>
      <?php } ?>

      <?php
      echo '</div>';
      echo '</footer>';
   endif;
}
*/

function get_sort_box($post_ids=null){
	
	$base_url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	
	$sort = 0;
	
	?>
	<div class="sort-box">
		<span class="sortoption"></span>
		<span class="sort-story" title="Sort stories"><i class="fa fa-sort" aria-hidden="true"></i></span>
		<div class="sort-options">
			<ul>
				<li data-value="0"<?php if ($sort==0): ?> class="cs-selected"<?php endif; ?>><a href="javascript:void(0);"><span>Newest</span></a></li>
				<li data-value="1"<?php if ($sort==1): ?> class="cs-selected"<?php endif; ?>><a href="javascript:void(0);"><span>Oldest</span></a></li>
				<li data-value="2"<?php if ($sort==2): ?> class="cs-selected"<?php endif; ?>><a href="javascript:void(0);"><span>A-Z</span></a></li>
				<li data-value="3"<?php if ($sort==3): ?> class="cs-selected"<?php endif; ?>><a href="javascript:void(0);"><span>Z-A</span></a></li>
			</ul>
		</div>
		<select class="sort-selectbox" data-posts="<?php echo json_encode($post_ids); ?>" data-base-url="<?php echo $base_url; ?>">
			<option value="0"<?php if ($sort==0): ?>  selected<?php endif; ?>>Newest</option>
			<option value="1"<?php if ($sort==1): ?>  selected<?php endif; ?>>Oldest</option>
			<option value="2"<?php if ($sort==2): ?>  selected<?php endif; ?>>A-Z</option>
			<option value="3"<?php if ($sort==3): ?>  selected<?php endif; ?>>Z-A</option>
		</select>
	</div>
	 <?php
}

function apply_sort_args($args){
	$sort = 0;

	switch($sort){
		case 0:
			$args['orderby'] = 'post_date';
			$args['order'] = 'DESC';
			break;
		case 1:
			$args['orderby'] = 'post_date';
			$args['order'] = 'ASC';
			break;
		case 2:
			$args['orderby'] = 'post_title';
			$args['order'] = 'ASC';
			break;
		case 3:
			$args['orderby'] = 'post_title';
			$args['order'] = 'DESC';
			break;
	}
	return $args;
}
?>
