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
	if(isset($_REQUEST['action']) && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'Search')
	{
		if (parse_url(get_permalink($post->ID), PHP_URL_QUERY))
		{
			$link = get_permalink($post->ID)."&searchresult=story";
		}
		else
		{
			$link = get_permalink($post->ID)."?searchresult=story";
		}
	}
	else
	{
		$link = get_permalink($post->ID);
	}
?>
<div class="col-md-12 pblctn_paramtr padding_left">
    <h3>
        <a href="<?php echo $link; ?>">
            <?php echo get_the_title($post->ID); ?>
        </a>
    </h3>
    <?php if(isset($url) && !empty($url)) : ?>
        <div class="scp_feature_image">
            <img class="featured_item_image" src="<?php echo $url; ?>" />
        </div>
    <?php endif; ?>
    <h4 class="substory_loc">
    	<?php
    		if(get_post_meta($post->ID, "story_district", true))
			{
				echo get_post_meta($post->ID, "story_district", true).', ';
			}
			$states = get_the_terms( $post->ID , 'state' );
			if(isset($states) && !empty($states))
			{
				foreach($states as $state)
				{
					echo $state->name.' - ';
					break;
				}
			}
			$grades = get_the_terms( $post->ID , 'grade_level' );
			if(isset($grades) && !empty($grades))
			{
				foreach($grades as $grade)
				{
					echo $grade->name;
					break;
				}
			}
		?>
    </h4>
    <p>
        <?php
		    $content = get_the_content($post->ID);
		    echo substr($content, 0, 300).'...';
        ?>
    </p>

	<?php
		$topics = get_the_terms( $post->ID , 'story_tag' );
		if(isset($topics) && !empty($topics))
		{
			$postedin = '<strong>Topic';
            if (count(topics) > 1) $postedin .= 's';
            $postedin .= ':</strong> ';

			foreach($topics as $topic)
			{
				$termlink = get_term_link($topic->term_id, $topic->taxonomy);
                $postedin .= '<a href="'.$termlink.'">'.$topic->name.'</a>, ';
			}
            echo '<p>' . trim($postedin,', ') . '</p>';
		}
	?>

</div>
