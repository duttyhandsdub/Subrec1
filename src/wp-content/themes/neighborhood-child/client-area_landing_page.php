<?php
/**
 * The Client Area Landing page template file
 *
 * Template Name: Client Area Landing page template
 */
check_client_area_permissions();

get_header(); ?>
<style>
.headingtext{ color:#000 !important; font-weight:200; margin-top:0px !important; font-size:18px;}
.nopaddingleft{ padding-left:0px !important; }
.nopaddingright{ padding-right:0px !important; }
.my_account_top_title{
	color:#000;
	font-weight:200;
	font-size:20px;
}
.my_account_sub_title{
	margin-bottom:0px !important;
}
.nosidepadding{
	padding-left:0px !important;
	padding-right:0px !important;
}
.btn {
    display: inline-block;
    padding: 6px 12px !important;
    margin-bottom: 0 !important;
    font-size: 14px !important;
    font-weight: 400 !important;
    line-height: 1.42857143 !important;
    text-align: center !important;
    white-space: nowrap !important;
    vertical-align: middle !important;
    -ms-touch-action: manipulation !important;
    touch-action: manipulation !important;
    cursor: pointer !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
    background-image: none !important;
    border: 1px solid transparent !important;
    border-radius: 4px !important;
    width: auto;
}
.btn-success {
    color: #fff !important;
    background-color: #5cb85c !important;
    border-color: #4cae4c !important;
}
.btn-success:active, .open>.dropdown-toggle.btn-success {
    color: #fff !important;
    background-color: #449d44 !important;
    border-color: #398439 !important;
}
.btn-success:hover{
	    color: #fff !important;
    	background-color: #398439 !important;
    	border-color: #255625 !important;
}
.btn-primary {
    color: #fff !important;
    background-color: #4187c7 !important;
    border-color: #336694 !important;
}
.btn-primary:active, .open>.dropdown-toggle.btn-primary {
    color: #fff !important;
    background-color: #336694 !important;
    border-color: #398439 !important;
}
.btn-primary:hover{
	    color: #fff !important;
    	background-color: #336694 !important;
    	border-color: #255625 !important;
}
.btn-danger {
    color: #fff;
    background-color: #d9534f;
    border-color: #d43f3a;
}
.btn-danger:hover {
    color: #fff;
    background-color: #c9302c;
    border-color: #ac2925;
}
.btn-danger:hover{
	color: #fff;
    background-color: #ac2925;
    border-color: #761c19;
}
p{ font-size:16px; }
</style>
	<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/1.5.16/clipboard.min.js"></script>
<div class="inner-page-wrap clearfix">
	<div class="page-content clearfix">
		<section class="container">
			<div class="row">
				<div class="spb-column-container span12  " style="">
					<div class="spb-asset-content">
						<section class="container">
				 			<div class="row">
								<div class="spb_content_element span12 spb_text_column">
									<p class="quiet">Your VPN View landing page where you can edit your account, view/update your subscription and get VPN View Server details.</p>
								</div>
							</div>
						</section>
					</div>
				</div>
				<div class="spb-column-container span4  " style="">
					<div class="spb-asset-content">
						<section class="container">
				 			<div class="row">
								<div class="spb_content_element span12 spb_text_column">
									<div class="spb_wrapper clearfix">
									<h4 class="spb_heading spb_text_heading"><span>Account Details</span></h4>
									<p>Edit your account details such as your name/email address/password.</p>
								</div>
							</div>
						</section>
						<section class="container">
			 				<div class="row">
								<div class="spb_content_element span12 spb_text_column">
									<div class="spb_wrapper clearfix">
										<p style="text-align: left;">
											<a class="sf-button medium accent slightlyroundedarrow " href="/client-area-my-account/" target="_self">Edit <span>Your Account</span><span class="arrow"></span></a>
										</p>
									</div> 
								</div>
							</div>
						</section>
					</div>
				</div>
				<div class="spb-column-container span4  " style="">
					<div class="spb-asset-content">
						<section class="container">
				 			<div class="row">
								<div class="spb_content_element span12 spb_text_column">
									<div class="spb_wrapper clearfix">
									<h4 class="spb_heading spb_text_heading"><span>Subscription Details</span></h4>
									<p>View & edit your subscription details such as days remaining and package.</p>
								</div>
							</div>
						</section>
						<section class="container">
			 				<div class="row">
								<div class="spb_content_element span12 spb_text_column">
									<div class="spb_wrapper clearfix">
										<p style="text-align: left;">
											<a class="sf-button medium accent slightlyroundedarrow " href="/client-area-my-subscription/" target="_self">View <span>Your Subscription</span><span class="arrow"></span></a>
										</p>
									</div> 
								</div>
							</div>
						</section>
					</div>
				</div>
				<div class="spb-column-container span4  " style="">
					<div class="spb-asset-content">
						<section class="container">
				 			<div class="row">
								<div class="spb_content_element span12 spb_text_column">
									<div class="spb_wrapper clearfix">
									<h4 class="spb_heading spb_text_heading"><span>SubRec Music</span></h4>
									<p>View and download from the extensive list of subscribers music and video available..</p>
								</div>
							</div>
						</section>
						<section class="container">
			 				<div class="row">
								<div class="spb_content_element span12 spb_text_column">
									<div class="spb_wrapper clearfix">
										<p style="text-align: left;">
											<a class="sf-button medium accent slightlyroundedarrow " href="/shop/" target="_self">View <span>SubRec Music</span><span class="arrow"></span></a>
										</p>
									</div> 
								</div>
							</div>
						</section>
					</div>
				</div>
	
			</div>
		</section>
	</div>
</div><!-- .main -->
<?php endwhile; endif; ?>
<?php get_footer(); ?>