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
				$searcharr = array();
				if(!empty($searchtext))
				{
					$s = trim($searchtext, " ");
					$querystr = "SELECT ID
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
					$searcharr[] = array('taxonomy' => 'state', 'field' => 'slug', 'terms' => array( "$taxonomy_state" ),);
				}
				if(!empty($taxonomy_program))
				{
					$searcharr[] = array('taxonomy' => 'program', 'field' => 'slug', 'terms' => array( "$taxonomy_program" ),);
				}
				if(!empty($taxonomy_grade_level))
				{
					$searcharr[] = array('taxonomy' => 'grade_level', 'field' => 'slug', 'terms' => array( "$taxonomy_grade_level" ),);
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
				/* $settings = get_post_meta(107, "settings", true);
				 $styleSettings = get_post_meta(107, "styleSettings", true);
				 $slides = get_post_meta(107, "slides", true);
				 print_r($settings);
				 print_r($styleSettings);
				 print_r($slides);
				 die;*/
				 
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