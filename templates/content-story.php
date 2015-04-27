<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
?>
<?php global $post; ?>
<div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
    <div>
        <?php
        $back_link_URL = site_url().'/stories/';
        $back_link_text = 'Back to Stories';
        if (isset($_GET['back']) && !empty($_GET['back']))
        {
            $back_link_URL = esc_html($_GET['back']);
            $back_link_text = 'Back to Results';
        }
        ?>
        <div class="col-md-12 col-sm-12 col-xs-12 noborder story_back">
            <a href="<?php echo $back_link_URL; ?>">
                <?php echo $back_link_text; ?>
            </a>
        </div>

        <?php
            $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
            $img_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
            $video_url = get_post_meta( $post->ID, "story_video" , true );
        ?>
        <?php if(isset($img_url) && !empty($img_url) && empty($video_url)) : ?>
            <div class="col-md-12 col-sm-12 col-xs-12 noborder nomargintop">
                <img src="<?php echo $img_url; ?>" alt="<?php echo $img_alt; ?>" />
            </div>
        <?php endif; ?>

        <?php if(isset($video_url) && !empty($video_url)) : ?>
            <div class="col-md-12 col-sm-12 col-xs-12 noborder nomargintop">
                <iframe src="<?php echo $video_url; ?>" height="250"></iframe>
            </div>
        <?php endif; ?>

        <aside class="story_sharewidget">
            <p class="rght_sid_wdgt_hedng">Share this story</p>
           <?php
                echo '<div class="story_sharewidgeticns">';
                            echo do_shortcode("[ssba]");
                echo '</div>';
           ?>
        </aside>

        <?php
            $programs = get_the_terms( $post->ID, "program" );
            $states = get_the_terms( $post->ID, "state" );
            $grade_levels = get_the_terms( $post->ID, "grade_level" );
            $story_tags = get_the_terms( $post->ID, "story_tag" );
			$characteristics = get_the_terms( $post->ID, "characteristics" );

            $story_highlight = get_post_meta($post->ID, "story_highlight", true);
            $story_district = get_post_meta($post->ID, "story_district", true);
            $story_school = get_post_meta($post->ID, "story_school", true);
            $story_mapaddress = get_post_meta($post->ID, "story_mapaddress", true);
            $story_sidebar_content = get_post_meta($post->ID, "story_sidebar_content", true);

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

            if(isset($grade_levels) && !empty($grade_levels))
            {
                $gradeurl = '';
                foreach($grade_levels as $grade_level)
                {
                    $url = get_term_link($grade_level->term_id, $grade_level->taxonomy);
                    $gradeurl .= '<a target="_blank" href="'. $url .'">'.$grade_level->name.'</a>, ';
                }
                $gradeurl = trim($gradeurl, ', ');
            }

            if(isset($story_tags) && !empty($story_tags))
            {
                $tagurl = '';
                $tagid = array();
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
            <p class="rght_sid_wdgt_hedng">Story Snapshot</p>
            <?php if(isset($story_school) && !empty($story_school)) : ?>
                 <p class="margn_none">
                     <b>School :</b> <?php echo $story_school; ?>
                 </p>
            <?php endif; ?>
            <?php //if(isset($story_mapaddress) && !empty($story_mapaddress)) : ?>
                 <!--<p class="margn_none">
                     <b>Address :</b> <?php //echo $story_mapaddress; ?>
                 </p>-->
            <?php // endif; ?>
            <?php if(isset($story_district) && !empty($story_district)) : ?>
                 <p class="margn_none">
                     <b>District :</b> <?php echo $story_district; ?>
                 </p>
            <?php endif; ?>
            <?php if(isset($stateurl) && !empty($stateurl)) : ?>
                 <p class="margn_none">
                     <b>State :</b> <?php echo $stateurl; ?>
                 </p>
            <?php endif; ?>
            <?php if(isset($gradeurl) && !empty($gradeurl)) : ?>
                 <p class="margn_none">
                     <b>Grade :</b> <?php echo $gradeurl; ?>
                 </p>
            <?php endif; ?>
            <?php if(isset($characteristicurl) && !empty($characteristicurl)) : ?>
                 <p class="margn_none">
                     <b>Community Type :</b> <?php echo $characteristicurl; ?>
                 </p>
            <?php endif; ?>
            <?php if(isset($programurl) && !empty($programurl)) : ?>
                 <p class="margn_none">
                     <b>Program :</b> <?php echo $programurl; ?>
                 </p>
            <?php endif; ?>
            <?php if(isset($tagurl) && !empty($tagurl)) : ?>
                 <p class="margn_none">
                     <b>Related Tags :</b> <?php echo $tagurl; ?>
                 </p>
            <?php endif; ?>


        </div>
        <?php if(isset($story_sidebar_content) && !empty($story_sidebar_content)) : ?>
            <div class="col-md-12 col-sm-12 col-xs-12">
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
                'caller_get_posts'=>1);
            $stories = get_posts($args);

            if(!empty($stories)) : ?>

            <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="pblctn_box"><span class="socl_icns fa-stack"><i class="fa fa-star "></i></span></div>
                <p class="rght_sid_wdgt_hedng">Related Stories</p>
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
        <h3><?php echo get_the_title($post->ID); ?></h3>
        <p>
            <?php
                $content = get_the_content($post->ID);
                $content = apply_filters('the_content', $content);
                echo do_shortcode($content);
            ?>
        </p>
     </div>
</div>
