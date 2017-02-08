<?php
/**
 *
 * Stories Shortcodes
 *
 **/

/**
  * OET Story
  * Example [oet_story id=1 width=6]Story content here[/oet_story]
 **/
add_shortcode( 'oet_story' , 'oet_story_func' );
function oet_story_func($attr, $content = null) {

    wp_enqueue_style('oet-story-embed', SCP_URL.'css/story.embed.css');

    //Default width
    $attr_width = 6;
    $title = "";
    $attr_bg = "";
    $styles = "";
    $styles_attrs = array();
    
    if (is_array($attr))
        extract($attr);
    
    $class_attrs[] = "story-embed-box";
    
    //Display Width
    if ($width){
        $attr_width = $width;
        $class_attrs[] = "col-sm-".$attr_width;
    }
    $class_attrs[] = "col-md-".$attr_width;
    
    $attrs = implode(" ", $class_attrs);
    
    //Display Title
    if ($id)
        $title = get_title_by_id($id);
    
    // Story Url
    $attr_url = get_story_url_from_id($id);
    
    $attr_title = '<h1><a href="'.$attr_url.'">'.$title.'</a></h1>';
    
    //Background
    $background = get_background($id);
    if ($background)
        $styles_attrs[] = "background:url('".$background."') no-repeat center center; background-size:cover;";
    
    $styles = implode(" ", $styles_attrs);
    
    //Content
    if ($content)
        $attr_content = '<p>'.$content.'</p>';
    else
        $attr_content = '<p>'.get_story_excerpt_from_id($id).'</p>';    
    
    $return = '<div class="'.$attrs.'"><div class="story-embed-content" style="'.$styles.'"><div class="story-embed-desc">'.$attr_title.$attr_content.'</div></div></div>';

    return $return;
}

?>