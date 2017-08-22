<?php
/**
 * The Template for displaying all single story
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
get_header(); ?>
<?php
	global $wpdb;
	global $enable_sidebar;
	$table = $wpdb->prefix."term_relationships";

	$termobject = get_queried_object();
	$term_id = $termobject->term_id;
	$postids = $wpdb->get_results("select object_id from $table where term_taxonomy_id=".$term_id,OBJECT_K);

	if(!empty($postids))
	{
		$postids = array_keys($postids);
	}

	$args = array('orderby' => 'term_order','order' => 'ASC','hide_empty' => true);
	$tags = get_terms('story_tag', $args);
?>
	<div id="content" class="row">
	<?php if ($enable_sidebar) { ?>
        <div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
              <?php get_stories_side_nav($termobject->taxonomy, $termobject->slug); ?>
        </div>
	<?php } ?>
        <div class="<?php if ($enable_sidebar) { ?>col-md-8<?php } else { ?>col-md-12<?php } ?> col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
            <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
               <?php get_storiesmap($postids);?>
            </div>

            <div class="col-md-12 col-sm-12 col-xs-12 profile-filters">
		<?php get_story_filters(); ?>
	    </div>
            <div class="entry-content">
                <p><?php _e( "Apologies, but no results were found. Perhaps searching will help find a related post.", "nn-story-custom-post-type" ); ?></p>
           	</div>

        </div>
    </div><!-- #row -->

<?php get_footer(); ?>
