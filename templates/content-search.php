<?php
/**
 * The Template for displaying all stories post type
 *
 * @package WordPress
 * @subpackage WP Stories Plugin
 * @since 0.2.8
 */
global $post;
$post_id = get_the_ID();
$img_url = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
$img_alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-content">
        <?php if(isset($img_url) && !empty($img_url)) : ?>
        <div class="col-md-3 col-sm-6 col-xs-12 search_story_image">
            <img class="search_story_featured_image" src="<?php echo $img_url; ?>" alt="<?php echo $img_alt; ?>" />
        </div>
        <?php endif; ?>
        <div class="col-md-9 col-sm-6 col-xs-12 search_story_content<?php if(empty($img_url)) : ?>_full<?php endif; ?>">
            <h3><a href="<?php the_permalink(); ?>"><?php printf(__('Story: %s', SCP_SLUG), get_the_title()) ?></a></h3>
            <h4 class="recent_story_loc">
            <?php
                if($story_district = get_post_meta($post_id, "story_district", true))
                {
                    if (strlen($story_district)>0)
                        echo $story_district.', ';
                }
                $states = get_the_terms( $post_id , 'state' );
                if(isset($states) && !empty($states))
                {
                        foreach($states as $state)
                        {
                                echo $state->name;
                                break;
                        }
                }
                $grades = get_the_terms( $post_id , 'grade_level' );
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
            <?php echo display_story_content($post_id, 300); ?>
        </div>
    </div>
    <?php
    if (function_exists('story_entry_meta'))
        story_entry_meta();
    ?>
</article>
