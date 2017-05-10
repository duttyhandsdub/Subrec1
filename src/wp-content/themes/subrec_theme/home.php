<?php
/**
 * The homepage template file
 *
 * Template Name: Homepage Template
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
							<div class="col-xs-15">
								<?php the_field( 'banner_text' ); ?>
							</div>
						</div>
					</div>
				</div>
				<?php include('banner-footer.php'); ?>
			</div>
		<?php endif; ?>

		<?php if( have_rows( 'features' ) ): ?>
			<div class="features">
				<div class="container">
					<div class="row">
						<?php $count = 1; while ( have_rows( 'features' ) ) : the_row(); ?>
							<div class="col-xs-15 col-sm-2">
								<div class="img-wrapper">
									<?php 
									$image = get_sub_field( 'feature_image' );
									if( !empty($image) ): ?>
										<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive" />
									<?php endif; ?>
								</div>
							</div>
							<div class="col-xs-15 col-sm-offset-0 col-sm-5 col-md-offset-1 col-md-4">
								<div class="content-wrapper<?php if( $count % 2 == 0 ): echo ' right-align-content'; endif; ?>">
									<h2><?php the_sub_field( 'feature_title' ); ?></h2>
									<?php the_sub_field( 'feature_text' ); ?>
								</div>
							</div>
							<?php if( !$count % 2 == 0 ): echo '<div class="col-sm-1"></div>'; endif; ?>
						<?php if( $count % 2 == 0 ): echo '</div><div class="row">'; endif; $count++; endwhile; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if( get_field( 'what_is_vpn_text' ) && get_field( 'what_is_vpn_image' ) ): ?>
			<div class="what-is-vpn blue-row">
				<div class="container">
					<div class="row">
						<div class="col-xs-15 col-sm-8">
							<h2>So, what is VPN?</h2>
							<?php the_field( 'what_is_vpn_text' ); ?>
						</div>
						<div class="col-xs-15 col-sm-offset-1 col-sm-6 col-md-offset-2 col-md-5">
							<?php 
							$image = get_field( 'what_is_vpn_image' );
							if( !empty($image) ): ?>
								<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive" />
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if( get_field( 'trusted_vpn_service_text' ) && get_field( 'trusted_vpn_service_image' ) ): ?>
			<div class="trusted-vpn">
				<div class="container">
					<div class="row">
						<div class="col-xs-15 col-sm-push-6 col-sm-9 col-md-push-5 col-md-5 col-lg-4">
							<h2>The trusted VPN service</h2>
							<?php the_field( 'trusted_vpn_service_text' ); ?>
						</div>
						<div class="col-xs-15 col-sm-pull-9 col-sm-6 col-md-pull-5 col-md-5 col-lg-pull-4">
							<?php 
							$image = get_field( 'trusted_vpn_service_image' );
							if( !empty($image) ): ?>
								<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive" />
							<?php endif; ?>
						</div>
						<div class="col-xs-15 col-md-5 col-lg-offset-1 col-lg-5">
							<?php
							$posts = get_field( 'testimonials' );

							if( $posts ): ?>
								<div class="testimonials">
									<?php foreach( $posts as $post): setup_postdata($post); ?>
										<div class="row">
											<div class="col-xs-3 col-sm-2">
												<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/quote-mark.png" class="img-responsive quote-mark" />
											</div>
											<div class="col-xs-12 col-sm-13">
												<?php the_content(); ?>
												<p class="quotee"><?php the_title(); ?></p>
											</div>
										</div>
									<?php endforeach; ?>
								</div>
							<?php wp_reset_postdata(); endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="footer-cta blue-row">
			<div class="container">
				<div class="row">
					<div class="col-xs-15 col-sm-10">
						<h2>It's easy to get started</h2>
						<p>Simply download the app to your computer or mobile device with one click and start using VPNView in just a few minutes.</p>
					</div>
					<div class="col-xs-15 col-sm-5">
						<a class="sf-button medium accent slightlyroundedarrow " href="<?php echo esc_url( get_permalink( 10 ) ); ?>">Get VPNView</a>
					</div>
				</div>
			</div>
		</div>

	<?php endwhile; endif; ?>

</div><!-- .main -->

<?php get_footer(); ?>
