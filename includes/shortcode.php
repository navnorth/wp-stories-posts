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
    //Default width
    $attr_width = 6;
    $title = "";
    
    $class_attrs[] = "col-xs-".$attr_width;
    
    if (is_array($attr))
        extract($attr);
    
    if ($width){
        $class_attrs[] = "col-md-".$width;
        $class_attrs[] = "col-sm-".$width;
    }
    
    $attrs = implode(" ", $class_attrs);
    
    if ($id)
        $title = get_story_by_id($id);
    
    $attr_title = '<h1><a href="">'.$title.'</a></h1>';
    
    if ($content)
        $attr_content = '<p>'.$content.'</p>';
    
    $return = '<div class="'.$attrs.'">'.$attr_title.$content.'</div>';

    return $return;
}

?>