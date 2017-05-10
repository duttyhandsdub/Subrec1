<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 */

get_header(); ?>

<div class="main">

	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

		<?php
		$bannerImage = get_field( 'banner_image' );
		if( !empty($bannerImage) ):
		?>
			<div class="banner">
				<img class="banner-image" src="<?php echo $bannerImage['url']; ?>" alt="<?php echo $bannerImage['alt']; ?>" class="img-responsive" />
				<div class="banner-title">
					<div class="container">
						<div class="row">
							<div class="col-lg-15">
								<?php the_field( 'banner_text' ); ?>
							</div>
						</div>
					</div>
				</div>
				<?php include('banner-footer.php'); ?>
			</div>
		<?php endif; ?>

		<div class="container">
			<div class="row">
				<div class="col-xs-15 col-md-offset-1 col-md-13 col-lg-offset-2 col-lg-11">
					<div class="content">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</div>

	<?php endwhile; endif; ?>
		
</div><!-- .main -->

<?php get_footer(); ?>
