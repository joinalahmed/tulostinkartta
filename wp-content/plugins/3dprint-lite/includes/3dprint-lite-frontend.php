<?php
/**
 *
 *
 * @author Sergey Burkov, http://www.wp3dprinting.com
 * @copyright 2015
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$p3dlite_email_status_message="";
add_action( 'plugins_loaded', 'p3dlite_request_price' );
function p3dlite_request_price() {
	global $p3dlite_email_status_message;
	if ( isset( $_POST['action'] ) && $_POST['action']=='request_price' ) {
		$product_id=(int)$_POST['p3dlite_product_id'];
		$printer_id=(int)$_POST['attribute_pa_p3dlite_printer'];
		$material_id=(int)$_POST['attribute_pa_p3dlite_material'];
		$coating_id=(int)$_POST['attribute_pa_p3dlite_coating'];
		$model_file= p3dlite_basename( $_POST['attribute_pa_p3dlite_model'] ) ;
		$email_address = sanitize_email( $_POST['email_address'] );
		$request_comment = sanitize_text_field( $_POST['request_comment'] );

		$db_printers=get_option( 'p3dlite_printers' );
		$db_materials=get_option( 'p3dlite_materials' );
		$db_coatings=get_option( 'p3dlite_coatings' );
		$settings=get_option( 'p3dlite_settings' );
		$error=false;
		$upload_dir = wp_upload_dir();

		if ( strlen( $model_file )==0 || !file_exists( $upload_dir['basedir'].'/p3d/'.$model_file ) || strlen( $printer_id )==0 || strlen( $material_id )==0 ) {
			$error=true;
			$p3dlite_email_status_message='<span class="p3dlite-mail-error">'.__( 'Please upload your model and select all options.' , '3dprint-lite' ).'</span>';
		}
		if ( empty( $email_address ) ) {
			$error=true;
			$p3dlite_email_status_message='<span class="p3dlite-mail-error">'.__( 'Please enter valid email address.' , '3dprint-lite' ).'</span>';
		}
		if ( !$error ) {
			$product_key=$product_id.'_'.$printer_id.'_'.$material_id.'_'.$coating_id.'_'.base64_encode( $model_file );
			$p3dlite_price_requests=get_option( 'p3dlite_price_requests' );
			$p3dlite_price_requests[$product_key]['printer'] = $db_printers[$printer_id]['name'];
			$p3dlite_price_requests[$product_key]['material'] = $db_materials[$material_id]['name'];
			$p3dlite_price_requests[$product_key]['coating'] = $db_coatings[$coating_id]['name'];
			foreach ( $_POST as $key => $value ) {
				if ( strpos( $key, 'attribute_' )===0 ) {
					if ( !strstr( $key, 'p3dlite_' ) ) $email_attrs[$key]=$value;

					$p3dlite_price_requests[$product_key]['attributes'][$key]=$value;
				}

			}


			$p3dlite_price_requests[$product_key]['price']='';
			$p3dlite_price_requests[$product_key]['estimated_price']=(float)$_POST['p3dlite_estimated_price'];
			$p3dlite_price_requests[$product_key]['email']=$email_address;
			$p3dlite_price_requests[$product_key]['request_comment']=$request_comment;



			update_option( "p3dlite_price_requests", $p3dlite_price_requests );

			// $request_comment
			$upload_dir = wp_upload_dir();
			$link = $upload_dir['baseurl'].'/p3d/'.urlencode($model_file);
			//todo: email template
			$subject=__( "Price enquiry from $email_address" , '3dprint-lite' );

			$message=__( "E-mail:" , '3dprint-lite' ) ." $email_address <br>";
			$message.=__( "Product ID:" , '3dprint-lite' )." $product_id <br>";
			$message.=__( "Printer:" , '3dprint-lite' )." ".$db_printers[$printer_id]['name']." <br>";
			$message.=__( "Material:" , '3dprint-lite' )." ".$db_materials[$material_id]['name']." <br>";
			$message.=__( "Coating:" , '3dprint-lite' )." ".$db_coatings[$coating_id]['name']." <br>";
			$message.=__( "Model:" , '3dprint-lite' )." <a href='".$link."'>".$model_file."</a> <br>";
			$message.=__( "Estimated Price:" , '3dprint-lite' ).p3dlite_format_price($p3dlite_price_requests[$product_key]['estimated_price'], $settings['currency'], $settings['currency_position']).'<br>';

			if ( isset( $email_attrs ) && count( $email_attrs ) ) {
				foreach ( $email_attrs as $key=>$value ) {
					$message.="$key: $value<br>";
				}
			}
			$message.=__( "Comments:" , '3dprint-lite' ) ." $request_comment <br>";
			$message.=__( "Manage Price Requests:" , '3dprint-lite' )." <a href='".admin_url( 'admin.php?page=3dprint-lite#p3dlite_tabs-3' )."'>".admin_url( 'admin.php?page=3dprint-lite#p3dlite_tabs-3' )."</a> <br>";

			$admin_email = get_option( 'admin_email' );
			$headers = array( 'Content-Type: text/html; charset=UTF-8' );
			if ( wp_mail( $admin_email, $subject, $message, $headers ) )
				$p3dlite_email_status_message='<span class="p3dlite-mail-success">'.__( 'Store owner has been notified about your request. You\'ll receive the email with the price shortly.' , '3dprint-lite' ).'</span>';
			else
				$p3dlite_email_status_message='<span class="p3dlite-mail-error">'.__( 'Could not send the email. Please try again later.' , '3dprint-lite' ).'</span>';

			p3dlite_clear_cookies();
			do_action( 'p3dlite_request_price' );
		}
	}
}

add_shortcode( '3dprint-lite', 'p3d_lite' );
function p3d_lite( $atts ) {
	global $p3dlite_email_status_message, $post;
	$db_printers=get_option( 'p3dlite_printers' );
	$db_materials=get_option( 'p3dlite_materials' );
	$db_coatings=get_option( 'p3dlite_coatings' );
	$settings=get_option( 'p3dlite_settings' );
	ob_start();
?>

<div class="p3dlite-images">
	<div id="prompt">
	  <!-- if IE without GCF, prompt goes here -->
	</div>


	<div id="p3dlite-viewer">
		<canvas id="p3dlite-cv" width="<?php echo $settings['canvas_width'];?>" height="<?php echo $settings['canvas_height'];?>" style="border: 1px solid;"></canvas>
		<div id="canvas-stats" style="<?php if ($settings['canvas_stats']!='on') echo 'display:none;';?>">
			<div class="canvas-stats">
				<a style="color:red;text-decoration:underline;" href="javascript:void(0)" onclick="p3dlite_viewer.setRenderMode('flat');p3dlite_viewer.update();"><?php _e( 'Solid', '3dprint-lite' ); ?></a> /
				<a style="color:red;text-decoration:underline;" href="javascript:void(0)" onclick="p3dlite_viewer.setRenderMode('wireframe');p3dlite_viewer.update();"><?php _e( 'Wireframe', '3dprint-lite' ); ?></a>
			</div>
			<div class="canvas-stats" id="p3dlite-statistics">
			</div>
		</div>
		<div id="p3dlite-file-loading">
			<img alt="Loading file" src="<?php echo plugins_url( '3dprint-lite/images/ajax-loader.gif' ); ?>">
		</div>
	</div>

	<br style="clear:both;">

	<div id="p3dlite-container" onclick="p3dliteDialogCheck();">

<?php
	if (preg_match('/MSIE (.*?);/', $_SERVER['HTTP_USER_AGENT']) || preg_match('/Trident/', $_SERVER['HTTP_USER_AGENT'])) { 
?>
	<button id="p3dlite-pickfiles" class="progress-button"><?php _e( 'Upload Model', '3dprint-lite' ); ?></button>
<?php
	}
	else {
?>
	<button id="p3dlite-pickfiles" class="progress-button" data-style="rotate-angle-bottom" data-perspective data-horizontal><?php _e( 'Upload Model', '3dprint-lite' ); ?></button>
<?php
	}
?>
	<div class="p3dlite-info">
	<?php _e( 'File Unit:', '3dprint-lite' );?>
		&nbsp;&nbsp;
		<input class="p3dlite-control" autocomplete="off" id="unit_mm" onclick="p3dliteSelectUnit(this);" type="radio" name="p3dlite_unit" value="mm">
		<span style="cursor:pointer;" onclick="p3dliteSelectUnit(jQuery('#unit_mm'));"><?php _e( 'mm', '3dprint-lite' );?></span>
		&nbsp;&nbsp;
		<input class="p3dlite-control" autocomplete="off" id="unit_inch" onclick="p3dliteSelectUnit(this);" type="radio" name="p3dlite_unit" value="inch">
		<span style="cursor:pointer;" onclick="p3dliteSelectUnit(jQuery('#unit_inch'));"><?php _e( 'inch', '3dprint-lite' );?></span>
	</div>

	</div>
	<div class="p3dlite-info">
		<pre id="p3dlite-console"></pre>
	</div>
	<div id="p3dlite-filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
	<div class="p3dlite-info">
	  	<span id="p3dlite-error-message" class="error"></span>
	</div>
	<div class="p3dlite-info" style="<?php if ($settings['model_stats']!='on') echo 'display:none;';?>">

		<table class="p3dlite-stats" style="display:none;">
			<tr>
				<td>
					<?php _e( 'Material Volume:', '3dprint-lite' );?>
				</td>
				<td>
					<span id="stats-material-volume"></span> <?php _e( 'cm3', '3dprint-lite' );?>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Box Volume:', '3dprint-lite' );?>
				</td>
				<td>
					<span id="stats-box-volume"></span> <?php _e( 'cm3', '3dprint-lite' );?>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Surface Area:', '3dprint-lite' );?>
				</td>
				<td>
					<span id="stats-surface-area"></span> <?php _e( 'cm2', '3dprint-lite' );?>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Model Weight:', '3dprint-lite' );?>
				</td>
				<td>
					<span id="stats-weight"></span> <?php _e( 'g', '3dprint-lite' );?>
				</td>
			</tr>
			<tr>
				<td>
					<?php _e( 'Model Dimensions:', '3dprint-lite' );?>
				</td>
				<td>
					<span id="stats-length"></span> x <span id="stats-width"></span> x <span id="stats-height"></span>
					<?php _e( 'cm', '3dprint-lite' );?>
				</td>
			</tr>

		</table>
	</div>
</div>
<div class="p3dlite-details">
	<div id="price-wrapper">
		<div id="price-container">
			<p class="price">
			        <?php if ( $settings['pricing']=='request_estimate' ) echo '<b>'.__( 'Estimated Price:', '3dprint-lite' ).'</b>';?>
				<span class="amount"></span>
			</p>
		</div>
	</div>

	<form action="" style="margin-bottom:0px;" class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo $post->ID; ?>">
		<input type="hidden" name="p3dlite_product_id" value="<?php echo get_the_ID();?>">
		<input type="hidden" id="pa_p3dlite_printer" name="attribute_pa_p3dlite_printer" value="">
		<input type="hidden" id="pa_p3dlite_material" name="attribute_pa_p3dlite_material" value="">
		<input type="hidden" id="pa_p3dlite_coating" name="attribute_pa_p3dlite_coating" value="">
		<input type="hidden" id="pa_p3dlite_model" name="attribute_pa_p3dlite_model" value="">
		<input type="hidden" id="pa_p3dlite_unit" name="attribute_pa_p3dlite_unit" value="">
		<input type="hidden" id="p3dlite_estimated_price" name="p3dlite_estimated_price" value="">
                <?php do_action( 'p3dlite_form' );?>
		<div id="p3dlite-quote-loading" class="p3dlite-info">
			<img alt="Loading price" src="<?php echo plugins_url( '3dprint-lite/images/ajax-loader.gif' ); ?>">
		</div>

<?php
	if ( !empty( $p3dlite_email_status_message ) ) echo '<div class="p3dlite-info">'.$p3dlite_email_status_message.'</div>';
?>
		<div id="add-cart-wrapper">
			<div id="add-cart-container">
				<div class="variations_button p3dlite-info">
					<input type="hidden" value="request_price" name="action">
					<input class="price-request-field" type="text" value="" placeholder="<?php _e( 'Enter Your E-mail', '3dprint-lite' );?>" name="email_address">
					<input class="price-request-field" type="text" value="" placeholder="<?php _e( 'Leave a comment', '3dprint-lite' );?>" name="request_comment"><br>
					<button style="float:left;" type="submit" class="button alt"><?php _e( 'Request a Quote', '3dprint-lite' ); ?></button>
				</div>
			</div>
		</div>
	</form>

<?php
	$db_printers=get_option( 'p3dlite_printers' );
	$db_materials=get_option( 'p3dlite_materials' );
	$db_coatings=get_option( 'p3dlite_coatings' );
?>



	<div <?php if ($settings['show_printers']!='on') echo 'style="display:none;"';?> class="p3dlite-info">
		<fieldset id="printer_fieldset" class="p3dlite-fieldset">
			<legend><?php _e( 'Printer', '3dprint-lite' );?></legend>
			<ul class="p3dlite-list">
<?php
		for ( $i=0;$i<count( $db_printers );$i++ ) {
			echo '<li onclick="p3dliteSelectPrinter(this);" data-name="'.esc_attr( $db_printers[$i]['name'] ).'"><input id="p3dlite_printer_'.$i.'" class="p3dlite-control" autocomplete="off" data-width="'.$db_printers[$i]['width'].'" data-length="'.$db_printers[$i]['length'].'" data-height="'.$db_printers[$i]['height'].'" data-id="'.$i.'" data-materials="'.(count($db_printers[$i]['materials']) ? implode(',', $db_printers[$i]['materials'] ) : '').'" data-price="'.esc_attr( $db_printers[$i]['price'] ).'" data-price_type="'.$db_printers[$i]['price_type'].'" type="radio" name="product_printer">'.__($db_printers[$i]['name'], '3dprint-lite').'</li>';
		}
?>
		  	</ul>
	  	</fieldset>
	</div>
	<div <?php if ($settings['show_materials']!='on') echo 'style="display:none;"';?> class="p3dlite-info">
		<fieldset id="material_fieldset" class="p3dlite-fieldset">
			<legend><?php _e( 'Material', '3dprint-lite' );?></legend>
			<ul class="p3dlite-list">
<?php
		for ( $i=0;$i<count( $db_materials );$i++ ) {
			echo '<li data-color=\''.$db_materials[$i]['color'].'\' data-name="'.esc_attr( $db_materials[$i]['name'] ).'" onclick="p3dliteSelectFilament(this);"><input id="p3dlite_material_'.$i.'" class="p3dlite-control" autocomplete="off" type="radio" data-id="'.$i.'" data-density="'.esc_attr( $db_materials[$i]['density'] ).'" data-price="'.esc_attr( $db_materials[$i]['price'] ).'" data-price_type="'.$db_materials[$i]['price_type'].'" name="product_filament" ><div style="background-color:'.$db_materials[$i]['color'].'" class="color-sample"></div>'.__($db_materials[$i]['name'], '3dprint-lite').'</li>';
		}
?>
			</ul>
		</fieldset>
	</div>
<?php 
if ($db_coatings && count($db_coatings)>0) {
?>
	<div <?php if ($settings['show_coatings']!='on') echo 'style="display:none;"';?> class="p3dlite-info">
		<fieldset id="coating_fieldset" class="p3dlite-fieldset">
			<legend><?php _e( 'Coating', '3dprint-lite' );?></legend>
			<ul class="p3dlite-list">
<?php
		for ( $i=0;$i<count( $db_coatings );$i++ ) {
			echo '<li data-color=\''.$db_coatings[$i]['color'].'\' data-name="'.esc_attr( $db_coatings[$i]['name'] ).'" onclick="p3dliteSelectCoating(this);"><input id="p3dlite_coating_'.$i.'" class="p3dlite-control" autocomplete="off" type="radio" data-id="'.$i.'"  data-materials="'.(count($db_coatings[$i]['materials']) ? implode(',', $db_coatings[$i]['materials'] ) : '').'" data-price="'.esc_attr( $db_coatings[$i]['price'] ).'" name="product_coating" ><div style="background-color:'.$db_coatings[$i]['color'].'" class="color-sample"></div>'.__($db_coatings[$i]['name'], '3dprint-lite').'</li>';
		}
?>
			</ul>
		</fieldset>
	</div>
<?php
}
?>

</div>




<?php

	$content = ob_get_clean();

	return $content;
}
?>