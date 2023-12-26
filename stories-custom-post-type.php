<?php
/*
 Plugin Name:  Story Custom Post Type
 Plugin URI:   https://www.navigationnorth.com/solutions/wordpress/stories-plugin
 Description:  Stories as a custom post type, with custom metadata and display.
 Version:      0.9.9
 Author:       Navigation North
 Author URI:   http://www.navigationnorth.com
 Text Domain:  wp-stories-posts
 License:      GPL3
 License URI:  https://www.gnu.org/licenses/gpl-3.0.html

 Copyright (C) 2023 Navigation North

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.

 */

//defining the url,path and slug for the plugin
global $wpdb, $characteristics, $districtsize, $_embed, $_backurl;
$characteristics = array('Rural','Suburban','Urban');
$districtsize = array("Less than 1,000 students","1,001-10,000 students","10,001-40,000 students","40,001+ students");

define( 'SCP_URL', plugin_dir_url(__FILE__) );
define( 'SCP_PATH', plugin_dir_path(__FILE__) );
define( 'SCP_SLUG','wp-stories-posts' );
define( 'SCP_FILE',__FILE__);
define( 'SCP_PLUGIN_NAME' , 'Story Custom Post Type' );
define( 'SCP_PLUGIN_INFO' , 'https://www.navigationnorth.com/solutions/wordpress/stories-plugin' );
define( 'SCP_VERSION' , '0.9.9');

include_once(SCP_PATH.'init.php');
include_once(SCP_PATH.'/includes/widgets.php');
include_once( SCP_PATH . '/includes/functions.php' );

/**
 * Stories Shortcode.
 */
include_once( SCP_PATH . '/includes/shortcode.php' );

if ( ! class_exists( 'Recursive_ArrayAccess' ) ) {
	require_once( SCP_PATH.'/classes/class-recursive-arrayaccess.php' );
}

// Only include the functionality if it's not pre-defined.

$_bootstrap = get_option( 'load_bootstrap' );
$_fontawesome = get_option( 'load_font_awesome' );
$_googleapikey = get_option( 'google_api_key' );
$_filters = array(
	"program" => get_option('enable_program'),
	"state" => get_option('enable_state'),
	"grade_level" => get_option('enable_grade_level'),
	"characteristics" => get_option('enable_characteristics'),
	"district_size" => get_option('enable_district_size'),
	"institutionenrollment" => get_option('enable_institution_enrollment'),
	"institutiontype" => get_option('enable_institution_type')
		  );
$_embed = get_option('enable_embed');

//plugin activation task
function create_installation_table()
{
	global $wpdb;

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	//table that will record data of units
	$table_name = $wpdb->prefix . "scp_stories";
	$sql = "CREATE TABLE IF NOT EXISTS ".$table_name ."(
		id int(20) NOT NULL AUTO_INCREMENT,
		postid int(20),
		title varchar(255),
		content varchar(255),
		image varchar(255),
		longitude varchar(255),
		latitude varchar(255),
		PRIMARY KEY (id));";
    dbDelta($sql);

	//check if the menu_order column exists;
	$query = "SHOW COLUMNS FROM $wpdb->terms LIKE 'term_order'";
	$result = $wpdb->query($query);

	if ($result == 0)
	{
		$query = "ALTER TABLE $wpdb->terms ADD `term_order` INT( 4 ) NULL DEFAULT '0'";
		$result = $wpdb->query($query);
	}

	//make sure the vars are set as default
	$options = get_option('scp_options');
	if (!isset($options['autosort']))
		$options['autosort'] = '1';

	if (!isset($options['adminsort']))
		$options['adminsort'] = '1';

	if (!isset($options['level']))
		$options['level'] = 8;

	update_option('scp_options', $options);
}
register_activation_hook(__FILE__, 'create_installation_table');

//Load localization directory
add_action('plugins_loaded', 'load_story_textdomain');
function load_story_textdomain() {
	load_plugin_textdomain( 'wp-stories-posts', false, dirname( plugin_basename(__FILE__) ) . '/lang/' );
}

//scripts and styles on backend
add_action('admin_enqueue_scripts', 'scp_backside_scripts');
function scp_backside_scripts()
{
	wp_enqueue_style('thickbox');
	wp_enqueue_style('back-styles', SCP_URL.'css/back_styles.css');
	wp_enqueue_style('ordercss', SCP_URL.'css/order.css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('jquery-ui-sortable');
	wp_enqueue_script('back-scripts', SCP_URL.'js/back_scripts.js');
	wp_enqueue_script('orderjs', SCP_URL.'js/order.js');
}

//scripts and styles on front end
add_action('wp_enqueue_scripts', 'scp_frontside_scripts');
function scp_frontside_scripts()
{
	global $_bootstrap,  $_fontawesome, $post;

	wp_enqueue_style('front-styles', SCP_URL.'css/front_styles.css');
	wp_enqueue_style('bxslider-styles', SCP_URL.'css/jquery.bxslider.css');

	if ($_bootstrap) {
		wp_enqueue_style('bootstrap-style', SCP_URL.'css/bootstrap.min.css');
	}
	if ($_fontawesome) {
		wp_enqueue_style('fontawesome-style', SCP_URL.'css/font-awesome.min.css');
	}

	wp_enqueue_script('jquery');
	$_cstmPostType = (isset($post->post_type))?$post->post_type:'';
	if ($_cstmPostType=="stories") {
		wp_enqueue_script('front-scripts', SCP_URL.'js/front_scripts.js');
	}
	wp_enqueue_script('bxslider-scripts', SCP_URL.'js/jquery.bxslider.min.js');

	if ($_bootstrap) {
		wp_enqueue_script('bootstrap-script', SCP_URL.'js/bootstrap.min.js');
	}
}

/*Enqueue ajax url on frontend*/
add_action('wp_head','stories_ajaxurl', 8);
function stories_ajaxurl()
{
	?>
    <script type="text/javascript">
    /* workaround to only use SSL when on SSL (avoid self-signed cert issues) */
    <?php if (!strpos($_SERVER['REQUEST_URI'],"wp-admin")): ?>
	var sajaxurl = '<?php echo SCP_URL ?>ajax.php';
    <?php else: ?>
	var sajaxurl = '<?php echo admin_url("admin-ajax.php", (is_ssl() ? "https": "http") ); ?>
    <?php endif; ?>
    </script>
<?php
}

add_action('admin_menu', 'taxonomy_order', 99);
function taxonomy_order()
{
	include (SCP_PATH . 'includes/interface.php');
	include (SCP_PATH . 'includes/terms_walker.php');

	$options = get_option('scp_options');

	if (!isset($options['level']))
		$options['level'] = 8;

	 //put a menu within all custom types if apply
	$post_types = array('stories' => 'stories');//get_post_types();
	foreach( $post_types as $post_type)
	{

		//check if there are any taxonomy for this post type
		$post_type_taxonomies = get_object_taxonomies($post_type);

		foreach ($post_type_taxonomies as $key => $taxonomy_name)
		{
			$taxonomy_info = get_taxonomy($taxonomy_name);
			if ($taxonomy_info->hierarchical !== TRUE)
				unset($post_type_taxonomies[$key]);
		}

		if (count($post_type_taxonomies) == 0)
			continue;

		if ($post_type == 'post')
			add_submenu_page('edit.php', __('Taxonomy Order', SCP_SLUG), __('Taxonomy Order', SCP_SLUG), 'level_'.$options['level'], 'ordersmenu-'.$post_type, 'ordersmenu' );
		else
			add_submenu_page('edit.php?post_type='.$post_type, __('Taxonomy Order', SCP_SLUG), __('Taxonomy Order', SCP_SLUG), 'level_'.$options['level'], 'ordersmenu-'.$post_type, 'ordersmenu' );
	}
}

add_filter('get_terms_orderby', 'applyorderfilter', 10, 2);
function applyorderfilter($orderby, $args)
{
	$options = get_option('scp_options');

	//if admin make sure use the admin setting
	if (is_admin())
	{
		if ($options['adminsort'] == "1")
			return 't.term_order';

		return $orderby;
	}
	//if autosort, then force the menu_order
	/*if ($options['autosort'] == 1)
	{
		return 't.term_order';
	}*/
	return $orderby;
}

add_filter('get_terms_orderby', 'getterms_orderby', 1, 2);
function getterms_orderby($orderby, $args)
{
	if (isset($args['orderby']) && $args['orderby'] == "term_order" && $orderby != "term_order")
		return "t.term_order";

	return $orderby;
}

add_action( 'wp_ajax_update-taxonomy-order', 'saveajaxorder' );
function saveajaxorder()
{
	global $wpdb;
	$taxonomy = stripslashes($_POST['taxonomy']);
	$data = stripslashes($_POST['order']);
	$unserialised_data = unserialize($data);

	if (is_array($unserialised_data))
	foreach($unserialised_data as $key => $values )
	{
		//$key_parent = str_replace("item_", "", $key);
		$items = explode("&", $values);
		unset($item);
		foreach ($items as $item_key => $item_)
		{
			$items[$item_key] = trim(str_replace("item[]=", "",$item_));
		}

		if (is_array($items) && count($items) > 0)
			foreach( $items as $item_key => $term_id )
			{
				$wpdb->update( $wpdb->terms, array('term_order' => ($item_key + 1)), array('term_id' => $term_id) );
			}
	}
	die();
}

//filte template for front end
add_filter( 'template_include', 'scp_template_loader' );
function scp_template_loader($template)
{
	global $wp_query;

	$file = '';

	/*
    if ($wp_query->is_search)
	{
		$file = 'content-search.php';
		$path = SCP_PATH."templates/".$file;
	}
	else
    */
    if ( is_single() && get_post_type() == 'stories' )
	{
		$file  = 'single-stories.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif ( is_tax( 'program' ) && get_post_type() == 'stories' )
	{
		$term   = get_queried_object();
		$file = 'taxonomy-' . $term->taxonomy . '.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif ( is_tax( 'state' ) && get_post_type() == 'stories' )
	{
		$term   = get_queried_object();
		$file = 'taxonomy-' . $term->taxonomy . '.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif ( is_tax( 'grade_level' ) && get_post_type() == 'stories' )
	{
		$term   = get_queried_object();
		$file = 'taxonomy-' . $term->taxonomy . '.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif ( is_tax( 'story_tag' ) && get_post_type() == 'stories' )
	{
		$term   = get_queried_object();
		$file = 'taxonomy-' . $term->taxonomy . '.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif ( is_tax( 'characteristics' ) && get_post_type() == 'stories' )
	{
		$term   = get_queried_object();
		$file = 'taxonomy-' . $term->taxonomy . '.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif ( is_tax( 'districtsize' ) && get_post_type() == 'stories' )
	{
		$term   = get_queried_object();
		$file = 'taxonomy-' . $term->taxonomy . '.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif ( is_tax( 'institutionenrollment' ) && get_post_type() == 'stories' )
	{
		$term   = get_queried_object();
		$file = 'taxonomy-' . $term->taxonomy . '.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif ( is_tax( 'institutiontype' ) && get_post_type() == 'stories' )
	{
		$term   = get_queried_object();
		$file = 'taxonomy-' . $term->taxonomy . '.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif (is_post_type_archive( 'stories' ))
	{
		$file 	= 'archive-stories.php';
		$path  = SCP_PATH."templates/".$file;
	}
	elseif(is_tax('program') || is_tax('state') || is_tax('story_tag') || is_tax('grade_level') || is_tax('characteristics') || is_tax('districtsize'))
	{
		$file 	= 'archive-404.php';
		$path  = SCP_PATH."templates/".$file;
	}

	if ( isset($path) && !empty($path) )
	{
		$template = $path;
	}

	return $template;
}
//Function for getting map
function get_storiesmap($pageposts=NULL)
{
	global $wpdb, $_googleapikey;
	$stateurl = "";
	$postid = "";
	$story_table = $wpdb->prefix . "scp_stories";
    $post_table = $wpdb->prefix . "posts";
	$sql = "SELECT S.id, S.postid, S.title, S.latitude, S.longitude, S.image, S.content, P.post_excerpt
        FROM $story_table S INNER JOIN $post_table P ON P.ID = S.postid
        WHERE S.latitude <> '' AND S.longitude <> ''";

	if(empty($pageposts) || $pageposts == NULL)
	{
		$stories = $wpdb->get_results($sql);
	}
	else
	{
		foreach($pageposts  as $ids)
		{
			$postid .= $ids.",";
		}
		$postid = trim($postid, ",");
		$sql .= " AND postid IN ($postid)";
		$stories = $wpdb->get_results($sql);
		//print_r($stories);
	}
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo SCP_URL ; ?>css/demo.css" />
   	<script src="//maps.google.com/maps/api/js?key=<?php echo $_googleapikey ; ?>" type="text/javascript"></script>
    <div class="mapcontainer">
         <div id="ss-container" class="ss-container">
         	<div id="map_canvas">
                <div id="map">

                </div>
            </div>
         </div>
     </div>
   			<script type="text/javascript" src="<?php echo SCP_URL ; ?>js/jquery.a11yfy.gmaps.js"></script>
            <script type="text/javascript">
                    var locations = [
                        <?php
                            if (isset($stories) && !empty($stories))
							{
								foreach ($stories as $story)
								{
									$pincolor = "#294179";
									$story_status = get_post_status($story->postid);
									$id = $story->id;
									$title = $story->title;
									$latitude = $story->latitude;
									$longitude = $story->longitude;
									$image = $story->image;
									if (has_post_thumbnail($story->postid)) {
										$image = get_the_post_thumbnail_url($story->postid, 'thumbnail');
									}
									$content = $story->post_excerpt ? $story->post_excerpt : $story->content;
									$link = get_the_permalink($story->postid)."?back=".urlencode($_SERVER['REQUEST_URI']);

									if(!empty($content))
									{
										$content = substr(addslashes($content), 0 ,95)."... <a href=$link>Read More</a>";
									}

									$district = get_post_meta($story->postid,"story_district",true);
									$states = get_the_terms( $story->postid, "state" );
									if(isset($states) && !empty($states))
									{
										foreach($states as $state)
										{
											$url = get_term_link($state->term_id, $state->taxonomy);
											//$stateurl = '<a target="_blank" href="'. $url .'">'.$state->name.'</a>';
									$stateurl = $state->name;
										}
									}

									$grades = get_the_terms( $story->postid , 'grade_level' );

									if(isset($grades) && !empty($grades))
									{
										if (count($grades)>1) {

											if ( class_exists('WPSEO_Primary_Term') ) {

												$primary_term = new WPSEO_Primary_Term('grade_level', $story->postid);
												$primary_term = $primary_term->get_primary_term();

												$term = get_term($primary_term, 'grade_level');

												if (is_wp_error($term)) {
													$pincolor = get_map_pin_color($grades);
												} else {
													if ($term->name=="Higher Education"  || $term->name=="Postsecondary"  || strcmp(html_entity_decode($term->name),"Higher & Adult Ed")==0) {
														$pincolor = "#e57200";
													} else {
														$pincolor = "#294179";
													}
												}
											} else {
												$pincolor = get_map_pin_color($grades);
											}
										} else {
											$pincolor = get_map_pin_color($grades);
										}
									}
									$district = trim(addslashes($district));
									$title = addslashes($title);
									$content = addslashes($content);
									if ($story_status == 'publish') {
										if($image) {
											echo "['<div class=info tabindex=0><h4><a href=$link>$title</a></h4><div class=popupcntnr><img src=$image alt=\"Story Thumbnail\"><div class=subinfo><p><b>$district</b>, <b>$stateurl</b></p></div>$content</div></div>', $latitude, $longitude, '$title - $story->postid', '$pincolor'],";
										} else {
											echo "['<div class=info tabindex=0><h4><a href=$link>$title</a></h4><div class=\'popupcntnr fullpopwidth\'><div class=subinfo><p><b>$district</b>, <b>$stateurl</b></p></div>$content</div></div>', $latitude, $longitude, '$title - $story->postid', '$pincolor'],";
										}
									}
								}
							}
							else
							{
								echo "\"<h3 align='center'><font color='#ff0000'>No Content Found</font></h3>\"";
							}
                        ?>];

                    // Setup the different icons and shadows
                    var iconURLPrefix = '<?php echo SCP_URL.'images/'?>';

                    var icons = [iconURLPrefix + 'marker_solid.png', iconURLPrefix + 'marker_orange.png']
                    var icons_length = icons.length;

                    var shadow =
                    {
                      anchor: new google.maps.Point(5,13),
                      url: iconURLPrefix + 'msmarker.shadow.png'
                    };

                    var map = new google.maps.Map(document.getElementById('map'), {
                      zoom: -5,
                      center: new google.maps.LatLng(40.715618, -74.011133),
                      mapTypeId: google.maps.MapTypeId.ROADMAP,
                      mapTypeControl: true,
                      streetViewControl: true,
                      disableDefaultUI: false,
                      panControl: true,
                      zoomControlOptions: {
                      position: google.maps.ControlPosition.LEFT_BOTTOM
                      }
                    });
                	map.set('scrollwheel', false);
					var isScrollWheelEnabled = map.get('scrollwheel');

                    var infowindow = new google.maps.InfoWindow({
                      maxWidth: 400,
					  Width: 400,
					  Height: 350,
                      maxHeight: 350
                    });

                    var iconSVG = {
                        path: "m 51.181656,3.9153604 c -16.876105,0 -30.606194,15.0340656 -30.606194,33.5151036 0,4.674622 1.254665,13.195031 12.10588,33.135792 5.549381,10.196603 11.020402,18.615602 11.251034,18.969563 l 7.24928,11.129771 7.250171,-11.129771 C 58.661567,89.182832 64.132588,80.762859 69.68286,70.566256 80.534074,50.625495 81.788741,42.105086 81.788741,37.430464 81.78785,18.949426 68.057761,3.9153604 51.181656,3.9153604 Z",
                        fillColor: "#294179",
                        fillOpacity: 1,
                        anchor: new google.maps.Point(54,100),
                        strokeWeight: 2,
                        strokeColor: '#cccccc',
                        scale: 1/3
                    }

                    var marker;
                    var markers = new Array();

                    var iconCounter = 0;

                    function AutoCenter()
                    {
                      	var bounds = new google.maps.LatLngBounds();
                      	jQuery.each(markers, function (index, marker)
						{
							bounds.extend(marker.position);
						});
                    	map.fitBounds(bounds);
                    	if (markers.length==1) {
	                      	var listener = google.maps.event.addListener(map, "idle", function() {
								  if (map.getZoom() > 16) map.setZoom(16);
								  google.maps.event.removeListener(listener);
							});
						}
                    }

                    // Add the markers and infowindows to the map
                    if (jQuery.isArray(locations[0]) && locations.length>0) {
	                    for (var i = 0; i < locations.length; i++)
	                    {
	                    	if (typeof locations[i][4] !== 'undefined')
								iconSVG.fillColor = locations[i][4];

							if (typeof locations[i][1] !== 'undefined')	{
								marker = new google.maps.Marker({
									//position: new google.maps.LatLng(locations[i][1], locations[i][2], locations[i][3], locations[i][4], locations[i][5]),
									position: new google.maps.LatLng(locations[i][1], locations[i][2]),
									map: map,
									title : locations[i][3],
									icon : iconSVG,
									shadow: shadow
						    	});

	                          	markers.push(marker);

		                        google.maps.event.addListener(marker, 'click', (function(marker, i)
		                        {
		                            return function() {
		                              infowindow.setContent(locations[i][0]);
		                              infowindow.open(map, marker);
		                            }
		                        })(marker, i));

		                        iconCounter++;
		                        if(iconCounter >= icons_length)
		                        {
		                            iconCounter = 0;
		                        }
	                        }
	                    }
	                    AutoCenter();
	                } else {
	                	map.setCenter(new google.maps.LatLng(40.812955, -100.907613));
	                	map.setZoom(4);
	            	}

                    // load the accessibility hacks for the map
                    jQuery(document).gmaps();
              </script>

    <?php
}
//Shortcode for tips
//example : [tips title="your title"]your html content[/tips]
add_shortcode("tips", "tips_function");
function tips_function($attr, $content=NULL)
{
	extract($attr);
	if(isset($title) && !empty($title))
	{
		$title = '<h4>'.$title.'</h4>';
	}
	else
	{
		$title = '';
	}
	$return = '<div class="col-md-12 col-sm-12 col-xs-12 blubrdr">
               		'.$title.'
					'.$content.'
			   </div>';
	return $return;
}

/**
 * Add Settings Submenu
 **/
add_action( 'admin_menu' , 'story_settings_menu', 100 );
function story_settings_menu() {
	add_submenu_page(
			 'edit.php?post_type=stories',
			 __( 'Stories Custom Post Type Settings' , SCP_SLUG ),
			 __( 'Settings' , SCP_SLUG ),
			 'manage_options',
			 'stories-settings-page',
			 'show_settings_page'
			 );
}

/**
 * Add Settings link on Plugins page
 **/
add_filter( "plugin_action_links_" . plugin_basename(__FILE__) , 'plugin_add_settings_link', 0 );
function plugin_add_settings_link( $links ) {
    $settings_link = '<a href="edit.php?post_type=stories&page=stories-settings-page">' . __( 'Settings', SCP_SLUG ) . '</a>';
    array_unshift( $links, $settings_link );
  	return $links;
}

/**
 * Add options on Settings page
 **/
add_action( 'admin_init' , 'setup_settings_form' );
function setup_settings_form() {
	add_settings_section(
			     'stories-settings-section',
			     '',
			     'first_section_callback',
			     'stories-settings-page'
			     );
	add_settings_field(
			'load_bootstrap',
			__( 'Load Bootstrap?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'load_bootstrap',
				'type' => 'checkbox',
				'description' => __('necessary for display if your WP theme does not use bootstrap', SCP_SLUG)
			)
			   );
	add_settings_field(
			'load_font_awesome',
			__( 'Load Font Awesome?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'load_font_awesome',
				'type' => 'checkbox',
				'description' => __('necessary for display if your WP theme does not load font awesome', SCP_SLUG)
			)
			   );
	add_settings_field(
			'google_api_key',
			__( 'Google API Key:' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'google_api_key',
				'type' => 'textbox',
				'description' => __('necessary for displaying map', SCP_SLUG)
			)
			   );

	/* Enable/Disable State Selection */
	add_settings_field(
			'enable_state',
			__( 'Enable State Selection?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_state',
				'type' => 'checkbox',
				'description' => __('necessary to display the State filter on the sidebar', SCP_SLUG)
			)
			   );
	/* Enable/Disable Grade Level */
	add_settings_field(
			'enable_grade_level',
			__( 'Enable Level?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_grade_level',
				'type' => 'checkbox',
				'description' => __('necessary to display the Level filter on the sidebar', SCP_SLUG)
			)
			   );
	/* Enable/Disable Characteristics */
	add_settings_field(
			'enable_characteristics',
			__( 'Enable Community Type?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_characteristics',
				'type' => 'checkbox',
				'description' => __('necessary to display the Community Type filter on the sidebar', SCP_SLUG)
			)
			   );
	/* Enable/Disable District Size */
	add_settings_field(
			'enable_district_size',
			__( 'Enable District Enrollment?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_district_size',
				'type' => 'checkbox',
				'description' => __('necessary to display the District Enrollment filter on the sidebar', SCP_SLUG)
			)
			   );
	/* Enable/Disable Institution Enrollment */
	add_settings_field(
			'enable_institution_enrollment',
			__( 'Enable Institution Enrollment?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_institution_enrollment',
				'type' => 'checkbox',
				'description' => __('necessary to display the Institution Enrollment filter on the sidebar', SCP_SLUG)
			)
			   );
	/* Enable/Disable Institution Type */
	add_settings_field(
			'enable_institution_type',
			__( 'Enable Institution Type?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_institution_type',
				'type' => 'checkbox',
				'description' => __('necessary to display the Institution Type filter on the sidebar', SCP_SLUG)
			)
			   );
	/* Enable Embed */
	add_settings_field(
			'enable_embed',
			__( 'Allow External Embedding?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_embed',
				'type' => 'checkbox',
				'description' => __('displays external embedding on the Share This widget', SCP_SLUG)
			)
			   );

	/* Enable YouTube Check */
	add_settings_field(
			'enable_youtube_check',
			__( 'Enable YouTube Checking?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_youtube_check',
				'type' => 'checkbox',
				'description' => __('enable YouTube Url validation', SCP_SLUG)
			)
			   );

	/* Enable Vimeo Thumbnail Fetching */
	add_settings_field(
			'enable_vimeo_thumbnail',
			__( 'Enable Vimeo Thumbnail Fetching?' , SCP_SLUG ),
			'setup_settings_field',
			'stories-settings-page',
			'stories-settings-section',
			array(
				'uid' => 'enable_vimeo_thumbnail',
				'type' => 'checkbox',
				'description' => __('enable fetching of Vimeo Thumbnail via API', SCP_SLUG)
			)
			   );

	/* Enable Archive Notice */
	add_settings_field(
		'enable_archive_notice',
		__( 'Archive Notice Enabled?' , SCP_SLUG ),
		'setup_settings_field',
		'stories-settings-page',
		'stories-settings-section',
		array(
			'uid' => 'enable_archive_notice',
			'type' => 'checkbox',
			'description' => __('display the archive notice on the main page of the Stories plugin', SCP_SLUG)
		)
	);

	/* Archive Notice Content */
	add_settings_field(
		'archive_notice_content',
		__( 'Archive Notice Content:' , SCP_SLUG ),
		'setup_settings_field',
		'stories-settings-page',
		'stories-settings-section',
		array(
			'uid' => 'archive_notice_content',
			'type' => 'textarea',
			'description' => __('content of the archive notice to display', SCP_SLUG)
		)
	);

	register_setting( 'stories-settings-section' , 'load_bootstrap' );
	register_setting( 'stories-settings-section' , 'load_font_awesome' );
	register_setting( 'stories-settings-section' , 'google_api_key' );
	register_setting( 'stories-settings-section' , 'enable_state' );
	register_setting( 'stories-settings-section' , 'enable_grade_level' );
	register_setting( 'stories-settings-section' , 'enable_characteristics' );
	register_setting( 'stories-settings-section' , 'enable_district_size' );
	register_setting( 'stories-settings-section' , 'enable_institution_enrollment' );
	register_setting( 'stories-settings-section' , 'enable_institution_type' );
	register_setting( 'stories-settings-section' , 'enable_embed' );
	register_setting( 'stories-settings-section' , 'enable_youtube_check' );
	register_setting( 'stories-settings-section' , 'enable_vimeo_thumbnail' );
	register_setting( 'stories-settings-section' , 'enable_archive_notice' );
	register_setting( 'stories-settings-section' , 'archive_notice_content' );
}

function first_section_callback() {

}

function setup_settings_field( $arguments ) {
	$selected = "";
	$size = "";
	$chkClass = "";
	$textareasize = 'rows="5" cols="60"';

	$value = get_option($arguments['uid']);

	if ($arguments['type']=="textbox") {
		$size = 'size="60"';
	}

	if ($arguments['type']=="checkbox"){
		$chkClass = " cb-desc";
		if ($value==1 || $value=="on")
			$selected = "checked='checked'";
		else{
			$value = 1;
		}
	}

	if ($arguments['type']=="textarea") {
		echo '<textarea name="'.$arguments['uid'].'" id="'.$arguments['uid'].'" '.$textareasize.'>'.$value.'</textarea>';
	} else {
		echo '<input name="'.$arguments['uid'].'" id="'.$arguments['uid'].'" type="'.$arguments['type'].'" value="' . $value . '" ' . $size . ' ' .  $selected . ' />';
	}

	//Show Helper Text if specified
	if (isset($arguments['helper'])) {
		$helper = $arguments['helper'];
		printf( '<span class="helper"> %s</span>' , $helper );
	}

	//Show Description if specified
	if( $description = $arguments['description'] ){
		printf( '<p class="description%s">%s</p>', $chkClass, $description );
	}
}

/* load ajax script */
function load_ajax_script(){
	wp_enqueue_script( "ajax-script", plugin_dir_url(__FILE__)."js/front-ajax.js", array("jquery"));
	//wp_localize_script( 'ajax-script', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
add_action('wp_print_scripts', 'load_ajax_script');

/* Ajax Callback */
function load_more_stories() {
	global $wpdb, $wp_query, $_backurl, $_mobile;

	if (isset($_POST["post_var"])) {
		$page_num = $_POST["post_var"];

		$args = array(
				'post_type' => 'stories',
				'posts_per_page' => 10,
				'post_status' => 'publish',
				'paged' => $page_num
				);

		if (isset($_POST['post_ids'])) {
			$post_ids = json_decode($_POST['post_ids']);
			$args['post__in'] = $post_ids;
		}

		//Sorting Results
		$sort = 0;

		if (isset($_POST['sort']))
			$sort = (int)$_POST['sort'];


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


		$postquery = new WP_Query($args);

		$_backurl = $_POST['back_url'];

		$_mobile = $_POST['mobile'];

		while ( $postquery->have_posts() ) : $postquery->the_post();
		    get_story_template_part( 'content', 'substory' );
		endwhile;
		die();
	}
}
add_action('wp_ajax_load_more', 'load_more_stories');
add_action('wp_ajax_nopriv_load_more', 'load_more_stories');

/** Sort Stories **/
function sort_stories(){
	global $wpdb, $_backurl;

	if (isset($_POST["sort"])) {

		$stories = new WP_Query(array('post_type' => 'stories', 'posts_per_page' => -1));

                $post_ids = wp_list_pluck( $stories->posts, 'ID' );

		$post_count = count($post_ids);

		$args = array('post_type' => 'stories', 'posts_per_page' => 10);

		switch($_POST["sort"]){
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

		$max_stories = new WP_Query($args);
		$max_page = $max_stories->max_num_pages;

		$paged = 1;
		if ($_POST['post_var']){
			$paged = (int)$_POST['post_var'];
		}

		if ($_REQUEST['page'])
			$paged = (int)$_REQUEST['page'];

		$args['posts_per_page'] = 10 * $paged;

		if (isset($_POST['post_ids']))
			$args['post__in'] = json_decode($_POST['post_ids']);;

		// Taxonomy/Term filter
		if($_POST["taxonomy"] && $_POST["term"])
		{
			$searcharr = array('taxonomy' => $_POST["taxonomy"], 'field' => 'slug', 'terms' => $_POST["term"]);
			$args['tax_query'] = array($searcharr);
		}

		$postquery = new WP_Query($args);

		$_backurl = $_POST['back_url'];

		while ( $postquery->have_posts() ) : $postquery->the_post();
		    get_story_template_part( 'content', 'substory' );
		endwhile;

		die();
	}
}
add_action('wp_ajax_sort_stories', 'sort_stories');
add_action('wp_ajax_nopriv_sort_stories', 'sort_stories');


/**
 * Override spacious_header_title function applicable only to Spacious Theme
 **/
function spacious_header_title() {
	if( is_archive() ) {
		if (get_post_type()=='stories'):
		    $spacious_header_title = __( 'Stories of EdTech Innovation', SCP_SLUG );
		else:
		    $spacious_header_title = __( 'Archives',  SCP_SLUG );
		endif;
	}
	elseif( is_404() ) {
		$spacious_header_title = __( 'Page NOT Found', SCP_SLUG );
	}
	elseif( is_search() ) {
		$spacious_header_title = __( 'Search Results', SCP_SLUG );
	}
	elseif( is_page()  ) {
		$spacious_header_title = get_the_title();
	}
	elseif( is_single()  ) {
		if (get_post_type()=='stories'):
			$spacious_header_title = __( 'Stories of EdTech Innovation', SCP_SLUG );
	    //$spacious_header_title = __("Stories: ", SCP_SLUG) . get_the_title();
		else:
			$spacious_header_title = get_the_title();
		endif;
	}
	elseif( is_home() ){
		$queried_id = get_option( 'page_for_posts' );
		$spacious_header_title = get_the_title( $queried_id );
	}
	else {
		$spacious_header_title = '';
	}

	return $spacious_header_title;

}

/** Check if theme used is Spacious then allow hiding of title in story page **/
function title_can_be_hidden(){
	$hidden = false;

	$current_theme = wp_get_theme();

	if ($current_theme['Name']=="Spacious Child")
		$hidden = true;

	return $hidden;
}

/** Register Widgets **/
function register_stories_widgets(){
	register_widget( 'WP_Widget_Recent_Stories' );
	register_widget( 'WP_Widget_Single_Story' );
}
add_action( 'widgets_init', 'register_stories_widgets' );

/** Add Excerpt on Story Editor - Backend **/
add_action( 'init', 'add_excerpts_to_stories' );
function add_excerpts_to_stories() {
     add_post_type_support( 'stories', 'excerpt' );
}
?>
