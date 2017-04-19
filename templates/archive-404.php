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
        <div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
              <?php get_stories_side_nav($termobject->taxonomy, $termobject->slug); ?>
        </div>
        <div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
            <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
               <?php get_storiesmap($postids);?>
            </div>

            <header class="tax-header">
                <h1 class="tax-title">
                	<?php _e( "Nothing Found", "nn-story-custom-post-type" ) ?>
                </h1>
                <div class="topics-search-box">
                    	<form method="get" action="<?php echo site_url();?>/stories">
                            <input type="hidden" name="action" value="search" />
                            <select name="story_tag" onchange="formsubmit(this);">
                                <option value=""><?php _e( "Select Topics", "nn-story-custom-post-type" ); ?></option>
                                <?php
                                    foreach($tags as $tag)
                                    {
                                        $count = get_counts($tag->term_id,$postids);
										echo '<option value="'.$tag->slug.'">'.$tag->name.' ('.$count.')</option>';
                                    }
                                ?>
                            </select>
                        </form>
                </div>
            </header>
            <div class="entry-content">
                <p><?php _e( "Apologies, but no results were found. Perhaps searching will help find a related post.", "nn-story-custom-post-type" ); ?></p>
           	</div>

        </div>
    </div><!-- #row -->

<?php get_footer(); ?>
