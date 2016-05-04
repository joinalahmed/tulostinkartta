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
    global $bp;  
	if ( isset( $_POST['action'] ) && $_POST['action']=='request_price' ) {
		$product_id=(int)$_POST['p3dlite_product_id'];
		$printer_id=(int)$_POST['attribute_pa_p3dlite_printer'];
		$material_id=(int)$_POST['attribute_pa_p3dlite_material'];
		$coating_id=(int)$_POST['attribute_pa_p3dlite_coating'];
		$model_file=sanitize_file_name( basename( $_POST['attribute_pa_p3dlite_model'] ) );
		$email_address = sanitize_email( $_POST['email_address'] );
        
		$printteri=(int)$_GET['printteri'];
                
		$request_comment = sanitize_text_field( $_POST['request_comment'] );
		$maara = sanitize_text_field( $_POST['maara'] );
        
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
 
			$hinta=(float)$_POST['p3dlite_estimated_price'];
            
			update_option( "p3dlite_price_requests", $p3dlite_price_requests ); 

			// $request_comment
			$upload_dir = wp_upload_dir();
			$link = $upload_dir['baseurl'].'/p3d/'.$model_file;
            
			$printteri=(int)$_GET['printteri'];

                        $materiaali = get_post_meta($printteri, 'filamentit_lista_' . $material_id . '_filamentin_nimi', true);
			$tulostinmalli = get_post_meta($printteri, 'tulostimet_' . $printer_id . '_tulostimen_malli', true);

            		$tulostin = $printteri;

            		$tilaajaid = get_current_user_id();

            		$uusititle = time();

            $newprintjob = array(
			 'post_content'   => "",
			 'post_title'     => $uusititle,
			 'post_status'    => "publish",
			 'post_type'      => "tulostuspyynto",
			 'post_author'    => $tilaajaid,
			 );  
            $newprintjobid = wp_insert_post( $newprintjob);

            $tulostinomistaja = get_post($printteri); 
            $tulostinomistaja = $tulostinomistaja->post_author;
            update_post_meta($newprintjobid, "tulostinomistaja", $tulostinomistaja);
            update_post_meta($newprintjobid, "materiaali", $materiaali);
            update_post_meta($newprintjobid, "tulostin", $tulostin);
            update_post_meta($newprintjobid, "tulostinmalli", $tulostinmalli);
            update_post_meta($newprintjobid, "tiedosto", $link);
            update_post_meta($newprintjobid, "maara", $maara);
            update_post_meta($newprintjobid, "hinta", $hinta);            
            update_post_meta($newprintjobid, "kuvaus", $request_comment);
            update_post_meta($newprintjobid, "tarjous", $maara*$hinta); 
            update_post_meta($newprintjobid, "tila", "tarjous");

            global $bp;  
            $tulostuspyyntourl = "/tulostuspyynto/" . $uusititle . "/";

	    $filename = $link;
	    $parent_post_id = $newprintjobid;
	    $filetype = wp_check_filetype( basename( $filename ), null );

	    $attachment = array(
	    		'guid'           => $link, 
	    		'post_mime_type' => "application/sla",
	    		'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $filename ) ),
	    		'post_content'   => '',
	    		'post_status'    => 'inherit'
			);

	    $attach_id = wp_insert_attachment( $attachment, $filename, $parent_post_id );

	    require_once( ABSPATH . 'wp-admin/includes/image.php' );

	    $attach_data = wp_generate_attachment_metadata( $attach_id, $filename );

	    wp_update_attachment_metadata( $attach_id, $attach_data );
            echo "<p>Uusi tulostuspyyntö on tehty ja voit muokata sitä osoitteessa " . $tulostuspyyntourl . "</p>"; 
            echo "<script type='text/javascript'>                                                                                                           
	    window.location.assign('" . $tulostuspyyntourl  . "?updated=true')
	    </script>";
            
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
    

    
    <?php
		$printteri=(int)$_GET['printteri'];
    ?>
    
    

	<div id="p3dlite-viewer">
		<canvas id="p3dlite-cv" width="<?php echo $settings['canvas_width'];?>" height="<?php echo $settings['canvas_height'];?>" style="border: 1px solid;"></canvas>
		<div id="canvas-stats">
			<div class="canvas-stats">
				<a style="color:red;text-decoration:underline;" href="javascript:void(0)" onclick="p3dlite_viewer.setRenderMode('flat');p3dlite_viewer.update();">Kiinteä</a> /
				<a style="color:red;text-decoration:underline;" href="javascript:void(0)" onclick="p3dlite_viewer.setRenderMode('wireframe');p3dlite_viewer.update();">Ääriviivat</a>
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
	<button id="p3dlite-pickfiles" class="progress-button">Lataa malli!</button>
<?php
	}
	else {
?>
	<button id="p3dlite-pickfiles" class="progress-button" data-style="rotate-angle-bottom" data-perspective data-horizontal>Lataa malli</button>
<?php
	}
?>
	<div class="p3dlite-info">
Tiedoston mittayksikkö:		&nbsp;&nbsp;
		<input class="p3dlite-control" autocomplete="off" id="unit_mm" onclick="p3dliteSelectUnit(this);" type="radio" name="p3dlite_unit" value="mm">
		<span style="cursor:pointer;" onclick="p3dliteSelectUnit(jQuery('#unit_mm'));">Millimetriä</span>
		&nbsp;&nbsp;
		<input class="p3dlite-control" autocomplete="off" id="unit_inch" onclick="p3dliteSelectUnit(this);" type="radio" name="p3dlite_unit" value="inch">
		<span style="cursor:pointer;" onclick="p3dliteSelectUnit(jQuery('#unit_inch'));">Tuumaa</span>
	</div>

	</div>
	<pre id="p3dlite-console"></pre>
	<div id="p3dlite-filelist">Your browser doesn't have Flash, Silverlight or HTML5 support.</div>
	<div class="p3dlite-info">
	  	<span id="p3dlite-error-message" class="error"></span>
	</div>
	<div class="p3dlite-info">

		<table class="p3dlite-stats">
			<tr>
				<td>
					Filamentin tilavuus:
				</td>
				<td>
					<span id="stats-material-volume"></span> <?php _e( 'cm3', '3dprint-lite' );?>
				</td>
			</tr>
			<tr>
				<td>
					Laatikon tilavuus:
				</td>
				<td>
					<span id="stats-box-volume"></span> <?php _e( 'cm3', '3dprint-lite' );?>
				</td>
			</tr>
			<tr>
				<td>
					Pinta-ala:
				</td>
				<td>
					<span id="stats-surface-area"></span> <?php _e( 'cm2', '3dprint-lite' );?>
				</td>
			</tr>
			<tr>
				<td>
					Mallin ulottuvuudet:
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
			        <?php if ( $settings['pricing']=='request_estimate' ) echo '<b>Arvioitu kappalehinta:</b>';?>
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
					<input class="price-request-field" type="hidden" value="tomi@sange.fi" placeholder="tomi@sange.fi" name="email_address"><br>
					<p>Kommentti tulostuspyynnöstä: <input class="price-request-field" type="text" value="" placeholder="Kommentti..." name="request_comment"></p><br>
                    			<p>Tulostettava määrä: <input style="width:100px" min="1" class="price-request-field" type="number" value="1" placeholder="1" name="maara"></p><br>
					<button style="float:left;" type="submit" class="button alt">Kysy hintaa!</button>
				</div>
			</div>
		</div>
	</form>

<?php	
    $db_printers=get_option( 'p3dlite_printers' );
	$db_materials=get_option( 'p3dlite_materials' );
	$db_coatings=get_option( 'p3dlite_coatings' );
?>

	<div class="p3dlite-info">
		<fieldset id="printer_fieldset" class="p3dlite-fieldset">
			<legend>Tulostin</legend>
			<ul class="p3dlite-list">
  
<?php                
  
    unset($db_printers);
    
    $printteri=(int)$_GET['printteri'];
?>                
                
<?php



    $printteri_numero = 0;
    $tulostimen_malli = get_post_meta($printteri, 'tulostimet_' . $printteri_numero . '_tulostimen_malli', true);

    while(!empty($tulostimen_malli)) {
    $tulostimen_malli = get_post_meta($printteri, 'tulostimet_' . $printteri_numero . '_tulostimen_malli', true);
    $tulostimen_pituus = get_post_meta($printteri, 'tulostimet_' . $printteri_numero . '_tulostimesi_pituus', true);
    $tulostimen_leveys = get_post_meta($printteri, 'tulostimet_' . $printteri_numero . '_tulostimesi_leveys', true);
    $tulostimen_korkeus = get_post_meta($printteri, 'tulostimet_' . $printteri_numero . '_tulostimesi_korkeus', true);
    $tulostimen_hinta = get_post_meta($printteri, 'tulostimet_' . $printteri_numero . '_tulostimesi_hinta', true);
    if(!empty($tulostimen_malli)) {
                        echo '<li onclick="p3dliteSelectPrinter(this);" data-name="'.esc_attr($tulostimen_malli).'"><input id="p3dlite_printer_' . $printteri_numero . '" class="p3dlite-control" autocomplete="off" data-width="'.$tulostimen_leveys.'" data-length="'.$tulostimen_pituus.'" data-height="'.$tulostimen_korkeus.'" data-id="'.$printteri_numero.'" data-price="'.esc_attr( $tulostimen_hinta ).'" data-price_type="material_volume" type="radio" name="product_printer">'.$tulostimen_malli.'</li>';
    }
    $printteri_numero++;
    }
?>
                
		  	</ul>
	  	</fieldset>
	</div>

    <?php 
        
    unset($db_materials);
        
    $filament_price_type = "cm3";
    
    unset($db_materials);
    
    
	<div class="p3dlite-info">
		<fieldset id="material_fieldset" class="p3dlite-fieldset">
			<legend>Filamentti</legend>
			<ul class="p3dlite-list">
<?php

    $filamentit_numero = 0;
    $filamentin_nimi = get_post_meta($printteri, 'filamentit_lista_' . $filamentit_numero . '_filamentin_nimi', true);
    while(!empty($filamentin_nimi)) {
    $filamentin_nimi = get_post_meta($printteri, 'filamentit_lista_' . $filamentit_numero . '_filamentin_nimi', true);
    $filamentin_hinta = get_post_meta($printteri, 'filamentit_lista_' . $filamentit_numero . '_filamenttisi_hinta', true);    
    $filamentin_vari = get_post_meta($printteri, 'filamentit_lista_' . $filamentit_numero . '_filamentin_vari', true);
    if(!empty($filamentin_nimi)) {
    echo '<li data-color="' . $filamentin_vari . '" data-name="' . $filamentin_nimi . '" onclick="p3dliteSelectFilament(this);"><input id="p3dlite_material_' . $filamentit_numero . '" class="p3dlite-control" autocomplete="off" type="radio" data-id="' . $filamentit_numero . '" data-density="1" data-price="'.esc_attr($filamentin_hinta).'" data-price_type="cm3" name="product_filament" ><div style="background-color:' . $filamentin_vari . '" class="color-sample"></div>' . $filamentin_nimi . '</li>';
    }
    $filamentit_numero++;
    }

?>
            </ul>
		</fieldset>
	</div>
<?php 
if ($db_coatings && count($db_coatings)>0) {
?>
	<div class="p3dlite-info">
		<fieldset id="coating_fieldset" class="p3dlite-fieldset">
			<legend><?php _e( 'Coating', '3dprint-lite' );?></legend>
			<ul class="p3dlite-list">
<?php
		for ( $i=0;$i<count( $db_coatings );$i++ ) {
			echo '<li data-color=\''.$db_coatings[$i]['color'].'\' data-name="'.esc_attr( $db_coatings[$i]['name'] ).'" onclick="p3dliteSelectCoating(this);"><input id="p3dlite_coating_'.$i.'" class="p3dlite-control" autocomplete="off" type="radio" data-id="'.$i.'"  data-price="'.esc_attr( $db_coatings[$i]['price'] ).'" name="product_coating" ><div style="background-color:'.$db_coatings[$i]['color'].'" class="color-sample"></div>'.$db_coatings[$i]['name'].'</li>';
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