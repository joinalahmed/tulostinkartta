<?php
add_action( 'wp_enqueue_scripts', 'tulostinkartta_enqueue_styles' );
function tulostinkartta_enqueue_styles() {
wp_enqueue_style( 'custom-community', get_template_directory_uri() . '/style.css' );
}

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

/*
add_filter ( 'pdf_content_additional_content' , 'pdf_additional_content_terms' );
function pdf_additional_content_terms( $content ) {
global $woocommerce;
$tilausnumero = $order_id;
$tilausnumero = substr($tilausnumero, 4);
$viitenumero = get_post_meta($tilausnumero, "viitenumero", true);
$content = str_replace( '[[PDFVIITENUMERO]]', $viitenumero, $content );
return $content;
}
*/

?>