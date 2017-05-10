<?php
/**
 * The template for displaying the header
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<title><?php bloginfo( 'name' ); ?><?php wp_title( '|' ); ?></title>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<link rel="shortcut icon" href="<?php echo get_stylesheet_directory_uri(); ?>/images/favicon.ico"/>

	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->
	<script src="https://use.fontawesome.com/8d51d62480.js"></script>

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<header class="site-header">
	<div class="container">
		<div class="row">
			<div class="col-xs-12 col-sm-5 col-lg-4">
				<a href="/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/logo.png" alt="Steppas SubRec" class="img-responsive" /></a>
			</div>
			<div class="mobile-menu-toggle">
				<div class="col-xs-3">
					<div class="mobile-menu-toggle">
						<i class="fa fa-bars" aria-hidden="true"></i>
					</div>
				</div>
			</div>
			<div class="col-xs-15 col-sm-10 col-lg-11">
				<p class="ip-block"><span></span><span></span></p>
				<div class="menu-wrapper">
					<?php wp_nav_menu(array( 'menu' => 'Main Navigation', 'container' => '', 'theme_location' => 'vpn-view' )); ?>
				</div>
			</div>
		</div>
	</div>
</header>
