<?php
$story_id = $_REQUEST['id'];
var_dump($_SERVER["SCRIPT_FILENAME"]);
exit;

//load WordPress
include_once(__DIR__.'/../../../../../wp-load.php');

//load plugin functions
include_once(__DIR__.'/../../stories-custom-post-type.php');

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
$html .= '          <h1><a href="'.$url.'">'.$title.'</a></h1>';
$html .= '          <p>'.$excerpt.'</p>';
$html .= '      </div>';
$html .= '  </div>';
$html .= '</div>';

echo json_encode(array("html"=> $html));
?>