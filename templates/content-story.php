<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
global $_embed;
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
function add_vimeo_script(){
    $script_url = SCP_URL."js/vimeo.ga.min.js";
    $tracking_script = "<script type='text/javascript' src='".$script_url."'></script>";
    $tracking_script .= '<script type="text/javascript">
                            jQuery(document).ready(function($){
                                vidid = $(".modal-video-thumbnail").attr("data-video-id");
                                if (vidid) {
                                    $.getJSON("https://vimeo.com/api/oembed.json?url=https://vimeo.com/" + vidid, {format: "json"}, function(data) {
                                        $(".modal-video-thumbnail").attr("src", data.thumbnail_url);
                                    });
                                }
                            });
                        </script>';
    echo $tracking_script;
}
?>
<?php global $post; ?>
<div id="content" class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
    <div>
        <?php
        $back_link_URL = site_url().'/stories/';
        $back_link_text = __( 'Back to Stories', SCP_SLUG );
        if (isset($_GET['back']) && !empty($_GET['back']))
        {
            $back_link_URL = esc_url(filter_var($_GET['back'],FILTER_SANITIZE_STRING));
            $back_link_text = __( 'Back to Results', SCP_SLUG );
        }
        ?>
        <div class="col-md-12 col-sm-12 col-xs-12 noborder story_back">
            <a href="<?php echo $back_link_URL; ?>">
                <?php echo $back_link_text; ?>
            </a>
        </div>

        <?php
        $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
        if (is_mobile()){
        $img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'medium');
        if ($img)
            $img_url = $img[0];
        } elseif (is_tablet()){
        $img = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large');
        if ($img)
            $img_url = $img[0];
        }
            $img_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
            $video_id = get_post_meta( $post->ID, "story_video" , true );
        $story_video_host   = get_post_meta($post->ID, "story_video_host", true);

    if(isset($img_url) && !empty($img_url) && empty($video_id))
        echo displayImage($img_url, $img_alt);

    if(isset($video_id) && !empty($video_id)) {
        $origin = get_site_url();
        $video_url = "https://www.youtube.com/embed/".$video_id."?enablejsapi=1&#038;origin=".$origin;

        ?>
        <div class="col-md-12 col-sm-12 col-xs-12 noborder nomargintop">
		  <div class="<?php if ($story_video_host=='1'): ?>video-wrap<?php else: ?>vid-wrap<?php endif; ?>">
		    <?php if ($story_video_host=="1") {
    			$enable_youtube_check = get_option('enable_youtube_check');
    			if (!empty($enable_youtube_check)) {
    			    if (isYoutubeVideoExists($video_id)) { 
        				if(!is_numeric($video_id)){
        				  echo get_modal_video_link($story_video_host,$video_id);
        				} ?>
    			    <?php } else {
        				$script = "<script>\n ".
        					    "jQuery(document).ready(function(e) { \n".
        					    "	ga('send',  'event', 'Story Video: " . $post->post_title . "', 'Failed', '". $video_id."'  ); \n".
        					    "}); \n ".
        					    "</script>";
        				echo $script;
    				
        				if (isset($img_url) && !empty($img_url)){
        				    echo displayImage($img_url, $img_alt);
        				}
    				
    			    }
    			} else {
    			    if(!is_numeric($video_id)){
    				echo get_modal_video_link($story_video_host,$video_id);
    			    }
    			}
	        } else { ?>
                <?php
                $enable_vimeo_thumbnail = (get_option('enable_vimeo_thumbnail')?true:false);
                if(is_numeric($video_id)){
                  echo get_modal_video_link($story_video_host,$video_id, $enable_vimeo_thumbnail);
                } ?>
        <?php } ?>  
		</div>
        </div>
	<?php
	    
	    if ($story_video_host=="1") {
		    $tracking_script = "<script type='text/javascript'>\n";

		    $tracking_script .= " function loadPlayer() { \n".
					    "	if (typeof(YT) == 'undefined' || typeof(YT.Player) == 'undefined') { \n".
					    "	    var tag = document.createElement('script'); \n ".
					    "	    tag.src = '//www.youtube.com/iframe_api'; \n ".
					    "	    var firstScriptTag = document.getElementsByTagName('script')[0]; \n".
					    "	    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag); \n".
					    "	    window.onYouTubeIframeAPIReady = function() { \n ".
					    "		onYouTubeIframeAPIReady_LoadPlayer(); \n ".
					    "	    }; \n ".
					    "	} else { \n ".
					    "	    onYouTubeIframeAPIReady_LoadPlayer(); \n ".
					    "	} \n".
					    "    } \n".
					    "    // This code loads the IFrame Player API code asynchronously \n".
					    "/*var tag = document.createElement('script'); \n".
					    "tag.src = \"//www.youtube.com/iframe_api\"; \n ".
					    "var firstScriptTag = document.getElementsByTagName('script')[0]; \n".
					    "firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);*/ \n".
					    "	// This code is called by the YouTube API to create the player object \n".
					    "var player;\n".
					    "function onYouTubeIframeAPIReady_LoadPlayer() { \n".
					    "	player = new YT.Player('ytvideo', { \n".
					    "	width: '', \n".
					    "	height: '360', \n".
					    "	videoId: '".$video_id."', \n".
					    "	playerVars: { \n".
					    "		'autoplay': 0, \n".
					    "		'controls': 1, \n".
					    "		'enablejsapi': 1, \n".
					    "		'rel' : 0, \n".
					    "		'origin' : '".$origin."' \n".
					    "	}, \n".
					    "	events: { \n".
					    "		'onError': onPlayerError, \n".
					    "		'onReady': onPlayerReady, \n".
					    "		'onStateChange': onPlayerStateChange \n".
					    "		} \n".
					    "	}); \n".
					    "	//console.log(player); \n".
					    "}\n".
					    "	var pauseFlag = false; \n".
					    "	var gaSent = false; \n".
					    "function onPlayerError(event) { \n".
					    "	if (event.data) { \n".
					    "		if (gaSent === false) { \n".
					    "			ga('send',  'event', 'Story Video: " . $post->post_title . "', 'Failed', '". $video_id."'  ); \n".
					    "			gaSent = true; \n".
					    "		} \n".
					    "		useFeaturedImage(); \n".
					    " 	} \n".
					    "} \n".
					    "function onPlayerReady(event) { \n".
					    "	// do nothing, no tracking needed \n".
					    "} \n".
					    "function onPlayerStateChange(event) { \n".
					    "	var url = event.target.getVideoUrl(); \n".
					    "	var match = url.match(/[?&]v=([^&]+)/); \n".
					    "	if( match != null) \n".
					    "	{ \n ".
					    "		var videoId = match[1]; \n".
					    "	} \n".
					    "	videoId = String(videoId); \n".
					    "	// track when user clicks to Play \n".
					    "	if (event.data == YT.PlayerState.PLAYING) { \n".
					    "		ga('send', 'event', 'Story Video: " . $post->post_title . "', 'Play', '". $video_id."'  );\n".
					    "		console.log(ga); \n".
					    "		pauseFlag = true; \n".
					    "	}\n".
					    "	// track when user clicks to Pause \n".
					    "	if (event.data == YT.PlayerState.PAUSED && pauseFlag) { \n".
					    "		ga('send',  'event', 'Story Video: " . $post->post_title . "', 'Pause', '". $video_id."'  ); \n".
					    "		pauseFlag = false; \n ".
					    "	} \n".
					    "	// track when video ends \n".
					    "	if (event.data == YT.PlayerState.ENDED) { \n".
					    "		ga('send', 'event', 'Story Video: " . $post->post_title . "', 'Finished', '". $video_id."'  ); \n".
					    "	}\n".
					    "}";
		    
		    $tracking_script .= "function useFeaturedImage(){ \n";
					
		    if(isset($img_url) && !empty($img_url)) : 
			$tracking_script .= "jQuery('#ytvideo').hide(); \n";
			$tracking_script .= "jQuery('#ytImage').remove(); \n";
			$tracking_script .= "jQuery('#ytvideo').parent().append(\"".
					    "<div id='ytImage' class='col-md-12 col-sm-12 col-xs-12 noborder nomargintop'>".
					    "	<img src='".$img_url."' alt='".$img_alt."' />".
					    "</div>".
					    "\");";
		    endif;

		    $tracking_script .= "}";
		    $tracking_script .= "</script>";
		    $tracking_script .= "<script>\n ".
					    "jQuery(document).ready(function(e) { \n".
					    "	loadPlayer(); \n ".
					    "}); \n ".
					    "</script>";
		    echo $tracking_script;
		}
		elseif ($story_video_host=="2") {
		    add_action('wp_footer','add_vimeo_script');
		    $video_url = "https://player.vimeo.com/video/".$video_id."?api=1&player_id=".$video_id;
            $tracking_script = "<script src='https://player.vimeo.com/api/player.js'></script>";
            $tracking_script .= "<script type='text/javascript'>\n"; 
            $tracking_script .= "var iframe = document.querySelector('#ytvideo');";
            $tracking_script .= "var vimplay = new Vimeo.Player(iframe);";
            $tracking_script .= "</script>";
            echo $tracking_script;
		}
	?>
        <?php } ?>

        <aside class="story_sharewidget">
            <h3 class="rght_sid_wdgt_hedng"><?php _e( 'Share this story' , SCP_SLUG ); ?></h3>
           <?php
                echo '<div class="story_sharewidgeticns">';
            //Checks if ShareThis is installed and active
            if (is_plugin_active("share-this/sharethis.php")){
                if (function_exists('sharethis_button')) {
                echo "<p>";

                sharethis_button();

                if ($_embed)
                    echo add_share_embed_code($post->ID);

                echo "</p>";
                }
            } else {

                if (shortcode_exists('oet_social')) echo do_shortcode("[oet_social]");

                if ($_embed)
                    echo add_share_embed_code($post->ID);
            }
                echo '</div>';
           ?>
        </aside>

        <?php
        $programs = get_the_terms( $post->ID, "program" );
        $states = get_the_terms( $post->ID, "state" );
        $grade_levels = get_the_terms( $post->ID, "grade_level" );
        $story_tags = get_the_terms( $post->ID, "story_tag" );
        $characteristics = get_the_terms( $post->ID, "characteristics" );
        $districtsize = get_the_terms( $post->ID, "districtsize" );
        $institutionenrollment = get_the_terms( $post->ID, "institutionenrollment" );
        $institutiontype = get_the_terms( $post->ID, "institutiontype" );

        $story_highlight = get_post_meta($post->ID, "story_highlight", true);
        $story_district = get_post_meta($post->ID, "story_district", true);
        $story_school = get_post_meta($post->ID, "story_school", true);
        $story_institution = get_post_meta($post->ID, "story_institution", true);
        $story_mapaddress = get_post_meta($post->ID, "story_mapaddress", true);
        $story_sidebar_content = get_post_meta($post->ID, "story_sidebar_content", true);

        $districturl = "";
        $tagurl = "";
        $characteristicurl = "";
        $tagid = array();

        if(isset($characteristics) && !empty($characteristics))
        {
            foreach($characteristics as $characteristic)
            {
                $url = get_term_link($characteristic->term_id, $characteristic->taxonomy);
                $characteristicurl .= '<a target="_blank" href="'. $url .'">'.$characteristic->name.'</a>, ';
            }
            $characteristicurl = trim($characteristicurl, ', ');
        }

        if(isset($states) && !empty($states))
        {
            foreach($states as $state)
            {
                $url = get_term_link($state->term_id, $state->taxonomy);
                $stateurl = '<a target="_blank" href="'. $url .'">'.$state->name.'</a>';
            }
        }

        if(isset($programs) && !empty($programs))
        {
            $programurl = '';
            foreach($programs as $program)
            {
                $url = get_term_link($program->term_id, $program->taxonomy);
                $programurl .= '<a target="_blank" href="'. $url .'">'.$program->name.'</a>, ';
            }
            $programurl = trim($programurl, ', ');
        }

        $grade_tag = array();
        $grades = array();
        if(isset($grade_levels) && !empty($grade_levels))
        {
            $gradeurl = '';
            foreach($grade_levels as $grade_level)
            {
                $url = get_term_link($grade_level->term_id, $grade_level->taxonomy);
                $gradeurl .= '<a target="_blank" href="'. $url .'">'.$grade_level->name.'</a>, ';

                if ($grade_level->name=="P-12" || $grade_level->name=="Early Childhood Education") {
                    $grade_tag[] = array("grade_color" => "bgblue",
                                    "grade_level" => __( 'P-12' , SCP_SLUG ),
                                    "grade_name" => 'P-12',
                                    "grade_url" => $url);
                    $grade_color = "bgblue";
                    $grade_level = __( 'P-12' , SCP_SLUG );
                    $grades[] = "P-12";
                }
                elseif ($grade_level->name=="Higher Education" || $grade_level->name=="Postsecondary" || strcmp(html_entity_decode($grade_level->name),"Higher & Adult Ed")==0) {
                    $grade_tag[] = array("grade_color" => "bgorange",
                                "grade_level" => __( 'Higher & Adult Ed' , SCP_SLUG ),
                                "grade_name" => 'Higher & Adult Ed',
                                "grade_url" => $url);
                    $grade_color = "bgorange";
                    $grade_level = __( 'Higher & Adult Ed' , SCP_SLUG );
                    $grades[] = "Higher & Adult Ed";
                }
            }
            $gradeurl = trim($gradeurl, ', ');
        }

        if(isset($districtsize) && !empty($districtsize))
            {
                $districturl = '';
                foreach($districtsize as $district)
                {
                    $url = get_term_link($district->term_id, $district->taxonomy);
                    $districturl .= '<a target="_blank" href="'. $url .'">'.$district->name.'</a>, ';
                }
                $districturl = trim($districturl, ', ');
            }

        if(isset($institutionenrollment) && !empty($institutionenrollment))
            {
                $institutionurl = '';
                foreach($institutionenrollment as $institution)
                {
                    $url = get_term_link($institution->term_id, $institution->taxonomy);
                    $institutionurl .= '<a target="_blank" href="'. $url .'">'.$institution->name.'</a>, ';
                }
                $institutionurl = trim($institutionurl, ', ');
            }

        if(isset($institutiontype) && !empty($institutiontype))
            {
                $institutiontypeurl = '';
                foreach($institutiontype as $type)
                {
                    $url = get_term_link($type->term_id, $type->taxonomy);
                    $institutiontypeurl .= '<a target="_blank" href="'. $url .'">'.$type->name.'</a>, ';
                }
                $institutiontypeurl = trim($institutiontypeurl, ', ');
            }

            if(isset($story_tags) && !empty($story_tags))
            {
                foreach($story_tags as $story_tag)
                {
                    $tagid[] = $story_tag->term_id;
                    $url = get_term_link($story_tag->term_id, $story_tag->taxonomy);
                    $tagurl .= '<a target="_blank" href="'. $url .'">'.$story_tag->name.'</a>, ';
                }
                $tagurl = trim($tagurl, ', ');
            }


        ?>

        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="pblctn_box"><span class="socl_icns fa-stack"><i class="fa fa-star "></i></span></div>
            <p class="rght_sid_wdgt_hedng"><?php _e( 'Story Snapshot' , SCP_SLUG); ?> </p>
        <?php

        $final_level = "P-12";

        //Display K-12 first before Higher Education
        if (!empty($grade_tag)) {

            $grade_tag = array_unique($grade_tag, SORT_REGULAR);
            sort($grade_tag);
            echo '<p class="margin_20">';
            foreach($grade_tag as $display) {
            if ((strcmp(html_entity_decode($display['grade_level']),"Higher & Adult Ed")==0 || $display['grade_level']=="Postsecondary") && count($grade_tag)==1)
                $final_level = "Higher & Adult Ed";
            $grade_label = '<a href="'.$display['grade_url'].'"><span class="'.$display['grade_color'].'">'.$display['grade_level'].'</span></a>';
            echo $grade_label;
            }
            echo '</p>';
        }
        ?>
        <?php if ($final_level=="P-12") : ?>
        <?php if(isset($story_school) && !empty($story_school)) : ?>
             <p class="margn_none">
             <b><?php _e( 'School :' , SCP_SLUG ); ?></b> <?php echo $story_school; ?>
             </p>
        <?php endif; ?>
        <?php endif; ?>
            <?php //if(isset($story_mapaddress) && !empty($story_mapaddress)) : ?>
                 <!--<p class="margn_none">
                     <b>Address :</b> <?php //echo $story_mapaddress; ?>
                 </p>-->
            <?php // endif; ?>

        <?php if ($final_level=="Postsecondary" || strcmp(html_entity_decode($final_level),"Higher & Adult Ed")==0) : ?>
        <?php if(isset($story_institution) && !empty($story_institution)) : ?>
            <p class="margn_none">
            <b><?php _e( 'Institution :' , SCP_SLUG ); ?></b> <?php echo $story_institution; ?>
            </p>
        <?php endif; ?>
        <?php endif; ?>

        <?php if ($final_level=="P-12") : ?>
        <?php if(isset($story_district) && !empty($story_district)) : ?>
             <p class="margn_none">
             <b><?php _e( 'District :' , SCP_SLUG ); ?></b> <?php echo $story_district; ?>
             </p>
        <?php endif; ?>
        <?php endif; ?>
            <?php if(isset($stateurl) && !empty($stateurl)) : ?>
                 <p class="margn_none">
                     <b><?php _e( 'State :' , SCP_SLUG ); ?></b> <?php echo $stateurl; ?>
                 </p>
            <?php endif; ?>
            <?php if(isset($gradeurl) && !empty($gradeurl)) : ?>
                 <p class="margn_none">
                     <b><?php _e( 'Level :' , SCP_SLUG ); ?></b> <?php echo $gradeurl; ?>
                 </p>
            <?php endif; ?>
         <?php if ($final_level=="P-12") : ?>
        <?php if(isset($districturl) && !empty($districturl)) : ?>
             <p class="margn_none">
             <b><?php _e( 'District Enrollment :' , SCP_SLUG ); ?></b> <?php echo $districturl; ?>
             </p>
        <?php endif; ?>
        <?php endif; ?>
        <?php if ($final_level=="P-12") : ?>
        <?php if(isset($characteristicurl) && !empty($characteristicurl)) : ?>
             <p class="margn_none">
             <b><?php _e( 'Community Type :' , SCP_SLUG ); ?></b> <?php echo $characteristicurl; ?>
             </p>
        <?php endif; ?>
        <?php endif; ?>
        <?php if ($final_level=="Postsecondary" || strcmp(html_entity_decode($final_level),"Higher & Adult Ed")==0) : ?>
        <?php if(isset($institutionurl) && !empty($institutionurl)) : ?>
             <p class="margn_none">
             <b><?php _e( 'Institution Enrollment :' , SCP_SLUG ); ?></b> <?php echo $institutionurl; ?>
             </p>
        <?php endif; ?>
        <?php endif; ?>
        <?php if ($final_level=="Postsecondary" || strcmp(html_entity_decode($final_level),"Higher & Adult Ed")==0 ) : ?>
        <?php if(isset($institutiontypeurl) && !empty($institutiontypeurl)) : ?>
             <p class="margn_none">
             <b><?php _e( 'Institution Type :' , SCP_SLUG ); ?></b> <?php echo $institutiontypeurl; ?>
             </p>
        <?php endif; ?>
        <?php endif; ?>
            <?php if(isset($programurl) && !empty($programurl)) : ?>
                 <p class="margn_none">
                     <b><?php _e( 'Program :' , SCP_SLUG ); ?></b> <?php echo $programurl; ?>
                 </p>
            <?php endif; ?>
            <?php if(isset($tagurl) && !empty($tagurl)) : ?>
                 <p class="margn_none">
                     <b><?php _e( 'Related Tags :' , SCP_SLUG ); ?></b> <?php echo $tagurl; ?>
                 </p>
            <?php endif; ?>


        </div>
        <?php if(isset($story_sidebar_content) && !empty($story_sidebar_content)) : ?>
            <div class="col-md-12 col-sm-12 col-xs-12 additional_sidebar_content">
                <p class="padding_top_btm">
                   <?php echo do_shortcode($story_sidebar_content); ?>
                </p>
            </div>
        <?php endif; ?>
        <?php
            $args=array(
                'tax_query' => array(array(
                                    'taxonomy'  => 'story_tag',
                                    'terms'     => $tagid,
                                    'operator'  => 'IN')),
                'post_type' => "stories",
                'post__not_in' => array($post->ID),
                'posts_per_page'=>5,
                'ignore_sticky_posts'=>1);
            $stories = get_posts($args);

            if(!empty($stories)) : ?>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="pblctn_box"><span class="socl_icns fa-stack"><i class="fa fa-star "></i></span></div>
                <p class="rght_sid_wdgt_hedng"><?php _e( 'Related Stories' , SCP_SLUG ); ?></p>
                <?php
                    foreach( $stories as $story)
                    {
                        echo '<p class="padding_top_btm">
                                <a target="_blank" href="'.get_the_permalink($story->ID).'">'.get_the_title($story->ID).'</a>
                              </p>';
                    }
                ?>
            </div>
            <?php endif; ?>
    </div>
</div>

<div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr">
    <div class="col-md-12 pblctn_paramtr padding_left">
        <h2><?php
        //if (!(title_can_be_hidden()))
        echo get_the_title($post->ID);
    ?></h2>
        <p>
            <?php
                $content = get_the_content($post->ID);
                $content = apply_filters('the_content', $content);
                echo do_shortcode($content);
            ?>
        </p>
     </div>
</div>