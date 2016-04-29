<?php
add_action( 'wp_enqueue_scripts', 'tulostinkartta_enqueue_styles' );
function tulostinkartta_enqueue_styles() {
wp_enqueue_style( 'custom-community', get_template_directory_uri() . '/style.css' );
}

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

?>