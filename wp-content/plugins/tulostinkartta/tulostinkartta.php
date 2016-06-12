<?php
/*
Plugin Name: 3D-Tarvike Tulostinkartta
Plugin URI: http://tulostimet.3d-tarvike.fi/
Description: 3D-Tarvike Tulostinkartta
Version: 1.0
Author: Tomi Toivio
Author URI: http://ukulilandia.lol/
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

include 'tulostinkartta-cpt.php';   
include 'tulostinkartta-printer.php';
include 'tulostinkartta-printjob.php';
include 'tulostinkartta-bitcoin.php';


add_action( 'wp_head', 'bittikukkaro_javascript' );

function bittikukkaro_javascript() {
        global $user_ID;
        $current_user_ID = get_current_user_id();
        if(is_user_logged_in()) {
?>
        <script type="text/javascript" >
        jQuery(document).ready(function($) {

                var data = {
                        'action': 'bittikukkaro',
                        'userid': <?php echo $current_user_ID; ?>
                };

                var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';

                jQuery.post(ajaxurl, data, function(response) {
                        alert('Got this from the server: ' + response);
                });
        });
        </script> <?php
}

add_action( 'wp_ajax_bittikukkaro', 'bittikukkaro_callback' );

function bittikukkaro_callback() {
        global $wpdb;
        $userid = $_POST['userid'];
        echo "dumbass";
        $btc_address = get_user_meta($userid,"btc_address",true);
        echo $btc_address;
        wp_die();
    }
}


/* Asetukset: 
    Block.io API key
    Block.io pin
    */

class Tulostinkartta {
	private $tulostinkartta_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'tulostinkartta_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'tulostinkartta_page_init' ) );
	}

	public function tulostinkartta_add_plugin_page() {
		add_plugins_page(
			'Tulostinkartta', // page_title
			'Tulostinkartta', // menu_title
			'manage_options', // capability
			'tulostinkartta', // menu_slug
			array( $this, 'tulostinkartta_create_admin_page' ) // function
		);
	}

	public function tulostinkartta_create_admin_page() {
		$this->tulostinkartta_options = get_option( 'tulostinkartta_option_name' ); ?>

		<div class="wrap">
			<h2>Tulostinkartta</h2>
			<p>Tulostinkartan asetukset.</p>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'tulostinkartta_option_group' );
					do_settings_sections( 'tulostinkartta-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }

	public function tulostinkartta_page_init() {
		register_setting(
			'tulostinkartta_option_group', // option_group
			'tulostinkartta_option_name', // option_name
			array( $this, 'tulostinkartta_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'tulostinkartta_setting_section', // id
			'Settings', // title
			array( $this, 'tulostinkartta_section_info' ), // callback
			'tulostinkartta-admin' // page
		);

		add_settings_field(
			'blockio_api_key_0', // id
			'blockio_api_key', // title
			array( $this, 'blockio_api_key_0_callback' ), // callback
			'tulostinkartta-admin', // page
			'tulostinkartta_setting_section' // section
		);

		add_settings_field(
			'blockio_pin_1', // id
			'blockio_pin', // title
			array( $this, 'blockio_pin_1_callback' ), // callback
			'tulostinkartta-admin', // page
			'tulostinkartta_setting_section' // section
		);
	}

	public function tulostinkartta_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['blockio_api_key_0'] ) ) {
			$sanitary_values['blockio_api_key_0'] = sanitize_text_field( $input['blockio_api_key_0'] );
		}

		if ( isset( $input['blockio_pin_1'] ) ) {
			$sanitary_values['blockio_pin_1'] = sanitize_text_field( $input['blockio_pin_1'] );
		}

		return $sanitary_values;
	}

	public function tulostinkartta_section_info() {
		
	}

	public function blockio_api_key_0_callback() {
		printf(
			'<input class="regular-text" type="text" name="tulostinkartta_option_name[blockio_api_key_0]" id="blockio_api_key_0" value="%s">',
			isset( $this->tulostinkartta_options['blockio_api_key_0'] ) ? esc_attr( $this->tulostinkartta_options['blockio_api_key_0']) : ''
		);
	}

	public function blockio_pin_1_callback() {
		printf(
			'<input class="regular-text" type="text" name="tulostinkartta_option_name[blockio_pin_1]" id="blockio_pin_1" value="%s">',
			isset( $this->tulostinkartta_options['blockio_pin_1'] ) ? esc_attr( $this->tulostinkartta_options['blockio_pin_1']) : ''
		);
	}

}
if ( is_admin() )
	$tulostinkartta = new Tulostinkartta();

