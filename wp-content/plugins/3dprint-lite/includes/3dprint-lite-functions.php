<?php
/**
 *
 *
 * @author Sergey Burkov, http://www.wp3dprinting.com
 * @copyright 2015
 */

/**
 * p3dlite_handle_upload() function
 *
 * Copyright 2013, Moxiecode Systems AB
 * Released under GPL License.
 *
 * License: http://www.plupload.com/license
 * Contributing: http://www.plupload.com/contributing
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

function p3dlite_activate() {
	$current_version = get_option('p3dlite_version');

	$default_printers[]=array(
		'name' => 'Default Printer',
		'width' => '300',
		'length' => '400',
		'height' => '300',
		'price' => '0.02',
		'materials' => array(0,1),
		'price_type' => 'box_volume'
	);
	add_option( 'p3dlite_printers', $default_printers );

	$default_materials[]=array(
		'name' => 'PLA (1.75 mm) Green',
		'type' => 'filament',
		'density' => '1.26',
		'length' => '330',
		'diameter' => '1.75',
		'weight' => '1',
		'price' => '0.03',
		'price_type' => 'gram',
		'roll_price' => '20',
		'color' => '#08c101'
	);
	$default_materials[]=array(
		'name' => 'ABS (3 mm) Red',
		'type' => 'filament',
		'density' => '1.41',
		'length' => '100',
		'diameter' => '3',
		'weight' => '1',
		'price' => '0.04',
		'price_type' => 'gram',
		'roll_price' => '25',
		'color' => '#dd3333'
	);
	add_option( 'p3dlite_materials', $default_materials );

	$current_printers = get_option( 'p3dlite_printers' );
	$current_materials = get_option( 'p3dlite_printers' );
	foreach ($current_printers as $printer_id => $printer) {
		if (!isset($printer['materials'])) {
			$current_printers[$printer_id]['materials']=array_keys($current_materials);
		}
	}
	update_option( 'p3dlite_printers', $current_printers );

	$current_settings = get_option( 'p3dlite_settings' );
	$settings=array(
		'pricing' => (isset($current_settings['checkout']) ? $current_settings['checkout'] : 'request_estimate'),
		'min_price' => (isset($current_settings['min_price']) ? $current_settings['min_price'] : '1'),
		'currency' => (isset($current_settings['currency']) ? $current_settings['currency'] : '$'),
		'currency_position' => (isset($current_settings['currency_position']) ? $current_settings['currency_position'] : 'left'),
		'num_decimals' => (isset($current_settings['num_decimals']) ? $current_settings['num_decimals'] : '2'),
		'thousand_sep' => (isset($current_settings['thousand_sep']) ? $current_settings['thousand_sep'] : ','),
		'decimal_sep' => (isset($current_settings['decimal_sep']) ? $current_settings['decimal_sep'] : '.'),
		'canvas_width' => (isset($current_settings['canvas_width']) ? $current_settings['canvas_width'] : '512'),
		'canvas_height' => (isset($current_settings['canvas_height']) ? $current_settings['canvas_height'] : '384'),
		'cookie_expire' => (isset($current_settings['cookie_expire']) ? $current_settings['cookie_expire'] : '2'),
		'background1' => (isset($current_settings['background1']) ? $current_settings['background1'] : '#FFFFFF'),
		'background2' => (isset($current_settings['background2']) ? $current_settings['background2'] : '#1e73be'),
		'plane_color' => (isset($current_settings['plane_color']) ? $current_settings['plane_color'] : '#FFFFFF'),
		'printer_color' => (isset($current_settings['printer_color']) ? $current_settings['printer_color'] : '#dd9933'),
		'button_color1' => (isset($current_settings['button_color1']) ? $current_settings['button_color1'] : '#1d9650'),
		'button_color2' => (isset($current_settings['button_color2']) ? $current_settings['button_color2'] : '#148544'),
		'button_color3' => (isset($current_settings['button_color3']) ? $current_settings['button_color3'] : '#0e7138'),
		'button_color4' => (isset($current_settings['button_color4']) ? $current_settings['button_color4'] : '#fff'),
		'button_color5' => (isset($current_settings['button_color5']) ? $current_settings['button_color5'] : '#fff'),
		'zoom' => (isset($current_settings['zoom']) ? $current_settings['zoom'] : '2'),
		'angle_x' => (isset($current_settings['angle_x']) ? $current_settings['angle_x'] : '-90'),
		'angle_y' => (isset($current_settings['angle_y']) ? $current_settings['angle_y'] : '25'),
		'angle_z' => (isset($current_settings['angle_z']) ? $current_settings['angle_z'] : '0'),
		'canvas_stats' => (isset($current_settings['canvas_stats']) ? $current_settings['canvas_stats'] : 'on'),
		'model_stats' => (isset($current_settings['model_stats']) ? $current_settings['model_stats'] : 'on'),
		'show_printers' => (isset($current_settings['show_printers']) ? $current_settings['show_printers'] : 'on'),
		'show_materials' => (isset($current_settings['show_materials']) ? $current_settings['show_materials'] : 'on'),
		'show_coatings' => (isset($current_settings['show_coatings']) ? $current_settings['show_coatings'] : 'on'),
		'file_extensions' => (isset($current_settings['file_extensions']) ? $current_settings['file_extensions'] : 'stl,obj,zip'),
		'file_max_size' => (isset($current_settings['file_max_size']) ? $current_settings['file_max_size'] : '30'),
		'max_days' => (isset($current_settings['max_days']) ? $current_settings['max_days'] : '')
	);

	update_option( 'p3dlite_settings', $settings );

	add_option( 'p3dlite_price_requests', '' );
	$upload_dir = wp_upload_dir();
	if ( !is_dir( $upload_dir['basedir'].'/p3d/' ) ) {
		mkdir( $upload_dir['basedir'].'/p3d/' );
	}

	if ( !file_exists( $upload_dir['basedir'].'/p3d/index.html' ) ) {
		$fp = fopen( $upload_dir['basedir'].'/p3d/index.html', "w" );
		fclose( $fp );
	}

	$htaccess_contents='
AddType application/octet-stream obj
AddType application/octet-stream stl
<ifmodule mod_deflate.c>
	AddOutputFilterByType DEFLATE application/octet-stream
</ifmodule>
<FilesMatch "\.(php([0-9]|s)?|s?p?html|cgi|py|pl|exe)$">
	Order Deny,Allow
	Deny from all
</FilesMatch>
<ifmodule mod_expires.c>
	ExpiresActive on
	ExpiresDefault "access plus 365 days"
</ifmodule>
<ifmodule mod_headers.c>
	Header set Cache-Control "max-age=31536050"
</ifmodule>
	';
	if ( !file_exists( $upload_dir['basedir'].'/p3d/.htaccess' ) || version_compare($current_version, '1.4.8', '<') ) {
		file_put_contents( $upload_dir['basedir'].'/p3d/.htaccess', $htaccess_contents );
	}
	update_option( 'p3dlite_version', '1.5.2.1' );
	add_option( 'p3dlite_do_activation_redirect', true );
	do_action( 'p3dlite_activate' );
}

add_action( 'plugins_loaded', 'p3dlite_load_textdomain' );
function p3dlite_load_textdomain() {
	load_plugin_textdomain( '3dprint-lite', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/' );
}

function p3dlite_enqueue_scripts_backend() {
	global $wp_scripts;
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui' );
	wp_enqueue_script( 'jquery-ui-tabs' );
	wp_enqueue_script( 'js/3dprint-lite-backend.js', plugin_dir_url( __FILE__ ).'js/3dprint-lite-backend.js', array( 'jquery' ) );
	wp_enqueue_script( 'jquery.sumoselect.min.js',  plugin_dir_url( __FILE__ ).'ext/sumoselect/jquery.sumoselect.min.js', array( 'jquery' ) );
	wp_enqueue_style( 'sumoselect.css', plugin_dir_url( __FILE__ ).'ext/sumoselect/sumoselect.css' );
	wp_enqueue_style( 'jquery-ui.min.css', plugin_dir_url( __FILE__ ).'css/jquery-ui.min.css' );
}

function p3dlite_enqueue_scripts_frontend() {
	global $post;

	//make sure there is no conflict with the premium plugin
	$available_variations = array();
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$product = new WC_Product_Variable( get_the_ID() );
		if ( method_exists( $product, 'get_available_variations' ) ) 
			$available_variations=$product->get_available_variations();
	}
	if ( count($available_variations)==0 || !isset( $available_variations[0]['attributes']['attribute_pa_p3d_printer'] ) ) {

		wp_enqueue_style( '3dprint-lite-frontend.css', plugin_dir_url( __FILE__ ).'css/3dprint-lite-frontend.css' );
		wp_enqueue_style( 'component.css', plugin_dir_url( __FILE__ ).'ext/ProgressButtonStyles/css/component.css' );
		wp_enqueue_script( 'modernizr.custom.js',  plugin_dir_url( __FILE__ ).'ext/ProgressButtonStyles/js/modernizr.custom.js', array( 'jquery' ) );
		wp_enqueue_script( 'jsc3d.js',  plugin_dir_url( __FILE__ ).'ext/jsc3d/jsc3d.js' );
		wp_enqueue_script( 'jsc3d.touch.js',  plugin_dir_url( __FILE__ ).'ext/jsc3d/jsc3d.touch.js' );
		wp_enqueue_script( 'jsc3d.console.js',  plugin_dir_url( __FILE__ ).'ext/jsc3d/jsc3d.console.js' );
		wp_enqueue_script( 'jsc3d.webgl.js',  plugin_dir_url( __FILE__ ).'ext/jsc3d/jsc3d.webgl.js' );
		wp_enqueue_script( 'plupload.full.min.js',  plugin_dir_url( __FILE__ ).'ext/plupload/plupload.full.min.js' );
		wp_enqueue_script( 'classie.js',  plugin_dir_url( __FILE__ ).'ext/ProgressButtonStyles/js/classie.js' );
		wp_enqueue_script( 'progressButton.js',  plugin_dir_url( __FILE__ ).'ext/ProgressButtonStyles/js/progressButton.js' );
		wp_enqueue_script( 'event-manager.js',  plugin_dir_url( __FILE__ ).'ext/event-manager/event-manager.js' );
		wp_enqueue_script( 'accounting.js',  plugin_dir_url( __FILE__ ).'ext/accounting/accounting.min.js' );
		wp_enqueue_script( '3dprint-lite-frontend.js',  plugin_dir_url( __FILE__ ).'js/3dprint-lite-frontend.js' );
		wp_enqueue_script( 'jquery.cookie.min.js',  plugin_dir_url( __FILE__ ).'ext/jquery-cookie/jquery.cookie.min.js' );


		$plupload_langs=array( 'ku_IQ', 'pt_BR', 'sr_RS', 'th_TH', 'uk_UA', 'zh_CN', 'zh_TW' );
		$current_locale = get_locale() ;
		list ( $lang, $LANG ) = explode( '_', $current_locale );
		if ( in_array( $current_locale, $plupload_langs ) ) $plupload_locale=$current_locale;
		else $plupload_locale=$lang;

		wp_enqueue_script( "$plupload_locale.js",  plugin_dir_url( __FILE__ )."ext/plupload/i18n/$plupload_locale.js" );
		$upload_dir = wp_upload_dir();
		$settings=get_option( 'p3dlite_settings' );
		wp_localize_script( 'jquery', 'p3dlite',
			array(
				'url' => admin_url( 'admin-ajax.php' ),
				'upload_url' => $upload_dir[ 'baseurl' ] . "/p3d/",
				'plugin_url' => plugin_dir_url( dirname(__FILE__) ),
				'error_box_fit' => __( '<span id=\'printer_fit_error\'><b>Error:</b> The model does not fit into the selected printer</span>', '3dprint-lite' ),
				'warning_box_fit' => __( '<span id=\'printer_fit_warning\'><b>Warning:</b> The model might not fit into the selected printer</span>', '3dprint-lite' ),
				'warning_cant_triangulate' => __( '<b>Warning:</b> Can\'t triangulate', '3dprint-lite' ),
				'pricing' => $settings['pricing'],
				'background1' => $settings['background1'],
				'background2' => $settings['background2'],
				'plane_color' => str_replace( '#', '0x', $settings['plane_color'] ),
				'printer_color' => str_replace( '#', '0x', $settings['printer_color'] ),
				'zoom' => $settings['zoom'],
				'angle_x' => $settings['angle_x'],
				'angle_y' => $settings['angle_y'],
				'angle_z' => $settings['angle_z'],
				'file_max_size' => $settings['file_max_size'],
				'file_extensions' => $settings['file_extensions'],
				'currency_symbol' => $settings['currency'],
				'currency_position' => $settings['currency_position'],
				'price_num_decimals' => $settings['num_decimals'],
				'thousand_sep' => $settings['thousand_sep'],
				'decimal_sep' => $settings['decimal_sep'],
				'min_price' =>  $settings['min_price'],
				'cookie_expire' => $settings['cookie_expire']
			)
		);
		$custom_css = "
			.progress-button[data-perspective] .content { 
			 	background: ".$settings['button_color1']."; 
			}

			.progress-button .progress { 
				background: ".$settings['button_color2']."; 
			}

			.progress-button .progress-inner { 
				background: ".$settings['button_color3']."; 
			}
			.progress-button {
				color: ".$settings['button_color4'].";
			}
			.progress-button .content::before,
			.progress-button .content::after  {
				color: ".$settings['button_color5'].";
			}
		";
		wp_add_inline_style( 'component.css', $custom_css );
	}
}


add_action( 'admin_init', 'p3dlite_plugin_redirect' );
function p3dlite_plugin_redirect() {
	if ( get_option( 'p3dlite_do_activation_redirect', false ) ) {
		delete_option( 'p3dlite_do_activation_redirect' );
		if ( !isset( $_GET['activate-multi'] ) ) {
			wp_redirect( admin_url( 'admin.php?page=3dprint-lite' ) );exit;
		}
	}
}
function p3dlite_deactivate() {
	do_action( 'p3dlite_deactivate' );
}

add_action( 'admin_enqueue_scripts', 'p3dlite_add_color_picker' );
function p3dlite_add_color_picker( $hook ) {
	if ( is_admin() ) {
		wp_enqueue_style( 'wp-color-picker' );
		wp_enqueue_script( 'p3dlite-color-picker', plugins_url( 'js/3dprint-lite-backend.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	}
}

function p3dlite_clear_cookies() {
	if ( count( $_COOKIE ) ) {
		foreach ( $_COOKIE as $key=>$value ) {
			if ( strpos( $key, 'p3dlite' )===0 ) {
				setcookie( $key, "", time()-3600*24*30 );
			}
		}
	}
}

function p3dlite_format_price($price, $currency, $currency_position='left') {
	if ($currency_position=='left') {
		$formatted_price=$currency.number_format_i18n($price);
	}
	elseif ($currency_position=='left_space') {
		$formatted_price=$currency.' '.number_format_i18n($price);
	}
	elseif ($currency_position=='right') {
		$formatted_price=number_format_i18n($price).$currency;
	}
	elseif ($currency_position=='right_space') {
		$formatted_price=number_format_i18n($price).' '.$currency;
	}
	return $formatted_price;
}

add_action( 'p3dlite_housekeeping', 'p3dlite_do_housekeeping' );
function p3dlite_do_housekeeping() {
	$uploads = wp_upload_dir( 'p3d' );
	$files = glob($uploads['path']."*");
	$now   = time();
	$settings = get_option( 'p3dlite_settings' );
	if ((int)$settings['max_days']>0) {
		foreach ($files as $file) {
			$filename = basename($file);
			if (is_file($file) && $filename != '.htaccess' && $filename != 'index.html') {
				if ($now - filemtime($file) >= 60 * 60 * 24 * $settings['max_days']) {
					unlink($file);
				}
			}
		}
	}
}

function p3dlite_basename($file) {
    return end(explode('/',$file));
} 

function p3dlite_find_all_files($dir) {
	$root = scandir($dir);
	foreach($root as $value) {
	if($value === '.' || $value === '..') {continue;}
		if(is_file("$dir/$value")) {$result[]="$dir/$value";continue;}
		foreach(p3dlite_find_all_files("$dir/$value") as $value) {
			$result[]=$value;
		}
	}
return $result;
} 

function p3dlite_handle_upload() {
	set_time_limit( 5 * 60 );
	ini_set( 'memory_limit', '-1' );
	$allowed_extensions=array('stl', 'obj', 'mtl', 'png', 'jpg', 'jpeg', 'gif', 'tga');

	$printer_id = (int)$_REQUEST['printer_id'];
	$material_id = (int)$_REQUEST['material_id'];
	if ( $_REQUEST['unit'] == 'inch' ) {
		$unit = "inch";
	}
	else {
		$unit = "mm";
	}
	$model_stats = array();
	$settings = get_option( 'p3dlite_settings' );

	$targetDir = get_temp_dir();

	$cleanupTargetDir = true; // Remove old files
	$maxFileAge = 5 * 3600; // Temp file age in seconds


	// Create target dir
	if ( !file_exists( $targetDir ) ) {
		@mkdir( $targetDir );
	}

	// Get a file name
	if ( isset( $_REQUEST["name"] ) ) {
		$fileName = $_REQUEST["name"];
	} elseif ( !empty( $_FILES ) ) {
		$fileName = $_FILES["file"]["name"];
	} else {
		$fileName = uniqid( "file_" );
	}
	$fileName = sanitize_file_name( $fileName );
	$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

	// Chunking might be enabled
	$chunk = isset( $_REQUEST["chunk"] ) ? intval( $_REQUEST["chunk"] ) : 0;
	$chunks = isset( $_REQUEST["chunks"] ) ? intval( $_REQUEST["chunks"] ) : 0;


	// Remove old temp files
	if ( $cleanupTargetDir ) {
		if ( !is_dir( $targetDir ) || !$dir = opendir( $targetDir ) ) {
			die( '{"jsonrpc" : "2.0", "error" : {"code": 100, "message": '.__( "Failed to open temp directory.", '3dprint-lite' ).'}, "id" : "id"}' );
		}

		while ( ( $file = readdir( $dir ) ) !== false ) {
			$tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

			// If temp file is current file proceed to the next
			if ( $tmpfilePath == "{$filePath}.part" ) {
				continue;
			}

			// Remove temp file if it is older than the max age and is not the current file
			if ( preg_match( '/\.part$/', $file ) && ( filemtime( $tmpfilePath ) < time() - $maxFileAge ) ) {
				@unlink( $tmpfilePath );
			}
		}
		closedir( $dir );
	}


	// Open temp file
	if ( !$out = @fopen( "{$filePath}.part", $chunks ? "ab" : "wb" ) ) {
		die( '{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "'.__( 'Failed to open output stream.', '3dprint-lite' ).'"}, "id" : "id"}' );
	}

	if ( !empty( $_FILES ) ) {
		if ( $_FILES["file"]["error"] || !is_uploaded_file( $_FILES["file"]["tmp_name"] ) ) {
			die( '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "'.__( 'Failed to move uploaded file.', '3dprint-lite' ).'"}, "id" : "id"}' );
		}

		// Read binary input stream and append it to temp file
		if ( !$in = @fopen( $_FILES["file"]["tmp_name"], "rb" ) ) {
			die( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "'.__( 'Failed to open input stream.', '3dprint-lite' ).'"}, "id" : "id"}' );
		}
	} else {
		if ( !$in = @fopen( "php://input", "rb" ) ) {
			die( '{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "'.__( 'Failed to open input stream.', '3dprint-lite' ).'"}, "id" : "id"}' );
		}
	}

	while ( $buff = fread( $in, 4096 ) ) {
		fwrite( $out, $buff );
	}

	@fclose( $out );
	@fclose( $in );

	// Check if file has been uploaded
	if ( !$chunks || $chunk == $chunks - 1 ) {
		// Strip the temp .part suffix off

		rename( "{$filePath}.part", $filePath );


		$uploads = wp_upload_dir( 'p3d' );
		$wp_filename =  time().'_'.urlencode( p3dlite_basename( $filePath ) ) ;
		$new_file = $uploads['path'] . "$wp_filename";
		$path_parts = pathinfo($new_file);
		$extension = strtolower($path_parts['extension']);
		$basename = $path_parts['basename'];

		if ($extension=='zip') {
			if (class_exists('ZipArchive')) {

				$zip = new ZipArchive;
				$res = $zip->open( $filePath );
				if ( $res === TRUE ) {
					for( $i = 0; $i < $zip->numFiles; $i++ ) {
						$file_to_extract = p3dlite_basename( $zip->getNameIndex($i) );
						$f2e_path_parts = pathinfo($file_to_extract);
						$f2e_extension = mb_strtolower($f2e_path_parts['extension']);
						if (!in_array(mb_strtolower($f2e_path_parts['extension']), $allowed_extensions)) continue;
						if ( $f2e_extension == 'obj' || $f2e_extension == 'stl' ) {
							$file_found = true;
							$wp_filename =  time().'_'.urlencode( p3dlite_basename( $file_to_extract ) ) ;
							$file_to_extract = $wp_filename;
						}

						$zip->extractTo( "$targetDir/$wp_filename", array( $zip->getNameIndex($i) ) );
                                                $files = p3dlite_find_all_files("$targetDir/$wp_filename");
						foreach ($files as $filename) {
							rename($filename, $uploads['path'].$file_to_extract);
						}
					}

					$zip->close();
					if ( !$file_found ) {
						die( '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "'.__( 'Model file not found.', '3dprint-lite' ).'"}, "id" : "id"}' );
					}
					rename($filePath, $uploads['path'].$wp_filename.'.zip');
				}
				else {
					die( '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "'.__( 'Could not extract the file.', '3dprint-lite' ).'"}, "id" : "id"}' );
				}
			}
			else {
				die( '{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "'.__( 'The server does not support zip archives.', '3dprint-lite' ).'"}, "id" : "id"}' );
			}
		} elseif ($extension == 'stl' || $extension == 'obj') {
			rename( $filePath, $new_file );
		}

		$output['jsonrpc'] = "2.0";
		$output['filename'] = $wp_filename;

		if (filesize($uploads['path'].$wp_filename) > ((int)$settings['file_max_size'] * 1048576)) {
			unlink($uploads['path'].$wp_filename);
			die( '{"jsonrpc" : "2.0", "error" : {"code": 113, "message": "'.__( 'Extracted file is too large.', '3dprint-lite' ).'"}, "id" : "id"}' );
		}


		$output = apply_filters( '3dprint-lite_upload', $output, $printer_id, $material_id );
		ob_clean();
		wp_die( json_encode( $output ) );

	}
}
?>