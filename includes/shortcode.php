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
    $class_attrs[] = "col-xs-".$attr_width;
    
    if (is_array($attribute))
        extract($attribute);
    
    if ($width){
        $class_attrs[] = "col-md-".$width;
        $class_attrs[] = "col-sm-".$width;
    }
    
    $attrs = implode(" ", $class_attrs);
    
    $return = '<div class="'.$attrs.'">'.$content.'</div>';

    return $return;
}

?>