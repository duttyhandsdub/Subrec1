<?php
/**
 * The Features page template file
 *
 * Template Name: Features page template
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

		<?php if( get_field( 'without_vpnview_image' ) && get_field( 'with_vpnview_image' ) ): ?>
			<div class="how-does-vpn-work blue-row">
				<div class="container">
					<div class="row">
						<div class="col-xs-15">
							<h2>How does VPN work?</h2>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-15 col-md-7">
							<h3>Without <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/vpn-view-small.png" alt="VPNView" /></h3>
							<?php
							$withoutImageLarge = get_field( 'without_vpnview_large' );
							$withoutImageSmall = get_field( 'without_vpnview_small' );
							if( !empty($withoutImageLarge) && !empty($withoutImageSmall) ):
							?>
								<img src="<?php echo $withoutImageLarge['url']; ?>" alt="<?php echo $withoutImageLarge['alt']; ?>" class="img-responsive vpn-work-image large" />
								<img src="<?php echo $withoutImageSmall['url']; ?>" alt="<?php echo $withoutImageSmall['alt']; ?>" class="img-responsive vpn-work-image small" />
							<?php endif; ?>
						</div>
						<div class="col-sm-15 col-md-offset-1 col-md-7">
							<h3>With <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/vpn-view-small.png" alt="VPNView" /></h3>
							<?php
							$withImageLarge = get_field( 'with_vpnview_large' );
							$withImageSmall = get_field( 'with_vpnview_small' );
							if( !empty($withImageLarge) && !empty($withImageSmall) ):
							?>
								<img src="<?php echo $withImageLarge['url']; ?>" alt="<?php echo $withImageLarge['alt']; ?>" class="img-responsive vpn-work-image large" />
								<img src="<?php echo $withImageSmall['url']; ?>" alt="<?php echo $withImageSmall['alt']; ?>" class="img-responsive vpn-work-image small" />
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if( have_rows( 'features_table' )): ?>
			<div class="what-you-get">
				<div class="container">
					<div class="row">
						<div class="col-xs-15 col-lg-offset-3 col-lg-9">
							<h2>What you get with <img src="<?php echo get_stylesheet_directory_uri(); ?>/images/vpn-view-small.png" alt="VPNView" /></h2>
							<table>
								<tr>
									<th>Features</th>
									<th>All plans</th>
								</tr>
								<?php while ( have_rows( 'features_table' ) ) : the_row(); ?>
									<tr>
										<td><?php the_sub_field( 'feature' ); ?></td>
										<td><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/tick.png" /></td>
									</tr>
								<?php endwhile; ?>
							</table>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php if( have_rows( 'content_rows' )): 
			$count = 1; while ( have_rows( 'content_rows' ) ) : the_row(); ?>
				<div class="content-row<?php if( $count % 2 != 0 ): echo ' blue-row'; endif; ?>">
					<div class="container">
						<div class="row">
							<div class="col-xs-15 col-sm-3<?php if( $count % 2 == 0 ): echo ' col-sm-push-12'; endif; ?>">
								<div class="img-wrapper">
									<?php 
									$image = get_sub_field( 'content_image' );
									if( !empty($image) ): ?>
										<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" class="img-responsive" />
									<?php endif; ?>
								</div>
							</div>
							<div class="col-xs-15 col-sm-11 <?php if( $count % 2 == 0 ): echo 'col-sm-pull-3'; else: echo 'col-sm-push-1'; endif; ?>">
								<h3><?php the_sub_field( 'content_title' ); ?></h3>
								<p><?php the_sub_field( 'content_text' ); ?></p>
							</div>
						</div>
					</div>
				</div>
			<?php $count++; endwhile;
		endif; ?>

		<div class="footer-cta<?php if( $count % 2 != 0 ): echo ' blue-row'; endif; ?>">
			<div class="container">
				<div class="row">
					<div class="col-xs-15 col-sm-10">
						<h2>It's simple to set up and use</h2>
						<p>Just sign in, download the app with one simple click, and start viewing in five minutes. Start now.</p>
					</div>
					<div class="col-xs-15 col-sm-5">
						<a class="button" href="<?php echo esc_url( get_permalink( 10 ) ); ?>">Get VPNView</a>
					</div>
				</div>
			</div>
		</div>

	<?php endwhile; endif; ?>

</div><!-- .main -->

<?php get_footer(); ?>
