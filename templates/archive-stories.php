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
				extract($_REQUEST);
				$pageposts1 = $pageposts2 = $pageposts3 = $pageposts4 = array();
				if(!empty($searchtext))
				{
					$s = trim($searchtext, " ");
					$querystr = "SELECT *
								FROM $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->terms
									WHERE ($wpdb->terms.name LIKE '%$s%'
									OR $wpdb->postmeta.meta_value LIKE '%$s%'
									OR $wpdb->posts.post_content LIKE '%$s%'
								OR $wpdb->posts.post_title LIKE '%$s%')
								AND $wpdb->posts.post_type = 'stories'
								AND $wpdb->posts.ID = $wpdb->postmeta.post_id
								AND $wpdb->posts.ID = $wpdb->term_relationships.object_id
								AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
								AND $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
								ORDER BY $wpdb->posts.post_date DESC";
					$pageposts1 = $wpdb->get_results($querystr, OBJECT_K);
				}
				if(!empty($taxonomy_state))
				{
					$querystr = "SELECT *
								FROM $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->terms
									WHERE ($wpdb->terms.name LIKE '%$taxonomy_state%')
								AND $wpdb->posts.post_type = 'stories'
								AND $wpdb->posts.ID = $wpdb->postmeta.post_id
								AND $wpdb->posts.ID = $wpdb->term_relationships.object_id
								AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
								AND $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
								ORDER BY $wpdb->posts.post_date DESC";
					$pageposts2 = $wpdb->get_results($querystr, OBJECT_K);
				}
				if(!empty($taxonomy_program))
				{
					$querystr = "SELECT *
								FROM $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->terms
									WHERE ($wpdb->terms.name LIKE '%$taxonomy_program%')
								AND $wpdb->posts.post_type = 'stories'
								AND $wpdb->posts.ID = $wpdb->postmeta.post_id
								AND $wpdb->posts.ID = $wpdb->term_relationships.object_id
								AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
								AND $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
								ORDER BY $wpdb->posts.post_date DESC";
					$pageposts3 = $wpdb->get_results($querystr, OBJECT_K);
				}
				if(!empty($taxonomy_grade_level))
				{
					$querystr = "SELECT *
								FROM $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->terms
									WHERE ($wpdb->terms.name LIKE '%$taxonomy_grade_level%')
								AND $wpdb->posts.post_type = 'stories'
								AND $wpdb->posts.ID = $wpdb->postmeta.post_id
								AND $wpdb->posts.ID = $wpdb->term_relationships.object_id
								AND $wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id
								AND $wpdb->term_taxonomy.term_id = $wpdb->terms.term_id
								ORDER BY $wpdb->posts.post_date DESC";
					$pageposts4 = $wpdb->get_results($querystr, OBJECT_K);
				}
				
				if(!empty($pageposts1) && !empty($pageposts2) && !empty($pageposts3) && !empty($pageposts4)):
					$pageposts = array_intersect_key($pageposts1, $pageposts2, $pageposts3, $pageposts4);
				elseif(!empty($pageposts1) && !empty($pageposts2) && !empty($pageposts3)):
					$pageposts = array_intersect_key($pageposts1, $pageposts2, $pageposts3);
				elseif(!empty($pageposts1) && !empty($pageposts3) && !empty($pageposts4)):
					$pageposts = array_intersect_key($pageposts1, $pageposts3, $pageposts4);
				elseif(!empty($pageposts1) && !empty($pageposts2) && !empty($pageposts4)):
					$pageposts = array_intersect_key($pageposts1, $pageposts2, $pageposts4);
				elseif(!empty($pageposts2) && !empty($pageposts3) && !empty($pageposts4)):
					$pageposts = array_intersect_key($pageposts2, $pageposts3, $pageposts4);
				elseif(!empty($pageposts1) && !empty($pageposts2)):
					$pageposts = array_intersect_key($pageposts1, $pageposts2);
				elseif(!empty($pageposts1) && !empty($pageposts3)):
					$pageposts = array_intersect_key($pageposts1, $pageposts3);
				elseif(!empty($pageposts1) && !empty($pageposts4)):
					$pageposts = array_intersect_key($pageposts1, $pageposts4);
				elseif(!empty($pageposts2) && !empty($pageposts3)):
					$pageposts = array_intersect_key($pageposts2, $pageposts3);
				elseif(!empty($pageposts2) && !empty($pageposts4)):
					$pageposts = array_intersect_key($pageposts2, $pageposts4);
				elseif(!empty($pageposts3) && !empty($pageposts4)):
					$pageposts = array_intersect_key($pageposts3, $pageposts4);
				elseif(!empty($pageposts1)):
					$pageposts = $pageposts1;
				elseif(!empty($pageposts2)):
					$pageposts = $pageposts2;
				elseif(!empty($pageposts3)):
					$pageposts = $pageposts3;
				elseif(!empty($pageposts4)):
					$pageposts = $pageposts4;	
				endif;						
					if(isset($pageposts) && !empty($pageposts))
					{
						?>
							<div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
								 <?php get_storiesmap();?>
							</div>
							
							<div class="col-md-3 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
								 <?php get_story_search($searchtext, $taxonomy_state, $taxonomy_program, $taxonomy_grade_level); ?>
							</div>
							
							<div class="col-md-9 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr">
								<header class="archive-header">
									<h1 class="archive-title">
										 <?php printf( __( 'Search for : %s', 'twentytwelve' ), '<span>' .$searchtext . '</span>' );?>
									</h1>
								</header><!-- .archive-header -->
		
								<?php
									foreach($pageposts as $post )
									{
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
                        	<div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
								 <?php get_storiesmap();?>
							</div>
							
							<div class="col-md-3 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
								 <?php get_story_search($searchtext, $taxonomy_state, $taxonomy_program, $taxonomy_grade_level); ?>
							</div>
							
							<div class="col-md-9 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr">
								<header class="archive-header">
									<h1 class="archive-title">
										 <?php printf( __( 'Search for : %s', 'twentytwelve' ), '<span>' .$searchtext . '</span>' );?>
									</h1>
								</header><!-- .archive-header -->
								<div class="col-md-12 pblctn_paramtr padding_left">
                            		<?php _e( 'Sorry, but nothing matched your search criteria. Please try again with some different keywords.' ); ?>    
                                </div>
							</div>
                        
                        <?php
					}
			}
			else
			{
				 if ( have_posts() )
				 { ?>
					<div class="col-md-12 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
						 <?php get_storiesmap();?>
					</div>
					
					<div class="col-md-3 col-sm-12 col-xs-12 pblctn_right_sid_mtr">
						 <?php get_story_search(); ?>
					</div>
					
					<div class="col-md-9 col-sm-12 col-xs-12 pblctn_lft_sid_img_cntnr">
						<?php while ( have_posts() ) : the_post(); ?>
			
							<?php get_story_template_part( 'content', 'substory' ); ?>
			
						<?php endwhile; ?>
					</div>
				<?php
				}
			}
		?>
         
	</div><!-- #row -->
	
<?php get_footer(); ?>