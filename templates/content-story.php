<?php
/**
 * The Template for displaying all single posts
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
global $_embed, $hide_title;
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
function add_vimeo_script(){
    $script_url = SCP_URL."js/vimeo.ga.min.js";
    $tracking_script = "<script type='text/javascript' src='".$script_url."'></script>";
    echo $tracking_script;
}
?>
<?php global $post; ?>
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
?>
<?php if ($img_url) { ?>
<style type="text/css">
    .fusion-page-title-bar { background-image:url(<?php echo $img_url; ?>); }
</style>
<?php } ?>
<div id="content" class="col-md-5 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
    <div>
        <?php
            $programs = get_the_terms( $post->ID, "program" );
            $states = get_the_terms( $post->ID, "state" );
            $grade_levels = get_the_terms( $post->ID, "grade_level" );
            $story_tags = get_the_terms( $post->ID, "story_tag" );
	    $characteristics = get_the_terms( $post->ID, "characteristics" );
	    $districtsize = get_the_terms( $post->ID, "districtsize" );
	    $institutionenrollment = get_the_terms( $post->ID, "institutionenrollment" );
	    $institutiontype = get_the_terms( $post->ID, "institutiontype" );

	    $story_team_lead = get_post_meta($post->ID, "story_team_lead", true);
	    $story_logic_model = get_post_meta($post->ID, "story_logic_model", true);
            $story_highlight = get_post_meta($post->ID, "story_highlight", true);
            $story_district = get_post_meta($post->ID, "story_district", true);
            $story_school = get_post_meta($post->ID, "story_school", true);
	    $story_institution = get_post_meta($post->ID, "story_institution", true);
            $story_mapaddress = get_post_meta($post->ID, "story_mapaddress", true);
            $story_sidebar_content = get_post_meta($post->ID, "story_sidebar_content", true);
	    
	    $enable_level = get_option('enable_grade_level');
	    $enable_community_type = get_option('enable_characteristics');
	    $enable_district_enrollment = get_option('enable_district_size');
	    $enable_institution_enrollment = get_option('enable_institution_enrollment');
	    $enable_institution_type = get_option('enable_institution_type');

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
		    elseif ($grade_level->name=="Higher Education" || $grade_level->name=="Postsecondary") {
			    $grade_tag[] = array("grade_color" => "bgorange",
						 "grade_level" => __( 'Postsecondary' , SCP_SLUG ),
						 "grade_name" => 'Postsecondary',
						 "grade_url" => $url);
			    $grade_color = "bgorange";
			    $grade_level = __( 'Postsecondary' , SCP_SLUG );
			    $grades[] = "Postsecondary";
		    }
                }
                $gradeurl = trim($gradeurl, ', ');
            }

	    if(isset($districtsize) && !empty($districtsize))
            {
                foreach($districtsize as $district)
                {
                    $url = get_term_link($district->term_id, $district->taxonomy);
                    $districturl .= '<a target="_blank" href="'. $url .'">'.$district->name.'</a>, ';
                }
		$districturl = trim($districturl, ', ');
            }
	    
	    if(isset($institutionenrollment) && !empty($institutionenrollment))
            {
                foreach($institutionenrollment as $institution)
                {
                    $url = get_term_link($institution->term_id, $institution->taxonomy);
                    $institutionurl .= '<a target="_blank" href="'. $url .'">'.$institution->name.'</a>, ';
                }
		$institutionurl = trim($institutionurl, ', ');
            }
	    
	    if(isset($institutiontype) && !empty($institutiontype))
            {
                foreach($institutiontype as $type)
                {
                    $url = get_term_link($type->term_id, $type->taxonomy);
                    $institutiontypeurl .= '<a target="_blank" href="'. $url .'">'.$type->name.'</a>, ';
                }
		$institutiontypeurl = trim($institutiontypeurl, ', ');
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
            <p class="rght_sid_wdgt_hedng hidden"><?php _e( 'Story Snapshot' , SCP_SLUG); ?> </p>
	    <?php
	    
	    $final_level = "P-12";
	    
	    ?>
	    <?php if(isset($programurl) && !empty($programurl)) : ?>
                 <h4 class="margn_none">
                     <b><?php _e( 'Program :' , SCP_SLUG ); ?></b> <?php echo $programurl; ?>
                 </h4>
            <?php endif; ?>
	    <?php if ($final_level=="P-12") : ?>
		<?php if(isset($story_school) && !empty($story_school)) : ?>
		     <h4 class="margn_none">
			 <b><?php _e( 'School :' , SCP_SLUG ); ?></b> <?php echo $story_school; ?>
		     </h4>
		<?php endif; ?>
	    <?php endif; ?>
            <?php //if(isset($story_mapaddress) && !empty($story_mapaddress)) : ?>
                 <!--<p class="margn_none">
                     <b>Address :</b> <?php //echo $story_mapaddress; ?>
                 </p>-->
            <?php // endif; ?>
	    
	    <?php if ($final_level=="Postsecondary") : ?>
		<?php if(isset($story_institution) && !empty($story_institution)) : ?>
		    <h4 class="margn_none">
			<b><?php _e( 'Institution :' , SCP_SLUG ); ?></b> <?php echo $story_institution; ?>
		    </h4>
		<?php endif; ?>
	    <?php endif; ?>
	    
	    <?php if ($final_level=="P-12") : ?>
		<?php if(isset($story_district) && !empty($story_district)) : ?>
		     <h4 class="margn_none">
			 <b><?php _e( 'District :' , SCP_SLUG ); ?></b> <?php echo $story_district; ?>
		     </h4>
		<?php endif; ?>
	    <?php endif; ?>
            <?php if(isset($stateurl) && !empty($stateurl)) : ?>
                 <h4 class="margn_none">
                     <b><?php _e( 'State :' , SCP_SLUG ); ?></b> <?php echo $stateurl; ?>
                 </h4>
            <?php endif; ?>
            <?php if(isset($gradeurl) && !empty($gradeurl) && $enable_level) : ?>
                 <h4 class="margn_none">
                     <b><?php _e( 'Level :' , SCP_SLUG ); ?></b> <?php echo $gradeurl; ?>
                 </h4>
            <?php endif; ?>
	     <?php if ($final_level=="P-12") : ?>
		<?php if(isset($districturl) && !empty($districturl) && $enable_district_enrollment) : ?>
		     <h4 class="margn_none">
			 <b><?php _e( 'District Enrollment :' , SCP_SLUG ); ?></b> <?php echo $districturl; ?>
		     </h4>
		<?php endif; ?>
	    <?php endif; ?>
	    <?php if ($final_level=="P-12") : ?>
		<?php if(isset($characteristicurl) && !empty($characteristicurl) && $enable_community_type) : ?>
		     <h4 class="margn_none">
			 <b><?php _e( 'Community Type :' , SCP_SLUG ); ?></b> <?php echo $characteristicurl; ?>
		     </h4>
		<?php endif; ?>
	    <?php endif; ?>
	    <?php if ($final_level=="Postsecondary") : ?>
		<?php if(isset($institutionurl) && !empty($institutionurl) && $enable_institution_enrollment) : ?>
		     <h4 class="margn_none">
			 <b><?php _e( 'Institution Enrollment :' , SCP_SLUG ); ?></b> <?php echo $institutionurl; ?>
		     </h4>
		<?php endif; ?>
	    <?php endif; ?>
	    <?php if ($final_level=="Postsecondary") : ?>
		<?php if(isset($institutiontypeurl) && !empty($institutiontypeurl) && $enable_institution_type) : ?>
		     <h4 class="margn_none">
			 <b><?php _e( 'Institution Type :' , SCP_SLUG ); ?></b> <?php echo $institutiontypeurl; ?>
		     </h4>
		<?php endif; ?>
	    <?php endif; ?>
	    <?php if(isset($story_team_lead) && !empty($story_team_lead)) : ?>
                 <h4 class="margn_none">
                     <b><?php _e( 'Team Lead :' , SCP_SLUG ); ?></b> <?php echo $story_team_lead; ?>
                 </h4>
            <?php endif; ?>
	    <?php if(isset($story_logic_model) && !empty($story_logic_model)) : ?>
                 <h4 class="margn_none">
                     <b><?php _e( 'Logic Model :' , SCP_SLUG ); ?></b> <a href="<?php echo esc_url($story_logic_model); ?>" target="_blank"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                 </h4>
            <?php endif; ?>
            <?php if(isset($tagurl) && !empty($tagurl)) : ?>
                 <h4 class="margn_none">
                     <b><?php _e( 'Related Tags :' , SCP_SLUG ); ?></b> <?php echo $tagurl; ?>
                 </h4>
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
	    $sharing_shortcode = shortcode_exists('fusion_sharing');
	    if ($sharing_shortcode)
		echo do_shortcode('[fusion_sharing tagline="SHARE THIS PROJECT:" tagline_color="" title="" link="" description="" pinterest_image="" icons_boxed="yes" icons_boxed_radius="4px" color_type="brand" box_colors="" icon_colors="" tooltip_placement="" backgroundcolor="" class="" id=""][/fusion_sharing]');
	    ?>
    </div>
</div>
<?php
    $content = get_the_content($post->ID);
    $content_length = strlen($content);
    $border_bottom = "";
    if ($content_length==0)
	$border_bottom = " no-border-bottom";
?>
<div class="col-md-7 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr">
    <div class="col-md-12 pblctn_paramtr padding_left<?php echo $border_bottom; ?>">
	<?php if (!$hide_title) { ?>
        <h2><?php
		echo get_the_title($post->ID);
	?></h2>
	<?php } ?>
        <p>
            <?php
                $content = apply_filters('the_content', $content);
                echo do_shortcode($content);
            ?>
        </p>
     </div>
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
	<h4 class="rght_sid_wdgt_hedng uppercase"><?php _e( 'Related Stories' , SCP_SLUG ); ?></h4>
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

