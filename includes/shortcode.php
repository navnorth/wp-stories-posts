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
    $attr_bg = "";
    $styles = "";
    $styles_attrs = array();
    
    $class_attrs[] = "col-xs-".$attr_width;
    
    if (is_array($attr))
        extract($attr);
    
    if ($width){
        $class_attrs[] = "col-md-".$width;
        $class_attrs[] = "col-sm-".$width;
    }
    
    $attrs = implode(" ", $class_attrs);
    
    if ($id)
        $title = get_title_by_id($id);
    
    $attr_title = '<h1><a href="">'.$title.'</a></h1>';
    
    $background = get_background($id);
    if ($background)
        $styles_attrs[] = "background:url('".$background."') no-repeat top left; background-size:cover;";
    
    $styles = implode(" ", $styles_attrs);
    
    if ($content)
        $attr_content = '<p>'.$content.'</p>';
    
    $return = '<div class="'.$attrs.'" style="'.$styles.'">'.$attr_title.$content.'</div>';

    return $return;
}

?>