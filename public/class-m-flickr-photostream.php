<?php

class mFlickrPhotostream {
	
	const VERSION = MFP_VERSION; 
	
	public $options = array();
	public $plugin_slug = MFP_SLUG;

	protected static $instance = null;
	
	function __construct() {
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'widgets_init', array( &$this, 'register_widget' ) );
		
		add_shortcode( 'mflickrphotostream', array( &$this, 'display_or_delegate' ) );
		
		$this->options = get_option( 'mflickrphotostream_options' );
		if ( $this->options['async'] ) {
			add_action( 'wp_ajax_mflickrphotostream_display', array( &$this, 'display' ) );
			add_action( 'wp_ajax_nopriv_mflickrphotostream_display', array( &$this, 'display' ) );
		}
	}
	

	/**
	 * Decide whether to show the output directly or setup the async call.
	 *
	 * @since    1.0.0
	 */	
	function display_or_delegate( $args ) {		
		if ( $this->options['async'] ) {
			extract( shortcode_atts( 
				array(
					'photoslimit' => $this->options['photoslimit'],
					'imagesize' => $this->options['imagesize'],
					'detailed' => FALSE
					), 
				$args )
			);
			
			?>

			<div id="<?php echo $this->plugin_slug; ?>-delegate" data-photoslimit="<?php echo $photoslimit; ?>" data-imagesize="<?php echo $imagesize; ?>" data-detailed="<?php echo $detailed; ?>">
				<img style="display: block; position: absolute; top: 8px; left: 46%;" src="<?php echo MFP_PLUGIN_URL; ?>public/_/parts/loader.gif" alt="Loading" />
			</div>

			<?php
			add_action( 'wp_footer', array( &$this, 'display_JSCode' ) );
		} else {
			$this->display( $args );
		}
	}
	

	/**
	 * Call the parser and display photos.
	 *
	 * @since    1.0.0
	 */	
	function display( $args ) {
		if ( $this->options['async'] ) {
			$photoslimit = $_POST['photoslimit'];
			$imagesize = $_POST['imagesize'];
			$detailed = $_POST['detailed'];
		} else {
			extract( shortcode_atts( 
				array(
					'photoslimit' => $this->options['photoslimit'],
					'imagesize' => $this->options['imagesize'],
					'detailed' => FALSE
					), 
				$args ) 
			);
		}
		
		require_once( MFP_PLUGIN_PATH . 'public/includes/class-m-flickr-photostream-template.php' );
		require_once( MFP_PLUGIN_PATH . 'public/includes/class-m-flickr-parser.php' );

		$fetcher = new mFlickrFetcher();
		$template = new MFlickrPhotostreamTemplate( $fetcher->photostream, $photoslimit, $imagesize, $detailed );
		$template->display_photos();
		
		if ( $this->options['async'] ) {
			die();
		}
	}
	

	/**
	 * Format HTML.
	 *
	 * @since    2.0.0
	 */	
	

	/**
	 * Insert JS.
	 *
	 * @since    1.0.0
	 */	
	function display_JSCode() {
		?>

		<script>
			jQuery( document ).ready( function() {
				var $trigger = jQuery( "#<?php echo $this->plugin_slug; ?>-delegate" ),
					photoslimit = $trigger.attr( "data-photoslimit" ),
					imagesize = $trigger.attr( "data-imagesize" ),
					detailed = $trigger.attr( "data-detailed" );
					
				jQuery.ajax( {
					'type' : 'POST',
					'url' : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
					'data' : { 
						"action" : "mflickrphotostream_display", 
					  	"photoslimit" : photoslimit, 
					  	"imagesize" : imagesize, 
					  	"detailed" : detailed 
					},
					'success' : function( data, status, request ) { 
						jQuery( "#<?php echo $this->plugin_slug; ?>-delegate" ).toggle( "fast" ).html( data ).show( 500 ); 
					},
					'dataType' : 'html'
				} );
			} );
		</script>

		<?php
	}

	
	/**
	 * Register sidebar widget.
	 *
	 * @since    1.0.0
	 */	
	function register_widget () {
		require_once( MFP_PLUGIN_PATH . 'public/class-m-flickr-photostream-widget.php' );
		register_widget( 'MFlickrPhotostream_Widget' );
	}


	/**
	 * Activation hook.
	 *
	 * @since    1.0.0
	 */	
	public static function activate() { 
		self::set_defaults();	
	}
	
	
	/**
	 * Set default options.
	 *
	 * @since    1.0.0
	 */	
	public static function set_defaults() {
		$defaultOptions = array(
			'apiurl' => 'http://api.flickr.com/services/feeds/',
			'photoslimit' => 5,
			'imagesize' => 'small',
			'async' => true,
			);

		update_option( 'mflickrphotostream_options', $defaultOptions );
	}

	
	/**
	 * Delete settings and options on deactivation.
	 *
	 * @since    1.0.0
	 */	
	public static function deactivate() { 
		unregister_setting( 'mflickrphotostream_options', 'mflickrphotostream_options', array( MFlickrPhotostreamAdmin, 'validate_settings' ) );
		delete_option( 'mflickrphotostream_options' );
	}
	
	
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    2.0.0
	 */
	public function load_plugin_textdomain() {
		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );
		
		define( 'MFP_TEXT_DOMAIN', $domain );
	}
	
	
	/**
	 * Return an instance of this class.
	 *
	 * @since     2.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}
	
}
