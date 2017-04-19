<?php
/**
 * The Template for displaying all single story
 *
 * @package WordPress
 * @subpackage Twenty_Twelve
 * @since Twenty Twelve 1.0
 */
get_header(); ?>

	<div id="content" class="row">
			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_story_template_part( 'content', 'story' ); ?>

			<?php endwhile; // end of the loop. ?>

	</div><!-- #row -->
	<!--
    <nav class="nav-single">
        <h3 class="assistive-text"><?php _e( 'Post navigation', SCP_SLUG ); ?></h3>
        <span class="nav-previous">
			<?php previous_post_link( '%link', '<span class="meta-nav">' . _x( '&larr;', 'Previous post link', SCP_SLUG ) . '</span> %title' ); ?>
        </span>
        <span class="nav-next">
			<?php next_post_link( '%link', '%title <span class="meta-nav">' . _x( '&rarr;', 'Next post link', SCP_SLUG ) . '</span>' ); ?>
        </span>
    </nav>
    .nav-single -->

<?php get_footer(); ?>
