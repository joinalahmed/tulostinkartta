<?php

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

	include_once '/var/www/html/wp-content/plugins/geo-my-wp/plugins/posts/includes/gmw-pt-update-location.php';

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
    
      include_once('/var/www/html/wp-content/plugins/geo-my-wp/plugins/posts/includes/gmw-fl-update-location.php' );

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