<?php
/*
 Plugin Name: Story Custom Post Type
 Plugin URI: http://www.navigationnorth.com/wordpress/stories-plugin
 Description: Stories as a custom post type, with custom metadata and display. Developed in collaboration with Monad Infotech (http://monadinfotech.com)
 Version: 0.2.2
 Author: Navigation North
 Author URI: http://www.navigationnorth.com

 Copyright (C) 2015 Navigation North

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
global $wpdb, $characteristics, $districtsize;
$characteristics = array('Rural','Suburban','Urban');
$districtsize = array("Less than 1,000 students","1,001-10,000 students","10,001-40,000 students","40,001+ students");

define( 'SCP_URL', plugin_dir_url(__FILE__) );
define( 'SCP_PATH', plugin_dir_path(__FILE__) );
define( 'SCP_SLUG','story-custom-posttype' );
define( 'SCP_FILE',__FILE__);

include_once(SCP_PATH.'init.php');

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
	wp_enqueue_style('front-styles', SCP_URL.'css/front_styles.css');
	wp_enqueue_style('bxslider-styles', SCP_URL.'css/jquery.bxslider.css');

	wp_enqueue_script('jquery');
	wp_enqueue_script('front-scripts', SCP_URL.'js/front_scripts.js');
	wp_enqueue_script('bxslider-scripts', SCP_URL.'js/jquery.bxslider.min.js');

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
			add_submenu_page('edit.php', __('Taxonomy Order', 'scp'), __('Taxonomy Order', 'scp'), 'level_'.$options['level'], 'ordersmenu-'.$post_type, 'ordersmenu' );
		else
			add_submenu_page('edit.php?post_type='.$post_type, __('Taxonomy Order', 'scp'), __('Taxonomy Order', 'scp'), 'level_'.$options['level'], 'ordersmenu-'.$post_type, 'ordersmenu' );
	}
}

add_action( 'wp_ajax_update-custom-type-order-hierarchical', array(&$this, 'saveAjaxOrderHierarchical') );

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
	$file = '';

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
	global $wpdb;
	$table_name = $wpdb->prefix . "scp_stories";
	if(empty($pageposts) || $pageposts == NULL)
	{
		$stories = $wpdb->get_results("select * from $table_name");
	}
	else
	{
		foreach($pageposts  as $ids)
		{
			$postid .= $ids.",";
		}
		$postid = trim($postid, ",");
		$stories = $wpdb->get_results("select * from $table_name where postid IN ($postid)");
	}
	?>
	<link rel="stylesheet" type="text/css" href="<?php echo SCP_URL ; ?>css/demo.css" />
   	<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
    <div class="mapcontainer">
         <div id="ss-container" class="ss-container">
         	<div id="map_canvas">
                <div id="map">

                </div>
            </div>
         </div>
     </div>
   			<script type="text/javascript">
                    var locations = [
                        <?php
                            if (isset($stories) && !empty($stories))
							{
								foreach ($stories as $story)
								{
									$id = $story->id;
									$title = $story->title;
									$latitude = $story->latitude;
									$longitude = $story->longitude;
									$image = $story->image;
									$content = $story->content;
									if(!empty($content))
									{
										$content = substr($content, 0 ,60);
										$content = $content."...";
									}
									$link = get_the_permalink($story->postid)."?back=".urlencode($_SERVER['REQUEST_URI']);
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
									echo "['<div class=info><h4><a href=$link>$title</a></h4><div class=popupcntnr><img src=$image alt=\"Story Thumbnail\"><div class=subinfo><p><b>$district</b>, <b>$stateurl</b></div></p>$content</div></div>', $latitude, $longitude],";
								}
							}
							else
							{
								echo "<h3 align='center'><font color='#ff0000'>No Content Found</font></h3>";
							}
                        ?>];

                    // Setup the different icons and shadows
                    var iconURLPrefix = '<?php echo SCP_URL.'images/'?>';

                    var icons = [iconURLPrefix + 'marker_solid.png']
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
                        fillColor: '#00529f',
                        fillOpacity: 1,
                        anchor: new google.maps.Point(54,100),
                        strokeWeight: 2,
                        strokeColor: '#cccccc',
                        scale: 1/3
                    }

                    var marker;
                    var markers = new Array();

                    var iconCounter = 0;

                    // Add the markers and infowindows to the map
                    for (var i = 0; i < locations.length; i++)
                    {
						  marker = new google.maps.Marker({
                            position: new google.maps.LatLng(locations[i][1], locations[i][2], locations[i][3], locations[i][4], locations[i][5]),
                            map: map,
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

                    function AutoCenter()
                    {
                      var bounds = new google.maps.LatLngBounds();
                      jQuery.each(markers, function (index, marker)
                      {
                        bounds.extend(marker.position);
                      });
                      map.fitBounds(bounds);
                    }
                    AutoCenter();
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
?>