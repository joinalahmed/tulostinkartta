<?php
add_action( 'wp_enqueue_scripts', 'tulostinkartta_enqueue_styles' );
function tulostinkartta_enqueue_styles() {
wp_enqueue_style( 'custom-community', get_template_directory_uri() . '/style.css' );
}

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

/*
add_filter ( 'pdf_content_additional_content' , 'pdf_additional_content_terms' );
function pdf_additional_content_terms( $content ) {
global $woocommerce;
$tilausnumero = $order_id;
$tilausnumero = substr($tilausnumero, 4);
$viitenumero = get_post_meta($tilausnumero, "viitenumero", true);
$content = str_replace( '[[PDFVIITENUMERO]]', $viitenumero, $content );
return $content;
}
*/

/* Ohjataan rekisterÃ¶inti uudelleen */
/*
add_action('init','spammeri_redirect');
function spammeri_redirect(){
  global $pagenow;
  if( 'wp-login.php' == $pagenow ) {
    if ( isset( $_POST['wp-submit'] ) ||   // in case of LOGIN
	 ( isset($_GET['action']) && $_GET['action']=='logout') ||   // in case of LOGOUT
	 ( isset($_GET['checkemail']) && $_GET['checkemail']=='confirm') ||   // in case of LOST PASSWORD
	 ( isset($_GET['checkemail']) && $_GET['checkemail']=='registered') ) return;    // in case of REGISTER
    else wp_redirect(home_url('/register/')); // or wp_redirect(home_url('/login'));
    exit();
  }
}
*/

/* Admin bar jemmaan, hÃ¤iritsee web designia */
/*
add_filter('show_admin_bar', '__return_false');
*/


/*
function filamentti_admin_bar_render() {
  global $wp_admin_bar;
  global $current_user;
  $user_roles = $current_user->roles;
  $user_role = array_shift($user_roles);
  if ($user_role == "subscriber") {
    $wp_admin_bar->remove_menu('site-name');
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-content');
    $wp_admin_bar->remove_menu('new-media');
    $wp_admin_bar->remove_menu('new-post');
    $wp_admin_bar->remove_menu('new-tulostuspyynto');
    $wp_admin_bar->remove_menu('new-tulostin');
    $wp_admin_bar->remove_menu('search');
  }
  if ($user_role == "contributor") {
    $wp_admin_bar->remove_menu('site-name');
    $wp_admin_bar->remove_menu('wp-logo');
    $wp_admin_bar->remove_menu('comments');
    $wp_admin_bar->remove_menu('new-media');
    $wp_admin_bar->remove_menu('new-post');
    $wp_admin_bar->remove_menu('new-tulostuspyynto');
    $wp_admin_bar->remove_menu('new-tulostin');
    $wp_admin_bar->remove_menu('search');
    $wp_admin_bar->add_menu( array(
				   'parent' => 'new-content',
				   'id' => 'tulostin',
				   'title' => __('Tulostin'),
				   'href' => 'http://www.3d-tarvike.fi/omat-tulostimet/'
				   ) );
    $wp_admin_bar->add_menu( array(
				   'parent' => 'new-content',
				   'id' => 'tulostuspyyntÃ¶',
				   'title' => __('TulostuspyyntÃ¶'),
				   'href' => 'http://www.3d-tarvike.fi/tulostuspyynto/'
				   ) );
    $wp_admin_bar->add_menu( array(
                                   'parent' => 'new-content',
                                   'id' => 'artikkeli',
                                   'title' => __('Artikkeli'),
                                   'href' => 'http://www.3d-tarvike.fi/omat-artikkelini/'
                                   ) );
  }
}
add_action( 'wp_before_admin_bar_render', 'filamentti_admin_bar_render' );
*/

/* Viitenumero  */
/*
add_action('woocommerce_checkout_order_processed', 'filamentti_viitenumero_laskuri');

function filamentti_viitenumero_laskuri($order_id) {
  global $woocommerce;
  $order = new WC_Order( $order_id );
  $maksutapa = get_post_meta( $order->id, '_payment_method', true);
  if ($maksutapa = "bacs") {
  $lasku = $order->id;
  $laskunro = $lasku+100;
  $kertoimet = array('7','3','1','7','3','1','7','3','1','7','3','1','7','3','1','7','3','1','7');
  $tarkiste = 0;
  $j = 0;
  $tmp = $laskunro;
  settype($tmp, "string");
  for($i=strlen($tmp)-1; $i>-1; $i--){
    $tarkiste = $tarkiste + $kertoimet[$j++] * intval(substr($tmp, $i, 1));
  }
  $tarkiste = ceil($tarkiste / 10) * 10 - $tarkiste;
  $viite = "$laskunro$tarkiste";
  update_post_meta($lasku, "viitenumero", $viite);
  $mailer = $woocommerce->mailer();
  $mailer->customer_invoice( $order );
  }
}
*/

/*
add_action( 'init', 'filamentti_blockusers_init' );
function filamentti_blockusers_init() {
  if ( is_admin() && ! current_user_can( 'administrator' ) &&
       ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
    wp_redirect( home_url() );
    exit;
  }
}
*/
/*
function filamentti_user_capability() {
  $role = get_role( 'contributor' );
  $role->add_cap( 'edit_posts' );
  $role->add_cap( 'edit_published_posts' );
  $role->add_cap( 'unfiltered_upload' );
  $role->add_cap( 'upload_files' );
  $role->add_cap( 'manage_categories' );
}
add_action( 'admin_init', 'filamentti_user_capability');
*/
/*
if ( current_user_can('contributor') && !current_user_can('upload_files') )
  add_action('admin_init', 'allow_contributor_uploads');
function allow_contributor_uploads() {
  $contributor = get_role('contributor');
  $contributor->add_cap('upload_files');
}
*/
/*
function block_wp_admin_init() {

  if (strpos(strtolower($_SERVER['REQUEST_URI']),'/wp-admin/') !== false) {

    if ( !is_site_admin() ) {

      wp_redirect( get_option('siteurl'), 302 );

    }

  }

}

add_action('init','block_wp_admin_init',0);
*/
/*
add_action('admin_menu', 'filamentti_restrict_admin');

function filamentti_restrict_admin() {
    if (current_user_can('administrator')) {
      return;
    }
    global $menu;
    $hMenu = $menu;
    foreach ($hMenu as $nMenuIndex => $hMenuItem) {
      if (in_array($hMenuItem[2], array(
					'edit.php?post_type=tulostin',
                                        'edit.php?post_type=printjob',
					'post-new.php?post_type=tulostin',
					'post-new.php?post_type=printjob',
					'users.php?page=bp-profile-edit',
					'profile.php'
					))
	  ) {
	continue;
      }
      unset($menu[$nMenuIndex]);
    }
}
*/
/* Vaihdetaan tilausten default status */
/*
add_action( 'woocommerce_thankyou', 'filamentti_woocommerce_tilaus_status' );
function filamentti_woocommerce_tilaus_status( $order_id ) {
  global $woocommerce;
  if ( !$order_id )
    return;
  $order = new WC_Order( $order_id );
  $order->update_status( 'on-hold' );
}
*/
/*
add_filter( 'user_has_cap', 'filamentti_unfiltered_upload' );

function filamentti_unfiltered_upload( $caps )
{
  $caps['unfiltered_upload'] = 1;
  return $caps;
}

function tulostin_upload_mimes($mimes=array()) {
  $mimes['stl']='application/octet-stream.stl';
  return $mimes;
}
add_filter("upload_mimes","tulostin_upload_mimes");

function tulostin_map_unrestricted_upload_filter($caps, $cap) {
  if ($cap == 'unfiltered_upload') {
    $caps = array();
    $caps[] = $cap;
  }

  return $caps;
}

add_filter('map_meta_cap', 'tulostin_map_unrestricted_upload_filter', 0, 2);

function filamentti_login_logo() { ?>
    <style type="text/css">
    .login h1 a {
    background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/images/site-login-logo.png);
    padding-bottom: 30px;
  }
    </style>
	<?php }
add_action( 'login_enqueue_scripts', 'filamentti_login_logo' );
*/
function remove_now_gmw_homostelu() {    

    $post = get_post();
    $postiljooni = $post->ID;
    
/*    if($_SERVER['REQUEST_URI'] === '/omatulostin/' or '/omatulostin/?updated=true') { */
    /*    if($_SERVER['REQUEST_URI'] === '/omatulostin/' or '/omatulostin/?updated=true') { */


if($postiljooni === 4199) { 
     wp_dequeue_script('gmw-google-autocomplete');
     wp_dequeue_script('gmw-js');
     wp_dequeue_script('gmw-marker-clusterer');
     wp_dequeue_script('gmw-marker-spiderfier');
     wp_dequeue_script('gmw-infobox');
     wp_dequeue_script('gmw-get-directions');
     wp_dequeue_script('jquery-ui-draggable');
     wp_dequeue_script('google-maps');
    } 
if($postiljooni === 4201) {
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
add_action('wp_head', 'remove_now_gmw_homostelu', 1);

?>