<?php
//load WordPress
include_once(__DIR__.'/../../../../../wp-load.php');

//load plugin functions
include_once(__DIR__.'/../../stories-custom-post-type.php');

$story_id = $_POST['id'];
$html = "<iframe>";
$embed = do_shortcode("[oet_story id=$story_id width=6][/oet_story]");
$html .= $embed;
$html .= "</iframe>";
echo json_encode(array("html"=> $html));
?>