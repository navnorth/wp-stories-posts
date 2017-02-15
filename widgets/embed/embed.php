<?php
//load WordPress
include_once(__DIR__.'/../../../../../wp-load.php');

//load plugin functions
include_once(__DIR__.'/../../stories-custom-post-type.php');

$story_id = $_REQUEST['id'];

$story = get_post($story_id);

$width = (int)($_REQUEST['width']==0)?400:$_REQUEST['width'];
$height = (int)($_REQUEST['height']==0)?420:$_REQUEST['height'];

$iframe_src = SCP_URL.'widgets/embed/?id='.$story_id;
$html = "<iframe src='".$iframe_src."' allowtransparency='true' title='".$story->post_title."' frameborder='0' width='".$width."' height='".$height."'>";
$html .= "</iframe>";
echo json_encode(array("html"=> $html));
?>