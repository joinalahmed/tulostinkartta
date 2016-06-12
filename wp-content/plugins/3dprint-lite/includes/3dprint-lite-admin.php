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



add_action( 'admin_menu', 'register_3dprintlite_menu_page' );
function register_3dprintlite_menu_page() {
	add_menu_page( '3DPrint Lite', '3DPrint Lite', 'manage_options', '3dprint-lite', 'register_3dprintlite_menu_page_callback' );
}

function register_3dprintlite_menu_page_callback() {
	if ( $_GET['page'] != '3dprint-lite' ) return false;
	if ( isset( $_POST['action'] ) && $_POST['action']=='remove_printer' ) {
		$printers_array = get_option( 'p3dlite_printers' );
		unset( $printers_array[$_POST['printer_id']] );
		$printers_array=array_values( $printers_array );
		update_option( 'p3dlite_printers', $printers_array );
	}

	if ( isset( $_POST['action'] ) && $_POST['action']=='remove_material' ) {
		$materials_array = get_option( 'p3dlite_materials' );
		unset( $materials_array[$_POST['material_id']] );
		$materials_array=array_values( $materials_array );
		update_option( 'p3dlite_materials', $materials_array );

		$printers_array = get_option( 'p3dlite_printers' );
		foreach ($printers_array as $printer_key => $printer) {
			if (count($printer['materials'])) {
				foreach ($printer['materials'] as $material_key => $material_id) {
					if ($_POST['material_id']==$material_id)
						unset ( $printers_array[$printer_key]['materials'][$material_key] );
				}
			}
		}

		update_option( 'p3dlite_printers', $printers_array );

	}

	if ( isset( $_POST['action'] ) && $_POST['action']=='remove_coating' ) {
		$coatings_array = get_option( 'p3dlite_coatings' );
		unset( $coatings_array[$_POST['coating_id']] );
		$coatings_array=array_values( $coatings_array );
		update_option( 'p3dlite_coatings', $coatings_array );
	}
	if ( isset( $_POST['action'] ) && $_POST['action']=='remove_request' ) {
		$price_requests=get_option( 'p3dlite_price_requests' );
		unset( $price_requests[$_POST['request_id']] );
		update_option( 'p3dlite_price_requests', $price_requests );
	}

	if ( isset( $_POST['p3dlite_printer_name'] ) && count( $_POST['p3dlite_printer_name'] )>0 ) {
		for ( $i=0;$i<count( $_POST['p3dlite_printer_name'] );$i++ ) {
			$printers[$i]['name']=sanitize_text_field( $_POST['p3dlite_printer_name'][$i] );
			$printers[$i]['width']=(float)( $_POST['p3dlite_printer_width'][$i] );
			$printers[$i]['length']=(float)( $_POST['p3dlite_printer_length'][$i] );
			$printers[$i]['height']=(float)( $_POST['p3dlite_printer_height'][$i] );
			$printers[$i]['price']= $_POST['p3dlite_printer_price'][$i] ;
			$printers[$i]['price_type']=$_POST['p3dlite_printer_price_type'][$i];
			if ( isset($_POST['p3dlite_printer_materials']) && count( $_POST['p3dlite_printer_materials'][$i] )>0 ) {
				$printers[$i]['materials']=$_POST['p3dlite_printer_materials'][$i];
			}
		}

		update_option( 'p3dlite_printers', $printers );
	}

	if ( isset( $_POST['p3dlite_material_name'] ) && count( $_POST['p3dlite_material_name'] )>0 ) {

		for ( $i=0;$i<count( $_POST['p3dlite_material_name'] );$i++ ) {

			if ( $_POST['p3dlite_material_type'][$i]=='filament' && !empty( $_POST['p3dlite_material_diameter'][$i] ) && !empty( $_POST['p3dlite_material_length'][$i] ) && !empty( $_POST['p3dlite_material_weight'][$i] ) ) {
				$materials[$i]['density']=round( ( $_POST['p3dlite_material_weight'][$i]*1000 )/( M_PI*( pow( $_POST['p3dlite_material_diameter'][$i], 2 )/4 )*$_POST['p3dlite_material_length'][$i] ), 2 );
			}
			else {
				$materials[$i]['density']=$_POST['p3dlite_material_density'][$i];
			}

			$materials[$i]['name']=sanitize_text_field( $_POST['p3dlite_material_name'][$i] );
			$materials[$i]['type'] = $_POST['p3dlite_material_type'][$i] ;
			$materials[$i]['diameter']=(float)( $_POST['p3dlite_material_diameter'][$i] );
			$materials[$i]['length']=(float)( $_POST['p3dlite_material_length'][$i] );
			$materials[$i]['weight']=(float)( $_POST['p3dlite_material_weight'][$i] );
			$materials[$i]['price']=$_POST['p3dlite_material_price'][$i];
			$materials[$i]['price_type']=$_POST['p3dlite_material_price_type'][$i];
			$materials[$i]['roll_price']=(float)( $_POST['p3dlite_material_roll_price'][$i] );
			$materials[$i]['color']=$_POST['p3dlite_material_color'][$i];

		}

		update_option( 'p3dlite_materials', $materials );
	}
	if ( isset( $_POST['p3dlite_coating_name'] ) && count( $_POST['p3dlite_coating_name'] )>0 ) {

		for ( $i=0;$i<count( $_POST['p3dlite_coating_name'] );$i++ ) {

			$coatings[$i]['name']=sanitize_text_field( $_POST['p3dlite_coating_name'][$i] );
			$coatings[$i]['price']= $_POST['p3dlite_coating_price'][$i] ;
			$coatings[$i]['color']=$_POST['p3dlite_coating_color'][$i];

			if ( isset($_POST['p3dlite_coating_materials']) && count( $_POST['p3dlite_coating_materials'][$i] )>0 ) {

				$coatings[$i]['materials']=$_POST['p3dlite_coating_materials'][$i];
			}

		}

		update_option( 'p3dlite_coatings', $coatings );
	}
	if ( isset( $_POST['p3dlite_settings'] ) && !empty( $_POST['p3dlite_settings'] ) ) {
		update_option( 'p3dlite_settings', $_POST['p3dlite_settings'] );
	}


	if ( isset( $_POST['p3d_buynow'] ) && count( $_POST['p3d_buynow'] )>0 ) {
		$settings=get_option( 'p3dlite_settings' );
		foreach ( $_POST['p3d_buynow'] as $key=>$price ) {
			list ( $post_id, $printer_id, $material_id, $coating_id, $base64_filename ) = explode( '_', $key );
			$filename=base64_decode( $base64_filename );
			$price_requests=get_option( 'p3dlite_price_requests' );

			if ( count( $price_requests ) ) {
				$email=$price_requests[$key]['email'];
				$variation=$price_requests[$key]['attributes'];

				if ( $price ) {
					$price_requests[$key]['price']=$price;

					$db_printers=get_option( 'p3dlite_printers' );
					$db_materials=get_option( 'p3dlite_materials' );
					$db_coatings=get_option( 'p3dlite_coatings' );
					$upload_dir = wp_upload_dir();
					$link = $upload_dir['baseurl'].'/p3d/'.urlencode($filename);
					$subject=__( "Your model's price" , '3dprint-lite' );

					$message="";
					$message.=__( "Printer:" , '3dprint-lite' )." ".__($db_printers[$printer_id]['name'], '3dprint-lite')." <br>";
					$message.=__( "Material:" , '3dprint-lite' )." ".__($db_materials[$material_id]['name'], '3dprint-lite')." <br>";
					$message.=__( "Coating:" , '3dprint-lite' )." ".__($db_coatings[$coating_id]['name'], '3dprint-lite')." <br>";
					$message.=__( "Model:" , '3dprint-lite' )." <a href='".$link."'>".$filename."</a> <br>";


					foreach ( $variation as $key => $value ) {
						if ( strpos( $key, 'attribute_' )===0 ) {
							$attribute_name=str_replace( 'attribute_', '', $key );
							$attribute_name=strtoupper( str_replace( '_', ' ', $key ) );
							if ( !strstr( $key, 'p3dlite_' ) ) $message.=$attribute_name.": $value <br>";
						}
					}
					$message.='<b>'.__( "Price:" , '3dprint-lite' )."</b> ".p3dlite_format_price($price, $settings['currency'], $settings['currency_position'])." <br>";
					$message.='<b>'.__( "Comments:" , '3dprint-lite' )."</b> ".$_POST['p3d_comments']." <br>";
					do_action( 'p3dlite_send_quote', $message );
					$headers = array( 'Content-Type: text/html; charset=UTF-8' );
					if (wp_mail( $email, $subject, $message, $headers )) {
						update_option( 'p3dlite_price_requests', $price_requests );
					}//todo: else show error
					
				}

			}//if ( count( $price_requests ) )
		}//foreach ( $_POST['p3d_buynow'] as $key=>$price )
		do_action( 'p3dlite_after_send_quotes' );
	}//if ( isset( $_POST['p3d_buynow'] ) && count( $_POST['p3d_buynow'] )>0 )

	$printers=get_option( 'p3dlite_printers' );
	$materials=get_option( 'p3dlite_materials' );
	$coatings=get_option( 'p3dlite_coatings' );
	$settings=get_option( 'p3dlite_settings' );
	$price_requests=get_option( 'p3dlite_price_requests' );
?>
<script language="javascript">

function p3dliteCalculateFilamentPrice(material_obj) {
	var diameter=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_diameter').val());
	var length=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_length').val());
	var weight=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_weight').val());
	var price=parseFloat(jQuery(material_obj).closest('table.material').find('input.p3dlite_roll_price').val());
	var price_type=jQuery(material_obj).closest('table.material').find('select.p3dlite_price_type').val();

	if (price_type=='cm3') {
		if (!diameter || !price || !length) {alert('<?php _e( 'Please input roll price, diameter and length', '3dprint-lite' );?>');return false;}
		var volume=(Math.PI*((diameter*diameter)/4)*(length*1000))/1000;
		var volume_cost=price/volume;
		jQuery(material_obj).closest('table.material').find('input.p3dlite_price').val(volume_cost.toFixed(2));
	}
	else if (price_type=='gram') {

		if (!weight || !price) {alert('<?php _e( 'Please input price and weight', '3dprint-lite' );?>');return false;}
		var weight_cost=price/(weight*1000);
		jQuery(material_obj).closest('table.material').find('input.p3dlite_price').val(weight_cost.toFixed(2));
	}

}
</script>
<div class="wrap">
	<?php _e('Shortcode:', '3dprint-lite');?> <input type="text" name="textbox" value="[3dprint-lite]" onclick="this.select()" />
	<br>
	<h2><?php _e( '3D printing settings', '3dprint-lite' );?></h2>

	<div id="p3dlite_tabs">

		<ul>
			<li><a href="#p3dlite_tabs-0"><?php _e( 'Settings', '3dprint-lite' );?></a></li>
			<li><a href="#p3dlite_tabs-1"><?php _e( 'Printers', '3dprint-lite' );?></a></li>
			<li><a href="#p3dlite_tabs-2"><?php _e( 'Materials', '3dprint-lite' );?></a></li>
			<li><a href="#p3dlite_tabs-3"><?php _e( 'Coatings', '3dprint-lite' );?></a></li>
			<li><a href="#p3dlite_tabs-4"><?php _e( 'Price Requests', '3dprint-lite' );?></a></li>
		</ul>
		<div id="p3dlite_tabs-0">
			<form method="post" action="admin.php?page=3dprint-lite#p3dlite_tabs-0">
				<p><b><?php _e( 'Pricing', '3dprint-lite' );?></b></p>
				<table>
					<tr>
						<td>
							<?php _e( 'Get a Quote', '3dprint-lite' );?>
						</td>
						<td>
							<select name="p3dlite_settings[pricing]">
								<option <?php if ( $settings['pricing']=='request_estimate' ) echo 'selected';?> value="request_estimate"><?php _e( 'Give an estimate and request price', '3dprint-lite' );?></option>
								<option <?php if ( $settings['pricing']=='request' ) echo 'selected';?> value="request"><?php _e( 'Request price', '3dprint-lite' );?></option>
								<option disabled value="checkout"><?php _e( 'Calculate price and allow checkout (Premium only)' , '3dprint-lite' );?></option>
			 				</select>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Minimum Price', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[min_price]" value="<?php echo $settings['min_price'];?>"><?php echo $settings['currency'];?>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Currency', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[currency]" value="<?php echo $settings['currency'];?>">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Currency Position', '3dprint-lite' );?>
						</td>
						<td>
							<select name="p3dlite_settings[currency_position]">
								<option <?php if ($settings['currency_position']=='left') echo 'selected';?> value="left"><?php _e('Left', '3dprint-lite');?>
								<option <?php if ($settings['currency_position']=='left_space') echo 'selected';?> value="left_space"><?php _e('Left with space', '3dprint-lite');?>
								<option <?php if ($settings['currency_position']=='right') echo 'selected';?> value="right"><?php _e('Right', '3dprint-lite');?>
								<option <?php if ($settings['currency_position']=='right_space') echo 'selected';?> value="right_space"><?php _e('Right with space', '3dprint-lite');?>
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Number of Decimals', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[num_decimals]" value="<?php echo $settings['num_decimals'];?>">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Thousands Separator', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[thousand_sep]" value="<?php echo $settings['thousand_sep'];?>">
						</td>
					</tr>
					<tr>
						<td>
							<?php _e( 'Decimal Point', '3dprint-lite' );?>
						</td>
						<td>
							<input type="text" size="1" name="p3dlite_settings[decimal_sep]" value="<?php echo $settings['decimal_sep'];?>">
						</td>
					</tr>
				</table>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />
				<hr>
				<p><b><?php _e( 'Product Viewer', '3dprint-lite' );?></b></p>
				<table>
					<tr>
						<td><?php _e( 'Canvas Resolution', '3dprint-lite' );?></td>
						<td><input size="3" type="text"  placeholder="<?php _e( 'Width', '3dprint-lite' );?>" name="p3dlite_settings[canvas_width]" value="<?php echo $settings['canvas_width'];?>">px &times; <input size="3"  type="text" placeholder="<?php _e( 'Height', '3dprint-lite' );?>" name="p3dlite_settings[canvas_height]" value="<?php echo $settings['canvas_height'];?>">px</td>
					</tr>
					<tr>
						<td><?php _e( 'Cookie Lifetime', '3dprint' );?></td>
						<td>
							<select name="p3dlite_settings[cookie_expire]">
								<option <?php if ( $settings['cookie_expire']=='0' ) echo 'selected';?> value="0">0 <?php _e( '(no cookies)', '3dprint-lite' );?> 
								<option <?php if ( $settings['cookie_expire']=='1' ) echo 'selected';?> value="1">1
								<option <?php if ( $settings['cookie_expire']=='2' ) echo 'selected';?> value="2">2
							</select> <?php _e( 'days', '3dprint-lite' );?> 
						</td>
					</tr>
					<tr>
						<td><?php _e( 'Background 1', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[background1]" value="<?php echo $settings['background1'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Background 2', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[background2]" value="<?php echo $settings['background2'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Plane Color', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[plane_color]" value="<?php echo $settings['plane_color'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Printer Color', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[printer_color]" value="<?php echo $settings['printer_color'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Background', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color1]" value="<?php echo $settings['button_color1'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Shadow', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color2]" value="<?php echo $settings['button_color2'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Progress Bar', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color3]" value="<?php echo $settings['button_color3'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Font', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color4]" value="<?php echo $settings['button_color4'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Button Tick', '3dprint-lite' );?></td>
						<td><input type="text" class="p3dlite_color_picker" name="p3dlite_settings[button_color5]" value="<?php echo $settings['button_color5'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Zoom', '3dprint-lite' );?></td>
						<td><input size="3" type="text" name="p3dlite_settings[zoom]" value="<?php echo $settings['zoom'];?>"></td>
					</tr>
					<tr>
						<td><?php _e( 'Angle X', '3dprint-lite' );?></td>
						<td><input size="3" type="text" name="p3dlite_settings[angle_x]" value="<?php echo $settings['angle_x'];?>">&deg;</td>
					</tr>
					<tr>
						<td><?php _e( 'Angle Y', '3dprint-lite' );?></td>
						<td><input size="3" type="text" name="p3dlite_settings[angle_y]" value="<?php echo $settings['angle_y'];?>">&deg;</td>
					</tr>
					<tr>
						<td><?php _e( 'Angle Z', '3dprint-lite' );?></td>
						<td><input size="3" type="text" name="p3dlite_settings[angle_z]" value="<?php echo $settings['angle_z'];?>">&deg;</td>
					</tr>
					<tr>
						<td><?php _e( 'Show Canvas Stats', '3dprint-lite' );?></td>
						<td><input type="checkbox" name="p3dlite_settings[canvas_stats]" <?php if ($settings['canvas_stats']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Model Stats', '3dprint' );?></td>
						<td><input type="checkbox" name="p3dlite_settings[model_stats]" <?php if ($settings['model_stats']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Printers', '3dprint-lite' );?></td>
						<td><input type="checkbox" name="p3dlite_settings[show_printers]" <?php if ($settings['show_printers']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Materials', '3dprint-lite' );?></td>
						<td><input type="checkbox" name="p3dlite_settings[show_materials]" <?php if ($settings['show_materials']=='on') echo 'checked';?>></td>
					</tr>
					<tr>
						<td><?php _e( 'Show Coatings', '3dprint-lite' );?></td>
						<td><input type="checkbox" name="p3dlite_settings[show_coatings]" <?php if ($settings['show_coatings']=='on') echo 'checked';?>></td>
					</tr>
				</table>
				<hr>
				<p><b><?php _e( 'File Upload', '3dprint-lite' );?></b></p>
				<table>
					<tr>
						<td><?php _e( 'Max. File Size', '3dprint-lite' );?></td>
						<td><input size="3" type="text" name="p3dlite_settings[file_max_size]" value="<?php echo $settings['file_max_size'];?>"><?php _e( 'mb' );?> </td>
					</tr>
				<tr>
					<td><?php _e( 'Allowed Extensions', '3dprint-lite' );?></td>
					<td><input size="9" type="text" name="p3dlite_settings[file_extensions]" value="<?php echo $settings['file_extensions'];?>"></td>
				</tr>
				<tr>
					<td><?php _e( 'Delete files older than', '3dprint-lite' );?></td>
					<td><input size="3" type="text" name="p3dlite_settings[max_days]" value="<?php echo $settings['max_days'];?>"><?php _e( 'days' );?> </td>
				</tr>
				</table>
				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', '3dprint-lite' ) ?>" />
				</p>
			</form>
		</div>
		<div id="p3dlite_tabs-1">
			<form method="post" action="admin.php?page=3dprint-lite#p3dlite_tabs-1">
<?php    wp_nonce_field( 'update-options' ); ?>
<?php
	if ( !$printers || count( $printers )==0 ) {
?>
				<table class="form-table printer">
					<tr>
						<td colspan="2"><hr></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Printer Name', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_name[]" value="Default Printer" /></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Build Tray Length', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_length[]" value="200" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Build Tray Width', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_width[]" value="200" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Build Tray Height', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_height[]" value="200" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>



					<tr valign="top">
						<th scope="row"><?php _e( 'Printing Cost', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_price[]" value="0.05" /><?php echo $settings['currenct']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select name="p3dlite_printer_price_type[]">
								<option value="box_volume"><?php _e( '1 cm3 of Bounding Box Volume', '3dprint-lite' );?></option>
								<option value="material_volume"><?php _e( '1 cm3 of Material Volume', '3dprint-lite' );?></option>
								<option value="gram"><?php _e( '1 gram of Material', '3dprint-lite' );?></option>
		 					</select>
						</td>
					</tr>

					<tr class="printer_materials" valign="top">
						<th scope="row"><?php _e( 'Materials', '3dprint-lite' ); ?></th>
						<td>
							<select autocomplete="off" name="p3dlite_printer_materials[0][]" multiple="multiple" class="sumoselect">
								<?php 
									for ($j=0; $j<count($materials); $j++) {
										echo '<option value="'.$j.'">'.$materials[$j]['name'];
									}
								?>
							</select>
						</td>
					</tr>


				</table>
			<?php } ?>
<?php

	if ( is_array( $printers ) && count( $printers )>0 ) {

		$i=0;
		foreach ( $printers as $printer ) {

?>
				<table class="form-table printer">
					<tr>
						<td colspan="3"><hr></td>
					</tr>
					<tr>
						<td colspan="3"><span class="item_id"><?php echo "<b>ID #$i</b>";?></span></td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<?php _e( 'Printer Name', '3dprint-lite' ); ?>
						</th>
						<td>
							<input type="text" name="p3dlite_printer_name[]" value="<?php echo $printer['name'];?>" />&nbsp;
							<a class="remove_printer" href="javascript:void(0);" onclick="p3dliteRemovePrinter(<?php echo $i;?>);return false;">
								<img alt="<?php _e( 'Remove Printer', '3dprint-lite' );?>" title="<?php _e( 'Remove Printer', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/remove.png' ); ?>">
							</a>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Build Tray Length', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_length[]" value="<?php echo $printer['length'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Build Tray Width', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_width[]" value="<?php echo $printer['width'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Build Tray Height', '3dprint-lite' ); ?></th>
						<td><input type="text" name="p3dlite_printer_height[]" value="<?php echo $printer['height'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>



					<tr valign="top">
						<th scope="row"><?php _e( 'Printing Cost', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" name="p3dlite_printer_price[]" value="<?php echo $printer['price'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select name="p3dlite_printer_price_type[]">
								<option <?php if ( $printer['price_type']=='box_volume' ) echo "selected";?> value="box_volume"><?php _e( '1 cm3 of Bounding Box Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type']=='material_volume' ) echo "selected";?> value="material_volume"><?php _e( '1 cm3 of Material Volume', '3dprint-lite' );?></option>
								<option <?php if ( $printer['price_type']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram of Material', '3dprint-lite' );?></option>
							</select>
						</td>
					</tr>

					<tr class="printer_materials" valign="top">
						<th scope="row"><?php _e( 'Materials', '3dprint-lite' ); ?></th>
						<td>
							<select autocomplete="off" name="p3dlite_printer_materials[<?php echo $i;?>][]" multiple="multiple" class="sumoselect">
								<?php 
									for ($j=0; $j<count($materials); $j++) {
										if (is_array($printer['materials']) && in_array($j, $printer['materials'])) $selected="selected"; else $selected="";
										echo '<option '.$selected.' value="'.$j.'">'.$materials[$j]['name'];
									}
								?>
							</select>
						</td>
					</tr>


				</table>
<?php
			$i++;
		}
	}
?>
				<button id="add_printer_button" class="button-secondary" onclick="p3dliteAddPrinter();return false;"><?php _e( 'Add Printer', '3dprint-lite' );?></button>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', '3dprint-lite' ) ?>" />
				</p>
			</form>
		</div><!-- p3dlite_tabs-1 -->
		<div id="p3dlite_tabs-2">
			<form method="post" action="admin.php?page=3dprint-lite#p3dlite_tabs-2">
<?php
	if ( !is_array($materials) || count( $materials )==0 ) {
?>
				<table class="form-table material">
					<tr>
						<td colspan="2"><hr></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Material Name', '3dprint-lite' );?></th>
						<td><input type="text" name="p3dlite_material_name[]" value="ABS (1.75mm)" /></td>
					</tr>

				 	<tr valign="top">
						<th scope="row"><?php _e( 'Material Type', '3dprint-lite' );?></th>
						<td>
							<select name="p3dlite_material_type[]" onchange="p3dliteSetMaterialType(this)">
								<option value="filament"><?php _e( 'Filament', '3dprint-lite' );?>
								<option value="other"><?php _e( 'Other', '3dprint-lite' );?>
							</select>
						</td>
					</tr>


					<tr valign="top">
						<th scope="row"><?php _e( 'Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_material_price[]" value="0.03" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select class="p3dlite_price_type" name="p3dlite_material_price_type[]">
								<option value="cm3"><?php _e( '1 cm3', '3dprint-lite' );?></option>
								<option value="gram"><?php _e( '1 gram', '3dprint-lite' );?></option>
							</select>
							<a class="material_filament" onclick="javascript:p3dliteCalculateFilamentPrice(this)" href="javascript:void(0)"><?php _e( 'Calculate', '3dprint-lite' );?></a>
					 	</td>
					</tr>

					<tr class="material_other" valign="top">
						<th scope="row"><?php _e( 'Material Density', '3dprint-lite' );?></th>
						<td><input type="text" name="p3dlite_material_density[]" value="0" /><?php _e( 'g/cm3', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Filament Diameter', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_diameter" name="p3dlite_material_diameter[]" value="1.75" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Filament Length', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_length" name="p3dlite_material_length[]" value="330" /><?php _e( 'm', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Roll Weight', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_weight" name="p3dlite_material_weight[]" value="1" /><?php _e( 'kg', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Roll Price', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_roll_price" name="p3dlite_material_roll_price[]" value="20" /><?php echo $settings['currency']; ?></td>
					</tr>


					<tr valign="top">
						<th scope="row"><?php _e( 'Material Color', '3dprint-lite' );?></th>
						<td class="color_td"><input type="text" class="p3dlite_color_picker" name="p3dlite_material_color[]" value="" /></td>
					</tr>
				</table>
			<?php } ?>
<?php
	if ( is_array( $materials ) && count( $materials )>0 ) {
		$i=0;
		foreach ( $materials as $material ) {
?>
				<table class="form-table material">
					<tr>
						<td colspan="2"><hr></td>
					</tr>
				 	<tr>
						<td colspan="2"><span class="item_id"><?php echo "<b>ID #$i</b>";?></span></td>
				 	</tr>
				 	<tr valign="top">
					<th scope="row"><?php _e( 'Material Name', '3dprint-lite' );?></th>
						<td>
							<input type="text" name="p3dlite_material_name[]" value="<?php echo $material['name'];?>" />&nbsp;
							<a class="remove_material" href="javascript:void(0);" onclick="p3dliteRemoveMaterial(<?php echo $i;?>);return false;">
								<img alt="<?php _e( 'Remove Material', '3dprint-lite' );?>" title="<?php _e( 'Remove Material', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/remove.png' ); ?>">
					 		</a>
						</td>
					</tr>

				 	<tr valign="top">
						<th scope="row"><?php _e( 'Material Type', '3dprint-lite' );?></th>
						<td>
							<select class="select_material" name="p3dlite_material_type[]" onchange="p3dliteSetMaterialType(this)">
								<option <?php if ( $material['type']=='filament' ) echo "selected";?> value="filament"><?php _e( 'Filament', '3dprint-lite' );?>
								<option <?php if ( $material['type']=='other' ) echo "selected";?> value="other"><?php _e( 'Other', '3dprint-lite' );?>
							</select>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_material_price[]" value="<?php echo $material['price'];?>" /><?php echo $settings['currency']; ?> <?php _e( 'per', '3dprint-lite' );?>
							<select class="p3dlite_price_type"  name="p3dlite_material_price_type[]">
								<option <?php if ( $material['price_type']=='cm3' ) echo "selected";?> value="cm3"><?php _e( '1 cm3', '3dprint-lite' );?></option>
								<option <?php if ( $material['price_type']=='gram' ) echo "selected";?> value="gram"><?php _e( '1 gram', '3dprint-lite' );?></option>
							</select>
							<a class="material_filament" onclick="javascript:p3dliteCalculateFilamentPrice(this)" href="javascript:void(0)"><?php _e( 'Calculate', '3dprint-lite' );?></a>
						</td>
					</tr>

					<tr class="material_other" valign="top">
						<th scope="row"><?php _e( 'Material Density', '3dprint-lite' );?></th>
						<td>
							<input type="text" name="p3dlite_material_density[]" value="<?php echo $material['density'];?>" /><?php _e( 'g/cm3', '3dprint-lite' );?>
						</td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Filament Diameter', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_diameter" name="p3dlite_material_diameter[]" value="<?php echo $material['diameter'];?>" /><?php _e( 'mm', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Filament Length', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_length" name="p3dlite_material_length[]" value="<?php echo $material['length'];?>" /><?php _e( 'm', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Roll Weight', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_weight" name="p3dlite_material_weight[]" value="<?php echo $material['weight'];?>" /><?php _e( 'kg', '3dprint-lite' );?></td>
					</tr>

					<tr class="material_filament" valign="top">
						<th scope="row"><?php _e( 'Roll Price', '3dprint-lite' );?></th>
						<td><input type="text" class="p3dlite_roll_price" name="p3dlite_material_roll_price[]" value="<?php echo $material['roll_price'];?>" /><?php echo $settings['currency']; ?></td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Material Color', '3dprint-lite' );?></th>
						<td class="color_td"><input type="text" class="p3dlite_color_picker" name="p3dlite_material_color[]" value="<?php echo $material['color'];?>" /></td>
					</tr>
				</table>
<?php
			$i++;
		}
	}
?>
				<button id="add_material_button" class="button-secondary" onclick="p3dliteAddMaterial();return false;"><?php _e( 'Add Material', '3dprint-lite' );?></button>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', '3dprint-lite' ) ?>" />
				</p>

			</form>

		</div><!-- p3dlite_tabs-2 -->

		<div id="p3dlite_tabs-3">
			<form method="post" action="admin.php?page=3dprint-lite#p3dlite_tabs-3">
<?php
			if ( !$coatings || count( $coatings )==0 ) {
?>
				<table class="form-table coating">
					<tr>
						<td colspan="2"><hr></td>
					</tr>
					<tr valign="top">
						<th scope="row"><?php _e( 'Coating Name', '3dprint-lite' );?></th>
						<td><input type="text" name="p3dlite_coating_name[]" value="" /></td>
					</tr>
	
					<tr valign="top">
						<th scope="row"><?php _e( 'Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_coating_price[]" value="" /><?php echo $settings['currency']; ?> <?php _e('per', '3dprint-lite');?> <?php _e('cm2 of surface area', '3dprint-lite');?>
					 	</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Coating Color', '3dprint-lite' );?></th>
						<td class="color_td"><input type="text" class="p3dlite_color_picker" name="p3dlite_coating_color[]" value="" /></td>
					</tr>

					<tr class="coating_materials" valign="top">
						<th scope="row"><?php _e( 'Materials', '3dprint-lite' ); ?></th>
						<td>
							<select autocomplete="off" name="p3dlite_coating_materials[][]" multiple="multiple" class="sumoselect">
								<?php 
									for ($j=0; $j<count($materials); $j++) {
										echo '<option value="'.$j.'">'.$materials[$j]['name'];
									}
								?>
							</select>
						</td>
					</tr>


				</table>
			<?php } ?>
<?php
	if ( is_array( $coatings ) && count( $coatings )>0 ) {
		$i=0;
		foreach ( $coatings as $coating ) {
?>
				<table class="form-table coating">
					<tr>
						<td colspan="2"><hr></td>
					</tr>
				 	<tr>
						<td colspan="2"><span class="item_id"><?php echo "<b>ID #$i</b>";?></span></td>
				 	</tr>
				 	<tr valign="top">
					<th scope="row"><?php _e( 'Coating Name', '3dprint-lite' );?></th>
						<td>
							<input type="text" name="p3dlite_coating_name[]" value="<?php echo $coating['name'];?>" />&nbsp;
							<a class="remove_coating" href="javascript:void(0);" onclick="p3dliteRemoveCoating(<?php echo $i;?>);return false;">
								<img alt="<?php _e( 'Remove Coating', '3dprint-lite' );?>" title="<?php _e( 'Remove Coating', '3dprint-lite' );?>" src="<?php echo plugins_url( '3dprint-lite/images/remove.png' ); ?>">
					 		</a>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Price', '3dprint-lite' ); ?></th>
						<td>
							<input type="text" class="p3dlite_price" name="p3dlite_coating_price[]" value="<?php echo $coating['price'];?>" /><?php echo $settings['currency']; ?> <?php _e('per', '3dprint-lite');?> <?php _e('cm2 of surface area', '3dprint-lite');?>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row"><?php _e( 'Coating Color', '3dprint-lite' );?></th>
						<td class="color_td"><input type="text" class="p3dlite_color_picker" name="p3dlite_coating_color[]" value="<?php echo $coating['color'];?>" /></td>
					</tr>
					<tr class="coating_materials" valign="top">
						<th scope="row"><?php _e( 'Materials', '3dprint-lite' ); ?></th>
						<td>

							<select autocomplete="off" name="p3dlite_coating_materials[<?php echo $i;?>][]" multiple="multiple" class="sumoselect">
								<?php 

									for ($j=0; $j<count($materials); $j++) {
										if (is_array($coating['materials']) && in_array($j, $coating['materials'])) $selected="selected"; else $selected="";
										echo '<option '.$selected.' value="'.$j.'">'.$materials[$j]['name'];
									}
								?>
							</select>
						</td>
					</tr>
				</table>
<?php
			$i++;
		}
	}
?>
				<button id="add_coating_button" class="button-secondary" onclick="p3dliteAddCoating();return false;"><?php _e( 'Add Coating', '3dprint-lite' );?></button>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Save Changes', '3dprint-lite' ) ?>" />
				</p>

			</form>

		</div><!-- p3dlite_tabs-3 -->
		<div id="p3dlite_tabs-4">
			<form method="post" action="admin.php?page=3dprint-lite#p3dlite_tabs-4">
<?php
	if ( is_array( $price_requests ) && count( $price_requests )>0 ) {
?>
				<table class="form-table">
					<tr>
						<td>X</td>
						<td><?php _e( 'Page', '3dprint-lite' );?></td>
						<td><?php _e( 'Customer', '3dprint-lite' );?></td>
						<td><?php _e( 'Details', '3dprint-lite' );?></td>
						<td><?php _e( 'Price', '3dprint-lite' );?></td>
						<td><?php _e( 'Comment', '3dprint-lite' );?></td>
					</tr>
<?php
		$db_printers=get_option( 'p3dlite_printers' );
		$db_materials=get_option( 'p3dlite_materials' );
		$db_coatings=get_option( 'p3dlite_coatings' );

		foreach ( $price_requests as $product_key=>$price_request ) {
			list ( $post_id, $printer_id, $material_id, $coating_id, $base64_filename ) = explode( '_', $product_key );
			$upload_dir = wp_upload_dir();

			$filename=base64_decode( $base64_filename );
			if ( $price_request['price']=='' ) {

				$attr_st='';

				foreach ( $price_request['attributes'] as $attr_key => $attr_value ) {

					if ( $attr_key=='attribute_pa_p3dlite_printer' ) {
						$attr_st.=__( "Printer" , '3dprint-lite' ).": ".$price_request['printer']."<br>";
					}
					elseif ( $attr_key=='attribute_pa_p3dlite_material' ) {
						$attr_st.=__( "Material" , '3dprint-lite' ).": ".$price_request['material']."<br>";
					}
					elseif ( $attr_key=='attribute_pa_p3dlite_coating' ) {
						$attr_st.=__( "Coating" , '3dprint-lite' )." : ".$price_request['coating']."<br>";
					}
					elseif ( $attr_key=='attribute_pa_p3dlite_model' ) {

						$link = $upload_dir['baseurl'].'/p3d/'.urlencode( p3dlite_basename( $attr_value ) );
						if (file_exists($upload_dir['basedir']."/p3d/$attr_value.zip")) {
							$link="$link.zip";
							$attr_value="$attr_value.zip";
						}
						$attr_st.=__( "Model" , '3dprint-lite' ).": <a href='".$link."'>".p3dlite_basename( $attr_value )."</a><br>";
					}
					elseif ( $attr_key=='attribute_pa_p3dlite_unit' ) {
						$attr_st.=__( "Unit" , '3dprint-lite' ).": ".__( $attr_value )."<br>";
					}
					else {
						//$product_attributes=( $product->get_attributes() );
						$attribute_name=str_replace( 'attribute_', '', $attr_key );
						$attribute_name=strtoupper( str_replace( '_', ' ', $attr_key ) );
						$attr_st.=$attribute_name .": $attr_value<br>";
					}
				}
				if (isset($price_request['estimated_price'])) {
					$attr_st.= __('Estimated Price:')."  ".p3dlite_format_price($price_request['estimated_price'], $settings['currency'], $settings['currency_position'])."<br>";
				}
				echo '
				<tr>
					<td>
						<a class="remove_request" href="javascript:void(0);" onclick="p3dliteRemoveRequest(\''.$product_key.'\');return false;">
							<img alt="'.__( 'Remove Request', '3dprint-lite' ).'" title="'.__( 'Remove Request', '3dprint-lite' ).'" src="'.plugins_url( '3dprint-lite/images/remove.png' ).'">
						</a>
					</td>
					<td>
						<a href="'.get_permalink( $post_id ).'">'.get_permalink( $post_id ).'</a>
					</td>
					<td>
						'.__( 'Email:', '3dprint-lite' ).' '.$price_request['email'].'<br>
						'.__( 'Comment:', '3dprint-lite' ).' '.$price_request['request_comment'].'
					</td>
					<td>'.$attr_st.'</td>
					<td>
						<span style="color:red;">*</span> <input name="p3d_buynow['.$product_key.']" type="text">'.$settings['currency'].'
					</td>
					<td>
						<textarea name="p3d_comments" style="width:250px;height:100px;" placeholder="'.__( 'Leave a comment or a payment link.', '3dprint-lite' ).'"></textarea>
					</td>
				</tr>';
			}
		}
?>
				</table>
<?php
	}
?>
				<input type="hidden" name="action" value="update" />
				<input type="hidden" name="page_options" value="new_option_name,some_other_option,option_etc" />

				<p class="submit">
					<input type="submit" class="button-primary" value="<?php _e( 'Email Quotes', '3dprint-lite' ) ?>" />
				</p>
			</form>
		</div><!-- p3dlite_tabs-4 -->
	</div><!-- p3dlite_tabs -->
</div> <!-- wrap -->
<?php
}
?>