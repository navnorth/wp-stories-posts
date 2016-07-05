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
	remove_filter ('the_content', 'wpautop');
	$img_url = wp_get_attachment_url( get_post_thumbnail_id($post->ID) );
    $img_alt = get_post_meta(get_post_thumbnail_id($post->ID), '_wp_attachment_image_alt', true);

    /*
    $img_args = array( 'post_type' => 'attachment', 'orderby' => 'menu_order', 'order' => 'ASC', 'post_mime_type' => 'image' ,'post_status' => null, 'numberposts' => null, 'post_parent' => $post->ID );

    $attachments = get_posts($img_args);
    if ($attachments) {
        foreach ( $attachments as $attachment ) {
            $img_alt = get_post_meta($attachment->ID, '_wp_attachment_image_alt', true);
            $img_url = wp_get_attachment_url( $attachment->ID );
        }
    }
    */

	if(isset($_REQUEST['action']) && !empty($_REQUEST['action']) && $_REQUEST['action'] == 'Search')
	{
		/* these appear to be the same thing
        if (parse_url(get_permalink($post->ID), PHP_URL_QUERY))
		{
			$link = get_permalink($post->ID)."&searchresult=story";
		}
		else
		{
			$link = get_permalink($post->ID)."?searchresult=story";
		}
        */
        $link = get_permalink($post->ID)."?searchresult=story&back=".urlencode($_SERVER['REQUEST_URI']);
	}
	else
	{
		$link = get_permalink($post->ID)."?back=".urlencode($_SERVER['REQUEST_URI']);
	}
?>
<div class="col-md-12 pblctn_paramtr padding_left">
    <h3>
        <a href="<?php echo $link; ?>">
            <?php echo get_the_title($post->ID); ?>
        </a>
    </h3>
    <?php if(isset($img_url) && !empty($img_url)) : ?>
        <div class="scp_feature_image">
            <img class="featured_item_image" src="<?php echo $img_url; ?>" alt="<?php echo $img_alt; ?>" />
        </div>
    <?php endif; ?>
	<h4 class="substory_loc">
	    <?php
		    if($story_district = get_post_meta($post->ID, "story_district", true))
			    {
				if (strlen($story_district)>0)
				    echo $story_district.', ';
			    }
			    $states = get_the_terms( $post->ID , 'state' );
			    if(isset($states) && !empty($states))
			    {
				    foreach($states as $state)
				    {
					    echo $state->name;
					    break;
				    }
			    }
			    $grades = get_the_terms( $post->ID , 'grade_level' );
			    if(isset($grades) && !empty($grades))
			    {
				if ($states)
					echo ' - ';
					
				foreach($grades as $grade)
				{
					echo $grade->name;
					break;
				}
			    }
		    ?>
	</h4>
	    <?php
			$content = display_story_content($post->ID, 300);
			
			//Fixing issue with blockquotes inside the paragraph tags causing extra p tags before and after
			$pos = strpos($content,"<blockquote>");
			
			if ($pos === false) {
				echo '<p>'.$content.'</p>';
			} else {
				//Filter block quote
				$startPos = strpos($content,"<blockquote>");
				$endPos = strpos($content,"</blockquote>") + 13;
				$blockquote = substr($content,$startPos,$endPos);
				$other_content = "<p>".trim(substr($content,$endPos, strlen($content)-$endPos))."</p>";
				echo $blockquote.$other_content;	
			}
	    ?>
	    <?php
		    $topics = get_the_terms( $post->ID , 'story_tag' );
		    if(isset($topics) && !empty($topics))
		    {
			    $postedin = '<strong>'.__( 'Topic' , SCP_SLUG );
		if (count($topics) > 1) $postedin .= 's';
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
