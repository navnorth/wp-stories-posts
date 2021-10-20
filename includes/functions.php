<?php
/**
 *
 * Contains functions that can be used throughout the plugin
 *
 **/
/**
 * Get Title by Id
 **/
function get_title_by_id($id) {
    global $wpdb;
    
    $title = get_the_title($id);
    
    return $title;
}

/**
 * Get Background Image
 **/
function get_background($id) {
    $background_image_url = "";
    
    if (has_post_thumbnail($id)){
        $bg_url = wp_get_attachment_image_src(get_post_thumbnail_id($id), 'full');
        $background_image_url = $bg_url[0];
    } elseif(has_youtube_video($id)){
        $youtubeID = get_videoID($id);
        $background_image_url = get_youtube_image($youtubeID);
    } elseif(has_vimeo_video($id)){
        $vimeoID = get_videoID($id);
        $background_image_url = get_vimeo_image($vimeoID);
    } else {
        $background_image_url = SCP_URL . "images/top_strap_img.jpg";
    }
    
    return $background_image_url;
}

/**
 * Checks if story has youtube video
 **/
function has_youtube_video($id) {
    $has_video = false;
    
    $video = get_post_meta($id,'story_video_host',true);
    
    if ($video=="1"){
        $has_video = true;
    }
    return $has_video;
}

/**
 * Checks if story has vimeo video
 **/
function has_vimeo_video($id){
    $has_vimeo = false;
    
    $video = get_post_meta($id,'story_video_host',true);
    
    if ($video=="2"){
        $has_vimeo = true;
    }
    return $has_vimeo;
}

/**
 * Get Youtube ID from embedded code
 **/
function get_videoID($id) {
    $videoID = null;
    
    $videoID = get_post_meta( $id, 'story_video', true);
    
    return $videoID;
}

/**
 * Get Youtube Image
 **/
function get_youtube_image($youtube_id) {
    $youtube_url = "//img.youtube.com/vi/$youtube_id/maxresdefault.jpg";
    return $youtube_url;
}

/**
 * Get Vimeo Image
 **/
function get_vimeo_image($vimeo_id) {
    $vimeo = unserialize(file_get_contents("http://vimeo.com/api/v2/video/$vimeo_id.php"));
    
    $vimeo_url = $vimeo[0]['thumbnail_large'];
    
    $vimeo_url = str_replace( "_640", "", $vimeo_url );
    
    $vimeo_url = str_replace( "http:" , "", $vimeo_url );
    
    return $vimeo_url;
}

/**
 * Get content from ID
 **/
function get_story_excerpt_from_id($id) {
    $char_limit = 130;
    $story = "";
    
    $content = get_post($id);
    
    if ($content->post_excerpt){
        $story = $content->post_excerpt;
    }
    else {
        $array = preg_split('/(.*?[?!.](?=\s|$)).*/', $content->post_content, -1, PREG_SPLIT_DELIM_CAPTURE);
        $story = $array[1];
    }
    
    //
    $ellipsis = "...";
    if (strlen($story)<$char_limit)
        $ellipsis = "";

    $story = substr($story, 0, $char_limit).$ellipsis;
    
    return $story;
}

/**
 * Get story url from ID
 **/
function get_story_url_from_id($id) {
    
    $url = get_post_permalink($id);
    
    return $url;
}

/**
 * Add Share Story Embed Code 
 **/
function add_share_embed_code($id){
    $content = '<script async src="'.SCP_URL.'widgets/embed/script.js" type="text/javascript"><\/script>';
    $content .= '<div class="oet-embed-story" data-story-id="'.$id.'"><\/div>';
    
    share_embed_script($content);
    $html = '<span class="st_embed buttons">';
    $html .= '  <span id="stEmbed" style="text-decoration:none;display:inline-block;cursor:pointer;" data-toggle="popover" data-placement="bottom" data-selector="true" title="Embed">';
    $html .= '      <img src="'.SCP_URL."images/share_embed.png".'" />';
    $html .= '  </span>';
    $html .= '</span>';
    return $html;
}


/**
 * Video Popup Overlay
 **/
function get_modal_video_link($vidtype,$vidid, $thumbnail = false){
  $ret = ''; $imagesrc = ''; $retvid=''; $reticon=''; $vimdata = null; $vim_api_url=''; $vim_addtl_attributes = '';
  if($vidtype == "1"){ //youtube
    $imagesrc = 'https://img.youtube.com/vi/'.$vidid.'/mqdefault.jpg';
    $retvid = '<div id="ytvideo"></div>';
    $reticon = '<span class="stry-youtube-play"></span>';
  }else{ //vimeo
    $vim_api_url = "http://vimeo.com/api/v2/video/$vidid.php";
    if ($thumbnail){
        if (ini_get('allow_url_fopen')){
            $vimdata = unserialize(file_get_contents($vim_api_url));
            $imagesrc = $vimdata[0]['thumbnail_large'];
        } else {
            $imagesrc = SCP_URL . "images/vimeo-default-thumbnail.jpg";    
        }
    } else {
        $imagesrc = SCP_URL . "images/vimeo-default-thumbnail.jpg";
        $vim_addtl_attributes = ' data-video-type="vimeo" data-video-id="'.$vidid.'"';
    }
    $reticon = '<span class="stry-vimeo-play"></span>';
    $retvid .= '<iframe id="ytvideo" title="Video Embed" src="https://player.vimeo.com/video/'.$vidid.'?api=1&player_id='.$vidid.'color=ef0800&title=0&byline=0&portrait=0" width="600" height="360" frameborder="0" allow="autoplay; fullscreen" allowfullscreen></iframe>';
  }
  
  $ret .= '<a href="#" class="stry-video-link" hst="'.$vidtype.'" data-toggle="modal" data-target="#stry-video-overlay">';
    $ret .= '<img class="modal-video-thumbnail" src="'.$imagesrc.'" alt="Story Video" '.$vim_addtl_attributes.' />';
    $ret .= '<div class="stry-video-avatar-table">';
      $ret .= '<div class="stry-video-avatar-cell">';
          $ret .= $reticon;
      $ret .= '</div>';
    $ret .= '</div>';
  $ret .= '</a>';

  $ret .= '<div class="modal fade" id="stry-video-overlay" role="dialog" hst="'.$vidtype.'" tabindex="-1">';
    $ret .= '<div class="stry-video-modal modal-dialog modal-lg">';
              $ret .= '<div class="stry-video-table">';
                $ret .= '<div class="stry-video-cell">';
                  $ret .= '<div class="stry-video-content">';
                    $ret .= $retvid;
                  $ret .= '</div>';
                $ret .= '</div>';
              $ret .= '</div>';
    $ret .= '</div>';
    $ret .= '<a href="#" class="stry-video-close" hst="'.$vidtype.'"><span class="dashicons dashicons-no-alt"></span></a>';
  $ret .= '</div>';
  
  return $ret;
}

/**
 * Load Dashicons at frontend
 **/
add_action( 'wp_enqueue_scripts', 'load_dashicons_front_end' );
function load_dashicons_front_end() {
  wp_enqueue_style( 'dashicons' );
}

/**
 * Add Share Story Embed Script 
 **/
function share_embed_script($content) {
?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            if (jQuery('.ssba-wrap').length>0) {
                jQuery('#stEmbed').appendTo('.ssba-wrap > div');
            }
            
            jQuery('#stEmbed').popover({html:true, content: '<p style="text-align:center"><small>Copy & paste the embed code below.</small></p><textarea id="st_oet_embed" cols="30" rows="7"><?php echo $content; ?></textarea>' });
            jQuery("#stEmbed").on('shown.bs.popover', function(){
                jQuery('#st_oet_embed').select();
            });
        });
    </script>
<?php
}

/**
 * Get Map Pin Color
 **/
function get_map_pin_color($grades) {
    $pincolor = "#294179";
    
    $grades = array_reverse($grades);
    
    foreach($grades as $grade)
    {
            if ($grade->name=="Higher Education"  || $grade->name=="Postsecondary") {
                    $pincolor = "#e57200";
            } else {
                    $pincolor = "#294179";
            }
    }
    return $pincolor;
}

/** Detect mobile devices **/
if ( ! class_exists( 'Mobile_Detect' ) ) {
	include SCP_PATH . 'classes/Mobile_Detect.php';
}
$mobile_detect = new Mobile_Detect();
$mobile_detect->setDetectionType( 'extended' );


/** Detect if using mobile **/
function is_mobile() {
    global $mobile_detect;
    $mobile = null;
    if ( is_tablet() ) {
            $mobile = false;
    } else {
            $mobile = $mobile_detect->isMobile();
    }
    return $mobile;
}

/** Detect if using tablet **/
function is_tablet() {
    global $mobile_detect;
    return $mobile_detect->isTablet();
}

function importStories($default=false) {
    global $wpdb;
    require_once SCP_PATH.'Excel/reader.php';

    $excl_obj = new Spreadsheet_Excel_Reader();
    //$excl_obj->setOutputEncoding('CP1251');
    $time = time();
    $date = date($time);
    
    //Set Maximum Excution Time
    ini_set('max_execution_time', 0);
    ini_set('max_input_time ', -1);
    ini_set('memory_limit ', -1);
    set_time_limit(0);

    $cnt = 0;
    try{
        if( isset($_FILES['stories_import']) && $_FILES['stories_import']['size'] != 0 )
        {
            $filename = $_FILES['stories_import']['name']."-".$date;

            if ($_FILES["stories_import"]["error"] > 0)
            {
                $message = "Error: " . $_FILES["stories_import"]["error"] . "<br>";
                $type = "error";
            }
            else
            {
                if (!(is_dir(SCP_PATH."upload"))){
                        mkdir(SCP_PATH."upload",0777);
                }
                "Upload: " . $_FILES["stories_import"]["name"] . "<br>";
                "Type: " . $_FILES["stories_import"]["type"] . "<br>";
                "Size: " . ($_FILES["stories_import"]["size"] / 1024) . " kB<br>";
                "stored in:" .move_uploaded_file($_FILES["stories_import"]["tmp_name"],SCP_PATH."upload/".$filename) ;
            }
            $excl_obj->read(SCP_PATH."upload/".$filename);
        }
                
        $stories = $excl_obj->sheets[0];
        for ($k =2; $k <= $stories['numRows']; $k++)
        {
            $project_title = "";
            $school = "";
            $district = "";
            $city = "";
            $state = "";
            $locale = "";
            $tag1 = "";
            $tag2 = "";
            $content = "";
            $post_name = "";
            $address = "";
            $tags = array();
            $keywords = "";
            
            /** Check first if column is set **/
            if (isset($stories['cells'][$k][1]))
                    $project_title          = $stories['cells'][$k][1];
            if (isset($stories['cells'][$k][3]))
                    $school    = $stories['cells'][$k][3];
            if (isset($stories['cells'][$k][5]))
                    $district    = $stories['cells'][$k][5];
            if (isset($stories['cells'][$k][6]))
                    $city      = $stories['cells'][$k][6];
            if (isset($stories['cells'][$k][7]))
                    $state     = $stories['cells'][$k][7];
            if (isset($stories['cells'][$k][11]))
                    $locale          = $stories['cells'][$k][11];
            if (isset($stories['cells'][$k][13]))
                    $tag1          = $stories['cells'][$k][13];
            if (isset($stories['cells'][$k][14]))
                    $tag2    = $stories['cells'][$k][14];

            //Check if $project_title is set
            if ( isset( $project_title ) ){
                    $post_name = strtolower($project_title);
                    $post_name = str_replace(' ','_', $post_name);
            }
           
            if(!empty($locale))
            {
                $communities = explode(",",$locale);
                $community_id = array();
                for($i = 0; $i <= sizeof($communities); $i++)
                {
                    if(!empty($communities[$i]))
                    {
                        $cat = get_term_by( 'name', $communities[$i], 'characteristics' );
                        if($cat)
                        {
                            $community_id[$i] = $cat->term_id;
                        }
                    }
                }
            }
            else
            {
                $community_id = array();
            }
            
            if (!empty($tag1) && ($tag1!=="n/a"))
                $tags[] = $tag1;
                
            if (!empty($tag2) && ($tag2!=="n/a"))
                $tags[] = $tag2;

            if (!empty($tags)) {
                $keywords = implode(",", $tags);
            }
            
            if(!empty($project_title))
            {
                /** Get Current WP User **/
                $user_id = get_current_user_id();
                /** Get Current Timestamp for post_date **/
                $cs_date = current_time('mysql');
                
                $post = array('post_content' => $content, 'post_name' => $post_name, 'post_title' => $project_title, 'post_status' => 'publish', 'post_type' => 'stories', 'post_author' => $user_id , 'post_date' => $cs_date, 'post_date_gmt'  => $cs_date, 'comment_status' => 'open');
                
                /** Set $wp_error to false to return 0 when error occurs **/
                $post_id = wp_insert_post( $post, false );
                
                //Set Community Type
                if(!empty($locale))
                {
                    $tax_ids = wp_set_object_terms( $post_id, $community_id, 'characteristics', true );
                }
                
                // Set Tags
                $keywords = strtolower(trim($keywords,","));
                wp_set_post_terms(  $post_id, $keywords , 'story_tag', true );

                // add school meta data
                if(!empty($school) && ($school!=="n/a"))
                {
                        update_post_meta( $post_id , 'story_school' , $school);
                }
                
                // add district meta data
                if(!empty($district) && ($district!=="n/a"))
                {
                        update_post_meta( $post_id , 'story_district' , $district);
                }

                // add map address
                if(!empty($city))
                {
                        $address = $city;
                }
                
                if(!empty($state))
                {
                    if (strlen($address)>0)
                        $address .= ", ". $state;
                    else
                        $address = $state;
                }
                
                if (!empty($address)) {
         
                    update_post_meta($post_id, "story_mapaddress", $address);
                    $latlong = get_latitude_longitude($address);
                    if($latlong)
                    {
                        $map = explode(',' ,$latlong);
                        $mapLatitude = $map[0];
                        $mapLongitude = $map[1];
                        save_metadata($post_id, $mapLatitude, $mapLongitude);
                    }
	    
                }
                
                //saving meta fields
                $cnt++;
            }
            
        }
    } catch(Exception $e) {
        error_log($e->getMessage());
    }		
    // Log finish of import process
    $message = sprintf(__("Successfully imported %s stories.", SCP_SLUG), $cnt);
    $type = "success";
    $response = array('message' => $message, 'type' => $type);
    return $response;
}

function isYoutubeVideoExists($videoId){
    $exists = false;
    $headers = get_headers('https://www.youtube.com/oembed?format=json&url=http://www.youtube.com/watch?v=' . $videoId);
    
    if(is_array($headers) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$headers[0]) : false){
        $exists = true;
    }
    
    return $exists;
}

function displayImage($imageUrl, $imageAlt){
    return '<div class="col-md-12 col-sm-12 col-xs-12 noborder nomargintop">
                <img src="'.$imageUrl.'" alt="'.$imageAlt.'" />
    </div>';
}

function oet_sanitize($input){
  $input = sanitize_text_field(htmlspecialchars_decode($input));
  $bad_chars = array("\"", "'", "(", "\\\\", "<", "&");
  $safe_chars = array("&quot;", "&apos;", "&lpar;", "&bsol;", "&lt;", "&amp;");
  $output = str_replace($bad_chars, $safe_chars, $input);
  return stripslashes($output);
}
?>