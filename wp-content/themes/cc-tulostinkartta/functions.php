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

?>