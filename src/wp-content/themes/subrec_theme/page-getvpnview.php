<?php
/**
 * The Get VPNView page template file
 *
 * Template Name: Get VPNView page template
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

		<?php if( get_field( 'plans' )): ?>
			<div class="plans blue-row">
				<div class="container">
					<div class="row">
						<div class="col-xs-15">
							<h2>Select the plan that you'd like</h2>
						</div>
					</div>
					<?php
					$plans = get_field( 'plans' );
					$leftPlan = $plans[0];
					$centerPlan = $plans[1];
					$rightPlan = $plans[2];
					?>
					<div class="row">
						<div class="col-xs-15 col-sm-offset-2 col-sm-11 col-md-offset-1 col-md-3">
							<div class="plan-wrapper">
								<div class="plan">
									<div class="plan-header">
										<p><?php echo $leftPlan['plan_title']; ?></p>
									</div>
									<p class="duration"><?php echo $leftPlan['plan_duration']; ?></p>
									<p class="cost"><?php echo $leftPlan['plan_cost']; ?><span> per month</span></p>
									<p><?php echo $leftPlan['plan_text']; ?></p>
									<a class="button">Start now<span>for <?php echo $leftPlan['plan_duration']; ?></span></a>
									<?php
									$leftPlanImage = $leftPlan['plan_optional_image'];
									if( $leftPlanImage ): ?>
										<img src="<?php echo $leftPlanImage['url']; ?>" alt="<?php echo $leftPlanImage['alt']; ?>" />
									<?php endif; ?> 
								</div>
							</div>
						</div>
						<div class="col-xs-15 col-sm-offset-2 col-sm-11 col-md-3">
							<div class="plan-wrapper main-plan active">
								<div class="plan">
									<div class="plan-header">
										<p><?php echo $centerPlan['plan_title']; ?></p>
									</div>
									<p class="duration"><?php echo $centerPlan['plan_duration']; ?></p>
									<p class="cost"><?php echo $centerPlan['plan_cost']; ?><span> per month</span></p>
									<p><?php echo $centerPlan['plan_text']; ?></p>
									<a class="button">Start now<span>for <?php echo $centerPlan['plan_duration']; ?></span></a>
									<?php
									$centerPlanImage = $centerPlan['plan_optional_image'];
									if( $centerPlanImage ): ?>
										<img src="<?php echo $centerPlanImage['url']; ?>" alt="<?php echo $centerPlanImage['alt']; ?>" />
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="col-xs-15 col-sm-offset-2 col-sm-11 col-md-3">
							<div class="plan-wrapper">
								<div class="plan">
									<div class="plan-header">
										<p><?php echo $rightPlan['plan_title']; ?></p>
									</div>
									<p class="duration"><?php echo $rightPlan['plan_duration']; ?></p>
									<p class="cost"><?php echo $rightPlan['plan_cost']; ?><span> per month</span></p>
									<p><?php echo $rightPlan['plan_text']; ?></p>
									<a class="button">Start now<span>for <?php echo $rightPlan['plan_duration']; ?></span></a>
									<?php
									$rightPlanImage = $rightPlan['plan_optional_image'];
									if( $rightPlanImage ): ?>
										<img src="<?php echo $rightPlanImage['url']; ?>" alt="<?php echo $rightPlanImage['alt']; ?>" />
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

	<?php endwhile; endif; ?>

</div><!-- .main -->

<?php get_footer(); ?>
