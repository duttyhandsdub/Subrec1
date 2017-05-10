<?php
class WP_Subscription_Manger{
	protected $_version = SUBSCRIPTION_MANAGER_VERSION;
	protected $pluginPath = SUBSCRIPTION_MANAGER_PATH;
    protected $pluginUrl;
    private $wpdb;
    private $table_prefix;
    private $filters;
    private $filter_array = array('subscription_term'=>null, 'subscription_status'=>null, 'subscription_email'=>null, 'expire_from'=>null, 'expire_to'=>null);
    private $subscriptions;
    private $current_subscription;
    private $subscription_user;
    private $payment_history;
    private $page;
    private $main_table_rows_count;
    private $main_table_max_rows = 20;
    private $vouchers;
    
	public function __construct()
    {
        // Set Plugin URL
        $this->pluginUrl = WP_PLUGIN_URL . '/subscription_manager';
        global $wpdb;
        $this->wpdb = $wpdb;
        global $table_prefix;
        $this->table_prefix = $table_prefix;
		if (is_admin())
		{
		    add_action('admin_menu', array($this, 'wp_subscription_manager_admin_menu'));
		}
		$options = get_option('wp_subscription_manager');
		$this->main_table_max_rows = $options['wp_subscriptions_manager_max_rows'];

		//add ajax actions
		add_action( 'wp_ajax_give_free_plan', array($this,'ajax_give_free_plan') );
		add_action( 'wp_ajax_nopriv_give_free_plan', array($this,'ajax_give_free_plan' ));

		add_action( 'wp_ajax_delete_voucher', array($this,'ajax_delete_voucher') );
		add_action( 'wp_ajax_nopriv_delete_voucher', array($this,'ajax_delete_voucher' ));

		add_action( 'wp_ajax_deactivate_voucher', array($this,'ajax_deactivate_voucher') );
		add_action( 'wp_ajax_nopriv_deactivate_voucher', array($this,'ajax_deactivate_voucher' ));

		add_action( 'wp_ajax_reactivate_voucher', array($this,'ajax_reactivate_voucher') );
		add_action( 'wp_ajax_nopriv_reactivate_voucher', array($this,'ajax_reactivate_voucher' ));

		add_action( 'wp_ajax_wp_subscription_apply_coupon', array($this,'ajax_apply_coupon_code') );
		add_action( 'wp_ajax_nopriv_wp_subscription_apply_coupon', array($this,'ajax_apply_coupon_code' ));

		add_action( 'gform_paypal_post_ipn', array($this, 'wp_subscription_paypal_post_ipn'), 10, 4) ;
    }

	public static function get_instance() {
		if ( self::$_instance == null ) {
			self::$_instance = new WP_Subscription_Manger();
		}
		return self::$_instance;
	}

	public function wp_subscription_manager_admin_menu(){
		add_menu_page("Subscription Manager", "Subscription Manager", 'manage_options', "wp_subscription_main", array($this, "wp_subscription_main_page"), get_option('home').'/wp-content/plugins/subscription_manager/img/icon.png');
		add_submenu_page("wp_subscription_main", "Settings", "Settings", 'manage_options', "wp_subscription_settings", array($this, "wp_subscription_settings_page"));
		add_submenu_page("wp_subscription_main", "Vouchers", "Vouchers", 'manage_options', "wp_subscription_vouchers", array($this, "wp_subscription_vouchers_page"));
	}

	public function enqueue_admin_styles(){
		/** * Add stylesheet to the page*/
	    
	}

	public function admin_page_scripts(){
		?>
		<link href='https://fonts.googleapis.com/css?family=Oxygen:400,300' rel='stylesheet' type='text/css'>
		<script src="https://use.fontawesome.com/8d51d62480.js"></script>
		<?php
	}

	public function admin_header($page_name){
		?>
		<div style='width:100%;height:100%;background:#efefef;margin-left: -20px; padding-left: 20px;padding-top:20px; pading-bottom:40px;margin-bottom:10px;'>
	        <h1 style='position:relative;color:#23282d;'>WP-Subscription Manager System</h1>
	    	<h3 style='color:#23282d;'><?php echo $page_name; ?></h3>
	    </div>
	    <?php
	}

	public function admin_footer(){
		$output .= "<p><i>Plugin Created by Nick Thompson &copy;".date('Y').", <a href='http://www.accentdesign.co.uk/'>Accent Design Group Ltd</a>.</i></p>";
		return $output;
	}

	public function wp_subscription_main_page(){
		$send_result_bool = "";
		$edit = null;
		if(isset($_POST['submit_email'])){
			//email submit is set so we need to send the email.
			$to = $_POST['email_to'];
			$toarray = explode( ',', $to);
			$subject = $_POST['email_subject'];
			$message = $_POST['test_email_body'];
			if(count($toarray) > 1){
				//we are dealing with an array of addresses not a single one...
				foreach($toarray as $email_to){
					$user = get_user_by('email', $email_to);
					$first_name = get_user_meta($user->ID, "first_name", true);
					$country_code = get_user_meta($user->ID, 'subscription_country_code', true);
					$vpn_password = get_user_meta($user->ID, 'subscription_password', true);
					$subscription_status = get_user_meta($user->ID, 'subscription_status', true);
					$subscription = $this->get_user_subscription_individual($user->ID);
					$user_message = str_replace("{first_name}", $first_name, $message);
					$user_message = str_replace("{subscription_term}", ucwords($subscription->current_term), $user_message);
					$user_message = str_replace("{subscription_renewal_date}", date("d-m-Y", strtotime($subscription->date_to_process)), $user_message);
					$user_message = str_replace("{subscription_amount}", $subscription->amount, $user_message);
					$user_message = str_replace("{vpn_password}", $vpn_password, $user_message);
					$send = wp_mail( $email_to, $subject, $user_message, '', null );
					$send_result = json_decode($send);
					if($send_result->message = "Queued. Thank you."){
						$send_result_bool = "true";
					} else {
						$send_result_bool = "false";
					}
				}
			} else {
				$send = wp_mail( $to, $subject, $message, '', null );
				$send_result = json_decode($send);
				if($send_result->message = "Queued. Thank you."){
					$send_result_bool = "true";
				} else {
					$send_result_bool = "false";
				}
			}
		}
		if(isset($_POST['submit_edit_form'])){
			$user_id = $_POST['user_id'];
			update_user_meta( $user_id, 'first_name', esc_attr( $_POST['first_name'] ) );
			update_user_meta( $user_id, 'last_name', esc_attr( $_POST['last_name'] ) );
			wp_update_user( array ('ID' => $user_id, 'user_email' => esc_attr( $_POST['user_email'] )));
			$edit = true;
		}
		$this->admin_page_scripts();
		$this->admin_header("Main Page");
		echo "<script type='text/javascript' src='".get_template_directory_uri()."/js/tiny_mce/tiny_mce.js'></script>";
		echo "<div class='main-area'>";
			echo "<div class='whitepanel'>";
				if(isset($_POST['submit_table']) || isset($_POST['submit_email']) || isset($_POST['submit_edit_form'])){
					if(count($toarray) > 1){
						$this->get_subscriptions();
						$this->get_filters();
						$this->post_filter();
						echo $this->generate_filter_markup();				
						echo $this->generate_subscriber_table_markup($send_result_bool);
					} else {
						$this->get_user($_POST['user_id']);
						$this->get_subscription($_POST['user_id']);
						$this->get_user_payment_history($_POST['user_id']);
						echo $this->generate_subscription_markup($send_result_bool, $edit);
					}
					
				} else {
					$this->get_subscriptions();
					$this->get_filters();
					$this->post_filter();
					echo $this->generate_filter_markup();				
					echo $this->generate_subscriber_table_markup();
				}				
			echo "</div>";
		echo "</div>";
		echo $this->admin_footer();
	}

	public function wp_subscription_settings_page(){
		$update = "false";
		$options = get_option('wp_subscription_manager');
		if(isset($_POST['submit'])){
			//options to update;
			if($_POST['wp_subscriptions_manager_log'] == 'on'){
				$wp_subscriptions_manager_log = true;
			} else {
				$wp_subscriptions_manager_log = false;
			}
			if($_POST['wp_subscriptions_manager_max_rows'] != ""){
				$wp_subscriptions_manager_max_rows = $_POST['wp_subscriptions_manager_max_rows'];
			}
			$newOptions = array(
				'wp_subscriptions_manager_log'=>$wp_subscriptions_manager_log, 
				'wp_subscriptions_manager_max_rows'=>$wp_subscriptions_manager_max_rows, 
			);
			$updateresult = update_option('wp_subscription_manager', $newOptions);
			$options = get_option('wp_subscription_manager');
			if($updateresult == true){ 
				$update = "true"; 
			} else {
				$update = "fail";
			}
		}
		$this->admin_page_scripts();
		$this->admin_header("Settings Page");
		echo "<div class='main-area'>";
			echo "<div class='whitepanel'>";
				echo "<div class='wp_subscribtion_manager_options'>";
					echo "<h2><i class='fa fa-cog' aria-hidden='true'></i> WP Subscription Manager Settings</h2>";
					echo $this->settings_page_markup($update);
				echo "</div>";
			echo "</div>";
		echo "</div>";
		
		echo $this->admin_footer();
	}

	public function wp_subscription_vouchers_page(){
		if(isset($_POST['submit_edit_voucher_form'])){
			//this is an edit so do the save function
			if($this->save_voucher_edit($_POST) == "true"){
				$update = "true";
			}
		}
		if(isset($_POST['submit_add_voucher_form'])){
			//this is a new voucher so do the save function
			if($this->save_voucher($_POST) == "true"){
				$update = "true";
			} 
		}
		$this->admin_page_scripts();
		$this->admin_header("Vouchers Page");
		echo "<div class='main-area'>";
			echo "<div class='whitepanel'>";
				echo "<div class='wp_subscribtion_manager_options'>";
					echo "<h2><i class='fa fa-cog' aria-hidden='true'></i> WP Subscription Manager Vouchers</h2>";
					echo $this->vouchers_page_markup($update);
				echo "</div>";
			echo "</div>";
		echo "</div>";
		
		echo $this->admin_footer();		
	}

	private function settings_page_markup($update){
		$options = get_option('wp_subscription_manager');
		if($update == "true"){
			$output .= "<div class='alert alert-success' style='margin-top:10px;'>Your settings were succesfully updated.</div>";
		} else if($update == "fail"){
			$output .= "<div class='alert alert-danger' style='margin-top:10px;'>Your settings failed to update.</div>";
		}
		$output .= "<form action='#' method='post' style='margin-right: 20px;'>";
						$output .= "<hr/>";
						$output .= "<table class='table table-striped table-hover'>
							<tr>
								<td style='width:250px; vertical-align: text-top;'>
									<b><label for='log'>Logging Enabled</label></b>
								</td>
								<td>
									<input type='checkbox' name='wp_subscriptions_manager_log' ";
									if($options['wp_subscriptions_manager_log'] == true){ $output .= 'checked="checked"'; }
								$output .= "</td>
							</tr>
							<tr>
								<td style='width:250px; vertical-align: text-top;'>
									<b><label for='log'>Pagination Break Value</label></b>
								</td>
								<td>
									<input type='text' style='width:800px;' name='wp_subscriptions_manager_max_rows' value='".$options['wp_subscriptions_manager_max_rows']."'>
									<br/><span>This is the value of records to for pagination to trigger, so for 20 records per page, enter 20.</span>
								</td>
							</tr>	            
				        </table>
				        <hr/>		
			    		<input type='submit' name='submit' value='Save' class='btn btn-success'>
			    	</form>";
    	return $output;
	}

	private function vouchers_page_markup($update){
		$this->get_vouchers();
		$options = get_option('wp_subscription_manager');
		if($update == "true"){
			$output .= "<div class='alert alert-success' style='margin-top:10px;'>Your settings were succesfully updated.</div>";
		} else if($update == "fail"){
			$output .= "<div class='alert alert-danger' style='margin-top:10px;'>Your settings failed to update.</div>";
		}
		$output .= "<hr/>";

		if(!isset($_POST['add_voucher_submit'])){
			if(isset($_POST['view_voucher'])){
				$voucher_id = $_POST['voucher_id'];
				$output .= $this->vouchers_edit_page($voucher_id);
			} else {
				$output .= $this->vouchers_page_table();
			}
		} else {
			$output .= $this->vouchers_page_add();
		}
		return $output;
	}	

	private function vouchers_edit_page($voucher_id){
		$voucher = $this->get_single_voucher($voucher_id);
		$output .= "<form action='#' method='post' id='voucher_edit_form'>";
		$output .= "<input type='hidden' name='voucher_id' value='".$voucher->id."'>";
			$output .= "<table class='table table-striped table-hover'>";
				$output .= "<tr>";
					$output .= "<th>Voucher Name</th><td><input type='text' name='name' class='form-input' value='".$voucher->name."'></td>";
				$output .= "</tr>";
				$output .= "<tr>";
					$output .= "<td>Voucher Type</td><td>";
						$output .= "<select id='voucher_type' name='voucher_type'>";
							$output .= "<option value=''>Please select an option...</option>";
							$output .= "<option value='time_bomb' ";
								if($voucher->type == "time_bomb"){ $output .= "selected='selected'"; }
							$output .= ">Time Bomb</option>";
							$output .= "<option value='single_usage' ";
								if($voucher->type == "single_usage"){ $output .= "selected='selected'"; }
							$output .= ">Single Usage</option>";
							$output .= "<option value='single_user_usage' ";
								if($voucher->type == "single_user_usage"){ $output .= "selected='selected'"; }
							$output .= ">Single User</option>";
							$output .= "<option value='usage_limited' ";
								if($voucher->type == "usage_limited"){ $output .= "selected='selected'"; }
							$output .= ">Usage Limited</option>";						
						$output .= "</select>";
						$output .= "<input type='hidden' name='voucher_single_usage' id='voucher_single_usage' value=''>";
					$output .= "</td>";
				$output .= "</tr>";
				$output .= "<tr class='time_bomb_row ";
					if(date("d-m-Y", strtotime($voucher->expire_date)) != "01-01-1970"){ $output .= "show_row"; }
				$output .= "'>";
					$output .= "<td>Expiration Date</td><td><input type='date' name='expiration_date' value='".date('Y-m-d', strtotime($voucher->expire_date))."'></td>";
				$output .= "</tr>";
				$output .= "<tr class='usage_limit_row ";
					if($voucher->max_usage != "0"){ $output .= "show_row"; }
				$output .= "'>";
					$output .= "<td>Maximum Usage</td><td><input type='text' name='max_usage' value='".$voucher->max_usage."'></td>";
				$output .= "</tr>";
				$output .= "<tr class='user_select_row ";
					if($voucher->user_id != "0"){ $output .= "show_row"; }
				$output .= "'>";
					$output .= "<td>Voucher For User</td><td>";
					$users = $this->get_all_users();
					$output .= "<select id='voucher_user_id' name='voucher_user_id'>";
						$output .= "<option value=''>Please select a user...</option>";
						foreach($users as $user){
							$output .= "<option value='".$user->ID."' ";
								if($user->ID == $voucher->user_id){ $output .= "selected='selected'"; }
							$output .= ">".$user->user_email."</option>";
						}
					$output .= "</select>";
					$output .= "</td>";
				$output .= "</tr>";
				$output .= "<tr>";
;					$output .= "<th>Voucher Code</th><td><input type='text' name='voucher_code' class='form-input' value='".$voucher->code."'></td>";
				$output .= "</tr>";

				$output .= "<tr>";
					$output .= "<td>Discount Type</td><td>";
						$output .= "<select name='voucher_discount_type' id='voucher_discount_type'>";
							$output .= "<option value='p' ";
								if($voucher->discount_modifier == 'p'){ $output .= "selected='selected'"; }
							$output .= ">Percentage</option>";
							$output .= "<option value='m' ";
								if($voucher->discount_modifier == 'm'){ $output .= "selected='selected'"; }
							$output .= ">Money</option>";
						$output .= "</select>";
					$output .= "</td>";
				$output .= "</tr>";
				$output .= "<tr>";
;					$output .= "<th>Voucher Amount</th><td><input type='text' name='voucher_amount' class='form-input' value='".$voucher->discount_amount."'></td>";
				$output .= "</tr>";
			$output .= "</table>";
			$output .= "<button type='submit' name='submit_edit_voucher_form' class='btn btn-success edit_save'><i class='fa fa-floppy-o' aria-hidden='true'></i> Save Updates</button>";
			if($voucher->active == 1){
				$output .= "<a href='#' class='btn btn-danger deactivate_voucher' data-id='".$voucher->id."'><i class='fa fa-ban' aria-hidden='true'></i> Deactivate Voucher</a>";
			} else {
				$output .= "<a href='#' class='btn btn-success reactivate_voucher' data-id='".$voucher->id."'><i class='fa fa-check' aria-hidden='true'></i> Reactivate Voucher</a>";
			}
			$output .= "<a href='/wp-admin/admin.php?page=wp_subscription_vouchers' class='btn btn-warning cancelbtn'><i class='fa fa-caret-left' aria-hidden='true'></i> Cancel</a>";
		$output .= "</form>";
		$output .= "<div class='clearfix'></div>";
		return $output;
	}

	private function vouchers_page_add(){
		$output .= "<form action='#' method='post' id='voucher_add_form'>";
			$output .= "<table class='table table-striped table-hover'>";
				$output .= "<tr>";
					$output .= "<th>Voucher Name</th><td><input type='text' name='name' class='form-input' value=''></td>";
				$output .= "</tr>";
				$output .= "<tr>";
					$output .= "<td>Voucher Type</td><td>";
						$output .= "<select id='voucher_type' name='voucher_type'>";
							$output .= "<option value=''>Please select an option...</option>";
							$output .= "<option value='time_bomb'>Time Bomb</option>";
							$output .= "<option value='single_usage'>Single Usage</option>";
							$output .= "<option value='single_user_usage'>Single User</option>";
							$output .= "<option value='usage_limited'>Usage Limited</option>";						
						$output .= "</select>";
						$output .= "<input type='hidden' name='voucher_single_usage' id='voucher_single_usage' value=''>";
					$output .= "</td>";
				$output .= "</tr>";
				$output .= "<tr class='time_bomb_row'>";
					$output .= "<td>Expiration Date</td><td><input type='date' name='expiration_date'></td>";
				$output .= "</tr>";
				$output .= "<tr class='usage_limit_row'>";
					$output .= "<td>Maximum Usage</td><td><input type='text' name='max_usage'></td>";
				$output .= "</tr>";
				$output .= "<tr class='user_select_row'>";
					$output .= "<td>Voucher For User</td><td>";
					$users = $this->get_active_users();
					$output .= "<select id='voucher_user_id' name='voucher_user_id'>";
						$output .= "<option value=''>Please select a user...</option>";
						foreach($users as $user){
							$output .= "<option value='".$user->ID."'>".$user->user_email."</option>";
						}
					$output .= "</select>";
					$output .= "</td>";
				$output .= "</tr>";
				$output .= "<tr>";
					$voucher_code = $this->generateStrongPassword(30, false, 'luds');
;					$output .= "<th>Voucher Code</th><td><input type='text' name='voucher_code' class='form-input' value='".$voucher_code."'></td>";
				$output .= "</tr>";
				$output .= "<tr>";
					$output .= "<td>Discount Type</td><td>";
						$output .= "<select name='voucher_discount_type' id='voucher_discount_type'>";
							$output .= "<option value=''>Please select a discount type...</option>";
							$output .= "<option value='p'>Percentage</option>";
							$output .= "<option value='m'>Money</option>";
						$output .= "</select>";
					$output .= "</td>";
				$output .= "</tr>";
				$output .= "<tr>";
;					$output .= "<th>Voucher Amount</th><td><input type='text' name='voucher_amount' class='form-input'></td>";
				$output .= "</tr>";
			$output .= "</table>";
			$output .= "<button type='submit' name='submit_add_voucher_form' class='btn btn-success edit_save'><i class='fa fa-floppy-o' aria-hidden='true'></i> Save Voucher</button>";
			$output .= "<a href='/wp-admin/admin.php?page=wp_subscription_vouchers' class='btn btn-warning cancelbtn'><i class='fa fa-caret-left' aria-hidden='true'></i> Cancel</a>";
		$output .= "</form>";
		$output .= "<div class='clearfix'></div>";
		return $output;
	}

	private function vouchers_page_table(){
		$output = "<table class='table table-striped table-hover'>
			<tr>
				<th>Voucher Type</th>
				<th>Active</th>
				<th>Current Usage</th>
				<th>Max Usage</th>
				<th>User</th>
				<th>Discount Type</th>
				<th>Discount Amount</th>
				<th>Expires</th>
				<th>Actions</th>
			</tr>";
			foreach($this->vouchers as $voucher){
				$output .= "<tr>";
					$output .= "<td>".$voucher->type."</td>";
					$output .= "<td>";
						if($voucher->active == 1){
							$output .= "<div class='green-circle'></div> <span class='green_text'>Active</span>";
						} else {
							$output .= "<div class='red-circle'></div> <span class='red_text'>Inactive</span>";
						}
					$output .= "</td>";
					$output .= "<td>";
						if($voucher->current_usage > 0){ $output .= $voucher->current_usage; } 
					$output .= "</td>";
					$output .= "<td>";
						if($voucher->max_usage > 0){ $output .= $voucher->max_usage; } 
					$output .= "</td>";
					$user = get_user_by('id', $voucher->user_id);
					$output .= "<td>";
						if(isset($user)){ $output .= $user->user_email; } 
					$output .= "</td>";
					$output .= "<td>";
						if($voucher->discount_modifier == "p"){ $output .= "%"; } else if($voucher->discount_modifier == "m"){ $output .= "£";}
					$output .= "</td>";
					$output .= "<td>".$voucher->discount_amount."</td>";
					$output .= "<td>";
						if($voucher->expire_date != "" && date("d-m-Y", strtotime($voucher->expire_date)) != "01-01-1970"){ $output .= date("d-m-Y", strtotime($voucher->expire_date)); }
					$output .= "</td>";
					$output .= "<td>
									<a href='#' class='btn btn-danger delete_voucher' data-id='".$voucher->id."'><i class='fa fa-trash-o' aria-hidden='true'></i></a>&nbsp;&nbsp;";
									$output .= "<form action='#' method='post' class='inline'>
													<input type='hidden' name='voucher_id' value='".$voucher->id."'>
													<button type='submit' name='view_voucher' class='btn btn-warning'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></button>
												</form>";
				$output .= "</td></tr>";
			}	
		$output .= "</table>";
		$output .= "<hr/>";
		$output .= "<form action='#' method='post'>
						<button type='submit' name='add_voucher_submit' class='btn btn-success add_voucher'><i class='fa fa-plus-circle' aria-hidden='true'></i> Add Voucher</button>
					</form>";
		return $output;
	}

	private function get_active_users(){
		$sql = "select ID, user_email,
					MIN(case when meta_key = 'subscription_term' then meta_value else null end) as subscription_term,
					MIN(case when meta_key = 'subscription_status' then meta_value else null end) as subscription_status
					FROM rv_users u INNER JOIN rv_usermeta m on u.ID = m.user_id HAVING subscription_status='active'";
		$results = $this->wpdb->get_results($sql);
		return $results;
	}

	private function get_all_users(){
		$sql = "select ID, user_email,
					MIN(case when meta_key = 'subscription_term' then meta_value else null end) as subscription_term,
					MIN(case when meta_key = 'subscription_status' then meta_value else null end) as subscription_status
					FROM rv_users u INNER JOIN rv_usermeta m on u.ID = m.user_id";
		$results = $this->wpdb->get_results($sql);
		return $results;
	}

	private function post_filter(){
		if(isset($_POST['filterSubmit'])){
			//submit is set so we know there are filters to add to the filter values array
			if(isset($_POST['subscription_term'])){
				$this->filter_array['subscription_term'] = $_POST['subscription_term'];
				$_SESSION['subscription_term'] = $_POST['subscription_term'];
			} else {
				$this->filter_array['subscription_term'] = null;
			}
			if(isset($_POST['subscription_status'])){
				$this->filter_array['subscription_status'] = $_POST['subscription_status'];
				$_SESSION['subscription_status'] = $_POST['subscription_status'];
			} else {
				$this->filter_array['subscription_status'] = null;
			}
			if(isset($_POST['subscription_email'])){
				$this->filter_array['subscription_email'] = $_POST['subscription_email'];
				$_SESSION['subscription_email'] = $_POST['subscription_email'];
			} else {
				$this->filter_array['subscription_email'] = null;
			}
			if(isset($_POST['expire_from'])){
				$this->filter_array['expire_from'] = $_POST['expire_from'];
				$_SESSION['expire_from'] = $_POST['expire_from'];
			} else {
				$this->filter_array['expire_from'] = null;
			}
			if(isset($_POST['expire_to'])){
				$this->filter_array['expire_to'] = $_POST['expire_to'];
				$_SESSION['expire_to'] = $_POST['expire_to'];
			} else {
				$this->filter_array['expire_to'] = null;
			}

			$_SESSION['wp_subscriber_page'] = 1;
		
		}
	}

	public function ajax_give_free_plan(){
		$user_id = $_POST['user_id'];
		$post_term = $_POST['term'];
		$user = get_user_by('id', $user_id);
		$first_name = get_user_meta($user_id, 'first_name', true);
		$last_name = get_user_meta($user_id, 'last_name', true);
		$password = $this->generateStrongPassword(10, false, 'luds');
		$amount = "0.00";
		$today = date("Y-m-d");
		$term = $this->convertTerm($post_term);
		$date_to_process = get_process_date($term);
		$sql = "INSERT INTO ".$this->table_prefix."rg_sagepay_schedule (user_id, old_transaction_id, date_to_process, processed, status, upgrade_on_expire, auto_renew, amount, current_term) VALUES ('".$user_id."', null, '".$date_to_process."', '0', 'to process', '0', '0', '".$amount."', '".$term."')";
		$this->wpdb->get_results($sql);
		echo $sql;
		update_user_meta($user_id, "subscription_status", "active");
		update_user_meta($user_id, "subscription_end", $date_to_process);
		$this->update_term_length($term, $user_id);
		$radius_user_id = insert_into_radius($user, $first_name, $last_name, $password, $term, $today, $date_to_process, $amount);
		update_user_meta($user->ID, "subscription_password", $password);
		update_user_meta($user->ID, "radius_user_id", $radius_user_id);
		$to = $user->user_email;
		$subject = "VPN View Free Subscription";

		$message = get_email_header();
		$message .= "<p>Hi ".$first_name.",</p>
		<p>You have been granted a <strong>Free ".ucwords($post_term)." subscription</strong> to VPN View.</p>
		<p>Your subscription details are as follows:</p>
		<p>Subscription Term: ".ucwords($post_term)."</p>
		<p>Subscription Amount: £ <strong>FREE</strong></p>
		<p>Subscription Expire Date: ".date("d-m-Y", strtotime($date_to_process))."</p>
		<p>VPN Password: ".$password."</p>
		<p>Please use the VPN Servers tab in the client area of the website to gain access to the VPN Server addresses.</p>
		<p>Many Thanks</p>
		<p>VPN View</p>";
		$message .= get_email_footer();
		$send = wp_mail( $to, $subject, $message, '', null );
	}

	public function ajax_deactivate_voucher(){
		$id = $_POST['id'];
		$affected = $this->wpdb->update(
			$this->table_prefix."wp_subscription_vouchers",
			array(
				'active'=>0
			),
			array('id'=>$id)
		);
		if ( false === $affected ){
			echo 0;	
		} else {
			echo 1;
		}
		die;
	}

	public function ajax_reactivate_voucher(){
		$id = $_POST['id'];
		$affected = $this->wpdb->update(
			$this->table_prefix."wp_subscription_vouchers",
			array(
				'active'=>1
			),
			array('id'=>$id)
		);
		if ( false === $affected ){
			echo 0;	
		} else {
			echo 1;
		}
		die;
	}

	private function get_process_date($term){
		$today = date("Y-m-d");
		if($term == "monthly"){
			$date_to_process = date('Y-m-d', strtotime("+1 month", strtotime($today)));
		} else if($term == "6monthly"){
			$date_to_process = date('Y-m-d', strtotime("+6 month", strtotime($today)));
		} else if($term == "yearly"){
			$date_to_process = date('Y-m-d', strtotime("+1 year", strtotime($today)));
		}
		return $date_to_process;
	}

	private function update_term_length($term, $user_id){
		if($term == "monthly"){
			update_user_meta($user_id, "subscription_term", "monthly");
		} else if($term == "6monthly"){
			update_user_meta($user_id, "subscription_term", "6 monthly");
		} else if($term == "yearly"){
			update_user_meta($user_id, "subscription_term", "yearly");
		}
	}

	private function convertTerm($value){
		if(trim($value) == "1 month"){ return "monthly"; }
		if(trim($value) == "6 months"){ return "6monthly"; }
		if(trim($value) == "12 months"){ return "yearly"; }		
	}

	private function get_subscriptions(){
		$this->subscriptions = $this->wpdb->get_results("SELECT * FROM ".$this->table_prefix."rg_sagepay_schedule WHERE processed = 0");
	}

	private function get_user_subscription_individual($user_id){
		$results = $this->wpdb->get_results("SELECT * FROM ".$this->table_prefix."rg_sagepay_schedule WHERE user_id = '".$user_id."' ORDER BY id DESC");
		if(isset($results[0])){
			return $results[0];
		}
		return false;
	}

	private function has_subscription($user_id){
		$total = count($this->wpdb->get_results("SELECT * FROM ".$this->table_prefix."rg_sagepay_schedule WHERE user_id = '".$user_id."' AND PROCESSED = 0"));
		if($total > 0){
			return true;
		}
		return false;
	}

	private function get_subscription($user_id){
		$results = $this->wpdb->get_results("SELECT * FROM ".$this->table_prefix."rg_sagepay_schedule WHERE user_id = '".$user_id."' AND PROCESSED = 0");
		if(isset($results[0])){ $this->current_subscription = $results[0]; } 
	}

	private function get_vouchers(){
		$this->vouchers = $this->wpdb->get_results("SELECT * FROM ".$this->table_prefix."wp_subscription_vouchers");
	}

	private function get_single_voucher($voucher_id){
		$result = $this->wpdb->get_results("SELECT * FROM ".$this->table_prefix."wp_subscription_vouchers WHERE id='".$voucher_id."'");
		return $result[0];
	}

	private function get_single_voucher_by_code($voucher_code){
		$result = $this->wpdb->get_results("SELECT * FROM ".$this->table_prefix."wp_subscription_vouchers WHERE code='".$voucher_code."'");
		return $result[0];
	}

	public function ajax_delete_voucher(){
		$id = $_POST['id'];
		$this->wpdb->get_results("DELETE FROM ".$this->table_prefix."wp_subscription_vouchers WHERE id='".$id."'");
		$this->get_vouchers();
	}

	private function get_user($user_id){
		$this->subscription_user = get_user_by('id', $user_id);
	}

	private function get_user_payment_history($user_id){
		$this->payment_history = $this->wpdb->get_results("SELECT * FROM ".$this->table_prefix."rg_sagepay_schedule WHERE user_id='".$user_id."' AND processed = 1");
	}

	private function get_users_avatar($user_id){
		$default = get_stylesheet_directory_uri().'/images/avatar_placeholder.png';
	    $img = get_user_meta($user_id, 'avatar', true);
	    if($img && $img != $default){
	        return $img;
		} else {
			return $default;
		}
	}

	private function save_voucher($post){
		if(isset($post['submit_add_voucher_form'])){
			$sql = "INSERT INTO ".$this->table_prefix."wp_subscription_vouchers (name, type, expire_date, user_id, max_usage, current_usage, code, discount_modifier, discount_amount) VALUES ('".$post['name']."', '".$post['voucher_type']."', '".date("Y-m-d", strtotime($post['expiration_date']))."', '".$post['voucher_user_id']."', '".$post['max_usage']."', '0', '".$post['voucher_code']."', '".$post['voucher_discount_type']."', '".$post['voucher_amount']."')";
			$this->wpdb->get_results($sql);
			if($this->wpdb->num_rows > 0){
				return "true";
			}
			return "false";			
		}
		return "false";
	}

	private function save_voucher_edit($post){
		if(isset($post['submit_edit_voucher_form'])){
			$affected = $this->wpdb->update(
				$this->table_prefix."wp_subscription_vouchers",
				array(
					'name'=>$post['name'],
					'type'=>$post['voucher_type'],
					'expire_date'=>date("Y-m-d", strtotime($post['expiration_date'])),
					'user_id'=>$post['voucher_user_id'],
					'max_usage'=>$post['max_usage'],
					'code'=>$post['voucher_code'],
					'discount_modifier'=>$post['voucher_discount_type'],
					'discount_amount'=>$post['voucher_amount']
				),
				array('id'=>$post['voucher_id'])
			);
			if ( false === $affected ){
				return "false";	
			} else {
				return "true";
			}
		}
		return "false";
	}

	private function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
	{
		$sets = array();
		if(strpos($available_sets, 'l') !== false)
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false)
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false)
			$sets[] = '23456789';
		if(strpos($available_sets, 's') !== false)
			$sets[] = '!@#$%&*?';
		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];
		$password = str_shuffle($password);
		if(!$add_dashes)
			return $password;
		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while(strlen($password) > $dash_len)
		{
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;
		return $dash_str;
	}

	private function get_filters(){
		$filters = array();
		$filters['subscription_status'] = array(''=>'blank','active'=>'active', 'inactive'=>'inactive', 'cancelled'=>'cancelled');
		$filters['subscription_term'] = array(''=>'blank','monthly'=>'monthly', '6 monthly'=>'6_monthly', 'yearly'=>'yearly');
		$filters['subscription_email'] = "";
		$filters['expire_from'] = "";
		$filters['expire_to'] = "";
		$this->filters = $filters;
	}

	private function generate_filter_markup(){
		$output = "<form action='#' method='post' class='filterform'>";
		$output .= "<h2>Filters</h2>";
		$output .= "<ul class='form floated'>";
		foreach($this->filters as $filter=>$value){
			if(empty($value)){
				//is a text field as has no default values
				$class_val = "";
				if(array_key_exists($filter, $this->filter_array)){ 
					$class_val = $this->filter_array[$filter]; 
				}
				if(isset($_SESSION[$filter])){
					$class_val = $_SESSION[$filter];
				}
				if($filter == "expire_from" || $filter == "expire_to"){

					$output .= "<li>
						<label for='".$filter."'>".ucwords(str_replace('_', ' ', $filter))."</label>
						<input type='date' name='".$filter."' id='".$filter."' value='".$class_val."'>
					</li>";
				} else {
					$output .= "<li>
						<label for='".$filter."'>".ucwords(str_replace('_', ' ', $filter))."</label>
						<input type='text' name='".$filter."' id='".$filter."' value='".$class_val."'>
					</li>";
				}
			} else {
				//is a dropdown as has values
				$class_val = "";
				if(array_key_exists($filter, $this->filter_array)){ 
					$class_val = $this->filter_array[$filter]; 
				}
				if(isset($_SESSION[$filter])){
					$class_val = $_SESSION[$filter];
				}
				$output .= "<li>
						<label>".ucwords(str_replace('_', ' ', $filter))."</label>
						<select id='".$filter."' name='".$filter."'>
						<option value='' selected=''>Any</option>";
						foreach($value as $val){
							if($val == $class_val){
								$output .= "<option value='".$val."' selected='selected'>".ucwords($val)."</option>";
							} else {
								$output .= "<option value='".$val."'>".ucwords($val)."</option>";
							}
						}
						$output .= "</select>";
					$output .= "</li>";
			}
		}
		$output .= "<li>
						<button type='submit' id='filterSubmit' name='filterSubmit' class='button orange'><i class='fa fa-search' aria-hidden='true'></i> Search</button>
						<a href='#' class='clearfilters'>Clear Filters</a>
					</li>";
		$output .= "</ul>";
		$output .= "</form>
		<hr/>";
		return $output;
	}

	private function get_subscription_query_params($limit=null, $offset=null){
		$where = "";
		$key = "";
		$having = array();
		$dates = "";
		foreach($this->filter_array as $key=>$value){
			if($key == "subscription_email"){
				if($value != null){
					$where = " WHERE user_email = '".$value."'";
				} else {
					if(isset($_SESSION['subscription_email'])){
						$value = $_SESSION['subscription_email'];
						if($value != null){
							$where = " WHERE user_email = '".$value."'";
						}
					}
				}
			}
			if($key == "subscription_term"){
				if($value != null){
					$having["subscription_term"] = $value;
				} else if(isset($_SESSION['subscription_term'])){
					$value = $_SESSION['subscription_term'];
					if($value != null){
						$having["subscription_term"] = $value;
					}
				}
			} 
			if($key == "subscription_status"){
				if($value != null){
					$having["subscription_status"] = $value;
				} else if(isset($_SESSION['subscription_status'])){
					$value = $_SESSION['subscription_status'];
					if($value != null){
						$having["subscription_status"] = $value;
					}
				}
			}
			if($key == "expire_from"){
				if($value != null){
					$having["expire_from"] = $value;
				} else if(isset($_SESSION['expire_from'])){
					$value = $_SESSION['expire_from'];
					if($value != null){
						$having["expire_from"] = $value;
					}
				}
			}
			if($key == "expire_to"){
				if($value != null){
					$having["expire_to"] = $value;
				} else if(isset($_SESSION['expire_to'])){
					$value = $_SESSION['expire_to'];
					if($value != null){
						$having["expire_to"] = $value;
					}
				}
			}
		}
		
		$havingstring = "";
		$keys = array_keys($having);
		if(count($having) >= 2){
			for($i=0;$i<count($having);$i++){
				if($i == 0){
					if($having[$keys[$i]] == "blank"){
						$havingstring = "HAVING ".$keys[$i]." IS NULL";
					} else if($keys[$i] == "expire_from"){

					} else if($keys[$i] == "expire_to"){

					} else {
						$havingstring = "HAVING ".$keys[$i]." = '".$having[$keys[$i]]."'";
					}
					
				} else {
					if($having[$keys[$i]] == "blank"){
						$havingstring .= " AND ".$keys[$i]." IS NULL";
					} else if($keys[$i] == "expire_from"){

					} else if($keys[$i] == "expire_to"){

					} else {
						$havingstring .= " AND ".$keys[$i]." = '".$having[$keys[$i]]."'";
					}
				}
			}
		} else if(count($having) == 1){
			if($having[$keys[0]] == "blank"){				
				$havingstring = "HAVING ".$keys[0]." IS NULL";
			} else {
				$havingstring = "HAVING ".$keys[0]." = '".$having[$keys[0]]."'";
			}
		} else if(count($having) == 0){
			$havingstring = "";
		}
		if($havingstring == ""){
			if(isset($having["expire_from"])){
				if(isset($having["expire_to"])){
					$date = " HAVING subscription_end BETWEEN '".$having['expire_from']."' AND '".$having['expire_to']."'";
				} else {
					$date = " HAVING subscription_end >= '".$having['expire_from']."'";
				}
			}
		} else {
			if(isset($having["expire_from"])){
				if(isset($having["expire_to"])){
					$date = " AND subscription_end BETWEEN '".$having['expire_from']."' AND '".$having['expire_to']."'";
				} else {
					$date = " AND subscription_end >= '".$having['expire_from']."'";
				}
			}
		}
		$query_string = $where." GROUP BY ID, user_email ".$havingstring.$date;
		if($limit != null){
			$query_string .= " LIMIT ".$limit;
		}
		if($offset != null){
			$query_string .= " OFFSET ".$offset;
		}

		return $query_string;
	}	

	private function get_main_table_total($query_params){
		$sql = "select ID, user_email,
					MIN(case when meta_key = 'subscription_term' then meta_value else null end) as subscription_term,
					MIN(case when meta_key = 'subscription_status' then meta_value else null end) as subscription_status
					FROM rv_users u INNER JOIN rv_usermeta m on u.ID = m.user_id".$query_params;
		$results = $this->wpdb->get_results($sql);
		return count($results);
	}

	private function generate_subscriber_table_markup($send_result_bool=null){
		if(!isset($_SESSION['wp_subscriber_page'])){
			$_SESSION['wp_subscriber_page'] = 1;
		}
		if(isset($_POST['page_next'])){
			$_SESSION['wp_subscriber_page'] = $_SESSION['wp_subscriber_page']+1;
		}
		if(isset($_POST['page_prev'])){
			$_SESSION['wp_subscriber_page'] = $_SESSION['wp_subscriber_page']-1;
		}
		$page = $_SESSION['wp_subscriber_page'];
		$offset = ($page-1) * $this->main_table_max_rows;
		$limit = $offset + $this->main_table_max_rows;
		$query_params = $this->get_subscription_query_params($limit, $offset);
		if(!isset($this->main_table_rows_count)){
			$this->main_table_rows_count = $this->get_main_table_total($this->get_subscription_query_params());
		}
		$output = "<div class='resultsarea'>";
		$sql = "select ID, user_email,
					MIN(case when meta_key = 'subscription_term' then meta_value else null end) as subscription_term,
					MIN(case when meta_key = 'subscription_status' then meta_value else null end) as subscription_status,
					MIN(case when meta_key = 'subscription_end' then meta_value else null end) as subscription_end
					FROM rv_users u INNER JOIN rv_usermeta m on u.ID = m.user_id".$query_params;
		$users = $this->wpdb->get_results($sql);
		if($send_result_bool == "true"){
			$output .= "<div class='alert alert-success' style='margin-top:10px;'>Your Email Sent Successfully.</div>";
		} else if($send_result_bool == "false"){
			$output .= "<div class='alert alert-danger' style='margin-top:10px;'>Your Email Failed to Send.</div>";
		}
		$output .= "<table class='table table-striped table-hover' id='main_table'>
				<tr>
					<th></th>
					<th>User Email</th>
					<th>Subscription Term</th>
					<th>Subscription Status</th>
					<th>Subscription Renew/Expire</th>
					<th>Actions</th>
				</tr>";
			foreach($users as $user){
				$output .= "<tr>";
					$output .= "<td><input type='checkbox' name='user_cb' class='user_cb' value='".$user->user_email."'></td>";
					$output .= "<td>".$user->user_email."</td>";
					$output .= "<td>".ucwords($user->subscription_term)."</td>";					
					$output .= "<td>".ucwords($user->subscription_status)."</td>"; 
					$output .= "<td>";
						if($user->subscription_end != ""){ $output .= date("d-m-Y", strtotime($user->subscription_end)); }
					$output .= "</td>";
					$output .= "<td>";
					
						$output .= "<form action='#' method='post'>
							<input type='hidden' name='user_id' value='".$user->ID."'>
							<input type='submit' name='submit_table' class='btn btn-success' value='View'>
						</form>";
					$output .= "</td>";
			}

			$output .= "<tr class='table_bottom'>
							<td colspan='5'></td>
							<td>
								<form action='#' method='post'>
									
									<input type='hidden' name='page' value='".($_SESSION['wp_subscriber_page'])."'>";
									if($page > 1){ 
										$output .= "<input type='submit' name='page_prev' value='Previous' class='btn btn-default'>";
									} 
									$output .= "&nbsp;&nbsp;Page: ".$_SESSION['wp_subscriber_page']."&nbsp;&nbsp;";
									if($page*$this->main_table_max_rows < $this->main_table_rows_count){ 
										$output .= "<input type='submit' name='page_next' value='Next' class='btn btn-default'>";
									} 
									$output .= "
								</form>
							</td>
						</tr>";
			$output .= "</table>";
			
			$output .= "<a href='#' class='email_trigger btn btn-success'><i class='fa fa-envelope-o' aria-hidden='true'></i> Send Email To Selected Subscribers Wizard</a>";
			$output .= $this->get_email_markup("main");
		$output .= "</div>";
		return $output;
	}

	private function generate_subscription_markup($send_result_bool, $edit){
		$first_name = get_user_meta($this->subscription_user->ID, 'first_name', true);		
		$last_name = get_user_meta($this->subscription_user->ID, 'last_name', true);
		$country_code = get_user_meta($this->subscription_user->ID, 'subscription_country_code', true);
		$vpn_password = get_user_meta($this->subscription_user->ID, 'subscription_password', true);
		$subscription_status = get_user_meta($this->subscription_user->ID, 'subscription_status', true);
		$avatar_url = $this->get_users_avatar($this->subscription_user->ID);
		$output = "<div class='filterform'>";
		$output .= "<div class='filterform-left'><img src='".$avatar_url."' class='user_avatar' id='avatarimage'></div>";
		$output .= "<div class='filterform-right'><h2>";
			if(isset($this->current_subscription->id)){ $output .= "Subscription ID: ".$this->current_subscription->id." - "; }
			$output .= $first_name." ".$last_name."</h2></div><div class='clearfix'></div>";
		$output .= "</div>";
		$output .= "<div class='resultsarea'>";
			if($send_result_bool == "true"){
				$output .= "<div class='alert alert-success' style='margin-top:10px;'>Your Email Sent Successfully.</div>";
			} else if($send_result_bool == "false"){
				$output .= "<div class='alert alert-danger' style='margin-top:10px;'>Your Email Failed to Send.</div>";
			}
			if($edit == true){
				$output .= "<div class='alert alert-success' style='margin-top:10px;'>Your Changes Have Neen Saved Successfully.</div>";
			} else if($edit == false){

			}
			$output .= "<h3 class='threequarterwidth'>Subscription Details</h3><a class='edit-btn' href='#'>Edit</a>";
			$output .= "<div class='subscription_info_table_wrapper' id='subscription_info_table_wrapper'>";
				$output .= "<table class='table table-striped table-hover subscription_info_table'>";
					$output .= "<tr>";
						$output .= "<th>Name</th><td>".$first_name." ".$last_name."</td>";
					$output .= "</tr>";
					$output .= "<tr>";
						$output .= "<th>Subscription Status</th><td>";
							if($subscription_status == "active"){
								$output .= "<div class='green-circle'></div> <span class='green_text'>".ucwords($subscription_status)."</span>";
							} else if($subscription_status == "inactive"){
								$output .= "<div class='red-circle'></div> <span class='red_text'>".ucwords($subscription_status)."</span>";
							} else {
								$output .= "<div class='red-circle'></div> ".ucwords($subscription_status);
							}
						$output .= "</td>";
					$output .= "</tr>";
					$output .= "<tr>";
						$output .= "<th>Username</th><td>".$this->subscription_user->user_email."</td>";
					$output .= "</tr>";
					$output .= "<tr>";
						$output .= "<th>Current Subscription Term</th><td>";
						if(isset($this->current_subscription)){
							if($this->current_subscription->current_term != ""){
								$output .= ucwords($this->current_subscription->current_term);
							}
						}
						$output .= "</td>";
					$output .= "</tr>";
					$output .= "<tr>";
						$output .= "<th>";
							if(isset($this->current_subscription)){
								if($this->current_subscription->auto_renew == 1){ 
									$output .= "Subscription Renewal Date"; 
								} else { 
									$output .= "Subscription Expire Date"; 
								}
							} else { $output .= "Subscription Expire Date"; }
						$output .= "</th><td>";
						if(isset($this->current_subscription)){
							$output .= date("d-m-Y", strtotime($this->current_subscription->date_to_process));
						}
						$output .= "</td>";
					$output .= "</tr>";
					$output .= "<tr>";
						$output .= "<th>User Registered Date</th><td>".date("d-m-Y", strtotime($this->subscription_user->user_registered))."</td>";
					$output .= "</tr>";
					$output .= "<tr>";
						$output .= "<th>Subscription Upgrade on Next Renewal Date</th><td>";
							if(isset($this->current_subscription)){
								if($this->current_subscription->upgrade_on_expire == 1){
									$output .= "<span class='green_text'>Yes</span>";
								} else {
									$output .= "<span class='red_text'>No</span>";
								}
							}
						$output .= "</td>";
					$output .= "</tr>";
					if(isset($this->current_subscription)){
						if($this->current_subscription->upgrade_term != ""){
							$output .= "<tr>";
								$output .= "<th>Upgrade to Subscription Term</th><td>".ucwords($this->current_subscription->upgrade_term)."</td>";
							$output .= "</tr>";
						}
					}
					$output .= "<tr>";
						$output .= "<th>Subscription Price</th><td>";
							if(isset($this->current_subscription)){
								if(!empty($country_code)){
									if($country_code['country_code'] == "GB"){ $output .= "&pound;"; } else { $output .= "&dollar;"; }
									$output .= $this->current_subscription->amount;
								}
							}
						$output .= "</td>";
					$output .= "</tr>";
					if($subscription_status != ""){
						$output .= "<tr>";
							$output .= "<th>VPN Password</th><td>".$vpn_password."</td>";
						$output .= "</tr>";
					}
					$output .= "<tr>";
						$output .= "<th>User Registered Date</th><td>".date("d-m-Y", strtotime($this->subscription_user->user_registered));
						$output .= "</td>";
					$output .= "</tr>";
				$output .= "</table>";
			$output .= "</div>";
			$output .= "<div class='subscription_info_table_wrapper_edit_form' id='subscription_info_table_wrapper_edit_form'>";
				$output .= "<form action='#' method='post'>";
					$output .= "<input type='hidden' name='user_id' value='".$this->subscription_user->ID."'>";
					$output .= "<table class='table table-striped table-hover'>";
						$output .= "<tr>";
							$output .= "<th>First Name</th><td><input type='text' name='first_name' class='form-input' value='".$first_name."'></td>";
						$output .= "</tr>";
						$output .= "<tr>";
							$output .= "<th>Last Name</th><td><input type='text' name='last_name' class='form-input' value='".$last_name."'></td>";
						$output .= "</tr>";
						$output .= "<tr>";
							$output .= "<th>Subscription Status</th><td>";
							if($subscription_status == "active"){
								$output .= "<div class='green-circle'></div> <span class='green_text'>".ucwords($subscription_status)."</span>";
							} else if($subscription_status == "inactive"){
								$output .= "<div class='red-circle'></div> <span class='red_text'>".ucwords($subscription_status)."</span>";
							} else {
								$output .= "<div class='red-circle'></div> ".ucwords($subscription_status);
							}
							$output .= "</td>";
						$output .= "</tr>";
						$output .= "<tr>";
							$output .= "<th>Username</th><td><input type='text' name='user_email' class='form-input' value='".$this->subscription_user->user_email."'></td>";
						$output .= "</tr>";
					$output .= "</table>";
					$output .= "<input type='submit' name='submit_edit_form' class='btn btn-success edit_save' value='Save Updates'>";
				$output .= "</form>";
			$output .= "</div>";
			$output .= "<hr/>";
			$output .= "<h3>Payment History</h3>";
			$output .= "<table class='table table-striped table-hover'>
							<tr>
								<th>Date</th>
								<th>Subscription Term</th>
								<th>Amount</th>									
								<th>Status</th>
							</tr>";
							foreach($this->payment_history as $history){
								$output .= "<tr>";
									$output .= "<td>".date("d-m-Y", strtotime($history->date_to_process))."</td>";
									$output .= "<td>".ucwords($history->current_term)."</td>";
									$output .= "<td>";
										if($country_code['country_code'] == "GB"){ $output .= "&pound;"; } else { $output .= "&dollar;"; }
										$output .= $history->amount;
									$output .= "</td>										
									<td>";
											if($history->status == "OK"){
												$output .= "<div class='green-circle'></div> ".$history->status; 
											} else {
												$output .= "<div class='red-circle'></div> ".$history->status; 
											}
									$output .= "</td>
								</tr>";
							}
						$output .= "</table>";
		$output .= "</div>";
		$output .= "<hr/>";
		$output .= "<div class='actions'>";
			$output .= "<h3>Actions</h3>";
			$output .= "<ul class='actions_list'>";
			$output .= "<li><a href='#' class='btn btn-success send_email_trigger'><i class='fa fa-envelope-o' aria-hidden='true'></i> Send Email To Subscriber Wizard</a></li>";
			if(isset($this->current_subscription)){
				if($this->current_subscription->current_term != ''){
					if($this->current_subscription->current_term != "yearly"){
						//upgrade
						$output .= "<li>&nbsp;&nbsp;<a href='#' class='btn btn-warning upgrade_trigger'><i class='fa fa-level-up' aria-hidden=true'></i> Upgrade Subscription</a></li>";
					} else if($this->current_subscription->current_term != "monthly"){
						//downgrade
						$output .= "<li>&nbsp;&nbsp;<a href='#' class='btn btn-warning downgrade_trigger'><i class='fa fa-level-down' aria-hidden=true'></i> Downgrade Subscription</a></li>";
					}
				}
			}
			if($subscription_status == "active"){ 
				$output .= "<li>&nbsp;&nbsp;<a href='#' class='btn btn-danger cancel_trigger'><i class='fa fa-ban' aria-hidden='true'></i> Cancel Subscription</a></li>";
			}
			if($subscription_status != "active"){ 
				$output .= "<li>&nbsp;&nbsp;<a href='#' class='btn btn-default free_trigger'><i class='fa fa-ellipsis-h' aria-hidden='true'></i> Give Free Subscription</a></li>";
			}
			$output .= "</ul>";
		$output .= "</div>";
		$output .= "<hr/>";
		$output .= "<div class='upgradedowngrade_area'>";
    		$output .= "<div class='form-inner' id='upgradearea'>";
	    		$output .= "<h2><i class='fa fa-level-up' aria-hidden='true'></i> Upgrade Subscription</h2>";
	    		$output .= "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>";
	    		$output .= "<div class='clearfix'></div>";

				$plans = get_field( 'plans', 'option' );
				$planArray = array();
				if(isset($this->current_subscription)){
					if($this->current_subscription->current_term == "monthly"){
						$planArray[] = $plans[1];
						$planArray[] = $plans[2];
					} else if($this->current_subscription->current_term  == "6 monthly"){
						$planArray[] = $plans[1];
					} 
					if(count($planArray) == 2){
						//is monthly so can upgrade to 6 month or yearly
						foreach($planArray as $plan){ 
							$output .= "<a href='#' class='btn btn-warning'><i class='fa fa-level-up' aria-hidden=true'></i> Upgrade to ".$plan['plan_duration']."</a>&nbsp;&nbsp;";
						}
					} else {
						//is 6 month so can only upgrade to yearly
						$output .= "yes";
					}
				}
			$output .= "</div>";
			$output .= "<hr/>";
    	$output .= "</div>";
    	$output .= "<div class='cancel_area'>";
    		$output .= "<div class='form-inner' id='cancelarea'>";
	    		$output .= "<h2><i class='fa fa-ban' aria-hidden='true'></i> Cancel Subscription</h2>";
	    		$output .= "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>";
	    		$output .= "<div class='clearfix'></div>";
	    		$output .= "<p class='quiet'>Are you sure you want to <strong>CANCEL THIS SUBSCRIPTION IMMEDIATELY</strong>? This is a destructive process which cannot be undone.</p>";
	    		$output .= "<a href='#' class='btn btn-danger cancel_confirm'><i class='fa fa-ban' aria-hidden='true'></i> Cancel Subscription</a>";
    		$output .= "</div>";
			$output .= "<hr/>";
    	$output .= "</div>";

    	$output .= "<div class='free_area'>";
    		$output .= "<div class='form-inner' id='freearea'>";
	    		$output .= "<h2><i class='fa fa-ellipsis-h' aria-hidden='true'></i> Give Free Subscription</h2>";
	    		$output .= "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>";
	    		$output .= "<div class='clearfix'></div>";
	    		$output .= "<p class='quiet'>To give a free term to a subscriber, please choose from the plans available below.</p><p><strong>Please Note</strong> that all free accounts are set to not autorenew as no payment was originally set to repeat the payment from.</p>";
    			$plans = get_field( 'plans', 'option' );
    			foreach($plans as $plan){
    				$output .= "<div class='plan'>
						<h5>".$plan['plan_duration']."</h5>
						<p><small>".$plan['plan_text']."</small></p>
						<a href='#' class='btn btn-warning givefreesubscription_trigger' data-user_id='".$this->subscription_user->ID."' data-term='".$plan['plan_duration']."'>Give ".$plan['plan_duration']." Free</a>
    				</div>";
    			}
    			$output .= "<div class='clearfix'></div>";

    		$output .= "</div>";
    		$output .= "<div class='clearfix'></div>";
			$output .= "<hr/>";
    	$output .= "</div>";

    	$output .= $this->get_email_markup("details");
		$output .= "<form action='#' method='post' class='returnform'>
			<input type='submit' name='submitback' value='Back to Subscription List' class='btn btn-danger'>
			<div class='clearfix'></div>
		</form>";
		$output .= "<div class='clearfix'></div>";		
    	return $output;
	}

	private function get_email_markup($location){
		$sqlt = "SELECT * FROM ".$this->table_prefix."email_templates";
		$rest = $this->wpdb->get_results($sqlt);
		$output = "<form action='#' method='post' class='email_form' id='email_form'>
			<div class='form-inner'>
		    	<h2><i class='fa fa-envelope-o' aria-hidden='true'></i> Send Test Email</h2>
		    	<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>
		    	<table style='width:100%;'>
					<tr>
						<td>
						<b><label for='log'>Load Template</label></b>
						</td>
						<td>
							<input type='hidden' name='email_to' id='email_to' value='".$this->subscription_user->user_email."'>
							<select name='template_id' id='template_id' data-first_name='".$first_name."' 
								data-subscription_term='";
								if(isset($current_subscription)){ 
									$output .= ucwords($this->current_subscription->current_term);
								}
								$output .="'
								data-subscription_amount='";
								if(isset($current_subscription)){ 
									$output .= $this->current_subscription->amount;
								}
								$output .= "'
								data-subscription_renewal_date='";
								if(isset($current_subscription)){ 
									$output .= date("d-m-Y", strtotime($this->current_subscription->date_to_process));
								}
								$output .="'
								data-password='";
								if(isset($current_subscription)){ 
									$output .= $this->current_subscription->vpn_password;
								}
								$output .="'>
								<option value=''>Please select a Template</option>";
								foreach($rest as $t){
									$output .= "<option value='".$t->id."'>".$t->name."</option>";
								}
					        $output .= "</select> 
						</td>
					</tr>
					<tr>
						<td style='width:250px; vertical-align: text-top;'>
							<b><label for='email_subject'>Subject</label></b>
						</td>
						<td>
							<input type='text' name='email_subject' id='subject' style='width:100%;'>
						</td>						
					</tr>
					<tr>
						<td style='width:250px; vertical-align: text-top;'>
							<b><label for='log'>Message Content</label></b>
						</td>
						<td>
							<textarea name='test_email_body' class='txtMessage' id='txtMessage' style='width:100%; height:300px;'></textarea>
						</td>
					</tr>
				</table>";	
				
				if($location == "main"){
					$output .= "<input type='submit' name='submit_email' value='Send Email' class='btn btn-success'>";
				} else {
					$output .= "<input type='hidden' name='user_id' value='".$this->subscription_user->ID."'>";
					$output .= "<input type='submit' name='submit_email' value='Send Email' class='btn btn-success submit_email_home'>";
				}	
				$output .= "</div>
			<hr/>
		</form>";
		return $output;
	}

	public function ajax_apply_coupon_code() {

		$voucher_code    = strtoupper( $_POST['couponCode'] );

		$result         = '';
		$invalid_reason = '';
		if ( empty( $voucher_code ) ) {
			$invalid_reason = esc_html__( 'You must enter a value for coupon code.', 'gravityformscoupons' );
			$result         = array( 'is_valid' => false, 'invalid_reason' => $invalid_reason );
			die( GFCommon::json_encode( $result ) );
		}

		$form_id               = intval( $_POST['formId'] );
		$existing_coupon_codes = $_POST['existing_coupons'];
		$total                 = $_POST['total'];

		$user = wp_get_current_user();

		$voucher = $this->get_single_voucher_by_code($voucher_code);

		if(!$voucher){
			$invalid_reason = esc_html__( 'Invalid coupon.', 'gravityformscoupons' );
			$result         = array( 'is_valid' => false, 'invalid_reason' => $invalid_reason );
			die( GFCommon::json_encode( $result ) );
		}
		$valid = false;
		if($voucher->type == "time_bomb"){
			$today = strtotime(date("Y-m-d"));
			$expire = strtotime(date("Y-m-d", strtotime($voucher->expire_date)));
			if($expire <= $today){
				$valid = false;
				$invalid_reason = "This Voucher has expired on ".date("d-m-Y", strtotime($voucher->expire_date)).".";
				$result         = array( 'is_valid' => false, 'invalid_reason' => $invalid_reason );
				die( GFCommon::json_encode( $result ) );
			} else {
				$valid = true;
			}			
		} else if($voucher->type == "single_usage"){
			if($voucher->current_usage == 0){
				$valid = true;
			} else {
				$valid = false;
				$invalid_reason = "Voucher has already been used.";
				$result         = array( 'is_valid' => false, 'invalid_reason' => $invalid_reason );
				die( GFCommon::json_encode( $result ) );
			}
		} else if($voucher->type == "single_user_usage"){
			$user = wp_get_current_user();
			if($user->ID == $voucher->user_id){
				$valid = true;
			} else {
				$valid = false;
				$invalid_reason = "This voucher is not available to your user account.";
				$result         = array( 'is_valid' => false, 'invalid_reason' => $invalid_reason );
				die( GFCommon::json_encode( $result ) );
			}
		} else if($voucher->type == "usage_limited"){
			if($voucher->current_usage < $voucher->max_usage){
				$valid = true;
			} else {
				$valid = false;
				$invalid_reason = "Voucher has been used too many times.";
				$result         = array( 'is_valid' => false, 'invalid_reason' => $invalid_reason );
				die( GFCommon::json_encode( $result ) );
			}
		}

		$couponss[ $voucher->code ] = array(
			'amount'      => $voucher->discount_amount,
			'name'        => $voucher->name,
			'type'        => $voucher->discount_modifier,
			'code'        => $voucher->code,
			'usage_count' => $voucher->current_usage,
		);

		$result = array(
			'is_valid'       => $valid,
			'coupons'        => $couponss,
			'invalid_reason' => $invalid_reason,
			'coupon_code'    => $voucher_code,
		);

		die( GFCommon::json_encode( $result ) );
	}

	public function wp_subscription_paypal_post_ipn($ipn_post, $entry, $feed, $cancel){
		do_action( 'gform_sagepay_subscription_payment_success', $entry, $result[0]->id, $result[0]->Amount, false, $feed );
	}
}