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
	<div class="row">
			<?php
				if($_REQUEST['action'] == 'search')
				{
					extract($_REQUEST);
					$searcharr = array();
					if(!empty($story_taxonomy))
					{
						$searcharr = array('taxonomy' => $story_taxonomy, 'field' => 'slug', 'terms' => $term);
					}

					if(!empty($searcharr))
					{
						$args = array('post_type' => 'stories','post__in' => $postids, 'tax_query' => array($searcharr));
						$query = new WP_Query( $args );
						$pageposts = $wpdb->get_results($query->request, OBJECT_K);
					}

					if(isset($pageposts) && !empty($pageposts))
					{?>
                        <div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
                        	<?php get_stories_side_nav($termobject->taxonomy, $termobject->slug); ?>
                        </div>
                        <div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
                            <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
                                 <?php get_storiesmap(array_keys($pageposts));?>
                            </div>
                            <header class="tax-header">
                                <h1 class="tax-title">
                                     <?php printf( __( 'Results: %s', 'twentytwelve' ), '<i>'.$termobject->name.'</i> <span>(' .count($pageposts).' '.story_plural(count($pageposts)).')</span>' );?>
                                </h1>
                                <div class="topics-search-box">
                                    <form method="get">
                                        <input type="hidden" name="action" value="search" />
                                        <input type="hidden" name="story_taxonomy" value="story_tag" />
                                        <select name="term" onchange="formsubmit(this);">
                                            <option value="">Filter by Topic</option>
                                            <?php
												foreach($tags as $tag)
												{
													$count = get_counts($tag->term_id,$postids);
													if ($count > 0)
                                                    {
                                                        if(isset($term) && !empty($term) && $term == $tag->slug):
    														$check='selected="selected"'; else: $check = '';
    													endif;
    													echo '<option '. $check .' value="'.$tag->slug.'">'.$tag->name.' ('.$count.')</option>';
                                                    }
												}
											?>
                                        </select>
                                    </form>
                                </div>
                            </header>

                            <?php
                                foreach($pageposts as $key => $data )
                                {
                                    $post = get_post($key);
                                    setup_postdata($post);
                                    get_story_template_part( 'content', 'substory' );
                                }
                                wp_reset_postdata();
                            ?>
                        </div>
                    <?php
					}
					else
					{
						?>
                        <div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
                        	<?php get_stories_side_nav($termobject->taxonomy, $termobject->slug); ?>
                        </div>
                        <div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
                            <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
                                 <?php get_storiesmap();?>
                            </div>
                            <header class="tax-header">
                                <h1 class="tax-title">
                                     <?php printf( __( 'Results: %s', 'twentytwelve' ), '<i>'.$termobject->name.'</i> <span>(0 Stories)</span>' );?>
                                </h1>
                                <div class="topics-search-box">
                                    <form method="get">
                                        <input type="hidden" name="action" value="search" />
                                        <input type="hidden" name="story_taxonomy" value="story_tag" />
                                        <select name="term" onchange="formsubmit(this);">
                                            <option value="">Filter by Topic</option>
                                            <?php
												foreach($tags as $tag)
												{
													$count = get_counts($tag->term_id,$postids);
													if ($count > 0)
                                                    {
                                                        if(isset($term) && !empty($term) && $term == $tag->slug):
    														$check='selected="selected"'; else: $check = '';
    													endif;
    													echo '<option '. $check .' value="'.$tag->slug.'">'.$tag->name.' ('.$count.')</option>';
                                                    }
												}
											?>
                                        </select>
                                    </form>
                                </div>
                            </header>
                            <div class="col-md-12 pblctn_paramtr padding_left">
                                <?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.' ); ?>
                            </div>
                        </div>
                        <?php
					}
				}
				else
				{
					?>
					<div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
						 <?php get_stories_side_nav($termobject->taxonomy, $termobject->slug); ?>
					</div>
					<div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
						<div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
							 <?php get_storiesmap($postids);?>
						</div>
                        <header class="tax-header">
                            <h1 class="tax-title">
                                 <?php printf( __( 'Results: %s', 'twentytwelve' ), '<i>'.$termobject->name.'</i> <span>(' .count($postids).' '.story_plural(count($postids)).')</span>' );?>
                            </h1>
                            <div class="topics-search-box">
                                <form method="get">
                                    <input type="hidden" name="action" value="search" />
                                    <input type="hidden" name="story_taxonomy" value="story_tag" />
                                    <select name="term" onchange="formsubmit(this);">
                                        <option value="">Filter by Topic</option>
                                        <?php
                                            foreach($tags as $tag)
                                            {
                                                $count = get_counts($tag->term_id,$postids);
                                                if ($count > 0)
                                                {
                                                    if(isset($termobject->slug) && !empty($termobject->slug) && $termobject->slug == $tag->slug):
                                                        $check='selected="selected"'; else: $check = '';
                                                    endif;
                                                    echo '<option '. $check .' value="'.$tag->slug.'">'.$tag->name.' ('.$count.')</option>';
                                                }
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
                    <?php
				}
			?>
	</div>
<?php get_footer(); ?>