<?php
//load WordPress
include_once(__DIR__.'/../../../../../wp-load.php');

//load plugin functions
include_once(__DIR__.'/../../stories-custom-post-type.php');

//Variables
$styles_attrs = array();

$story_id = $_REQUEST['id'];

$story = get_post($story_id);

//Story Background
$background = get_background($id);

if ($background)
    $styles_attrs[] = "background:url('".$background."') no-repeat center center; background-size:cover;";

//Styles    
$styles = implode(" ", $styles_attrs);

$style_src = SCP_URL."css/story.embed.css";

//Title
$title = get_title_by_id($story_id);

//Story url
$url = get_story_url_from_id($story_id);

//Story excerpt
$excerpt = get_story_excerpt_from_id($story_id)
?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf8">
        <title><?php echo $story->post_title; ?></title>
        <link rel="stylesheet" href="<?php echo $style_src; ?>">
    </head>
    <body>
        <div class="story-embed-box">
            <div class="story-embed-content" style="<?php echo $styles; ?>">
                <div class="story-embed-desc">
                    <h1><a href="<?php echo $url; ?>"><?php echo $title; ?></a></h1>
                    <p><?php echo $excerpt; ?></p>
                </div>
            </div>
        </div>
    </body>
</html>