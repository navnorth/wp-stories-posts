<?php
header('Access-Control-Allow-Origin: *');

$cur_dir = dirname($_SERVER["SCRIPT_FILENAME"]);
$root_dir = dirname(dirname(dirname(dirname(dirname(dirname($_SERVER["SCRIPT_FILENAME"]))))));

//load WordPress
if (file_exists($root_dir.'/wp-load.php'))
    include_once($root_dir.'/wp-load.php');

//load plugin functions
if (file_exists($cur_dir.'/../../stories-custom-post-type.php'))
    include_once($cur_dir.'/../../stories-custom-post-type.php');

$story_id = $_REQUEST['id'];

$story = get_post($story_id);

//Variables
$styles_attrs = array();

//Story Background
$background = get_background($story_id);

$styles_attrs[] = "background:url('".$background."') no-repeat center center; background-size:cover;";

//Styles    
$styles = implode(" ", $styles_attrs);

//Title
$title = get_title_by_id($story_id);

//Story url
$url = get_story_url_from_id($story_id);

//Story excerpt
$excerpt = get_story_excerpt_from_id($story_id);

$html = '<div class="story-embed-box">';
$html .= '  <div class="story-embed-content" style="'.$styles.'">';
$html .=  '     <div class="story-embed-desc">';
$html .= '          <h1><a href="'.$url.'" target="_blank">'.$title.'</a></h1>';
$html .= '          <p>'.$excerpt.'</p>';
$html .= '          <p><small><a href="'.$url.'" target="_blank">Via the Office of Educational Technology</a></small></p>';
$html .= '      </div>';
$html .= '  </div>';
$html .= '</div>';

echo $_GET['callback']. "(" . json_encode(array("id"=>$story_id, "html"=> $html)) . ")";
?>