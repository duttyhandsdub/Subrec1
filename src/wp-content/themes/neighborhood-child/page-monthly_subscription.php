<?php
/**
 * The Monthly Subscription page template file
 *
 * Template Name: Monthly Subscription page template
 */
add_filter( 'gform_field_value_currency', 'populate_date' );
function populate_date( $value ) {
	return 'GBP';
}
add_filter( 'gform_field_value_price', 'populate_price' );
function populate_price( $value ) {
   	return '5.99';
}
get_header(); ?>
<div class="inner-page-wrap clearfix">
	<div class="page-content clearfix">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<section class="container">
				<?php the_content(); ?>
			</section>
		<?php endwhile; endif; ?>
	</div>
</div><!-- .main -->

<?php get_footer(); ?>
