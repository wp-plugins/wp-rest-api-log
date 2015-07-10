<?php
/*
Plugin Name: WP REST API Log
Description: Logs requests and responses for the WP REST API
Author: Pete Nelson
Version: 1.0.0-beta1
Plugin URI: https://github.com/petenelson/wp-rest-api-log
License: GPL2+
*/

if ( ! defined( 'ABSPATH' ) ) die( 'restricted access' );

$plugin_class_file = 'wp-rest-api-log';

$includes = array(
	'includes/class-' . $plugin_class_file . '-common.php',
	'includes/class-' . $plugin_class_file . '-db-base.php',
	'includes/class-' . $plugin_class_file . '-db-entries.php',
	'includes/class-' . $plugin_class_file . '-db-meta.php',
	'includes/class-' . $plugin_class_file . '-i18n.php',
	'includes/class-' . $plugin_class_file . '-controller.php',
	'includes/class-' . $plugin_class_file . '-response-base.php',
	'includes/class-' . $plugin_class_file . '-entries-response.php',
	'includes/class-' . $plugin_class_file . '-delete-response.php',
	'includes/class-' . $plugin_class_file . '-routes-response.php',
	'includes/class-' . $plugin_class_file . '.php',
	'admin/class-' . $plugin_class_file . '-admin.php',
);

$class_base = 'WP_REST_API_Log';

$classes = array(
	$class_base . '_Common',
	$class_base . '_DB_Entries',
	$class_base . '_DB_Meta',
	$class_base . '_i18n',
	$class_base . '_Controller',
	$class_base . '',
	$class_base . '_Admin',
);


// include classes
foreach ( $includes as $include ) {
	require_once plugin_dir_path( __FILE__ ) . $include;
}

// record the start time so we can log total millisecons
if ( class_exists( 'WP_REST_API_Log_Common' ) ) {
	global $wp_rest_api_log_start;
	$wp_rest_api_log_start = WP_REST_API_Log_Common::current_milliseconds();
}


// instantiate classes and hook into WordPress
foreach ( $classes as $class ) {
	$plugin = new $class();
	if ( method_exists( $class, 'plugins_loaded' ) ) {
		add_action( 'plugins_loaded', array( $plugin, 'plugins_loaded' ), 1 );
	}
}


// activation hook
register_activation_hook( __FILE__, function() {
	require_once 'includes/class-wp-rest-api-log-db-entries.php';
	require_once 'includes/class-wp-rest-api-log-db-meta.php';
	require_once 'includes/class-wp-rest-api-log-activator.php';
	WP_REST_API_Log_Activator::activate();
} );

