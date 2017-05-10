<?php
/**
 * The Login Page template file
 *
 * Template Name: Login Page template
 */
get_header(); ?>
<style>
form label{
    width:100%;
    display:block; 
    text-align:left;
}
form input{
    width:100%;
    display:block;
}
</style>
<div class="inner-page-wrap clearfix">
	<div class="page-content clearfix">
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<section class="container">
				<div class="row">
					<h1><?php the_title(); ?></h1>
	                <?php if(isset($_GET['not_logged_in'])){ ?>
	                    <div class="alert alert-danger">You must be <strong>logged in</strong> to view the client area</div>
	                <?php }
	                $login  = (isset($_GET['login']) ) ? $_GET['login'] : 0;
	                if ( $login === "failed" ) {
	                  echo '<div class="alert alert-danger"><strong>ERROR:</strong> Invalid username and/or password.</div>';
	                } elseif ( $login === "empty" ) {
	                  echo '<div class="alert alert-danger"><strong>ERROR:</strong> Username and/or Password is empty.</div>';
	                } elseif ( $login === "false" ) {
	                  echo '<div class="alert alert-danger"><strong>ERROR:</strong> You are logged out.</div>';
	                }
	                $args = array(
	                    'redirect' => home_url().'/client-area-my-account/', 
	                    'id_username' => 'user',
	                    'id_password' => 'pass',
	                   ); ?>
	                <div class="login-form-main">
	                    <?php wp_login_form( $args ); ?>
	                    
	                </div>
	                <script>
	                    // animate the alert to height 0 after 3 seconds
	                    setTimeout(function(){ jQuery('.alert').animate({ height: "0px", padding:"0px", border: "0"}, 400); }, 3000);
	                </script>
				</div>
			</section>
		<?php endwhile; endif; ?>
		
	</div>
</div><!-- .main -->

<?php get_footer(); ?>
