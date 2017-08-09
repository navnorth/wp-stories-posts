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
global $scp_session;
global $wpdb;
global $enable_sidebar;

	// Initialize WP_Session
	if (!isset($scp_session))
		$scp_session = WP_Session::get_instance();
		
	if (isset($_POST['active_tab'])) {
		$scp_session['active_tab'] = isset($_POST['active_tab'])?$_POST['active_tab']:'all';
		$active_tab = $scp_session['active_tab'];
	} elseif (isset($scp_session['active_tab'])) {
		$active_tab = $scp_session['active_tab'];
	}
	
	if ($active_tab=="all")
		unset($scp_session['post_ids']);
	
	$table = $wpdb->prefix."term_relationships";
	$termobject = get_queried_object();
	$term_id = $termobject->term_id;
	
	if (isset($_POST['post_ids']) || isset($scp_session['post_ids'])) {
		if ($_POST['post_ids'])
			$scp_session['post_ids'] = $_POST['post_ids'];
			
		$post_ids = json_decode($scp_session['post_ids']);
		$post_ids = implode(",", $post_ids);
		$postids = $wpdb->get_results("select object_id from $table where term_taxonomy_id=".$term_id." and object_id in (".$post_ids.")",OBJECT_K);
	} else {
		$postids = $wpdb->get_results("select object_id from $table where term_taxonomy_id=".$term_id,OBJECT_K);
	}
	
	if(!empty($postids))
	{
		$postids = array_keys($postids);
	}
	
	$args = array('orderby' => 'term_order','order' => 'ASC','hide_empty' => true);
	$tags = get_terms('story_tag', $args);
	
?>
	<div id="content" class="row">
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
				$args = array('post_type' => 'stories','post__in' => $postids, 'posts_per_page' => -1, 'tax_query' => array($searcharr));
				//Apply sort args
				$args = apply_sort_args($args);
				$query = new WP_Query( $args );
				$pageposts = $wpdb->get_results($query->request, OBJECT_K);
			}

			if(isset($pageposts) && !empty($pageposts))
			{?>
			<?php if ($enable_sidebar): ?>
		<div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
			<?php get_stories_side_nav($termobject->taxonomy, $termobject->slug, NULL, $active_tab); ?>
		</div>
			<?php endif; ?>
		<div class="<?php if ($enable_sidebar) { ?>col-md-8<?php } else { ?>col-md-12<?php } ?> col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
		    <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
			 <?php get_storiesmap(array_keys($pageposts));?>
		    </div>
		    <header class="tax-header">
			<h1 class="tax-title">
			     <?php printf( __( 'Results: %s', SCP_SLUG ), '<i>'.$termobject->name.'</i> <span>(' .count($pageposts).' '.story_plural(count($pageposts)).')</span>' );?>
			</h1>
			<div class="topics-search-box">
			    <form method="get">
				<input type="hidden" name="action" value="search" />
				<input type="hidden" name="story_taxonomy" value="story_tag" />
				<select name="term" onchange="formsubmit(this);">
				    <option value=""><?php _e( 'Filter by Topic' , SCP_SLUG ); ?></option>
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
			    <?php get_sort_box($postids); ?>
			</div>
		    </header>

		    <?php
		    echo '<div id="content-stories">';
			foreach($pageposts as $key => $data )
			{
			    $post = get_post($key);
			    setup_postdata($post);
			    get_story_template_part( 'content', 'substory' );
			}
			wp_reset_postdata();
		    echo '</div>';
		    ?>
		</div>
	    <?php
				}
				else
				{
					?>
			<?php if ($enable_sidebar) { ?>
		<div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
			<?php get_stories_side_nav($termobject->taxonomy, $termobject->slug, NULL, $active_tab); ?>
		</div>
		<?php } ?>
		<div class="<?php if ($enable_sidebar) { ?>col-md-8<?php } else { ?>col-md-12<?php } ?> col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
		    <div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
			 <?php get_storiesmap();?>
		    </div>
		    <header class="tax-header">
			<h1 class="tax-title">
			     <?php printf( __( 'Results: %s', SCP_SLUG ), '<i>'.$termobject->name.'</i> <span>(' .count($postids).' '.story_plural(count($postids)).')</span>' );?>
			</h1>
			<div class="topics-search-box">
			    <form method="get">
				<input type="hidden" name="action" value="search" />
				<input type="hidden" name="story_taxonomy" value="story_tag" />
				<select name="term" onchange="formsubmit(this);">
				    <option value=""><?php _e( 'Filter by Topic' , SCP_SLUG ); ?></option>
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
			    <?php get_sort_box($postids); ?>
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
				<?php if ($enable_sidebar) { ?>
				<div class="col-md-4 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
					 <?php get_stories_side_nav($termobject->taxonomy, $termobject->slug, NULL, $active_tab); ?>
				</div>
				<?php } ?>
				<div class="<?php if ($enable_sidebar) { ?>col-md-8<?php } else { ?>col-md-12<?php } ?> col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr map_cntnr">
					<div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
						 <?php get_storiesmap($postids);?>
					</div>
					<div class="col-md-12 col-sm-12 col-xs-12 profile-filters">
							<?php get_story_filters($termobject->taxonomy, $termobject->slug); ?>
					</div>
					
					<?php
					//Get Max number of pages
					$postquery = new WP_Query(array('post_type' => 'stories', 'post__in' => $postids, 'posts_per_page' => 6));
					$max_page = $postquery->max_num_pages;
					
					$paged = 1;
					if ($_GET['page'])
						$paged = (int)$_GET['page'];
					
					//Change query to show just  10
					$args = array('post_type' => 'stories', 'post__in' => $postids, 'posts_per_page' => 6*$paged);
					
					//Apply sort args
					$args = apply_sort_args($args);
					
					$postquery = new WP_Query( $args );
					
					echo '<div id="content-stories">';
					$i = 1;
					while ( $postquery->have_posts() ) : $postquery->the_post(); ?>
						<?php
						if (($i%3)==1)
							echo '<div class="profile-row">';
										
						echo '<div class="col-md-4">'; ?>
						<?php get_story_template_part( 'content', 'subprofile' ); ?>
						<?php
						echo '</div>';
								
						if (($i%3)==0)
							echo '</div>';
						$i++;
						?>
					<?php endwhile; // end of the loop.
					echo '</div>';
					
					//Show Load More Button
					if ($post_count>6 & $paged<$max_page) {
						$base_url = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://") . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
						
						if (strpos($base_url,"page"))
							$base_url = substr($base_url,0,strpos($base_url, "page")-1);
							
						echo '<div class="col-md-12 pblctn_paramtr padding_left"><a href="?page='.($paged+1).'" data-page-number="'.($paged+1).'" data-page="state" data-base-url="'.$base_url.'" data-max-page="'.$max_page.'" data-posts="'.json_encode($postids).'" class="btn-load-more">Load More</a></div>';		
					}
					?>
				</div>
	    <?php
			}
		?>
	</div>
<?php get_footer(); ?>