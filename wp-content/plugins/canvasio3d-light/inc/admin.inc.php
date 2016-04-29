<?php
add_action( 'init', 'wpLight3D_buttons' );
function wpLight3D_buttons() {
	wp_enqueue_script( 'jquery' );
	if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) return;
	add_filter("mce_external_plugins", "wpLight3D_add_buttons");
    add_filter('mce_buttons', 'wpLight3D_register_buttons');
}	
function wpLight3D_add_buttons($plugin_array) {
	$plugin_array['wpLight3D'] = plugins_url().'/canvasio3d-light/inc/js/menuLight3D.js';
	return $plugin_array;
}
function wpLight3D_register_buttons($buttons) {
	array_push( $buttons, 'canvasioLightBtn' );
	return $buttons;
}
?>