<?php
/*
Plugin Name: 3D-Tarvike Tulostinkartta
Plugin URI: http://tulostimet.3d-tarvike.fi/
Description: 3D-Tarvike Tulostinkartta
Version: 1.0
Author: Tomi Toivio
Author URI: http://sange.fi/
License: GPL2
*/

/*  Copyright 2016 Tomi Toivio (email: tomi@sange.fi)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/* Valikkosivusta ei ole juuri nyt mitään hyötyä */

add_action( 'admin_menu', 'tulostin_register_kartta_menu_page' );

function tulostin_register_kartta_menu_page() {
	add_menu_page( 'Tulostinkartta', 'Tulostinkartta', 'edit_plugins', 'tulostin_kartta', 'tulostin_kartta_menu_page' );
}

function tulostin_kartta_menu_page() {
	echo "<h1>Karttaplugari</h1>";
	tt_blockio_balances();
	tt_blockio_accounts();
}

/* Lisätään 3D-filamentit taksonomiaksi */

function materiaali_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Tulostusmateriaalit', 'Taxonomy General Name', 'tulostustarvike' ),
		'singular_name'              => _x( 'Tulostusmateriaali', 'Taxonomy Singular Name', 'tulostustarvike' ),
		'menu_name'                  => __( 'Tulostusmateriaali', 'tulostustarvike' ),
		'all_items'                  => __( 'Kaikki tulostusmateriaalit', 'tulostustarvike' ),
		'parent_item'                => __( 'Isäntä', 'tulostustarvike' ),
		'parent_item_colon'          => __( 'Isäntä:', 'tulostustarvike' ),
		'new_item_name'              => __( 'Uuden materiaalin nimi', 'tulostustarvike' ),
		'add_new_item'               => __( 'Lisää uusi tulostusmateriaali', 'tulostustarvike' ),
		'edit_item'                  => __( 'Muokkaa tulostusmateriaalia', 'tulostustarvike' ),
		'update_item'                => __( 'Päivitä tulostusmateriaali', 'tulostustarvike' ),
		'separate_items_with_commas' => __( 'Erota tulostusmateriaalit pilkuilla', 'tulostustarvike' ),
		'search_items'               => __( 'Haku tulostusmateriaaleista', 'tulostustarvike' ),
		'add_or_remove_items'        => __( 'Lisää tai poista tulostusmateriaaleja', 'tulostustarvike' ),
		'choose_from_most_used'      => __( 'Valitse käytetyimmistä tulostusmateriaaleista', 'tulostustarvike' ),
		'not_found'                  => __( 'Tulostusmateriaalia ei löydy', 'tulostustarvike' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'tulostusmateriaali', array( 'tulostin' ), $args );
}

add_action( 'init', 'materiaali_taxonomy', 0 );

/* Lisätään 3D-tulostinmallit taksonomiaksi */

function tulostinmalli_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Tulostinmallit', 'Taxonomy General Name', 'filamentti' ),
		'singular_name'              => _x( 'Tulostinmalli', 'Taxonomy Singular Name', 'filamentti' ),
		'menu_name'                  => __( 'Tulostinmalli', 'filamentti' ),
		'all_items'                  => __( 'Tulostimien mallit', 'filamentti' ),
		'parent_item'                => __( 'Isäntä', 'filamentti' ),
		'parent_item_colon'          => __( 'Isäntä:', 'filamentti' ),
		'new_item_name'              => __( 'Uusi tulostinmalli', 'filamentti' ),
		'add_new_item'               => __( 'Lisää uusi tulostinmalli', 'filamentti' ),
		'edit_item'                  => __( 'Muokkaa tulostinmallia', 'filamentti' ),
		'update_item'                => __( 'Päivitä tulostinmalli', 'filamentti' ),
		'separate_items_with_commas' => __( 'Erota tulostinmallit pilkuilla', 'filamentti' ),
		'search_items'               => __( 'Etsi tulostinmallia', 'filamentti' ),
		'add_or_remove_items'        => __( 'Lisää tai poista tulostinmalli', 'filamentti' ),
		'choose_from_most_used'      => __( 'Valitse käytetyimmistä tulostinmalleista', 'filamentti' ),
		'not_found'                  => __( 'Tulostinmallia ei löydy', 'filamentti' ),
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'tulostinmalli', array( 'tulostin' ), $args );
}
add_action( 'init', 'tulostinmalli_taxonomy', 0 );

/* Lisätään 3D-tulostinsivut custom post typeksi */

if ( ! function_exists( 'filamentti_tulostin_post' ) ) {

	function filamentti_tulostin_post() {

		$labels = array(
			'name'                => _x( 'Tulostimet', 'Post Type General Name', 'filamentti' ),
			'singular_name'       => _x( 'Tulostin', 'Post Type Singular Name', 'filamentti' ),
			'menu_name'           => __( 'Tulostin', 'filamentti' ),
			'parent_item_colon'   => __( 'Isäntä', 'filamentti' ),
			'all_items'           => __( 'Tulostimet', 'filamentti' ),
			'view_item'           => __( 'Katso tulostinta', 'filamentti' ),
			'add_new_item'        => __( 'Lisää tulostin', 'filamentti' ),
			'add_new'             => __( 'Uusi tulostin', 'filamentti' ),
			'edit_item'           => __( 'Muokkaa tulostinta', 'filamentti' ),
			'update_item'         => __( 'Päivitä tulostin', 'filamentti' ),
			'search_items'        => __( 'Etsi tulostimia', 'filamentti' ),
			'not_found'           => __( 'Tulostimia ei löytynyt', 'filamentti' ),
			'not_found_in_trash'  => __( 'Tulostimia ei roskakorissa', 'filamentti' ),
		);
		$args = array(
			'label'               => __( 'tulostin', 'filamentti' ),
			'description'         => __( 'Kolmiulotteinen tulostin', 'filamentti' ),
			'labels'              => $labels,
			'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'custom-fields', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
		);
		register_post_type( 'tulostin', $args );

	}
	add_action( 'init', 'filamentti_tulostin_post', 0 );
}

/* Lisätään tulostuspyyntö custom post typeksi */

function tulostuspyynto_post_type() {

	$labels = array(
		'name'                => _x( 'Tulostuspyynnöt', 'Post Type General Name', 'filamentti' ),
		'singular_name'       => _x( 'Tulostuspyyntö', 'Post Type Singular Name', 'filamentti' ),
		'menu_name'           => __( 'Tulostuspyyntö', 'filamentti' ),
		'parent_item_colon'   => __( 'Isäntä', 'filamentti' ),
		'all_items'           => __( 'Kaikki tulostuspyynnöt', 'filamentti' ),
		'view_item'           => __( 'Katso tulostuspyyntöä', 'filamentti' ),
		'add_new_item'        => __( 'Lisää tulostuspyyntö', 'filamentti' ),
		'add_new'             => __( 'Lisää uusi tulostuspyyntö', 'filamentti' ),
		'edit_item'           => __( 'Muokkaa tulostuspyyntöä', 'filamentti' ),
		'update_item'         => __( 'Päivitä tulostuspyyntöä', 'filamentti' ),
		'search_items'        => __( 'Hae tulostuspyyntöjä', 'filamentti' ),
		'not_found'           => __( 'Tulostuspyyntöjä ei löydy', 'filamentti' ),
		'not_found_in_trash'  => __( 'Tulostuspyyntöjä ei löydy roskakorista', 'filamentti' ),
	);
	$args = array(
		'label'               => __( 'tulostuspyynto', 'filamentti' ),
		'description'         => __( 'Tulostuspyyntö', 'filamentti' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'editor', 'author', 'comments', 'custom-fields' ),
		'hierarchical'        => false,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => 'post',
	);
	register_post_type( 'tulostuspyynto', $args );
}
add_action( 'init', 'tulostuspyynto_post_type', 0 );

/* Admin bar piiloon epäadmineilta */

/*
add_action( 'after_setup_theme', 'remove_admin_bar' );

function remove_admin_bar() {
	if ( !current_user_can( 'administrator' ) && !is_admin() ) {
		show_admin_bar( false );
	}
}
*/

/* Geokoodataan tulostimien sijainnit kartalle tulostinsivuja päivitettäessä */ 

/*
function gmw_update_location_filamentti( $post_id, $post, $update ) {
	   global $wpdb, $bp;
	   global $post;
	   global $user_ID;

	   $post = get_post( $post_id );

	   $slug = 'tulostin';
	   if ( $slug != $post->post_type ) {
		  return;
	   }
	    $contact_tulostaja = get_post( $post_id );
	    $contact_tulostaja = $contact_tulostaja->post_author;

	    $table_name = "wppl_friends_locator";

	    $data = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE member_id='" . $contact_tulostaja . "'" );

	    $data = $data[0];
	    $address = $data->address;
	    $lat = $data->lat;
	    $long = $data->long;
	    $street = $data->street;
	    $zipcode = $data->zipcode;
	    $country = $data->country;
	    $country_long = $data->country_long;
	    $state = $data->state;
	    $state_long = $data->state_long;
	    $formatted_address = $data->formatted_address;

  	    $osoite = bp_get_profile_field_data("field=Osoite&user_id=" . $contact_tulostaja);

        update_post_meta($post_id, 'osoite', $osoite);
        update_post_meta($post_id, 'formatted_address', $formatted_address);   
        update_post_meta($post_id, 'address', $address);
        update_post_meta($post_id, 'lat', $lat);
        update_post_meta($post_id, 'long', $long);
        update_post_meta($post_id, 'street', $street);
        update_post_meta($post_id, 'zipcode', $zipcode);
        update_post_meta($post_id, 'country', $country);
        update_post_meta($post_id, 'country_long', $country_long);
        update_post_meta($post_id, 'state', $state);
        update_post_meta($post_id, 'state_long', $state_long);

	    include_once (__ROOT__.'/wp-content/plugins/geo-my-wp/plugins/posts/includes/gmw-pt-update-location.php');

	if ( function_exists( 'gmw_pt_update_location' ) ) {
		$args = array(
			'post_id'         => $post_id,
			'post_type'       => 'tulostin',
			'post_title'      => get_the_title( $post_id ),
			'address'         => $address,
			'lat'               => $lat,
			'long'              => $long,
			'street'              => $street,
			'formatted_address' => $formatted_address,
			'zipcode'           => $zipcode,
			'country'           => $country,
			'country_long'      => $country_long,
			'state'           => $state,
			'state_long'      => $state_long,
			'post_status'       => 'publish',
		);
		gmw_pt_update_location( $args );
	}
}
add_action( 'save_post', 'gmw_update_location_filamentti', 10, 1 );
*/

/* Geokoodataan käyttäjien sijainnit osoite-profiilikentän perusteella */

/*
function kartta_profiili_updated($user_id) {
  global $wpdb, $bp;
    
  $sijainti = bp_get_profile_field_data("field=Osoite&user_id=" . $user_id);

  if(!empty($sijainti)) {

    $vanhasijainti = get_user_meta($user_id, "sijainti", true);

    if ($sijainti !=  $vanhasijainti) {

    update_user_meta($user_id, 'sijainti', $sijainti);

    $address = urlencode($sijainti);

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, "http://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&region=fi");
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      $okf = curl_exec($ch);
      curl_close($ch);

      $okf = json_decode($okf,true);
        
      $okf=$okf['results']; 
      $okf=$okf[0]; 
      $address_components = $okf["address_components"];
      
      foreach ($address_components as &$address_component) {

      $addresstypes = $address_component["types"];

      if (in_array("locality", $addresstypes)) {
        $city = $address_component["long_name"];
	}

        if (in_array("country", $addresstypes)) {
          $country = $address_component["long_name"];
	    $country_iso = $address_component["short_name"];
	    }

        if (in_array("postal_code", $addresstypes)) {
          $zip = $address_component["long_name"];
        }

      }
      $formatted_address=$okf['formatted_address'];
      $okf=$okf['geometry']; 
      $latlong=$okf['location'];
      $latitude=$latlong['lat'];
      $longitude=$latlong['lng'];                      
      update_user_meta($user_id, 'formatted_address', $formatted_address);
      update_user_meta($user_id, 'latitude', $latitude);
      update_user_meta($user_id, 'longitude', $longitude);
      update_user_meta($user_id, 'zip', $zip);
      update_user_meta($user_id, 'city', $city);
      update_user_meta($user_id, 'country', $country);
      update_user_meta($user_id, 'address', $sijainti);
    
      require_once(__ROOT__.'/wp-content/plugins/geo-my-wp/plugins/posts/includes/gmw-fl-update-location.php');

      $wpdb->replace('wppl_friends_locator', array(
            'member_id'         => $user_id,
            'city'              => $city,
            'zipcode'           => $zip,
            'country'           => $country,
            'address'           => $sijainti,
            'formatted_address' => $formatted_address,
            'lat'               => $latitude,
            'long'              => $longitude));
    }
  }
}
add_action( 'xprofile_updated_profile', 'kartta_profiili_updated', 1, 5 );
*/
/*
add_filter( 'option_active_plugins', 'tt_disable_gmw_plugin' );

function tt_disable_gmw_plugin($plugins){

if($_SERVER['REQUEST_URI'] === '/omatulostin/') {
        $key = array_search( 'geo-my-wp/geo-my-wp.php' , $plugins );
        if ( false !== $key ) {
            unset( $plugins[$key] );
        }
    }
    return $plugins;
}
*/

/* Poistetaan GMW:n ylimääräiset Google Maps -scriptit sivuilta joilla niitä ei tarvita */

/*
function remove_gmw_homostelu() {
if(preg_match($_SERVER['REQUEST_URI'], '/omatulostin/')) {
     wp_dequeue_script('gmw-google-autocomplete');
     wp_dequeue_script('gmw-js');
     wp_dequeue_script('gmw-marker-clusterer');
     wp_dequeue_script('gmw-marker-spiderfier');
     wp_dequeue_script('gmw-infobox');
     wp_dequeue_script('gmw-get-directions');
     wp_dequeue_script('jquery-ui-draggable');
     wp_dequeue_script('google-maps');
    } 
if(preg_match($_SERVER['REQUEST_URI'], '/tulostinkartta/')) {
     wp_enqueue_script( 'gmw-google-autocomplete' );
     wp_enqueue_script( 'gmw-js' );
     wp_enqueue_script( 'gmw-marker-clusterer' );
     wp_enqueue_script( 'gmw-marker-spiderfier' );
     wp_enqueue_script( 'gmw-infobox' );
     wp_enqueue_script( 'gmw-get-directions' );
     wp_enqueue_script( 'jquery-ui-draggable' );
     wp_enqueue_script( 'google-maps' );
    } 
}
add_action('wp_head', 'remove_gmw_homostelu', 1);
*/

/* Tulostuspyyntösivulle pääsy vain lähettäjälle ja vastaanottajalle */

function printjob_page() {
                global $post;
                global $wpdb;
                global $bp;
                $postid = get_the_ID();

                $current_user_ID = get_current_user_id(); 

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

                if ($current_user_ID != ($kirjoittaja || $vastaanottaja)){
			         die ( 'You do not have sufficient permissions to access this page!' );
	            }
    
            $updated = $_GET['updated'];
            if ($updated == "true") {
                printjob_updated();
                }
            printjob_status();
}

/* Tulostuspyynnön päivitysfunktio */

/* Tulostuspyynnön tilat:
    - tarjous
    - tilaus
    - maksu
    - arvostelu
    - valmis
    - peruutettu
    */

function printjob_updated() {
	    global $wpdb,$bp;
        global $post;
        global $user_ID;

        $postid = get_the_ID();

        $current_user_ID = get_current_user_id();
    
        $updated = $_GET['updated'];

	    $today = date("d.m.Y H:i:s");
    
        $kirjoittaja = get_post($post->ID);      
        $kirjoittaja = $kirjoittaja->post_author;
    
        $vastaanottaja = get_post_meta($post->ID, "tulostin",true);
        $contact_kirjoittaja = get_post($vastaanottaja);
        $vastaanottaja = $contact_kirjoittaja->post_author;  

	    $tila = get_post_meta( get_the_ID(), 'tila', true );    
    
      if (($updated == "true") && ($tila == "tarjous") && ($current_user_ID == $kirjoittaja)) {
        $message = "\nUusi tulostuspyyntö: " . $post->guid . "\n";     
        $otsikko = "Uusi tulostuspyyntö";
        $recipient = $vastaanottaja;
        $sender = $kirjoittaja;
	    update_post_meta($post->ID, "tilaaja",$kirjoittaja);
  	    update_post_meta($post->ID, "tulostaja",$vastaanottaja);
	    update_post_meta($post->ID, "aika",$today);
      } 
      if (($updated == "true") && ($tila == "tilaus") && ($current_user_ID == $vastaanottaja)) {
        $message = "\nUusi tarjous: " . $post->guid . "\n";
        $otsikko = "Uusi tarjous";
        $recipient = $kirjoittaja;
        $sender = $vastaanottaja;
        update_post_meta($post->ID, "tarjousaika",$today);
	    $kurssi = get_option("btc_kurssi");
	    $hinta = get_post_meta($post->ID, "tarjous",true);	
	    $hinta_bitcoineissa = $hinta / $kurssi;
	    update_post_meta($post->ID, "bitcoin_hinta",$hinta_bitcoineissa);
      }
      if (($updated == "true") && ($tila == "maksu") && ($current_user_ID == $kirjoittaja)) {
        $message = "\nUusi tilaus: " . $post->guid . "\n";
        $otsikko = "Uusi tilaus";
        $recipient = $vastaanottaja;
        $sender = $kirjoittaja;
        update_post_meta($post->ID, "tilausaika",$today);
        require_once(__ROOT__.'/wp-content/plugins/tulostinkartta/block_io.php'); 
        $apiKey = get_option( 'blockio_api_key' );  
    	$version = 2; 
        $pin = get_option( 'blockio_pin' );    
    	$block_io = new BlockIo($apiKey, $pin, $version);    	
   	    $amounts = get_post_meta($post->ID, "bitcoin_hinta",true);
	    $amounts = floatval($amounts);
        $amounts = round($amounts, 4);
	    $amounts = ($amounts-0.0001);
	    $tulos = $block_io->withdraw_from_labels(array('amounts' => $amounts, 'from_labels' => $kirjoittaja, 'to_labels' => '1'));
	    tt_blockio_balances();
	    update_post_meta($post->ID, "btc_escrow",$amounts);
      }
      if (($updated == "true") && ($tila == "arvostelu") && ($current_user_ID == $vastaanottaja)) {
        $message = "\nTilaus merkitty maksetuksi: " . $post->guid . "\n";
        $otsikko = "Tilaus merkitty maksetuksi";
        $recipient = $kirjoittaja;
        $sender = $vastaanottaja;
        update_post_meta($post->ID, "maksuaika",$today);
      }
      if (($updated == "true") && ($tila == "valmis") && ($current_user_ID == $kirjoittaja)) {
        $message = "\nTulostustyö arvosteltu: " . $post->guid . "\n";
        $otsikko = "Uusi arvostelu";
        $recipient = $vastaanottaja;
        $sender = $kirjoittaja;
        update_post_meta($post->ID, "valmisaika",$today);
        require_once(__ROOT__.'/wp-content/plugins/tulostinkartta/block_io.php'); 
        $apiKey = get_option( 'blockio_api_key' );  
    	$version = 2; 
        $pin = get_option( 'blockio_pin' );    
        $block_io = new BlockIo($apiKey, $pin, $version);   
        $amounts = get_post_meta($post->ID, "bitcoin_hinta",true);
        $amounts = floatval($amounts);
        $amounts = round($amounts, 4);
        $amounts = ($amounts-0.0001);
        $tulos = $block_io->withdraw_from_labels(array('amounts' => $amounts, 'from_labels' => '1', 'to_labels' => $vastaanottaja));
        tt_blockio_balances();
      }
      if (($updated == "true") && ($tila == "peruutettu") && ($current_user_ID == $kirjoittaja)) {
        $message = "\nTulostustyö peruutettu: " . $post->guid . "\n";
        $otsikko = "Tulostustyö peruutettu";
        $recipient = $vastaanottaja;
        $sender = $kirjoittaja;
        update_post_meta($post->ID, "peruutusaika",$today);
      }
      if (($updated == "true") && ($tila == "peruutettu") && ($current_user_ID == $vastaanottaja)) {
        $message = "\nTulostustyö peruutettu: " . $post->guid . "\n";
        $otsikko = "Tulostustyö peruutettu";
        $recipient = $kirjoittaja;
        $sender = $vastaanottaja;
        update_post_meta($post->ID, "peruutusaika",$today);
      }          
        $args = array( 'recipients' => $recipient, 'sender_id' => $sender, 'subject' => $otsikko, 'content' => $message );
        messages_new_message( $args );    
        printjob_reload();    
    }

/* BuddyPress-privaattiviestin lähettäminen tulostuspyynnöstä */
                
function printjob_message($recipient,$sender,$otsikko,$message) {
    global $wpdb,$bp;
    global $post;
    global $user_ID;

    $args = array( 'recipients' => $recipient, 'sender_id' => $sender, 'subject' => $otsikko, 'content' => $message );      
    messages_new_message( $args );
    }

/* Ladataan tulostuspyyntösivu uudelleen päivityksen jälkeen */

function printjob_reload() {    
    $tulostuspyyntourl =  get_permalink($post->ID);
        echo "<script type='text/javascript'>                                                                                                   
	           window.location.assign('" . $tulostuspyyntourl  . "')                                                                             
	           </script>";
        }
    
/* Tulostuspyynnön tilaviestit ja funktiot */

function printjob_status() {
                global $post;
                global $wpdb;
                global $bp;

                $tila = get_post_meta( get_the_ID(), 'tila', true );				   

                if ($tila == "tarjous") { 
                    echo "<h1>Odottaa tulostajan tarjousta</h1>";
                    printjob_text();
		            printjob_tarjous();
                }
    
                if ($tila == "tilaus") { 
                    echo "<h1>Odottaa tilaajan tilausta</h1>";
		            printjob_text(); 
                    printjob_tilaus();
                }
    
                if ($tila == "maksu") { 
                    echo "<h1>Odottaa tulostajan kuittausta toimituksesta</h1>"; 
		            printjob_text();
                    printjob_maksu();
                }
    
                if ($tila == "arvostelu") { 
                    echo "<h1>Odottaa tilaajan arvostelua</h1>";
		            printjob_text(); 
                    printjob_arvostelu();
                }
    
                if ($tila == "valmis") { 
                    echo "<h1>Tulostustyö on valmis</h1>";
		            printjob_text(); 
                    printjob_valmis();
                }
    
                if ($tila == "peruutettu") { 
                    echo "<h1>Tulostustyö on peruutettu</h1>"; 
		            printjob_text();
                    printjob_peruutettu();                       
                }                
        }

/* Tulostuspyyntösivun tekstit */

function printjob_text() {
                global $post;
                global $wpdb;
                global $bp;

		        $tiedosto = get_post_meta($post->ID,"tiedosto",true);

		        echo do_shortcode( '[canvasio3D width="320" height="320" border="1" borderCol="#F6F6F6" dropShadow="0" backCol="#000000" backImg="..." Mouse="on" rollMode="off" rollSpeedH="10" rollSpeedV="10" objPath="' . $tiedosto . '" objScale="1" objColor="#808080" lightSet="7" reflection="off" refVal="5" objShadow="off" floor="off" floorHeight="42" lightRotate="off" Help="off"] [/canvasio3D]' );
	                
		        echo "<h3>Tulostuspyynnön tiedot</h3>";
		        echo "<p>Lähetetty ";
		        the_field("aika");
		        echo "</p>";
		        echo "<ul>";
                echo "<li>Tilaaja: ";
                the_author_link();
                echo "</li><li>Tulostin: ";
		        echo '<a href="/tulostin/';
                the_field("tulostin");
                echo '/">';
                the_field("tulostin");
                echo '</a>';
                echo "</li><li>Tiedosto: ";
		        echo '<a href="';
                the_field("tiedosto");
                echo '">';
                the_field("tiedosto");
                echo '</a>';
                echo "</li><li>Materiaali: ";
                the_field("materiaali");
                echo "</li><li>Määrä: ";    
                the_field("maara");
                echo "</li><li>Kappalehinta: ";    
                the_field("hinta");
                echo "</li><li>Kokonaishinta: ";    
                the_field("tarjous");
                echo "</li>";
                echo "</li><li>Kuvaus: ";
                the_field("kuvaus");
                echo "</li>";
                echo "</ul>";

if( get_field( "tarjousaika" ) ): ?>
    <h3>Tarjouksen tiedot</h3>
    <p>Tarjous lähetetty: <?php the_field( "tarjousaika" ); ?></p>
    <ul>
        <li>Tarjouksen kommentti: <?php the_field( "tarjous_kommentti" ); ?></li>
        <li>Hinta bitcoineissa: <?php the_field( "bitcoin_hinta" ); ?></li>
    </ul>
<?php endif;

if( get_field( "tilausaika" ) ): ?>
    <h3>Tilauksen tiedot</h3>
    <p>Tilattu: <?php the_field( "tilausaika" ); ?></p>
    <ul>
        <li>Tilaajan osoite: <?php the_field( "toimitusosoite" ); ?></li>
        <li>Tilauksen kommentti: <?php the_field( "tilauksen_kommentti" ); ?></li>
    </ul>
<?php endif;

if( get_field( "maksuaika" ) ): ?>
    <h3>Tilauksen toimitus</h3>
    <p>Merkitty toimitetuksi: <?php the_field( "maksuaika" ); ?></p>
    <ul>
        <li>Tulostajan kommentti toimituksesta: <?php the_field( "maksu_kommentti" ); ?></li>
    </ul>
<?php endif;

if( get_field( "valmisaika" ) ): ?>
    <h3>Tulostustyön arvostelu</h3>
    <p>Merkitty valmiiksi: <?php the_field( "valmisaika" ); ?></p>
    <ul>
        <li>Arvosana: <?php the_field( "arvostelunumero" ); ?></li>
        <li>Arvio: <?php the_field( "tekstiarvostelu" ); ?></li>
    </ul>
<?php endif;
}

/* Tarjouksen Advanced Custom Forms -kentät */

function printjob_tarjous() {
                global $post;
                global $wpdb;
                global $bp;
                $postid = get_the_ID();

                $current_user_ID = get_current_user_id();

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

  if ($current_user_ID == $vastaanottaja){
    acf_form(array(
                   'post_id'=> $postid,
                   'field_groups' => array('4321'),
                   'return' => '?updated=true',
                   'submit_value'=> 'Päivitä',
                   ));
}
}

/* Tilauksen Advanced Custom Forms -kentät */

function printjob_tilaus() {
                global $post;
                global $wpdb;
                global $bp;
				global $user_ID;
                $postid = get_the_ID();

                $current_user_ID = get_current_user_id();

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

                if ($current_user_ID == $kirjoittaja){
                    $hinnoittelu = get_post_meta($postid,"bitcoin_hinta",true);
                    $lompakko = get_user_meta($current_user_ID,"btc_available",true);
                    $hinnoittelu = floatval($hinnoittelu);
                    $lompakko = floatval($lompakko);

                    /* Näitä bitcoin-laskelmia pitää katsoa vielä "hieman" tarkemmin */    
    
                    if($lompakko>$hinnoittelu) {
                        acf_form(array(
                            'post_id'=> $postid,
                            'field_groups' => array('4322'),
                            'return' => '?updated=true',
                            'submit_value'=> 'Päivitä',
                            ));
                    }

                    if($lompakko<$hinnoittelu) {
			             echo "<h1>Sinulla ei ole tarpeeksi varaa!</h1>";
                         echo "<p>Siirrä ensin bittirahaa lompakkoosi...</p>";
                    }
                }
}

/* Maksun Advanced Custom Forms -kentät */

function printjob_maksu() {
                global $post;
                global $wpdb;
                global $bp;
                global $user_ID;

                $postid = get_the_ID();

                $current_user_ID = get_current_user_id();

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

                if ($current_user_ID == $vastaanottaja){
                    acf_form(array(
                        'post_id'=> $postid,
                        'field_groups' => array('4323'),
                        'return' => '?updated=true',
                        'submit_value'=> 'Päivitä',
                        ));
                }
}

/* Arvostelun Advanced Custom Forms -kentät */

function printjob_arvostelu() {
                global $post;
                global $wpdb;
                global $bp;
                global $user_ID;

                $postid = get_the_ID();

                $current_user_ID = get_current_user_id();

                $postid = get_the_ID();
                $kirjoittaja = get_post();
                $kirjoittaja = $kirjoittaja->post_author;
                $vastaanottaja = get_post_meta($postid,"tulostin",true);
                $vastaanottaja = get_post($vastaanottaja);
                $vastaanottaja = $vastaanottaja->post_author;

                if ($current_user_ID == $kirjoittaja){
                    acf_form(array(
                        'post_id'=> $postid,
                        'field_groups' => array('4324'),
                        'return' => '?updated=true',
                        'submit_value'=> 'Päivitä',
                        ));
                }
}

/* Valmiiseen ja peruutettuun tulostuspyyntöön ei tarvita mitään? */

function printjob_valmis() {
}

function printjob_peruutettu() {
}

/* Turha funktio Block_IO:n testaamiseen */

function tt_coinbase_test() {
    global $wpdb;
    require_once(__ROOT__.'/wp-content/plugins/tulostinkartta/block_io.php'); 
    $apiKey = get_option( 'blockio_api_key' );  
    $version = 2; 
    $pin = get_option( 'blockio_pin' );    
    $block_io = new BlockIo($apiKey, $pin, $version);
    $accountbalance = $block_io->get_current_price(array('price_base' => 'EUR'));
    $accountbalance = $accountbalance->data->prices;
    $accountbalance = $accountbalance[0]; 
    $accountbalance = $accountbalance->price;
    echo var_dump($accountbalance);
    update_option("btc_kurssi", $accountbalance); 
}

/* Haetaan Bitcoinin kurssi Block.io:sta */

function tt_update_kurssi() {
    global $wpdb;
    require_once(__ROOT__.'/wp-content/plugins/tulostinkartta/block_io.php'); 
    $apiKey = get_option( 'blockio_api_key' );  
    $version = 2; 
    $pin = get_option( 'blockio_pin' );    
    $block_io = new BlockIo($apiKey, $pin, $version);
    $accountbalance = $block_io->get_current_price(array('price_base' => 'EUR'));
    $accountbalance = $accountbalance->data->prices;
    $accountbalance = $accountbalance[0]; 
    $accountbalance = $accountbalance->price;
    update_option("btc_kurssi", $accountbalance);
}

/* Jokaiselle käyttäjälle oma henkilökohtainen Bitcoin-osoite Block.io:sta */

function tt_blockio_accounts() {
    global $wpdb;
    require_once(__ROOT__.'/wp-content/plugins/tulostinkartta/block_io.php'); 
    $apiKey = get_option( 'blockio_api_key' );  
    $version = 2; 
    $pin = get_option( 'blockio_pin' );    
    $block_io = new BlockIo($apiKey, $pin, $version);
    $table_name = $wpdb->prefix . "users";
    $users = $wpdb->get_results( "SELECT * FROM " . $table_name);
    foreach ($users as $user) {
              $user_id = $user->ID;
              $key = 'btc_address';
              $single = true;
              $btc_address = get_user_meta( $user_id, $key, $single );
              if (empty($btc_address)) { 
              	 $newAddressInfo = $block_io->get_new_address(array('label' => $user_id));
                 update_user_meta($user_id,"btc_address",$newAddressInfo->data->address);                   
    		  } else { 
		         $newAddressInfo = $block_io->get_address_by_label(array('label' => $user_id));
		         update_user_meta($user_id,"btc_address",$newAddressInfo->data->address);                   
    		  } 
	   }
}

/* Tehdään WordPress-vimpain, jossa on käyttäjän Bitcoin-tilin saldo ja osoite */

add_action( 'widgets_init', function(){
	    register_widget( 'BTC_Vimpain' );
});

class BTC_Vimpain extends WP_Widget {
    public function __construct() {
        $widget_ops = array( 
            'classname' => 'btc_vimpain',
            'description' => 'BTC Vimpain',
        );
        parent::__construct( 'btc_vimpain', 'BTC Vimpain', $widget_ops );
    }
    
    public function widget( $args, $instance ) {
        global $user_ID;
        if(is_user_logged_in()) {
            $current_user_ID = get_current_user_id();
            echo "<b>Bitcoin-tilisi</b>";								    
            echo "<p>BTC balance: " . get_user_meta($current_user_ID,"btc_available",true) . "</p>";
            echo "<p>BTC pending: " . get_user_meta($current_user_ID,"btc_pending",true) . "</p>";
            echo "<p>BTC address: " . get_user_meta($current_user_ID,"btc_address",true) . "</p>";
            echo "<p>BTC/EUR: " . get_option("btc_kurssi") . "</p>";
        }   
    }
    
    public function form( $instance ) {

    }
    
    public function update( $new_instance, $old_instance ) {

    }
}   
    
/* Päivitetään käyttäjien Block.io Bitcoin-tilien tiedot */
    
function tt_blockio_balances() {
    global $wpdb;
    require_once(__ROOT__.'/wp-content/plugins/tulostinkartta/block_io.php'); 
    $apiKey = get_option( 'blockio_api_key' );  
    $version = 2; 
    $pin = get_option( 'blockio_pin' );    
    $block_io = new BlockIo($apiKey, $pin, $version);
    $table_name = $wpdb->prefix . "users";
    $users = $wpdb->get_results( "SELECT * FROM " . $table_name);
    foreach ($users as $user) {
              $user_id = $user->ID;
              $key = 'btc_address';
              $single = true;
              $accountbalance = $block_io->get_address_balance(array('labels' => $user_id));            
              $accountbalance = $accountbalance->data->balances;
              $accountbalance = $accountbalance[0];
			  $bitcoin_balance = round($accountbalance->available_balance, 4);
			  $bitcoin_pending = round($accountbalance->pending_received_balance, 4);
              update_user_meta($user_id,"btc_available",$bitcoin_balance);
              update_user_meta($user_id,"btc_pending",$bitcoin_pending);
        }
}

/* Uuden käyttäjän rekisteröityessä tehdään käyttäjälle Bitcoin-tili */

add_action( 'user_register', 'tt_register_btc', 10, 1 );

function tt_register_btc($user_id) {
  tt_blockio_accounts();
  tt_blockio_balances();
  tt_update_kurssi();
}

function tt_login_btc($user_login, $user) {
  tt_blockio_accounts();
  tt_blockio_balances();
  tt_update_kurssi();
}
add_action('wp_login', 'tt_login_btc', 10, 2);

/* WordPress päivittämään tilejä kerran tunnissa */

add_action( 'wp', 'tulostinkartta_setup_schedule' );
function tulostinkartta_setup_schedule() {
  if ( !wp_next_scheduled( 'tulostinkartta_hourly_event' ) ) {
    wp_schedule_event( time(), 'hourly', 'tulostinkartta_hourly_event');
  }
}

add_action( 'tulostinkartta_hourly_event', 'tulostinkartta_do_this_hourly' );
function tulostinkartta_do_this_hourly() {
  tulostinkartta_notifikaatio_botti();
  tt_blockio_accounts();
  tt_blockio_balances();
  tt_update_kurssi();
}

/* Ilmeisesti täysin turha notifikaatiobotin funktio */

function tulostinkartta_notifikaatio_botti() {
  global $wpdb,$bp;
  global $post;
  global $user_ID;

  $args = array(
  	'post_type' => 'tulostuspyynto',
	'post_status' => 'publish',
        'posts_per_page' => -1
	);
}

/* Bittikukkaro bittirahojen nostamiseksi pois Tulostuskartan tililtä */

function tulostinkartta_bittikukkaro() {
        global $user_ID;
        if(is_user_logged_in()) {
            $current_user_ID = get_current_user_id();
		  if (!empty($_POST["to"])) {
            if (!empty($_POST["amount"])) {
		        $to = $_POST["to"];
                require_once(__ROOT__.'/wp-content/plugins/tulostinkartta/block_io.php'); 
                $apiKey = get_option( 'blockio_api_key' );  
    	        $version = 2; 
                $pin = get_option( 'blockio_pin' );    
			    $block_io = new BlockIo($apiKey, $pin, $version); 
			    $from_addresses = get_user_meta($current_user_ID,"btc_address",true);
			    $to_addresses = $_POST["to"];
			    $amounts = $_POST["amount"];
			    $amounts = floatval($amounts);
			    $amounts = ($amounts-0.0002);
			    $tulos = $block_io->withdraw_from_addresses(array('amounts' => $amounts, 'from_addresses' => $from_addresses, 'to_addresses' => $to_addresses));
				} 
          }
			        tt_blockio_balances();
            		echo "<h1>Bitcoin-tilisi</h1>";
            		echo "<p>BTC balance: " . get_user_meta($current_user_ID,"btc_available",true) . "</p>";
            		echo "<p>BTC pending: " . get_user_meta($current_user_ID,"btc_pending",true) . "</p>";
            		echo "<p>BTC address: " . get_user_meta($current_user_ID,"btc_address",true) . "</p>";
            		echo "<p>BTC/EUR: " . get_option("btc_kurssi") . "</p>";
			        echo '<p><img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' . get_user_meta($current_user_ID,"btc_address",true) . '&choe=UTF-8" title="' . get_user_meta($current_user_ID,"btc_address",true) . '" /></p>'; 
			        $maxval = get_user_meta($current_user_ID,"btc_available",true);
			        echo "<h1>Send BTC</h1>";
			        echo '<form name="bittikukkaro" method="post" action="">'; 
				    echo 'BTC Address: <input type="text" name="to" required/> <br />';
				    echo 'BTC Amount: <input type="number" name="amount" max="' . $maxval . '" min="0" step="0.0001" required/><br />';
				    echo '<input type="submit"  value="send"/>';
			        echo '</form>';
	}   
}