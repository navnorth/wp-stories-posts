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
<?php 
	$url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
?>
<div class="col-md-12 pblctn_paramtr padding_left">
    <h3>
        <a href="<?php echo get_the_permalink($post->ID); ?>">
            <?php echo get_the_title($post->ID); ?>
        </a>
    </h3>
    <?php if(isset($url) && !empty($url)) : ?>
        <div class="scp_feature_image">
            <img class="featured_item_image" src="<?php echo $url; ?>" />
        </div>
    <?php endif; ?>
    <p>
        <?php 
		    $content = get_the_content($post->ID);
		    echo substr($content, 0, 300);
        ?>
    </p>
    <p>
    	<?php
			$state = get_the_terms( $post->ID , 'state' );
			$program = get_the_terms( $post->ID , 'program' );
			$grade_level = get_the_terms( $post->ID , 'grade_level' );
			
			if(!isset($state) || empty($state) )
				$state = array();
			if(!isset($program) || empty($program) )
				$program = array();
			if(!isset($grade_level) || empty($grade_level) )
				$grade_level = array();
			
			$terms = array_merge($state,$program,$grade_level);
			if(isset($terms) && !empty($terms))
			{
				$postedin = '';
				foreach($terms as $term)
				{
					$link = get_term_link($term->term_id, $term->taxonomy);
					$postedin .= '<a href="'.$link.'">'.$term->name.'</a>, ';
				}
			}
		?>
       	Posted in : <?php echo trim($postedin,','); ?>
    </p>    
</div>
