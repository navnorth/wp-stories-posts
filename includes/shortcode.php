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
    $attr_title = "";
    $attr_content = "";
    $style = "";
    if (is_array($attr)){
        if (is_admin()) {
          $_arr = getShortcodeAttr($attr);  
          foreach($_arr as $key => $value) $$key = $value;
        }
        extract($attr);
    }
    
    $class_attrs[] = "story-embed-box";
    
    //Display Width
    if ($width){
        $attr_width = $width;
        $class_attrs[] = "col-sm-".$attr_width;
    }
    $class_attrs[] = "col-md-".$attr_width;
    
    //Display Title
    if ($id){
        if (!$title)
            $title = get_title_by_id($id);
    
    
        // Story Url
        $attr_url = get_story_url_from_id($id);
        $attr_title = '<h1><a href="'.$attr_url.'">'.$title.'</a></h1>';
        
        //Background
        var_dump($id);
        $background = get_background($id);
        var_dump($background);
        if ($background)
            $styles_attrs[] = "background:url('".$background."') no-repeat center center; background-size:cover;";
        
        $styles = implode(" ", $styles_attrs);
        
        //Content
        if ($content)
            $attr_content = '<p>'.$content.'</p>';
        else
            $attr_content = '<p>'.get_story_excerpt_from_id($id).'</p>';    
    
    }
    
    //Set Alignment
    $alignment = (isset($alignment))?$alignment:false;
    if ($alignment)
            $class_attrs[] = "pull-".$alignment;
            
    //Set Color
    $callout_color = (isset($callout_color))?$callout_color:false;
    if ($callout_color){

            $color_class = $callout_color;

            if (strpos($callout_color,"#")>=0){
                    $color_class = substr($callout_color,1,strlen($callout_color)-1);
            }

            $class_attrs[] = "color-".$color_class;

            $style = '<style>';
            //Set Line Color
            $style .= '.color-'.$color_class.'{
                            border-color:'.$callout_color.' !important;
                      }';
            //Set Icon Background Color
            $style .= '.color-'.$color_class.':before {
                            background-color:'.$callout_color.' !important;
                    }';
            $style .= '</style>';
    }
    
    //Set Type
    $attr_type = '';
    $callout_type = (isset($callout_type))?$callout_type:false;
    if ($callout_type){
            $attr_type = $callout_type;
            $class_attrs[] = "pull-out-box";
    }
    
    $class_attrs[] = $attr_type;
    
    $attrs = implode(" ", $class_attrs);
    
    $return = '<div class="'.$attrs.'"><div class="story-embed-content" style="'.$styles.'"><div class="story-embed-desc">'.$attr_title.$attr_content.'</div></div></div>'.$style;

    return $return;
}

?>