<?php

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
