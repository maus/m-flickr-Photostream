<?php

class MFlickrPhotostream_Widget extends WP_Widget {
	
	function __construct() {
		parent::WP_Widget( 
			false, 
			__( 'flickr Photostream', MFP_TEXT_DOMAIN ), 
			array( 
				'description' => __( 'Display flickr photostream', MFP_TEXT_DOMAIN ) 
				) 
			);		
	}

	
	/**
	 * Display widget.
	 *
	 * @since    1.0.0
	 */	
	function widget( $args, $instance ) {
		extract( $args );
		
		echo $before_widget;
		
		$title = apply_filters( 'widget_title', $instance['title'] );
		if ( $title ) {
			echo $before_title . $title . $after_title;
		}

		require_once( MFP_PLUGIN_PATH . 'public/includes/class-m-flickr-photostream-template.php' );
		require_once( MFP_PLUGIN_PATH . 'public/includes/class-m-flickr-parser.php' );

		$fetcher = new mFlickrFetcher();
		$template = new MFlickrPhotostreamTemplate( $fetcher->get_photostream(), $instance['photoslimit'] );
		$template->display_photos();
		
		echo $after_widget;
	}
	

	/**
	 * Update settings.
	 *
	 * @since    1.0.0
	 */	
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['photoslimit'] = $new_instance['photoslimit'];
		
        return $instance;
	}
	

	/**
	 * Display widget form.
	 *
	 * @since    1.0.0
	 */	
	function form( $instance ) { 
		$title = esc_attr( $instance['title'] );
		$tracklimit = esc_attr( $instance['photoslimit'] );
		if ( ! $photoslimit ) {
			$options = get_option( 'mflickrphotostream_options' );
			$photoslimit = $options['photoslimit'];
		}
		?>
		
        <p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', MFP_TEXT_DOMAIN ); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
        <p><label for="<?php echo $this->get_field_id( 'photoslimit' ); ?>"><?php _e( 'How many?', MFP_TEXT_DOMAIN ); ?> <input class="widefat" id="<?php echo $this->get_field_id('photoslimit'); ?>" name="<?php echo $this->get_field_name( 'photoslimit' ); ?>" type="text" value="<?php echo $photoslimit; ?>" size='2' /></label></p>
		
        <?php 
	}

}