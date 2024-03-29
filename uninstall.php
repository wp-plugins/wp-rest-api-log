<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$tables = array(
		$wpdb->prefix . 'wp_rest_api_log',
		$wpdb->prefix . 'wp_rest_api_logmeta',
	);

foreach ( $tables as $table_name ) {
	$wpdb->query( "drop table $table_name");
}


$options = array(
	'wp-rest-api-log-meta-dbversion',
	'wp-rest-api-log-entries-dbversion',
	);

foreach ( $options as $option ) {
	delete_option( $option );
}
