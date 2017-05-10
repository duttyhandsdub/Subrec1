<?php
/**
 * The template for displaying all single posts and attachments
 */

get_header(); ?>

<div class="main">
	<div class="container">
		<?php if ( have_posts() ) :
			while ( have_posts() ) : the_post(); ?>
			<div class="row">
				<div class="col-xs-15 col-sm-offset-2 col-sm-11">
					<article>
						<div>
							<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
							<p><span class="quiet"><?php echo date("F m, Y", strtotime($post->post_date_gmt)); ?></span></p>
						</div>
						<?php if ( has_post_thumbnail()) { ?>
							<div class="feature_image">
								<?php the_post_thumbnail('large', array( 'class' => 'img-responsive news-thumbnail' ));?>
							</div>
						<?php } ?>
						<div>
							<?php the_content(); ?>
						</div>
					</article>
					<div class="line"></div>
                    <?php
                    // Previous/next post navigation
                    the_post_navigation( array(
                        'prev_text' => '<i class="fa fa-arrow-left" aria-hidden="true"></i><span class="post-title">%title</span>',
                        'next_text' => '<span class="post-title">%title</span><i class="fa fa-arrow-right" aria-hidden="true"></i>',
                    ) ); ?>
				</div>
			</div>
			<?php
			endwhile;
		endif; ?>
	</div><!-- .container -->
</div><!-- .main -->

<?php get_footer(); ?>
