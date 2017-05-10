<?php
/**
 * The homepage template file
 *
 * Template Name: Homepage Template
 */

get_header(); ?>

<div class="inner-page-wrap clearfix">
	<div class="page-content clearfix">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div class="what-is-vpn blue-row">
					<div class="container">
						<div class="col-xs-15 col-sm-8">
							<?php the_content(); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="footer-cta blue-row">
				<div class="container">
					<div class="col-xs-15 col-sm-10">
						<h2>It's easy to get started</h2>
						<p>Simply click the button and fill in the form.</p>
					</div>
					<div class="col-xs-15 col-sm-5">
						<a class="sf-button medium accent slightlyroundedarrow " href="<?php echo esc_url( get_permalink( 10 ) ); ?>">Get SubRec<span class="arrow"></span></a>
					</div>
				</div>
			</div>
		<?php endwhile; endif; ?>
	</div>
</div><!-- .main -->

<?php get_footer(); ?>
