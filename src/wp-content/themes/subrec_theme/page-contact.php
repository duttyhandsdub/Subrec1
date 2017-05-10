<?php
/**
 * The Contact Us page template file
 *
 * Template Name: Contact Us page template
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
                        <h1><?php the_title(); ?></h1>
                        <?php the_content(); ?>
                    </div>
                </div>
            </div>
        </div>

        <?php $page_type = "troubleshooting-and-setup-guide";
        include('partials/search_form_markup.php'); ?>

        <div class="container">
            <div class="row">
                <div class="col-xs-15 col-md-offset-3 col-md-9">
                    <?php echo do_shortcode('[gravityform id="1" title="false" description="false" ajax="true"]'); ?>
                </div>
            </div>
        </div>

    <?php endwhile; endif; ?>

</div><!-- .main -->

<?php get_footer(); ?>
