<?php
/**
 * The Client Area page template file
 *
 * Template Name: Client Area My Subscription page template
 */
check_client_area_permissions();

get_header(); ?>
<?php $user = wp_get_current_user(); ?>
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
th{ font-size:16px !important; font-weight:400 !important; border-bottom:1px solid #4187c7;}
td{ font-size:16px !important; }
td:nth-child(odd){ border-right: 1px solid #4187c7 !important; }
td:nth-child(even){ border-right: 1px solid #4187c7 !important; }
th:nth-child(odd){ border-right: 1px solid #4187c7 !important; }
th:nth-child(even){ border-right: 1px solid #4187c7 !important; }
.subscription_renewal_date{
	font-size:16px;
}
.cancelsubscription{
	background-color: #d9534f !important;
	color:#fff;
}
.reactivate_trigger{ margin-top:10px; }
.green-circle{ background: #5cb85c; width:10px; height:10px; border-radius:50%; display:inline-block;}
.red-circle{ background: #d9534f; width:10px; height:10px; border-radius:50%; display:inline-block;}
.orange-circle{ background: #f0ad4e; width:10px; height:10px; border-radius:50%; display:inline-block;}
.subscription_status_value{ display:block; width:100%;}
.subscription_term_value{ display: block; width:100%;}
.subscription_renewal_date_value{ display: block; width:100%;}
.green_text{ color: #5cb85c; }
.orange_text{ color: #f0ad4e; }
.red_text{ color: #d9534f; }
.body_text{ font-size:16px; }
.hideme{ display:none; }
.table-payments{ width:100%; }
</style>
<div class="main">
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
											<h2 class="my_account_top_title">My Subscription</h2>
											<p class="quiet my_account_sub_title">Please use this page to update your subscription settings.</p>
											<?php if($fail == true){ ?>
												<div class="alert alert-danger"><strong>Failed</strong> to upload your avatar</div>
											<?php } ?>
											<hr/>
											<?php if(isset($_GET['upgrade_complete'])){
												if($_GET['upgrade_complete'] == 1){
													?>
													<div class="alert alert-success">
														<strong>Success!</strong> Your subscription has been set to upgrade on the next payment date.
													</div>
													<?php
												}
											}
											if(isset($_GET['cancellation_complete'])){
												if($_GET['cancellation_complete'] == 1){
													?>
													<div class="alert alert-success">
														<strong>Success!</strong> Your subscription has been set to cancel on the next payment date.
													</div>
													<?php
												}
											}
											if(isset($_GET['cancellation_now_complete'])){
												if($_GET['cancellation_now_complete'] == 1){
													?>
													<div class="alert alert-success">
														<strong>Success!</strong> Your subscription has been cancelled.
													</div>
													<?php
												}
											}
											?>
											<div class="alert alert-success upgrade_cancel_alert hideme">
												<strong>Success!</strong> Your subscription Upgrade/Downgrade has been cancelled.
											</div>
											<div class="alert alert-danger upgrade_cancel_fail_alert hideme">
												<strong>Failure!</strong> Your subscription Upgrade/Downgrade failed to cancel.
											</div>
										</div>
									</div>
								</section>
							</div>
						</div>
						<div class="spb-column-container span12  " style="">
							<div class="spb-asset-content">
								<section class="container">
						 			<div class="row">
										<div class="spb_content_element span12 spb_text_column">
											<?php $subscription = get_client_subscription_data(); ?>
											<h2 class="my_account_top_title">My Subscription Details</h2>
											<h3 class="headingtext">Subscription Status</h3>
											<p class="subscription_status">
												<div class="subscription_status_value body_text">
												<?php
													if($subscription["subscription_status"] == "active"){ 
														echo "<div class='green-circle'></div> ".trim(ucwords($subscription["subscription_status"])); 
													} else {
														echo "<div class='red-circle'></div> ".trim(ucwords($subscription["subscription_status"])); 
													}
												?>
												</div>
												<?php if($subscription->status != "Cancelled"){ ?>
													<?php if(isset($subscription["schedule"]->auto_renew) && $subscription["schedule"]->auto_renew == 0){ echo "<span class='label label-danger'>Cancel on ".date("d-m-Y", strtotime($subscription["subscription_end"]))."</span>"; 
														?>
														<a href="#" class="btn btn-success reactivate_trigger"><i class="fa fa-shield" aria-hidden="true"></i> Re-Activate Subscription</a>
														<?php
													} ?>
													<?php if($subscription['subscription_status'] == 'active'){ ?>
													<a href="/client-area-cancel-subscription/" class="cancelsubscription sf-button medium slightlyroundedarrow <?php if($subscription["subscription_status"] == "active" && $subscription["schedule"]->auto_renew == 0){ echo 'hideme'; } ?>"><i class="fa fa-ban" aria-hidden="true"></i> Cancel Subscription</a>
													<?php } ?>
												<?php } ?>
											</p>
										</div>
									</div>
								</section>
							</div>
						</div>
						<div class="spb-column-container span12  " style="">
							<div class="spb-asset-content">
								<section class="container">
						 			<div class="row">
										<div class="spb_content_element span12 spb_text_column">
											<h3 class="headingtext">Subscription Term</h3>
											<p class="subscription_term"><div class="subscription_term_value body_text"><?php echo ucwords($subscription["subscription_term"]); ?></div>
												<?php if($subscription['schedule']->upgrade_on_expire == 1){
													if($subscription["subscription_term"] != $subscription['schedule']->upgrade_term){
														if($subscription['subscription_term'] == "monthly"){
															if($subscription['schedule']->upgrade_term == "6 monthly" || $subscription['schedule']->upgrade_term == "yearly"){
																$updown = "Upgrade";
															} 
														}
														if($subscription['subscription_term'] == "6 monthly"){
															if($subscription['schedule']->upgrade_term == "monthly"){
																$updown = "Downgrade";
															} 
															if($subscription['schedule']->upgrade_term == "yearly"){
																$updown = "Upgrade";
															} 
														}
														if($subscription['subscription_term'] == "yearly"){
															if($subscription['schedule']->upgrade_term == "6 monthly" || $subscription['schedule']->upgrade_term == "monthly"){
																$updown = "Downgrade";
															} 
														}
													}
												}
												?>
												<?php if($subscription['subscription_term'] != "yearly" && $subscription['schedule']->upgrade_on_expire != 1 && $subscription['subscription_status'] == "active"){ ?>
											 		<a href="/client-area-upgrade-subscription/" class="sf-button medium accent slightlyroundedarrow  upgradetrigger <?php if(isset($subscription["schedule"]->auto_renew) && $subscription["schedule"]->auto_renew == 0 && $subscription['subscription_status'] == 'active'){ echo 'hideme'; } ?>"><i class="fa fa-level-up" aria-hidden="true"></i> Upgrade Subscription</a>
											 	<?php } 
											 	if($subscription['subscription_term'] != 'monthly' && $subscription['schedule']->upgrade_on_expire != 1 && $subscription['subscription_status'] == 'active'){ 
											 		?>
											 		<a href="/client-area-downgrade-subscription/" class="btn btn-success  upgradetrigger <?php if(isset($subscription["schedule"]->auto_renew) && $subscription["schedule"]->auto_renew == 0){ echo 'hideme'; } ?>"><i class="fa fa-level-down" aria-hidden="true"></i> Downgrade Subscription</a>
											 	<?php
											 	} 
											 	if($subscription['schedule']->upgrade_on_expire == 1){
											 		?>
											 		<a href="#" class="btn btn-success cancel_upgrade_downgrade_trigger"><i class="fa fa-ban" aria-hidden="true"></i> Cancel <?php echo $updown; ?></a>
											 		<?php
										 		}
										 		?>
										 		<?php if($subscription['schedule']->upgrade_on_expire == 1){ ?>
												<span class='label label-info upgradeInfo'>Set to <?php echo $updown; ?> to: <?php echo trim(ucwords($subscription['schedule']->upgrade_term)); ?> Subscription</span>
												<?php } ?>
											</p>
										</div>
									</div>
								</section>
							</div>
						</div>
						<div class="spb-column-container span12  " style="">
							<div class="spb-asset-content">
								<section class="container">
						 			<div class="row">
										<div class="spb_content_element span12 spb_text_column">
											<h3 class="headingtext">Subscription Renew Date</h3>
											<?php 
											$datetime1 = new DateTime(date("Y-m-d"));
											$datetime2 = new DateTime(date("Y-m-d", strtotime($subscription["subscription_end"])));
											$interval = $datetime1->diff($datetime2);
											$days = $interval->format('%a');
											?>
											<?php if(isset($subscription["schedule"]->auto_renew) && $subscription['subscription_status'] == "active"){ ?>
												<p>
													<div class="subscription_renewal_date_value body_text">
														<?php echo date("d-m-Y", strtotime($subscription["subscription_end"])); ?>
													</div>
													<?php if(isset($subscription["schedule"]->auto_renew) && $subscription['subscription_status'] == "active"){ ?>
														<?php 
															if($days == 0){
																echo "<span class='red_text body_text'>".$days." Days Remaning</span>";
															} else if($days <= 14){
																echo "<span class='orange_text body_text'>".$days." Days Remaning</span>";
															} else {
																echo "<span class='green_text body_text'>".$days." Days Remaning</span>";
															}
														?></span>
													<?php } ?>
												</p>
											<?php } ?>	
											<hr/>									
										</div>
									</div>
								</section>
							</div>
						</div>
						<div class="spb-column-container span12  " style="">
							<div class="spb-asset-content">
								<section class="container">
						 			<div class="row">
										<div class="spb_content_element span12 spb_text_column">
											<h3 class="my_account_top_title">Payment History</h3>
											<?php						
											$historyItems = getPaymentHistory();
											$country_code = get_user_meta($user->ID,'subscription_country_code', true)['country_code'];
											?>
											<table class='table table-payments'>
												<thead>
													<tr>
														<th>Date</th>
														<th>Subscription Term</th>
														<th>Amount</th>									
														<th>Status</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($historyItems as $item){
														?>
														<tr>
															<td><?php echo date("d-m-Y", strtotime($item->date_to_process)); ?></td>
															<td><?php echo ucwords($item->current_term); ?></td>
															<td>
																<?php if($country_code == "GB"){ echo "&pound;"; } else { echo "&dollar;"; } ?>
																<?php echo $item->amount; ?>
															</td>										
															<td>
																<?php 
																	if($item->status == "OK"){
																		echo "<div class='green-circle'></div> ".$item->status; 
																	} else if($item->status == "to process") {
																		echo "<div class='orange-circle'></div> ".$item->status;
																	} else {
																		echo "<div class='red-circle'></div> ".$item->status; 
																	}
																?>	
															</td>
														</tr>
														<?php
													}
													?>
												</tbody>
											</table>
										</div>
									</div>
								</section>
							</div>
						</div>
				</div>
			</div>
		</div>

	<?php endwhile; endif; ?>
		
</div><!-- .main -->
<script>
jQuery(document).ready(function(){
	<?php if(isset($_GET['upgrade_complete']) || isset($_GET['cancellation_complete']) || isset($_GET['cancellation_now_complete'])){
		if($_GET['upgrade_complete'] == 1 || $_GET['cancellation_complete'] == 1 || $_GET['cancellation_now_complete'] == 1){
			?>
			setTimeout(function(){ jQuery('.alert').animate({ height: "0px", padding:"0px", border: "0"}, 400); }, 3000);
			<?php
		}
	} ?>
});
</script>
<?php get_footer(); ?>