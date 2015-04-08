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

	$args = array('orderby' => 'term_order','order' => 'ASC','hide_empty' => false);
	$tags = get_terms('story_tag', $args);
?>
	<div class="row">

            <div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
				 <?php get_stories_side_nav($termobject->taxonomy, $termobject->slug); ?>
            </div>

            <div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
                <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
                     <?php get_storiesmap($postids);?>
                </div>
                <header class="tax-header">
                    <h1 class="tax-title">
                         <?php printf( __( 'Results: %s', 'twentytwelve' ), '<i>'.$termobject->name.'</i>' );?>
                    </h1>
                    <div class="topics-search-box">
                    	<form method="get" action="<?php echo site_url();?>/stories">
                            <input type="hidden" name="action" value="search" />
                            <select name="story_tag" onchange="formsubmit(this);">
                                <option value="">Filter by Topic</option>
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

                <?php while ( have_posts() ) : the_post(); ?>
					<?php get_story_template_part( 'content', 'substory' ); ?>
				<?php endwhile; // end of the loop. ?>
            </div>
	</div>

<?php get_footer(); ?>