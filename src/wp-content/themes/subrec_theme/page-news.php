<?php
/**
 * The News page template file
 *
 * Template Name: News page template
 */

get_header(); ?>

<div class="main">
    <div class="container">
        <div class="row">
            <div class="col-lg-offset-3 col-lg-9">
                <div class="content">
                    <h1><?php the_title(); ?></h1>
                    <?php the_content(); ?>
                </div>
            </div>
        </div>

        <?php
        $args = array(
            'post_status' => 'publish',
            'post_type' => 'post',
            'posts_per_page' => -1,
            'order' => 'DESC',
            'order_by' => 'date',
        );
        $the_query = new WP_Query( $args );
        ?>
        <?php if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) :
                $the_query->the_post();
                ?>
                <article>
                    <div class="row">
                        <div class="col-xs-15 col-sm-3">
                            <?php
                            if ( has_post_thumbnail()) {
                                ?>
                                <div class="feature_image">
                                    <?php the_post_thumbnail('thumbnail', array( 'class' => 'img-responsive news-thumbnail' ));?>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <div class="col-xs-15 col-sm-12">
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <p><span class="quiet"><?php echo date("F m, Y", strtotime($post->post_date_gmt)); ?></span></p>
                            <?php the_excerpt(); ?>
                            <a href="<?php the_permalink();?>" class="quiet-orange">Read More...</a>

                        </div>
                    </div>
                    <hr/>
                </article>
            <?php endwhile;

            // Previous/next page navigation.
            the_posts_pagination( array(
                'prev_text'          => __( 'Previous page', 'vpn-view' ),
                'next_text'          => __( 'Next page', 'vpn-view' ),
            ) );
            wp_reset_postdata();

        // If no content, include the "No posts found" template.
        else :
            get_template_part( 'content', 'none' );

        endif; ?>
            
    </div><!-- .container -->
</div><!-- .main -->

<?php get_footer(); ?>
