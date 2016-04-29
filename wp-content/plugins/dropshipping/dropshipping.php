<?php
  /*
    Plugin Name: Dropshipping Checker
    Plugin URI: http://sange.fi/
    Description: Check availability of dropshipping products in WooCommerce
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
 
function dropshipping_get_meta( $value ) {
	  global $post;

	   $field = get_post_meta( $post->ID, $value, true );
	    if ( ! empty( $field ) ) {
	        return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
		    } else {
		          return false;
			        }
}

function dropshipping_add_meta_box() {
	  add_meta_box(
	  'dropshipping-dropshipping',
				__( 'Dropshipping', 'dropshipping' ),
				    		        'dropshipping_html',
										'product',
														'side',
															    'high'
															         );
}
add_action( 'add_meta_boxes', 'dropshipping_add_meta_box' );

function dropshipping_html( $post) {
	  wp_nonce_field( '_dropshipping_nonce', 'dropshipping_nonce' ); ?>

	   <p>Add the url and the availability text for dropshipping checker.</p>
         <p>Leave empty if not a dropshipping product.</p>

	  <p>
		<label for="dropshipping_dropshipping_url"><?php _e( 'Dropshipping URL', 'dropshipping' ); ?></label><br>
		              <input type="text" name="dropshipping_dropshipping_url" id="dropshipping_dropshipping_url" value="<?php echo dropshipping_get_meta( 'dropshipping_dropshipping_url' ); ?>">
			      	            </p>   <p>
								      <label for="dropshipping_out_of_stock_text"><?php _e( 'Out Of Stock Text', 'dropshipping' ); ?></label><br>
								      	     						      	      <input type="text" name="dropshipping_out_of_stock_text" id="dropshipping_out_of_stock_text" value="<?php echo dropshipping_get_meta( 'dropshipping_out_of_stock_text' ); ?>">
																      	     		 				            </p>   <p>
																								    	               <label for="dropshipping_in_stock_text"><?php _e( 'In Stock Text', 'dropshipping' ); ?></label><br>
																										       	      					     	              <input type="text" name="dropshipping_in_stock_text" id="dropshipping_in_stock_text" value="<?php echo dropshipping_get_meta( 'dropshipping_in_stock_text' ); ?>">
																																		      	     		 				   				   		   </p><?php
}

function dropshipping_save( $post_id ) {
	  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	   if ( ! isset( $_POST['dropshipping_nonce'] ) || ! wp_verify_nonce( $_POST['dropshipping_nonce'], '_dropshipping_nonce' ) ) return;
	    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	     if ( isset( $_POST['dropshipping_dropshipping_url'] ) )
	         update_post_meta( $post_id, 'dropshipping_dropshipping_url', esc_attr( $_POST['dropshipping_dropshipping_url'] ) );
		     if ( isset( $_POST['dropshipping_out_of_stock_text'] ) )
		            update_post_meta( $post_id, 'dropshipping_out_of_stock_text', esc_attr( $_POST['dropshipping_out_of_stock_text'] ) );
			           if ( isset( $_POST['dropshipping_in_stock_text'] ) )
				                 update_post_meta( $post_id, 'dropshipping_in_stock_text', esc_attr( $_POST['dropshipping_in_stock_text'] ) );
}
add_action( 'save_post', 'dropshipping_save' );

function dropshipping_check_stock() {


  global $post, $woocommerce, $product, $woocommerce_loop, $wpdb;
  
  $args = array(
  	'post_type' => 'product',
		    'post_status' => 'publish',
		    		  'posts_per_page' => -1
				  		   );

  $posts_array = get_posts( $args );
        
  foreach ($posts_array as $post) {        

    $dropshippingurl = get_post_meta($post->ID, 'dropshipping_dropshipping_url', true);		  
    $dropshippingout = "/" . get_post_meta($post->ID, 'dropshipping_out_of_stock_text', true) . "/";
    $dropshippingin = "/" . get_post_meta($post->ID, 'dropshipping_in_stock_text', true). "/";

    if (!empty($dropshippingurl)) {                                                                                    
      $drophtml = wp_remote_get($dropshippingurl); 

      if (preg_match($dropshippingout, $drophtml['body'])) {
      	 update_post_meta($post->ID, '_stock_status', 'outofstock' );
      } elseif (preg_match($dropshippingin, $drophtml['body'])) {
      	update_post_meta($post->ID, '_stock_status', 'instock' );
      } else {
              update_post_meta($post->ID, '_stock_status', 'outofstock' );
              }
	      }
	      }
}

register_activation_hook(__FILE__, 'dropshipping_activation');
add_action('dropshipping_hourly_event', 'dropshipping_do_this_hourly');
  
function dropshipping_activation() {
  wp_schedule_event( time(), 'hourly', 'dropshipping_hourly_event');
}

function dropshipping_do_this_hourly() {
  dropshipping_check_stock();
}

register_deactivation_hook(__FILE__, 'dropshipping_deactivation');

function dropshipping_deactivation() {
  wp_clear_scheduled_hook('dropshipping_hourly_event');
}

add_action( 'admin_menu', 'dropshipping_menu' );

function dropshipping_menu() {
	 add_options_page( 'Dropshipping Options', 'Dropshipping', 'manage_options', 'dropshipping', 'dropshipping_options' );
}

function dropshipping_options() {
	  
}