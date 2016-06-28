<?php
/**
 * List of Stories widget class
 *
 * since @0.2.8
 */
class WP_Widget_Recent_Stories extends WP_Widget {

	public function __construct() {
		$widget_ops = array('classname' => 'widget_recent_stories', 'description' => __( "Your website's most recent stories.") );
		parent::__construct('recent-stories', __('Stories: Recent'), $widget_ops);
		$this->alt_option_name = 'widget_recent_stories';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	public function widget($args, $instance) {
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_recent_stories', 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Stories: Recent' );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
		
		$display_site = isset( $instance['display_site'] ) ? $instance['display_site'] : false;

		/**
		 * Filter the arguments for the Recent Posts widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'        => $number,
			'no_found_rows'         => true,
			'post_status'           => 'publish',
			'ignore_sticky_posts'   => true,
                        'post_type'             => 'stories'
		) ) );

		if ($r->have_posts()) :
?>
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
                            <?php
                            $post_id = get_the_ID();
                            $img_url = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
                            $img_alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
                            ?>
                                <?php if(isset($img_url) && !empty($img_url)) : ?>
                                    <div class="recent_story_image">
                                        <img class="recent_story_featured_image" src="<?php echo $img_url; ?>" alt="<?php echo $img_alt; ?>" width="125" height="125" align="left" />
                                    </div>
                                <?php endif; ?>
                                <div class="recent_story_content<?php if(empty($img_url)) : ?>_full<?php endif; ?>">
				<h3><a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a></h3>
				<?php if ($display_site): ?>
					<h4 class="recent_story_loc">
					<?php
					    if($story_district = get_post_meta($post_id, "story_district", true))
					    {
						if (strlen($story_district)>0)
						    echo get_post_meta($post_id, "story_district", true).', ';
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
				<?php endif; ?>
				<?php if ( $show_date ) : ?>
					<span class="post-date">posted <?php echo get_the_date(); ?></span>
				<?php endif; ?>
                            </div>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $args['after_widget']; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_recent_stories', $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
                $instance['display_site'] = isset( $new_instance['display_site'] ) ? (bool) $new_instance['display_site'] : false;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_stories']) )
			delete_option('widget_recent_stories');

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete('widget_recent_posts', 'widget');
	}

	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['number'] ) ? absint( $instance['number'] ) : 5;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
                $display_site = isset( $instance['display_site'] ) ? (bool) $instance['display_site'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number of stories to show:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
                
                <p><input class="checkbox" type="checkbox" <?php checked( $display_site ); ?> id="<?php echo $this->get_field_id( 'display_site' ); ?>" name="<?php echo $this->get_field_name( 'display_site' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'display_site' ); ?>"><?php _e( 'Display organization and location?' ); ?></label></p>
<?php
	}
}
/**
 * Single Story widget class
 *
 * since @0.2.8
 */
class WP_Widget_Single_Story extends WP_Widget {

	public function __construct() {
		$widget_ops = array('classname' => 'widget_single_story', 'description' => __( "Displays single story.") );
		parent::__construct('single-story', __('Single Story'), $widget_ops);
		$this->alt_option_name = 'widget_single_story';

		add_action( 'save_post', array($this, 'flush_widget_cache') );
		add_action( 'deleted_post', array($this, 'flush_widget_cache') );
		add_action( 'switch_theme', array($this, 'flush_widget_cache') );
	}

	public function widget($args, $instance) {
		$cache = array();
		if ( ! $this->is_preview() ) {
			$cache = wp_cache_get( 'widget_single_story', 'widget' );
		}

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( ! isset( $args['widget_id'] ) ) {
			$args['widget_id'] = $this->id;
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		$title = ( ! empty( $instance['title'] ) ) ? $instance['title'] : __( 'Single Story' );

		/** This filter is documented in wp-includes/default-widgets.php */
		$title = apply_filters( 'widget_title', $title, $instance, $this->id_base );

		$number = ( ! empty( $instance['number'] ) ) ? absint( $instance['number'] ) : 5;
		if ( ! $number )
			$number = 5;
		$show_date = isset( $instance['show_date'] ) ? $instance['show_date'] : false;
		
		$display_site = isset( $instance['display_site'] ) ? $instance['display_site'] : false;

		/**
		 * Filter the arguments for the Recent Posts widget.
		 *
		 * @since 3.4.0
		 *
		 * @see WP_Query::get_posts()
		 *
		 * @param array $args An array of arguments used to retrieve the recent posts.
		 */
		$r = new WP_Query( apply_filters( 'widget_posts_args', array(
			'posts_per_page'        => $number,
			'no_found_rows'         => true,
			'post_status'           => 'publish',
			'ignore_sticky_posts'   => true,
                        'post_type'             => 'stories'
		) ) );

		if ($r->have_posts()) :
?>
		<?php echo $args['before_widget']; ?>
		<?php if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		} ?>
		<ul>
		<?php while ( $r->have_posts() ) : $r->the_post(); ?>
			<li>
                            <?php
                            $post_id = get_the_ID();
                            $img_url = wp_get_attachment_url( get_post_thumbnail_id($post_id) );
                            $img_alt = get_post_meta(get_post_thumbnail_id($post_id), '_wp_attachment_image_alt', true);
                            ?>
                                <?php if(isset($img_url) && !empty($img_url)) : ?>
                                    <div class="recent_story_image">
                                        <img class="recent_story_featured_image" src="<?php echo $img_url; ?>" alt="<?php echo $img_alt; ?>" width="125" height="125" align="left" />
                                    </div>
                                <?php endif; ?>
                                <div class="recent_story_content<?php if(empty($img_url)) : ?>_full<?php endif; ?>">
				<h3><a href="<?php the_permalink(); ?>"><?php get_the_title() ? the_title() : the_ID(); ?></a></h3>
				<?php if ($display_site): ?>
					<h4 class="recent_story_loc">
					<?php
					    if($story_district = get_post_meta($post_id, "story_district", true))
					    {
						if (strlen($story_district)>0)
						    echo get_post_meta($post_id, "story_district", true).', ';
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
				<?php endif; ?>
				<?php if ( $show_date ) : ?>
					<span class="post-date">posted <?php echo get_the_date(); ?></span>
				<?php endif; ?>
                            </div>
			</li>
		<?php endwhile; ?>
		</ul>
		<?php echo $args['after_widget']; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		if ( ! $this->is_preview() ) {
			$cache[ $args['widget_id'] ] = ob_get_flush();
			wp_cache_set( 'widget_single_story', $cache, 'widget' );
		} else {
			ob_end_flush();
		}
	}

	public function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['story_id'] = (int) $new_instance['story_id'];
		$instance['embed_video'] = isset( $new_instance['embed_video'] ) ? (bool) $new_instance['embed_video'] : true;
		$instance['show_date'] = isset( $new_instance['show_date'] ) ? (bool) $new_instance['show_date'] : false;
                $instance['display_site'] = isset( $new_instance['display_site'] ) ? (bool) $new_instance['display_site'] : false;
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_single_story']) )
			delete_option('widget_single_story');

		return $instance;
	}

	public function flush_widget_cache() {
		wp_cache_delete('widget_single_story', 'widget');
	}

	public function form( $instance ) {
		$title     = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';
		$number    = isset( $instance['story_id'] ) ? absint( $instance['story_id'] ) : 5;
		$embed_video = isset( $instance['embed_video'] ) ? (bool) $instance['embed_video'] : true;
		$show_date = isset( $instance['show_date'] ) ? (bool) $instance['show_date'] : false;
                $display_site = isset( $instance['display_site'] ) ? (bool) $instance['display_site'] : false;
?>
		<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id( 'story_id' ); ?>"><?php _e( 'Select story to display:' ); ?></label>
		<input id="<?php echo $this->get_field_id( 'story_id' ); ?>" name="<?php echo $this->get_field_name( 'story_id' ); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>

		<p><input class="checkbox" type="checkbox" <?php checked( $embed_video ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Embed video if available?' ); ?></label></p>
		
		<p><input class="checkbox" type="checkbox" <?php checked( $show_date ); ?> id="<?php echo $this->get_field_id( 'show_date' ); ?>" name="<?php echo $this->get_field_name( 'show_date' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'show_date' ); ?>"><?php _e( 'Display post date?' ); ?></label></p>
                
                <p><input class="checkbox" type="checkbox" <?php checked( $display_site ); ?> id="<?php echo $this->get_field_id( 'display_site' ); ?>" name="<?php echo $this->get_field_name( 'display_site' ); ?>" />
		<label for="<?php echo $this->get_field_id( 'display_site' ); ?>"><?php _e( 'Display organization and location?' ); ?></label></p>
<?php
	}
}

?>