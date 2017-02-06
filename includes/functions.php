<?php
/**
 *
 * Contains functions that can be used throughout the plugin
 *
 **/

function get_story_by_id($id) {
    global $wpdb;
    
    $title = get_the_title($id);
    
    return $title;
}
?>