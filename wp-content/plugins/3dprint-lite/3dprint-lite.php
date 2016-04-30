<?php
/*
Plugin Name: 3DPrint Lite Forked (Sorry)
Description: A plugin for selling 3D printing services.
Author: Sergey Burkov
Plugin URI: http://www.wp3dprinting.com
Version: 1.3
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( !function_exists( 'get_home_path' ) ) {
	require_once ABSPATH . '/wp-admin/includes/file.php';
}

include 'includes/3dprint-lite-functions.php';
if ( is_admin() ) {
	add_action( 'admin_enqueue_scripts', 'p3dlite_enqueue_scripts_backend' );
	add_action( 'wp_ajax_p3dlite_handle_upload', 'p3dlite_handle_upload' );
	add_action( 'wp_ajax_nopriv_p3dlite_handle_upload', 'p3dlite_handle_upload' );
	include 'includes/3dprint-lite-admin.php';
}
else {
	add_action( 'wp_enqueue_scripts', 'p3dlite_enqueue_scripts_frontend' );
	include 'includes/3dprint-lite-frontend.php';
}

register_activation_hook( __FILE__, 'p3dlite_activate' );
register_deactivation_hook( __FILE__, 'p3dlite_deactivate' );
?>