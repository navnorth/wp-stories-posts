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
        $youtubeID = get_youtubeID($id);
        $background_image_url = get_youtube_image($youtubeID);
    } else {
        $background_image_url = SCP_URL . "images/top_strap_img.jpg";
    }
    
    return $background_image_url;
}

/**
 * Checks if story has embedded video
 **/
function has_youtube_video($id) {
    $has_video = false;
    
    $video = get_post_meta($id,'story_video',true);
    
    if (strlen($video)>0){
        $has_video = true;
    }
    return $has_video;
}

/**
 * Get Youtube ID from embedded code
 **/
function get_youtubeID($id) {
    $youtubeID = null;
    
    $youtubeID = get_post_meta( $id, 'story_video', true);
    
    return $youtubeID;
}

/**
 * Get Youtube Image
 **/
function get_youtube_image($youtube_id) {
    $youtube_url = "//img.youtube.com/vi/$youtube_id/maxresdefault.jpg";
    return $youtube_url;
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
?>