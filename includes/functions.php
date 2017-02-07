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
    $content = get_post($id);
    preg_match('/<iframe(.+)\"/', $content->post_content, $matches);
    
    if (!empty($matches)){
        $has_video = true;
    }
    return $has_video;
}

/**
 * Get Youtube ID from embedded code
 **/
function get_youtubeID($id) {
    $youtubeID = null;
    
    $content = get_post($id);
    
    if (preg_match("#(\.be/|/embed/|/v/|/watch\?v=)([A-Za-z0-9_-]{5,11})#", $content->post_content, $id)) {
        $youtubeID = $id[2];
    } 
    return $youtubeID;
}

/**
 * Get Youtube Image
 **/
function get_youtube_image($youtube_id) {
    $youtube_url = "https://img.youtube.com/vi/$youtube_id/sddefault.jpg";
    return $youtube_url;
}
?>