<?php
/*
Plugin Name: [m~] Flickr Photostream
Description: Retrieve your latest photos from flickr. Optionally, choose a photoset, sizes and tags.
License: MIT
Author: Marius Marinescu (m~)
Author URI: http://marius.marinescu.biz/
Text Domain: m-flickr-photostream
Domain Path: /languages
Version: 2.0.0
*/

define( "MFP_VERSION", '2.0.0' );

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( "MFP_SLUG", 'm-flickr-photostream' );
define( "MFP_PLUGIN_PATH", plugin_dir_path( __FILE__ ) );
define( "MFP_PLUGIN_URL", plugins_url( MFP_SLUG ) . "/" );

require_once( MFP_PLUGIN_PATH . 'public/class-' . MFP_SLUG . '.php' );

register_activation_hook( __FILE__, array( 'mFlickrPhotostream', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'mFlickrPhotostream', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'mFlickrPhotostream', 'get_instance' ) );

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
	require_once( MFP_PLUGIN_PATH . 'admin/class-' . MFP_SLUG . '-admin.php' );
	
	add_action( 'plugins_loaded', array( 'MFlickrPhotostreamAdmin', 'get_instance' ) );
}

