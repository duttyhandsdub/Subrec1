<?php
/**
 * The template for displaying the footer
 */
?>

<footer>
	<a id="scroll_top" class="button round-button" ><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/arrow-down.png" /></a>
	<div class="container">
		<div class="row">
			<div class="col-xs-15 col-md-10">
				<a href="/"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/vpn-view-logo-2.png" alt="VPN View" class="img-responsive logo" /></a>
			</div>
			<div class="col-xs-15 col-md-5">
				<div class="social">
					<a href="https://www.facebook.com/" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/fb-icon.png" alt="Find VPN View on Facebook" class="icon img-responsive" /></a>
					<a href="https://twitter.com/VPNView" target="_blank"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/twitter-icon.png" alt="Follo VPN View on Twitter" class="icon img-responsive" /></a>
				</div>
			</div>
			<div class="col-xs-15">
				<div class="menu-wrapper">
					<?php wp_nav_menu(array( 'menu' => 'Footer Navigation', 'container' => '', 'theme_location' => 'vpn-view' )); ?>
				</div>
			</div>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>

</body>
</html>
