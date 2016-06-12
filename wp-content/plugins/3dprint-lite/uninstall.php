<?php
//if uninstall not called from WordPress exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) 
    exit();

delete_option( 'p3dlite_settings' );
delete_option( 'p3dlite_printers' );
delete_option( 'p3dlite_materials' );
delete_option( 'p3dlite_coatings' );
delete_option( 'p3dlite_price_requests' );
delete_option( 'p3dlite_version' );


?>