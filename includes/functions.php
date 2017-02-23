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
    $html .= '  <a id="stEmbed" style="text-decoration:none;display:inline-block;cursor:pointer;" data-toggle="popover" data-placement="bottom">';
    $html .= '      <img src="'.SCP_URL."images/share_embed.png".'" />';
    $html .= '  </a>';
    $html .= '</span>';
    return $html;
}

/**
 * Add Share Story Embed Script 
 **/
function share_embed_script($content) {
?>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            jQuery('#stEmbed').popover({html:true, content: '<p style="text-align:center"><small>Copy & paste the embed code below.</small></p><textarea id="st_oet_embed" cols="30" rows="7"><?php echo $content; ?></textarea>' });
            jQuery("#stEmbed").on('shown.bs.popover', function(){
                jQuery('#st_oet_embed').select();
            });
        });
    </script>
<?php
}
?>