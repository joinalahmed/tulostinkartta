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
function filamentti_slogan() {
$num = Rand (1,6);
switch ($num)
  {
  case 1:
    $slogan = "Filamentti on unelmiesi materiaali.";
    break;
  case 2:
    $slogan = "Tulosta ajatuksesi.";
    break;
  case 3:
    $slogan = "Tulevaisuus on nyt.";
    break;
  case 4:
    $slogan = "Kolmiulotteinen tulostaminen on uusi teollinen vallankumous.";
    break;
  case 5:
    $slogan = "Kaiken tiedon pitÃ¤Ã¤ olla avointa.";
    break;
  case 6:
    $slogan = "Tuotannon yhteisÃ¶llisyyttÃ¤.";
  }
echo $slogan;
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

/*
Export

Export Field Groups to PHP
Instructions
Copy the PHP code generated
Paste into your functions.php file
To activate any Add-ons, edit and use the code in the first few lines.

Notes
Registered field groups will not appear in the list of editable field groups. This is useful for including fields in themes.
Please note that if you export and register field groups within the same WP, you will see duplicate fields on your edit screens. To fix this, please move the original field group to the trash or remove the code from your functions.php file.

Include in theme
The Advanced Custom Fields plugin can be included within a theme. To do so, move the ACF plugin inside your theme and add the following code to your functions.php file:
include_once('advanced-custom-fields/acf.php');
To remove all visual interfaces from the ACF plugin, you can use a constant to enable lite mode. Add the following code to your functions.php file before the include_once code:
define( 'ACF_LITE', true );

« Back to export

if(function_exists("register_field_group"))
{
	register_field_group(array (
		'id' => 'acf_muokkaa-omaa-tulostintasi',
		'title' => 'Muokkaa omaa tulostintasi',
		'fields' => array (
			array (
				'key' => 'field_56880e1c57d0a',
				'label' => 'Tulostinsivu',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_5688095f351e5',
				'label' => 'Otsikko',
				'name' => 'name',
				'type' => 'text',
				'instructions' => 'Tulostinsivun otsikko?',
				'required' => 1,
				'default_value' => '3D-Tulostimeni',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_56be850eb2054',
				'label' => 'Sijainti',
				'name' => 'sijainti',
				'type' => 'google_map',
				'instructions' => 'Tulostimen sijainti tulostinkartalla.',
				'required' => 1,
				'center_lat' => '60.8501746',
				'center_lng' => '24.4181247',
				'zoom' => 7,
				'height' => '',
			),
			array (
				'key' => 'field_56beadc2fa596',
				'label' => 'Tulostimen kuva',
				'name' => 'tulostimen_kuva',
				'type' => 'file',
				'instructions' => 'Tulostinsivun otsikkokuva?',
				'save_format' => 'id',
				'library' => 'uploadedTo',
			),
			array (
				'key' => 'field_56be83a759832',
				'label' => 'Kuvaus',
				'name' => 'kuvaus',
				'type' => 'wysiwyg',
				'instructions' => 'Kirjoita tulostimesi kuvaus.',
				'required' => 1,
				'default_value' => '',
				'toolbar' => 'full',
				'media_upload' => 'yes',
			),
			array (
				'key' => 'field_56be8148b5535',
				'label' => 'Tulostin',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_56be8179b5536',
				'label' => 'Tulostimen tekniset tiedot',
				'name' => '',
				'type' => 'message',
				'message' => 'Tulostimen tekniset tiedot.',
			),
			array (
				'key' => 'field_56be81b2ca485',
				'label' => 'Tulostimen malli',
				'name' => 'tulostimen_malli',
				'type' => 'text',
				'instructions' => 'Kirjoita tulostimesi malli?',
				'required' => 1,
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'formatting' => 'html',
				'maxlength' => '',
			),
			array (
				'key' => 'field_56880979351e6',
				'label' => 'Leveys',
				'name' => 'width',
				'type' => 'number',
				'instructions' => 'Tulostimen tulostuslaatikon leveys millimetreinä.',
				'required' => 1,
				'default_value' => 200,
				'placeholder' => 200,
				'prepend' => 'Tulostimen tulostuslaatikon leveys',
				'append' => 'mm.',
				'min' => 1,
				'max' => 1000,
				'step' => 1,
			),
			array (
				'key' => 'field_56880996351e7',
				'label' => 'Pituus',
				'name' => 'length',
				'type' => 'number',
				'instructions' => 'Tulostimen tulostuslaatikon pituus millimetreinä.',
				'required' => 1,
				'default_value' => 200,
				'placeholder' => 200,
				'prepend' => 'Tulostimen tulostuslaatikon pituus',
				'append' => 'mm.',
				'min' => 1,
				'max' => 1000,
				'step' => 1,
			),
			array (
				'key' => 'field_568809a5351e8',
				'label' => 'Korkeus',
				'name' => 'height',
				'type' => 'number',
				'instructions' => 'Tulostimen tulostuslaatikon korkeus millimetreinä.',
				'required' => 1,
				'default_value' => 200,
				'placeholder' => 200,
				'prepend' => 'Tulostimen tulostuslaatikon korkeus',
				'append' => 'mm.',
				'min' => 1,
				'max' => 1000,
				'step' => 1,
			),
			array (
				'key' => 'field_568809de351e9',
				'label' => 'Hinta',
				'name' => 'price',
				'type' => 'number',
				'instructions' => 'Tulostushinta',
				'required' => 1,
				'default_value' => '0.05',
				'placeholder' => '0,05',
				'prepend' => 'Tulostustyön hinta',
				'append' => 'per cm3?',
				'min' => '0.01',
				'max' => 10,
				'step' => '0.01',
			),
			array (
				'key' => 'field_56880e4719c3a',
				'label' => 'Filamentit',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_56be820480b63',
				'label' => 'Filamenttien tiedot',
				'name' => '',
				'type' => 'message',
				'message' => 'Lisää käytettävissä olevat filamentit ja niiden hinnat!',
			),
			array (
				'key' => 'field_56be24afb568f',
				'label' => 'ABS',
				'name' => 'abs',
				'type' => 'select',
				'instructions' => 'Voitko tulostaa ABS-filamenttia?',
				'required' => 1,
				'choices' => array (
					'kyllä' => 'kyllä',
					'ei' => 'ei',
				),
				'default_value' => 'ei',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_56be2513b5691',
				'label' => 'ABS-filamentin hinta',
				'name' => 'abs-filamentin_hinta',
				'type' => 'number',
				'instructions' => 'ABS-filamentin hinta euroina per kuutiosentti.',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56be24afb568f',
							'operator' => '==',
							'value' => 'kyllä',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '0.03',
				'placeholder' => '0,03',
				'prepend' => 'PLA-hinta €',
				'append' => 'per cm3?',
				'min' => '0.01',
				'max' => 1,
				'step' => '0.01',
			),
			array (
				'key' => 'field_56be24f0b5690',
				'label' => 'PLA',
				'name' => 'pla',
				'type' => 'select',
				'instructions' => 'Voitko tulostaa PLA-filamenttia?',
				'required' => 1,
				'choices' => array (
					'kyllä' => 'kyllä',
					'ei' => 'ei',
				),
				'default_value' => 'ei',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_56be25a5b5692',
				'label' => 'PLA-filamentin hinta',
				'name' => 'pla-filamentin_hinta',
				'type' => 'number',
				'instructions' => 'PLA-filamentin hinta per kuutiosentti.',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56be24f0b5690',
							'operator' => '==',
							'value' => 'kyllä',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '0.03',
				'placeholder' => '0,03',
				'prepend' => 'PLA-filamentin hinta',
				'append' => 'per cm3.',
				'min' => '0.01',
				'max' => 1,
				'step' => '0.01',
			),
			array (
				'key' => 'field_56be90fce8a17',
				'label' => 'Bioflex',
				'name' => 'bioflex',
				'type' => 'select',
				'instructions' => 'Voitko tulostaa bioflex-filamenttia?',
				'required' => 1,
				'choices' => array (
					'kyllä' => 'kyllä',
					'ei' => 'ei',
				),
				'default_value' => 'ei',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_56be92a6d1fef',
				'label' => 'Bioflex-filamentin hinta',
				'name' => 'bioflex-filamentin_hinta',
				'type' => 'number',
				'instructions' => 'Bioflex-filamentin hinta per kuutiosentti.',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56be90fce8a17',
							'operator' => '==',
							'value' => 'kyllä',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '0.03',
				'placeholder' => '0,03',
				'prepend' => 'Bioflex-filamentin hinta',
				'append' => 'per kuutiosentti',
				'min' => '0.01',
				'max' => 1,
				'step' => '0.01',
			),
			array (
				'key' => 'field_56be9157e8a18',
				'label' => 'Nylon',
				'name' => 'nylon',
				'type' => 'select',
				'instructions' => 'Voitko tulostaa nylon-filamenttia?',
				'required' => 1,
				'choices' => array (
					'kyllä' => 'kyllä',
					'ei' => 'ei',
				),
				'default_value' => 'ei',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_56be9313d1ff0',
				'label' => 'Nylon-filamentin hinta',
				'name' => 'nylon-filamentin_hinta',
				'type' => 'number',
				'instructions' => 'Nylon-filamentin hinta per kuutiosentti.',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56be9157e8a18',
							'operator' => '==',
							'value' => 'kyllä',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '0.03',
				'placeholder' => '0,03',
				'prepend' => 'Nylon-filamentin hinta',
				'append' => 'per cm3.',
				'min' => '0.01',
				'max' => 1,
				'step' => '0.01',
			),
			array (
				'key' => 'field_56be9192e8a19',
				'label' => 'Pleksi',
				'name' => 'pleksi',
				'type' => 'select',
				'instructions' => 'Voitko tulostaa pleksi-filamenttia?',
				'required' => 1,
				'choices' => array (
					'kyllä' => 'kyllä',
					'ei' => 'ei',
				),
				'default_value' => 'ei',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_56be9360d1ff1',
				'label' => 'Pleksi-filamentin hinta',
				'name' => 'pleksi-filamentin_hinta',
				'type' => 'number',
				'instructions' => 'Pleksi-filamentin hinta per kuutiosentti.',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56be9192e8a19',
							'operator' => '==',
							'value' => 'kyllä',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '0.03',
				'placeholder' => '0,03',
				'prepend' => 'Pleksi-filamentin hinta',
				'append' => 'per cm3.',
				'min' => '0.01',
				'max' => 1,
				'step' => '0.01',
			),
			array (
				'key' => 'field_56be91b8e8a1a',
				'label' => 'Puu',
				'name' => 'puu',
				'type' => 'select',
				'instructions' => 'Voitko tulostaa puu-filamenttia?',
				'required' => 1,
				'choices' => array (
					'kyllä' => 'kyllä',
					'ei' => 'ei',
				),
				'default_value' => 'ei',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_56be939ad1ff2',
				'label' => 'Puu-filamentin hinta',
				'name' => 'puu-filamentin_hinta',
				'type' => 'number',
				'instructions' => 'Puu-filamentin hinta per kuutiosentti.',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56be91b8e8a1a',
							'operator' => '==',
							'value' => 'kyllä',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '0.03',
				'placeholder' => '0,03',
				'prepend' => 'Puu-filamentin hinta',
				'append' => 'per cm3.',
				'min' => '0.01',
				'max' => 1,
				'step' => '0.01',
			),
			array (
				'key' => 'field_56be91cfe8a1b',
				'label' => 'PVA',
				'name' => 'pva',
				'type' => 'select',
				'instructions' => 'Voitko tulostaa PVA-filamenttia?',
				'required' => 1,
				'choices' => array (
					'kyllä' => 'kyllä',
					'ei' => 'ei',
				),
				'default_value' => 'ei',
				'allow_null' => 0,
				'multiple' => 0,
			),
			array (
				'key' => 'field_56be93dbd1ff3',
				'label' => 'PVA-filamentin hinta',
				'name' => 'pva-filamentin_hinta',
				'type' => 'number',
				'instructions' => 'PVA-filamentin hinta per kuutiosentti?',
				'required' => 1,
				'conditional_logic' => array (
					'status' => 1,
					'rules' => array (
						array (
							'field' => 'field_56be91cfe8a1b',
							'operator' => '==',
							'value' => 'kyllä',
						),
					),
					'allorany' => 'all',
				),
				'default_value' => '0.03',
				'placeholder' => '0,03',
				'prepend' => 'PVA-filamentin hinta',
				'append' => 'per cm3.',
				'min' => '0.01',
				'max' => 1,
				'step' => '0.01',
			),
			array (
				'key' => 'field_56be832e98dc0',
				'label' => 'Julkaisu',
				'name' => '',
				'type' => 'tab',
			),
			array (
				'key' => 'field_56be834298dc1',
				'label' => 'Julkaise tulostin?',
				'name' => '',
				'type' => 'message',
				'message' => 'Täällä voit julkaista tulostimesi tulostinkartalla tai piilottaa sen.',
			),
			array (
				'key' => 'field_56be82c520768',
				'label' => 'Julkaise',
				'name' => 'julkaise',
				'type' => 'select',
				'instructions' => 'Onko tulostimesi julkinen vai piilossa?',
				'required' => 1,
				'choices' => array (
					'publish' => 'julkaise',
					'draft' => 'piilota',
				),
				'default_value' => 'piilota',
				'allow_null' => 0,
				'multiple' => 0,
			),
		),
		'location' => array (
			array (
				array (
					'param' => 'post_type',
					'operator' => '==',
					'value' => 'tulostin',
					'order_no' => 0,
					'group_no' => 0,
				),
			),
		),
		'options' => array (
			'position' => 'normal',
			'layout' => 'default',
			'hide_on_screen' => array (
			),
		),
		'menu_order' => 0,
	));
}
*/
?>