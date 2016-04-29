<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package cc2
 */
$content_class = array('main-content-inner');
acf_form_head(); 
get_header(); ?>

    <div class="main-content">
        <div id="container" class="container">
            <div class="row">
				

                <?php do_action( 'cc_first_inside_main_content'); ?>

                <?php
                // get the left sidebar if it should be displayed
                if( cc2_display_sidebar( 'left' ) )
                    get_sidebar( 'left' ); ?>

                <div id="content" class="<?php echo apply_filters( 'cc2_content_class', $content_class ); ?>">

                    <?php do_action( 'cc_first_inside_main_content_inner'); ?>

		    <?php
                    global $wpdb,$bp;
                    global $post;
                    global $user_ID;
                    $current_user = wp_get_current_user(); 
                    $userid = $current_user->ID;
                    if ( 0 == $current_user->ID ) {
                        die();
                    } else {
                        $tulostinnumero = get_user_meta($userid, "tulostinnumero", "true");                         
                    }
                    
                    if (empty($tulostinnumero)) {
                        $tulostajaid = get_current_user_id();
                        $uusititle = $current_user->user_login;
                        $newprinter = array(
			                 'post_content'   => "",
			                 'post_title'     => $uusititle,
			                 'post_status'    => "draft",
			                 'post_type'      => "tulostin",
			                 'post_author'    => $tulostajaid,
			            );  
                    
                    $tulostinnumero = wp_insert_post($newprinter);
                    update_user_meta( $userid, "tulostinnumero", $tulostinnumero);
                    } 
		    /*
		    if (empty($tuotenumero)) {
                        $tulostajaid = get_current_user_id();
                        $uusititle = $current_user->user_login;
                        $newprinter = array(
                                         'post_content'   => "",
                                         'post_title'     => $uusititle,
                                         'post_status'    => "draft",
                                         'post_type'      => "product",
                                         'post_author'    => $tulostajaid,
                                    );

                    $tuotenumero = wp_insert_post($newprinter);
                    update_user_meta( $userid, "tuotenumero", $tuotenumero);
                    } */
                    
                    acf_form(array(
                        'post_id'=> $tulostinnumero,
                        'field_groups' => array('4225'),
                        'return' => '?updated=true',
                        'submit_value'=> 'Päivitä tulostin!',
                    ));
                                                            
                    $updated = $_GET['updated'];
                    if ($updated == "true") {
                        
                    include_once '/var/www/gonzo/wp-content/plugins/geo-my-wp/plugins/posts/includes/gmw-pt-update-location.php'; 
                    $sijainti = get_post_meta($tulostinnumero,"sijainti",TRUE);
                    $otsikko = get_post_meta($tulostinnumero,"name",TRUE);
                    $julkaise = get_post_meta($tulostinnumero,"julkaise",TRUE);
                    $kuvaus = get_post_meta($tulostinnumero,"kuvaus",TRUE);
                    $malli = get_post_meta($tulostinnumero,"tulostimen_malli",TRUE);
                    $tulostimen_kuva = get_post_meta($tulostinnumero,"tulostimen_kuva",TRUE);

/*                    $abs = get_post_meta($tulostinnumero, 'abs', TRUE); 
                    $pla = get_post_meta($tulostinnumero, 'pla', TRUE); 
                    $bioflex = get_post_meta($tulostinnumero, 'bioflex', TRUE);
                    $nylon = get_post_meta($tulostinnumero, 'nylon', TRUE);
                    $pleksi = get_post_meta($tulostinnumero, 'pleksi', TRUE);
                    $puu = get_post_meta($tulostinnumero, 'puu', TRUE);
                    $pva = get_post_meta($tulostinnumero, 'pva', TRUE); */
    
/*                  $abs = $abs[0];
                    $pla = $pla[0];
                    $bioflex = $bioflex[0];
                    $nylon = $nylon[0];
                    $pleksi = $pleksi[0];
                    $puu = $puu[0];
                    $pva = $pva[0]; */
                        
                    $materiaalilista = "";     
		    $filamentit_numero = 0;
    		    $filamentin_nimi = get_post_meta($tulostinnumero, 'filamentit_lista_' . $filamentit_numero . '_filamentin_nimi', true);
    		    while(!empty($filamentin_nimi)) {
    		    $filamentin_nimi = get_post_meta($tulostinnumero, 'filamentit_lista_' . $filamentit_numero . '_filamentin_nimi', true);
    		    if(!empty($filamentin_nimi)) {
 		    				 $materiaalilista .= $filamentin_nimi . ",";
		    }
    		    $filamentit_numero++;
    		    }

                    $malli = "";

    		    $printteri_numero = 0;
    		    $tulostimen_malli = get_post_meta($tulostinnumero, 'tulostimet_' . $printteri_numero . '_tulostimen_malli', true);

    		      while(!empty($tulostimen_malli)) {
    				     $tulostimen_malli = get_post_meta($tulostinnumero, 'tulostimet_' . $printteri_numero . '_tulostimen_malli', true);
                            if(!empty($tulostimen_malli)) {
			                        $malli .= $tulostimen_malli . ",";
    

    		    }
		    $printteri_numero++;
		    }

                    $my_post = array(
                        'ID'           => $tulostinnumero,
                        'post_title'   => $otsikko,
                        'post_content' => $kuvaus,
                        'post_status'    => $julkaise,
                    );

                    wp_update_post( $my_post );
    
                    wp_set_post_terms( $tulostinnumero, $malli, "tulostinmalli", false);
                    wp_set_post_terms( $tulostinnumero, $materiaalilista, "tulostusmateriaali", false);
       
                    set_post_thumbnail( $tulostinnumero, $tulostimen_kuva );
                        
                    $sijainti = get_post_meta($tulostinnumero,"sijainti",TRUE);
                    
                    $lat = $sijainti['lat'];
                    $long = $sijainti['lng'];
                    $formatted_address = $sijainti['address'];
                        
                    update_post_meta($tulostinnumero, "formatted_address", $formatted_address);
                    update_post_meta($tulostinnumero, "long", $long);
                    update_post_meta($tulostinnumero, "lat", $lat);
                        
	               if ( function_exists( 'gmw_pt_update_location' ) ) {
		                  $args = array(
			             'post_id'         => $tulostinnumero,
                         	     'post_type'       => 'tulostin',
			             'post_title'      => $otsikko,
			             'address'         => $sijainti['address'],
			             'lat'               => $sijainti['lat'],
                         	     'long'              => $sijainti['lng'],
			             'street'              => "",
			             'formatted_address' => "",
			             'zipcode'           => "",
			             'country'           => "",
			             'country_long'      => "",
			             'state'           => "",
			             'state_long'      => "",
			             'post_status'       => $julkaise,
		          );
		          gmw_pt_update_location( $args );

                    }
                    }
  
		    /*
		    $custom_fields = get_post_custom($tuotenumero);
		    echo var_dump($custom_fields);
		    */

                    /*
		    $julkaise = get_post_meta($tulostinnumero,"julkaise",TRUE);
                    $sijainti = get_post_meta($tulostinnumero,"sijainti",TRUE);
                    echo $sijainti['lat'];
                    echo $sijainti['lng'];
                    echo $sijainti['address'];
                    echo $julkaise; 
                    echo var_dump($julkaise); */
                    ?>
                    
                </div><!-- close #content -->

                <?php if( cc2_display_sidebar( 'right' ) )
                    get_sidebar( 'right' ); ?>


            </div><!-- close .row -->
        </div><!-- close .container -->
    </div><!-- close .main-content -->

<?php get_footer(); ?>






