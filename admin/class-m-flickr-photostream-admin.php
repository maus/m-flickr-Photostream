<?php

class MFlickrPhotostreamAdmin {

	protected static $instance = null;

	private $options;
	
	function __construct() {
		$plugin = mFlickrPhotostream::get_instance();
		$this->options = $plugin->options;

		add_action( 'admin_init', array( &$this, 'register_settings' ) );
		add_action( 'admin_menu', array( &$this, 'admin_menu' ) ); 

		add_filter( 'plugin_action_links', array( &$this, 'display_actionLinks' ), 10, 2 ); 
	}
	

	/**
	 * Register plugin settings.
	 *
	 * @since    1.0.0
	 */	
	function register_settings() { 
		add_settings_section( 'api_section', __( 'API Settings', MFP_TEXT_DOMAIN ), array( &$this, 'display_apiSection' ),  basename( __FILE__ ) );
		add_settings_field( 'username', __( 'Username:', MFP_TEXT_DOMAIN ), array( &$this, 'display_usernameInput' ), basename( __FILE__ ), 'api_section' );
		add_settings_field( 'userid', __( 'User ID:', MFP_TEXT_DOMAIN ), array( &$this, 'display_useridInput' ), basename( __FILE__ ), 'api_section' );
		add_settings_field( 'apiurl', __( 'flickr.com API URL:', MFP_TEXT_DOMAIN ), array( &$this, 'display_apiurlInput' ), basename( __FILE__ ), 'api_section' );

		add_settings_section( 'fetch_section', __( 'Data Settings', MFP_TEXT_DOMAIN ), array( &$this, 'display_fetchSection' ),  basename(__FILE__));
		add_settings_field( 'async', __( 'Async Display:', MFP_TEXT_DOMAIN ), array( &$this, 'display_fetchInput' ), basename( __FILE__ ), 'fetch_section' );
		
		add_settings_section( 'photos_section', __( 'Photos', MFP_TEXT_DOMAIN ), array( &$this, 'display_photosSection' ),  basename(__FILE__) );
		add_settings_field( 'photoset', __( 'Photoset ID:', MFP_TEXT_DOMAIN ), array( &$this, 'display_photosetInput' ), basename( __FILE__ ), 'photos_section' );
		add_settings_field( 'photoslimit', __( 'How many photos:', MFP_TEXT_DOMAIN ), array( &$this, 'display_photoslimitInput' ), basename( __FILE__ ), 'photos_section' );
		add_settings_field( 'imagesize', __( 'And their sizes:', MFP_TEXT_DOMAIN ), array( &$this, 'display_imagesizeInput' ), basename( __FILE__ ), 'photos_section' );
		
		register_setting( 'mflickrphotostream_options', 'mflickrphotostream_options', array( MFlickrPhotostreamAdmin, 'validate_settings' ) );
	}
	

	/**
	 * @since     1.0.0
	 */
	function display_apiSection() {
		echo "<p>", __( "Enter your account and API settings.", MFP_TEXT_DOMAIN ), "</p>";
	}
	

	/**
	 * @since     1.0.0
	 */
	function display_usernameInput() {
		echo "<input name='mflickrphotostream_options[username]' type='text' id='username' value='" . $this->options['username'] . "' size='32' /> <span class='description'>" . __( 'Your flickr username', MFP_TEXT_DOMAIN ) . "</span>";
	}


	/**
	 * @since     1.0.0
	 */
	function display_useridInput() {
		echo "<input name='mflickrphotostream_options[userid]' type='text' id='userid' value='" . $this->options['userid'] . "' size='32' /> <span class='description'>" . __( "If you don't know it, use <a href='http://idgettr.com/'>idGettr</a>", MFP_TEXT_DOMAIN ) . "</span>";
	}


	/**
	 * @since     1.0.0
	 */
	function display_apiurlInput() {
		echo "<input name='mflickrphotostream_options[apiurl]' type='text' id='apiurl' value='" . $this->options['apiurl'] . "' size='32' /> <span class='description'>" . __( "You probably won't need to change this until flickr.com decides to change it", MFP_TEXT_DOMAIN ) . "</span>";
	}


	/**
	 * @since     1.0.0
	 */
	function display_fetchSection() {
		echo "<p>", __( "Choose if the images should be fetched asynchronously. That will speed up your page", MFP_TEXT_DOMAIN ), "</p>";
	}
	

	/**
	 * @since     1.0.0
	 */
	function display_fetchInput() {
		$options = get_option( 'mflickrphotostream_options' );
		echo "<input name='mflickrphotostream_options[async]' type='checkbox' id='async' ", ( ( $this->options['async'] ) ? "checked='checked' " : "" ), "/> " . __( "Asynchronous", MFP_TEXT_DOMAIN );
	}


	/**
	 * @since     1.0.0
	 */
	function display_photosSection() {
		echo "<p>", __( "Let's setup the photos.", MFP_TEXT_DOMAIN ), "</p>";
	}
	

	/**
	 * @since     1.0.0
	 */
	function display_photosetInput() {
		echo "<input name='mflickrphotostream_options[photoset]' type='text' id='photoset' value='" . $this->options['photoset'] . "' size='32' /> <span class='description'>" . __( "If you leave it out, photos will belong to all photosets", MFP_TEXT_DOMAIN ) . "</span>";
	}


	/**
	 * @since     1.0.0
	 */
	function display_photoslimitInput() {
		echo "<input name='mflickrphotostream_options[photoslimit]' type='text' id='photoslimit' value='" . $this->options['photoslimit'] . "' size='3' />";
	}


	/**
	 * @since     1.0.0
	 */
	function display_imagesizeInput() {
		?>
		<select name="mflickrphotostream_options[imagesize]" id="imagesize">
			<option <?php if ( $this->options['imagesize'] == 'square' ) { echo 'selected'; } ?> value="square">Square (75&times;75)</option>
			<option <?php if ( $this->options['imagesize'] == 'thumbnail' ) { echo 'selected'; } ?> value="thumbnail">Thumbnail (100&times;100)</option>
			<option <?php if ( $this->options['imagesize'] == 'small' ) { echo 'selected'; } ?> value="small">Small (240&times;240)</option>
			<option <?php if ( $this->options['imagesize'] == 'medium' ) { echo 'selected'; } ?> value="medium">Medium (500&times;500)</option>
			<option <?php if ( $this->options['imagesize'] == 'large' ) { echo 'selected'; } ?> value="large">Large</option>
		</select> 
		<span class='description'><?php _e( "All sizes (except square, which is exact) are maximum sizes", MFP_TEXT_DOMAIN ); ?></span>
		<?php
	}


	/**
	 * Validate settings form submission
	 *
	 * @since     1.0.0
	 */
	public static function validate_settings ( $plugin_options ) {
		return $plugin_options;
	}


	/**
	 * Add options page to settings menu
	 *
	 * @since     1.0.0
	 */
	function admin_menu() {
		if ( function_exists( 'add_options_page' ) ) {
			add_options_page( __( 'flickr Photostream Options', MFP_TEXT_DOMAIN ), __( 'flickr Photostream', MFP_TEXT_DOMAIN ), 8, basename( __FILE__ ), array( &$this, 'build_optionsPage' ) );
		}
	}
	

	/**
	 * Create the options page
	 *
	 * @since     1.0.0
	 */
	function build_optionsPage() {
		if ( ! current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.', MFP_TEXT_DOMAIN ) );
		}
		?>

		<div class="wrap" id="theme-options-wrap">
			<div class="icon32" id="icon-themes"><br /></div>
			<h2><?php _e( 'flickr Photostream Options', MFP_TEXT_DOMAIN ); ?></h2>
			
			<form method="post" action="options.php">
				<?php settings_fields( 'mflickrphotostream_options' ); ?>
				<?php do_settings_sections( basename( __FILE__ ) ); ?>
				<p class="submit"><input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes', MFP_TEXT_DOMAIN ); ?>" /></p>
			</form>
		</div>

		<?php
	}


	/**
	 * Display a settings link on Plugins admin page.
	 *
	 * @since     1.0.0
	 */
	function display_actionLinks( $links, $file ) {
 		$pluginFile = MFP_SLUG . '/loader.php';

		if ( $file == $pluginFile ) {
			$links[] = '<a href="options-general.php?page=' . basename( __FILE__ ) . '">' . __( 'Settings', MFP_TEXT_DOMAIN ) . '</a>';
		}
		
		return $links;
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
