<?php

/* Haetaan Bitcoinin kurssi Block.io:sta */

function tt_update_kurssi() {
    global $wpdb;
    require_once('/var/www/dev/wp-content/plugins/tulostinkartta/block_io.php'); 
 $tulostinkartta_options = get_option( 'tulostinkartta_option_name' );
 $apiKey = $tulostinkartta_options['blockio_api_key_0'];
 $pin = $tulostinkartta_options['blockio_pin_1'];
    $block_io = new BlockIo($apiKey, $pin, 2);
    $accountbalance = $block_io->get_current_price(array('price_base' => 'EUR'));
    $accountbalance = $accountbalance->data->prices;
    $accountbalance = $accountbalance[0]; 
    $accountbalance = $accountbalance->price;
    update_option("btc_kurssi", $accountbalance);
}

/* Jokaiselle käyttäjälle oma henkilökohtainen Bitcoin-osoite Block.io:sta */

function tt_blockio_accounts() {
    global $wpdb;
    require_once('/var/www/dev/wp-content/plugins/tulostinkartta/block_io.php'); 
    $table_name = $wpdb->prefix . "users";
    $users = $wpdb->get_results( "SELECT * FROM " . $table_name);
    foreach ($users as $user) {
              $user_id = $user->ID;
              $key = 'btc_address';
              $single = true;
              $btc_address = get_user_meta( $user_id, $key, $single );
              $tulostinkartta_options = get_option( 'tulostinkartta_option_name' );
              $apiKey = $tulostinkartta_options['blockio_api_key_0'];
              $pin = $tulostinkartta_options['blockio_pin_1'];
              $block_io = new BlockIo($apiKey, $pin, 2);

	      $newAddressInfo = $block_io->get_address_by_label(array('label' => $user_id));
              update_user_meta($user_id,"btc_address",$newAddressInfo->data->address);
              $btc_address = get_user_meta( $user_id, $key, $single );

              if (empty($btc_address)) { 
	          $tulostinkartta_options = get_option( 'tulostinkartta_option_name' );
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
    require_once('/var/www/dev/wp-content/plugins/tulostinkartta/block_io.php');
    $tulostinkartta_options = get_option( 'tulostinkartta_option_name' );
    $apiKey = $tulostinkartta_options['blockio_api_key_0'];
    $pin = $tulostinkartta_options['blockio_pin_1'];
    $block_io = new BlockIo($apiKey, $pin, 2);
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

/* WordPress päivittämään tilejä kerran tunnissa */

add_action( 'wp', 'tulostinkartta_setup_schedule' );
function tulostinkartta_setup_schedule() {
  if ( !wp_next_scheduled( 'tulostinkartta_hourly_event' ) ) {
    wp_schedule_event( time(), 'hourly', 'tulostinkartta_hourly_event');
  }
}

add_action( 'tulostinkartta_hourly_event', 'tulostinkartta_do_this_hourly' );
function tulostinkartta_do_this_hourly() {
  tt_blockio_accounts();
  tt_blockio_balances();
  tt_update_kurssi();
}

/* Bittikukkaro bittirahojen nostamiseksi pois Tulostuskartan tililtä */

function tulostinkartta_bittikukkaro() {
        global $user_ID;
        $current_user_ID = get_current_user_id();
        if(is_user_logged_in()) {
		  if (!empty($_POST["to"])) {
            	     if (!empty($_POST["amount"])) {
		        $to = $_POST["to"];
    			require_once('/var/www/dev/wp-content/plugins/tulostinkartta/block_io.php');
 			$tulostinkartta_options = get_option( 'tulostinkartta_option_name' );
 			$apiKey = $tulostinkartta_options['blockio_api_key_0'];
			$pin = $tulostinkartta_options['blockio_pin_1'];
			$block_io = new BlockIo($apiKey, $pin, 2); 
			$from_addresses = get_user_meta($current_user_ID,"btc_address",true);
			$to_addresses = $_POST["to"];
			$amounts = $_POST["amount"];
			$amounts = floatval($amounts);
			$amounts = ($amounts-0.0002);
			$tulos = $block_io->withdraw_from_addresses(array('amounts' => $amounts, 'from_addresses' => $from_addresses, 'to_addresses' => $to_addresses));
			echo var_dump($tulos);
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

