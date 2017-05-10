<?php

	/* ========================================================================================================================
	
	Theme specific settings

	Uncomment register_nav_menus to enable a single menu with the title of "Primary Navigation" in your theme
	
	======================================================================================================================== */

	add_theme_support( 'post-thumbnails' );


	function register_my_menus() {
		register_nav_menus( array(
			'main-nav' => 'Main Navigation',
			'footer-nav' => 'Footer Navigation'	
		));
	}
	add_action( 'init', 'register_my_menus' );

	
	/* ========================================================================================================================
	
	Custom Image Size
	
	======================================================================================================================== */

	/*add_image_size( 'homepage-tile', 250, 150, array( 'center', 'center' ));  // Used on homepage

	add_filter( 'image_size_names_choose', 'custom_image_sizes');

	function custom_image_sizes( $sizes ) {
		return array_merge( $sizes, array(
			'homepage-tile' => __( 'Homepage Tile 250x150' ),
		));
	}*/


	/* ========================================================================================================================
	
	Enqueue scripts and styles

	======================================================================================================================== */

	function subrec_scripts() {
		//force jquery update if not admin
		if( !is_admin() ) {
			wp_deregister_script( 'jquery' );
			wp_register_script( 'jquery', ("https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"), false, '1.11.3' );
			wp_enqueue_script( 'jquery' );
		}
		// Load main stylesheet
		wp_register_style( 'subrec-styles', get_template_directory_uri() . '/style.css', '', '', 'screen' );
		wp_enqueue_style( 'subrec-styles' );

		// Load main script
		wp_register_script( 'subrec-script', get_template_directory_uri() . '/js/main.js', array( 'jquery' ) );
		wp_enqueue_script( 'subrec-script' );
	}
	
	add_action( 'wp_enqueue_scripts', 'subrec_scripts' );


	/* ========================================================================================================================
	
	Add options page - requires ACF Pro plugin
	
	======================================================================================================================== */
	
	if( function_exists('acf_add_options_page') ) {
		acf_add_options_page();
	}


	/* ========================================================================================================================
    
    Custom Login Logo
    
    ======================================================================================================================== */
    
    function my_login_head() {
    ?>
        <style>
            body.login #login h1 a {
                background:url('<?= get_stylesheet_directory_uri(); ?>/images/logo.png') no-repeat;
                background-size:cover;
                width:320px;
                height:72px;
                display:block;
                margin:0 auto;
                margin-bottom:30px;
            }
        </style>
    <?php
    }
    add_action('login_head', 'my_login_head');
?>