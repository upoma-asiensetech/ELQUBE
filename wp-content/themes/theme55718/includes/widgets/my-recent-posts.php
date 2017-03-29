<?php
// =============================== My Recent Posts (News widget) ======================================
class MY_PostWidget extends WP_Widget {
	/** constructor */
	function MY_PostWidget() {
		parent::WP_Widget(false, $name = __('Cherry - Recent Posts', CHERRY_PLUGIN_DOMAIN));
	}

	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract( $args );
		$title         = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance);
		$category      = apply_filters('widget_category', $instance['category']);
		$post_format   = apply_filters('widget_post_format', $instance['post_format']);
		$linktext      = apply_filters('cherry_text_translate', $instance['linktext'], $instance['title'] . ' linktext');
		$linkurl       = apply_filters('widget_linkurl', $instance['linkurl']);
		$count         = apply_filters('widget_count', $instance['count']);
		$sort_by       = apply_filters('widget_sort_by', $instance['sort_by']);
		$excerpt_count = apply_filters('widget_excerpt_count', $instance['excerpt_count']);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;

			if ($post_format == 'post-format-standard') {
				$args = array(
						'showposts'     => $count,
						'category_name' => $category,
						'orderby'       => $sort_by,
						'tax_query'     => array(
						'relation'      => 'AND',
							array(
								'taxonomy' => 'post_format',
								'field'    => 'slug',
								'terms'    => array('post-format-aside', 'post-format-gallery', 'post-format-link', 'post-format-image', 'post-format-quote', 'post-format-audio', 'post-format-video'),
								'operator' => 'NOT IN'
							)
						)
					);
			} else {
				$args = array(
					'showposts'     => $count,
					'category_name' => $category,
					'orderby'       => $sort_by,
					'tax_query'     => array(
					'relation'      => 'AND',
						array(
							'taxonomy' => 'post_format',
							'field' => 'slug',
							'terms' => array($post_format)
						)
					)
				);
			}

			$wp_query = new WP_Query( $args ); ?>
			<ul class="post-list unstyled">

			<?php if ($wp_query->have_posts()) : while ($wp_query->have_posts()) : $wp_query->the_post();?>

			<li class="post-list_li clearfix">
                
				<?php if(has_post_thumbnail()) {
					$thumb   = get_post_thumbnail_id();
					$img_url = wp_get_attachment_url( $thumb,'full'); //get img URL
					$image   = aq_resize( $img_url, 195, 195, true ); //resize & crop img
				?>
				<figure class="featured-thumbnail thumbnail post_thumbnail">
					<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php echo $image ?>" alt="<?php the_title(); ?>" /></a>
				</figure>
				<?php } ?>
                    
            <div class="caption">
    				<time datetime="<?php the_time('Y-m-d\H:i'); ?>"><?php echo get_the_date(); ?></time>
    
    				<h4 class="post-list_h">
    					<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', CHERRY_PLUGIN_DOMAIN); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a>
    				</h4>
    
    				<?php if($excerpt_count!="") { ?>
    				<div class="excerpt">
    					<?php
    						$excerpt = get_the_excerpt();
    						echo wp_trim_words($excerpt,$excerpt_count);
    					?>
    				</div>
    				<?php } ?>
    				<a href="<?php the_permalink() ?>" class="btn btn-primary"><?php _e('Read more', CHERRY_PLUGIN_DOMAIN); ?></a>
                </div>
			</li>

			<?php endwhile; ?>
			</ul>

			<?php
				endif;
				$wp_query = null;
			?>

			<!-- Link under post cycle -->
			<?php if($linkurl !=""){?>
				<a href="<?php echo $linkurl; ?>" class="btn btn-primary"><?php echo $linktext; ?></a>
			<?php } ?>

			<?php echo $after_widget;
	}

	/** @see WP_Widget::update */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/** @see WP_Widget::form */
	function form($instance) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => '', 'category' => '', 'post_format' => '', 'linktext' => '', 'linkurl' => '', 'count' => '', 'sort_by' => '', 'excerpt_count' => '' );
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title         = esc_attr($instance['title']);
		$category      = esc_attr($instance['category']);
		$post_format   = esc_attr($instance['post_format']);
		$linktext      = esc_attr($instance['linktext']);
		$linkurl       = esc_attr($instance['linkurl']);
		$count         = esc_attr($instance['count']);
		$sort_by       = esc_attr($instance['sort_by']);
		$excerpt_count = esc_attr($instance['excerpt_count']);
	?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title', CHERRY_PLUGIN_DOMAIN); ?><input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Category Slug', CHERRY_PLUGIN_DOMAIN); ?>:<input class="widefat" id="<?php echo $this->get_field_id('category'); ?>" name="<?php echo $this->get_field_name('category'); ?>" type="text" value="<?php echo $category; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('post_format'); ?>"><?php _e('Post format', CHERRY_PLUGIN_DOMAIN); ?>:<br />

			<select id="<?php echo $this->get_field_id('post_format'); ?>" name="<?php echo $this->get_field_name('post_format'); ?>" style="width:150px;" >
				<option value="post-format-standard" <?php echo ($post_format === 'post-format-standard' ? ' selected="selected"' : ''); ?>><?php _e('Standard', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="post-format-aside" <?php echo ($post_format === 'post-format-aside' ? ' selected="selected"' : ''); ?>><?php _e('Aside', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="post-format-quote" <?php echo ($post_format === 'post-format-quote' ? ' selected="selected"' : ''); ?> ><?php _e('Quote', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="post-format-link" <?php echo ($post_format === 'post-format-link' ? ' selected="selected"' : ''); ?> ><?php _e('Link', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="post-format-image" <?php echo ($post_format === 'post-format-image' ? ' selected="selected"' : ''); ?> ><?php _e('Image', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="post-format-gallery" <?php echo ($post_format === 'post-format-gallery' ? ' selected="selected"' : ''); ?> ><?php _e('Gallery', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="post-format-audio" <?php echo ($post_format === 'post-format-audio' ? ' selected="selected"' : ''); ?> ><?php _e('Audio', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="post-format-video" <?php echo ($post_format === 'post-format-video' ? ' selected="selected"' : ''); ?> ><?php _e('Video', CHERRY_PLUGIN_DOMAIN); ?></option>
			</select>
		</label></p>

		<p><label for="<?php echo $this->get_field_id('sort_by'); ?>"><?php _e('Post order', CHERRY_PLUGIN_DOMAIN); ?>:<br />

			<select id="<?php echo $this->get_field_id('sort_by'); ?>" name="<?php echo $this->get_field_name('sort_by'); ?>" style="width:150px;" >
				<option value="date" <?php echo ($sort_by === 'date' ? ' selected="selected"' : ''); ?>><?php _e('Date', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="title" <?php echo ($sort_by === 'title' ? ' selected="selected"' : ''); ?>><?php _e('Title', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="comment_count" <?php echo ($sort_by === 'comment_count' ? ' selected="selected"' : ''); ?>><?php _e('Comment count', CHERRY_PLUGIN_DOMAIN); ?></option>
				<option value="rand" <?php echo ($sort_by === 'rand' ? ' selected="selected"' : ''); ?>><?php _e('Random', CHERRY_PLUGIN_DOMAIN); ?></option>
			</select>
		</label></p>

		<p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Posts per page', CHERRY_PLUGIN_DOMAIN); ?>:<input class="widefat" style="width:30px; display:block; text-align:center" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="text" value="<?php echo $count; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('excerpt_count'); ?>"><?php _e('Excerpt length (words)', CHERRY_PLUGIN_DOMAIN); ?>:<input class="widefat" style="width:30px; display:block; text-align:center" id="<?php echo $this->get_field_id('excerpt_count'); ?>" name="<?php echo $this->get_field_name('excerpt_count'); ?>" type="text" value="<?php echo $excerpt_count; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('linktext'); ?>"><?php _e('Link Text', CHERRY_PLUGIN_DOMAIN); ?>: <input class="widefat" id="<?php echo $this->get_field_id('linktext'); ?>" name="<?php echo $this->get_field_name('linktext'); ?>" type="text" value="<?php echo $linktext; ?>" /></label></p>

		<p><label for="<?php echo $this->get_field_id('linkurl'); ?>"><?php _e('Link URL', CHERRY_PLUGIN_DOMAIN); ?>: <input class="widefat" id="<?php echo $this->get_field_id('linkurl'); ?>" name="<?php echo $this->get_field_name('linkurl'); ?>" type="text" value="<?php echo $linkurl; ?>" /></label></p>
		<?php
	}
} // class Widget
?>