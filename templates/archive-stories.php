<?php
/**
 * The Template for displaying all single story
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
get_header(); ?>

	<div class="row">
    	<?php
			if(isset($_REQUEST['action']) && !empty($_REQUEST['action']))
			{
				global $wpdb;
				if($_REQUEST['action'] == 'showall')
				{
					$postquery = new WP_Query(array('post_type' => 'stories', 'postperpage' => -1));

                    $post_ids = wp_list_pluck( $postquery->posts, 'ID' );

                    $args = array('orderby' => 'term_order','order' => 'ASC','hide_empty' => true);
                    $tags = get_terms('story_tag', $args);

					if ( $postquery->have_posts() ) ?>
						<div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
							 <?php get_stories_side_nav(); ?>
						</div>

						<div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
							<div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
								 <?php get_storiesmap();?>
							</div>

                            <header class="tax-header">
                                <h1 class="tax-title">
                                     <?php printf( __( 'Results: %s', 'twentytwelve' ), '<i>All Stories</i> <span>(' .count($post_ids).' '.story_plural(count($postids)).')</span>' );?>
                                </h1>
                                <div class="topics-search-box">
                                    <form method="get">
                                        <input type="hidden" name="action" value="showall" />
                                        <select name="term" id="showalltopic">
                                            <option value="">Filter by Topic</option>
                                            <?php
                                                foreach($tags as $tag)
                                                {
                                                    $count = get_counts($tag->term_id,$post_ids);
                                                    if(isset($term) && !empty($term) && $term == $tag->slug):
                                                        $check='selected="selected"'; else: $check = '';
                                                    endif;
                                                    echo '<option '. $check .' value="'.site_url().'/stories/story_tag/'.$tag->slug.'">'.$tag->name.' ('.$count.')</option>';
                                                }
                                            ?>
                                        </select>
                                    </form>
                                </div>
                            </header>

							<?php while ( $postquery->have_posts() ) : $postquery->the_post(); ?>
                                    <?php get_story_template_part( 'content', 'substory' ); ?>
                            <?php endwhile; ?>
					 	</div>
					<?php
				}

				/*?>if($_REQUEST['action'] == 'Search')
				{
					extract($_REQUEST);
					$searcharr = array();
					if(!empty($searchtext) || !empty($district_location) || !empty($district_size))
					{
						$s = trim($searchtext, " ");
						if(!empty($district_location))
						{
							foreach($district_location as $location)
							{
								$searchlocation .=  "location.meta_value LIKE '%$location%' OR ";
							}
							$searchlocation = substr($searchlocation, 0, -3);
						}
						else
						{
							$searchlocation .=  "$wpdb->posts.post_content LIKE '%$s%'
											 		OR $wpdb->posts.post_title LIKE '%$s%'";
						}

						if(!empty($district_size))
						{
							foreach($district_size as $size)
							{
								$searchsize .=  "size.meta_value LIKE '%$size%' OR ";
							}
							$searchsize = substr($searchsize, 0, -3);
						}
						else
						{
							$searchsize .=  "$wpdb->posts.post_content LIKE '%$s%'
											 		OR $wpdb->posts.post_title LIKE '%$s%'";
						}

						$querystr = "SELECT ID FROM $wpdb->posts
								 LEFT JOIN $wpdb->postmeta as location
								 ON $wpdb->posts.ID = location.post_id
								 LEFT JOIN $wpdb->postmeta as size
								 ON $wpdb->posts.ID = size.post_id
									WHERE (($searchlocation)
										AND ($searchsize)
										AND ($wpdb->posts.post_content LIKE '%$s%'
											 OR $wpdb->posts.post_title LIKE '%$s%'))
								AND $wpdb->posts.post_type = 'stories'
								AND $wpdb->posts.ID = location.post_id
								AND $wpdb->posts.ID = size.post_id
								ORDER BY $wpdb->posts.post_date DESC";
						$pageposts1 = $wpdb->get_results($querystr, OBJECT_K);
					}

					if(!empty($taxonomy_state))
					{
						$searcharr[] = array('taxonomy' => 'state', 'field' => 'slug', 'terms' => $taxonomy_state,);
					}
					if(!empty($taxonomy_program))
					{
						$searcharr[] = array('taxonomy' => 'program', 'field' => 'slug', 'terms' => $taxonomy_program,);
					}
					if(!empty($taxonomy_grade_level))
					{
						$searcharr[] = array('taxonomy' => 'grade_level', 'field' => 'slug', 'terms' => $taxonomy_grade_level,);
					}
					if(!empty($story_tags))
					{
						$searcharr[] = array('taxonomy' => 'story_tag', 'field' => 'slug', 'terms' => $story_tags,);
					}

					if(!empty($searcharr))
					{
						$args = array('post_type' => 'stories','tax_query' => array('relation' => 'AND',$searcharr),);
						$query = new WP_Query( $args );
						$pageposts2 = $wpdb->get_results($query->request, OBJECT_K);
					}

					if(isset($pageposts1) && isset($pageposts2) )
					{
						if(!empty($pageposts1) && !empty($pageposts2) )
						{
							$pageposts = array_intersect_key($pageposts1, $pageposts2);
						}
						else
						{
							$pageposts = array();
						}
					}
					elseif(isset($pageposts1))
					{
						$pageposts = $pageposts1;
					}
					elseif(isset($pageposts2))
					{
						$pageposts = $pageposts2;
					}

					if(isset($pageposts) && !empty($pageposts))
					{ ?>
                        <div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
                        	<?php get_stories_side_nav($searchtext, $taxonomy_state, $taxonomy_program, $taxonomy_grade_level, $district_location, $district_size,$story_tags); ?>
                        </div>

                        <div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
                            <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
                                 <?php get_storiesmap($pageposts);?>
                            </div>
                            <header class="archive-header">
                                <h1 class="archive-title">
                                     <?php printf( __( 'Results %s', 'twentytwelve' ), '<span>(' . count($pageposts).' Stories)</span>' );?>
                                </h1>
                            </header><!-- .archive-header -->

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
                        <?php get_stories_side_nav($searchtext, $taxonomy_state, $taxonomy_program, $taxonomy_grade_level, $district_location, $district_size,$story_tags); ?>
                        </div>

                        <div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
                            <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
                                 <?php get_storiesmap();?>
                            </div>
                            <header class="archive-header">
                                <h1 class="archive-title">
                                     <?php printf( __( 'Results %s', 'twentytwelve' ), '<span>(0 Stories)</span>' );?>
                                </h1>
                            </header><!-- .archive-header -->
                            <div class="col-md-12 pblctn_paramtr padding_left">
                                <?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.' ); ?>
                            </div>
                        </div>

                        <?php
					}
				}<?php */
			}
			else
			{

                // topics query
                $args = array('post_type' => 'stories','post_status' => 'publish','meta_query' => array(array('key' => 'story_highlight','value' => 'true')));
				$postquery = new WP_Query( $args );

				if ( $postquery->have_posts() )
				{ ?>
					<div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
						 <?php get_stories_side_nav(); ?>
					</div>

					<div class="col-md-8 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
						<div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
							 <?php get_storiesmap();?>
                        </div>
                        <!-- Slider -->
						<div class="slidermainwrpr">
							<div class="slidersubwrpr">
                        		<ul class="bxslider">
									<?php while ( $postquery->have_posts() ) : $postquery->the_post(); ?>
                                    	<li>
                                            <div class="sliderinnrwrap">
                                                <div class="sliderimgwrpr">
                                                    <?php
                                                    $img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
                                                    $img_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);
                                                    if(isset($img_url) && !empty($img_url))
                                                    {
                                                        echo '<img src="'.$img_url.'" alt="'.$img_alt. '" />';
                                                    }
                                                    ?>
                                                </div>
                                                <div class="slidercontentnrwrpr">
                                                	<div class="sldr_top_hdng"> Featured Story: </div>
                                                    <h3>
                                                    	<a href="<?php echo get_the_permalink($post->ID); ?>">
															<?php echo get_the_title($post->ID); ?>
                                                        </a>
                                                    </h3>

                                                    <?php
                                                    $states = get_the_terms( $post->ID, "state" );
                                                    if(isset($states) && !empty($states))
                                                    {
                                                        foreach($states as $state) {
                                                            $state_name = $state->name;
                                                        }
                                                    }
                                                    ?>

                                                    <h4><?php echo get_post_meta($post->ID, "story_district", true) . ', ' . $state_name ?></h4>
                                                    <p>
                                                       <?php
															$content = strip_tags(get_the_content($post->ID));
															echo substr($content, 0, 200)."...";
														?>
                                                    </p>
                                                </div>
                                                <div class="sldr_readmr_btn">
                                                	<a href="<?php echo get_permalink($post->ID);?>">Read More</a>
                                                </div>
                                            </div>
                                        </li>
                                    <?php endwhile; ?>
                        		</ul>
                    		</div>
                        </div>
                    </div>
                <?php
				}
			}
		?>

	</div><!-- #row -->

<?php get_footer(); ?>
