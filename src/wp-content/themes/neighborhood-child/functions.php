<?php
	
	/*
	*
	*	Neighborhood Functions - Child Theme
	*	------------------------------------------------
	*	These functions will override the parent theme
	*	functions. We have provided some examples below.
	*
	*
	*/
	
	/* LOAD PARENT THEME STYLES
	================================================== */
	function neighborhood_child_enqueue_styles() {
	    wp_enqueue_style( 'neighborhood-parent-style', get_template_directory_uri() . '/style.css' );
	
	}
	add_action( 'wp_enqueue_scripts', 'neighborhood_child_enqueue_styles' );


	/* NEW THEME OPTIONS SECTION
	================================================== */
	// function new_section($sections) {
	//     //$sections = array();
	//     $sections[] = array(
	//         'title' => __('A Section added by hook', 'swift-framework-admin'),
	//         'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'swift-framework-admin'),
	//         // Redux ships with the glyphicons free icon pack, included in the options folder.
	//         // Feel free to use them, add your own icons, or leave this blank for the default.
	//         'icon' => trailingslashit(get_template_directory_uri()) . 'options/img/icons/glyphicons_062_attach.png',
	//         // Leave this as a blank section, no options just some intro text set above.
	//         'fields' => array()
	//     );
	
	//     return $sections;
	// }
	// add_filter('redux-opts-sections-sf_neighborhood_options', 'new_section');

	/* ========================================================================================================================
	
	Theme specific settings

	Uncomment register_nav_menus to enable a single menu with the title of "Primary Navigation" in your theme
	
	======================================================================================================================== */

	add_theme_support( 'post-thumbnails' );


	function register_my_menus() {
		register_nav_menus( array(
			'main-nav' => 'Main Navigation',
			'footer-nav' => 'Footer Navigation'	
		));
	}
	add_action( 'init', 'register_my_menus' );

	
	/* ========================================================================================================================
	
	Custom Image Size
	
	======================================================================================================================== */

	/*add_image_size( 'homepage-tile', 250, 150, array( 'center', 'center' ));  // Used on homepage

	add_filter( 'image_size_names_choose', 'custom_image_sizes');

	function custom_image_sizes( $sizes ) {
		return array_merge( $sizes, array(
			'homepage-tile' => __( 'Homepage Tile 250x150' ),
		));
	}*/


	/* ========================================================================================================================
	
	Custom Post Types - include custom post types and taxonimies here
	
	======================================================================================================================== */

	/* ========================================================================================================================
	
	Enqueue scripts and styles

	======================================================================================================================== */

	function subrec_scripts() {

		if ( is_page_template('page-client_area_my_subscription.php') ) {
			wp_register_script( 'subrec-my_subscription_script', get_template_directory_uri() . '/js/subscription_details.js', array( 'jquery' ) );
			wp_enqueue_script( 'subrec-my_subscription_script' );
		}
	}
	
	add_action( 'wp_enqueue_scripts', 'subrec_scripts' );


	/* ========================================================================================================================
		
	Disable default WordPress jQuery
	
	======================================================================================================================== */

	


	/* ========================================================================================================================
	
	Add options page - requires ACF Pro plugin
	
	======================================================================================================================== */
	
	if( function_exists('acf_add_options_page') ) {
		acf_add_options_page();
	}


	/* ========================================================================================================================
    
    Custom Login Logo
    
    ======================================================================================================================== */

    /* HIDE WP ADMIN BAR UNLESS ADMIN LEVEL USER */
    add_action('after_setup_theme', 'remove_admin_bar');

	function remove_admin_bar() {
		if (!current_user_can('administrator') && !is_admin()) {
		  show_admin_bar(false);
		}
	}

	function get_client_subscription_data()
	{
		$user = wp_get_current_user();
		global $wpdb;
		global $table_prefix;
		$schedule = $wpdb->get_results("SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->id."' ORDER BY id DESC")[0];
		$subscription["schedule"] = $schedule;
		$subscription["user"] = $user;
		$subscription["subscription_status"] = get_user_meta($user->ID, "subscription_status", true);
		$subscription["subscription_term"] = get_user_meta($user->ID, "subscription_term", true);
		$subscription["subscription_end"] = get_user_meta($user->ID, "subscription_end", true);
		$subscription["subscription_password"] = get_user_meta($user->ID, "subscription_password", true);
		return $subscription;
	}

	function check_client_area_permissions()
	{
		if(is_user_logged_in()){
			$today = date("Y-m-d");
			$subscription = get_client_subscription_data();
			if($subscription["subscription_status"] != ""){
				return true;
			} else {
				wp_redirect("/sign-in?not_logged_in=true");
			}
		} else {
			wp_redirect("/sign-in?not_logged_in=true");
		}
	}

	//SAGEPAY GFORM HOOKS
	add_action('gform_sagepay_subscription_cancel_1_week_warning', 'subscription_cancel_1_week_warning', 10, 1);

	function subscription_cancel_1_week_warning($schedule){
		$user = get_user_by('id', $schedule->user_id);
		$first_name = get_user_meta($user->ID, 'first_name', true);
		$vars = array();
		$vars['{first_name}'] = $first_name;
		$vars['{subscription_expiration}'] = date("d-m-Y", strtotime($schedule->date_to_process));
		$vars['{username}'] = $user->user_email;
		send_cancellation_warning_email($user->user_email, $vars);
	}

	add_action('gform_sagepay_subscription_cancel', 'sagepay_subscription_cancel', 10, 1);

	function sagepay_subscription_cancel($schedule){
		global $wpdb;
		global $table_prefix;
		$user = get_user_by('id', $schedule->user_id);
		//remove from the radius tables, send email and remove from the schedule
		update_user_meta($user->ID, "subscription_status", "inactive");
		$today = date("Y-m-d");
		update_user_meta($user->ID, "subscription_end", $today);
		$sql = "DELETE FROM ".$table_prefix."rg_sagepay_schedule WHERE id=".$schedule->id;
		$wpdb->get_results($sql);
		$first_name = get_user_meta($user->ID, 'first_name', true);
		$vars = array();
		$vars['{first_name}'] = $first_name;
		$vars['{subscription_expiration}'] = date("d-m-Y", strtotime($schedule->date_to_process));
		send_cancellation_email($user->user_email, $vars);
	}

	add_action('gform_sagepay_subscription_payment_success', 'sagepay_success_payment', 10, 5);

	function sagepay_success_payment($entry, $transaction_id, $amount, $repeat = false, $feed){
		$form_id = $entry['form_id'];
		$form = GFAPI::get_form($form_id);
		$field_ids = get_subscription_field_ids($form);
		$to = $entry[$field_ids['email_id']];
		$password = $entry[$field_ids['password_id']];
		//get name field details
		$first_name = $entry[$field_ids["name_id"].".3"];
		$last_name = $entry[$field_ids["name_id"].".6"];
		// Place code here to be processed on successful payment.
		set_subscription_active($to, $password, $feed, $amount, $transaction_id, $first_name, $last_name, $repeat);
		update_user_country($user);

		//code to add here for send to radius
	}

	function update_user_country($user){
		$country = ip_info("Visitor", "Country"); 
		$country_code = ip_info("Visitor", "Country Code"); 
		update_user_meta($user->ID, "subscription_country", $country);
		update_user_meta($user->ID, "subscription_country_code", $country_code);
	}

	

	function convertPackageName($value){
		if($value == "monthly"){ return "1M"; }
		if($value == "6monthly"){ return "6M"; }
		if($value == "yearly"){ return "12M"; }
		if($value == "1M"){ return "monthly"; }
		if($value == "6M"){ return "6monthly"; }
		if($value == "12M"){ return "yearly"; }		
	}

	function convertTerm($value){
		if(trim($value) == "1 month"){ return "monthly"; }
		if(trim($value) == "6 months"){ return "6 monthly"; }
		if(trim($value) == "12 months"){ return "yearly"; }		
	}

	function getPaymentHistory(){
		global $wpdb;
		global $table_prefix;
		$user = wp_get_current_user();
		$sql_schedule_select = "SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->ID."' AND processed = 1";
		$res_schedule = $wpdb->get_results($sql_schedule_select);
		return $res_schedule;
	}


	//SUBSCRIPTION AJAX FUNCTIONS
	add_action('wp_ajax_nopriv_set_subscription_cancel_autorenew', 'set_subscription_cancel_autorenew');
	add_action('wp_ajax_set_subscription_cancel_autorenew', 'set_subscription_cancel_autorenew');
	function set_subscription_cancel_autorenew(){
		$user = wp_get_current_user();
		global $wpdb;
		global $table_prefix;
		$sql = "SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->id."' AND processed=0 ORDER BY id DESC";
		$res = $wpdb->get_results($sql)[0];
		$affected = $wpdb->update($table_prefix."rg_sagepay_schedule",array('auto_renew'=>0),array('id'=>$res->id));
		if ( false === $affected ){
			echo 0;
		} else {
			echo 1;
		}
		die;
	}

	add_action('wp_ajax_nopriv_set_subscription_enable_autorenew', 'set_subscription_enable_autorenew');
	add_action('wp_ajax_set_subscription_cancel_enable', 'set_subscription_enable_autorenew');
	function set_subscription_enable_autorenew(){
		$user = wp_get_current_user();
		global $wpdb;
		global $table_prefix;
		$sql = "SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->id."' AND processed=0 ORDER BY id DESC";
		$res = $wpdb->get_results($sql)[0];
		$affected = $wpdb->update($table_prefix."rg_sagepay_schedule",array('auto_renew'=>1),array('id'=>$res->id));
		if ( false === $affected ){
			echo 0;
		} else {
			echo 1;
		}
		die;
	}

	add_action('wp_ajax_nopriv_set_subscription_cancel', 'set_subscription_cancel');
	add_action('wp_ajax_set_subscription_cancel', 'set_subscription_cancel');
	function set_subscription_cancel(){
		$user = wp_get_current_user();
		global $wpdb;
		global $table_prefix;
		$sql = "SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->id."' AND processed=0 ORDER BY id DESC";
		$res = $wpdb->get_results($sql)[0];
		//$affected = $wpdb->delete($table_prefix."rg_sagepay_schedule",array('id'=>$res->id));
		$affected = $wpdb->update($table_prefix."rg_sagepay_schedule",array('status'=>'Cancelled', 'processed'=>1),array('id'=>$res->id));
		update_user_meta($user->ID, "subscription_status", "cancelled");
		$first_name = get_user_meta($user->ID, 'first_name', true);
		$vars = array();
		$vars['{first_name}'] = $first_name;
		$vars['{subscription_expiration}'] = date("d-m-Y", strtotime($res->date_to_process));
		send_cancellation_email($user->user_email, $vars);
		if ( false === $affected ){
			echo 0;
		} else {
			echo $res->date_to_process;
		}
		die;
	}

	add_action('wp_ajax_nopriv_reactivate_auto_renew', 'reactivate_auto_renew');
	add_action('wp_ajax_reactivate_auto_renew', 'reactivate_auto_renew');
	function reactivate_auto_renew(){
		$user = wp_get_current_user();
		global $wpdb;
		global $table_prefix;
		$sql = "SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->id."' AND processed=0 ORDER BY id DESC";
		$res = $wpdb->get_results($sql)[0];
		$affected = $wpdb->update($table_prefix."rg_sagepay_schedule",array('auto_renew'=>1),array('id'=>$res->id));
		if ( false === $affected ){
			echo 0;
		} else {
			echo $res->date_to_process;
		}
		die;
	}

	add_action('wp_ajax_nopriv_upgrade_subscription', 'ajax_upgrade_subscription');
	add_action('wp_ajax_upgrade_subscription', 'ajax_upgrade_subscription');

	function ajax_upgrade_subscription(){
		global $wpdb;
		global $table_prefix;
		$user = wp_get_current_user();
		$new_term = convertTerm($_POST['new_term']);
		$old_term = get_user_meta($user->ID, 'subscription_term', true);
		
		$sql = "SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->id."' AND processed=0 ORDER BY id DESC";
		$res = $wpdb->get_results($sql)[0];
		$affected = $wpdb->update($table_prefix."rg_sagepay_schedule",array('upgrade_term'=>$new_term, 'upgrade_on_expire'=>1),array('id'=>$res->id));
		if ( false === $affected ){
			echo 0;
		} else {
			echo 1;
		}
		//radius updates to do here...
		die;
	}

	add_action('wp_ajax_nopriv_downgrade_subscription', 'ajax_downgrade_subscription');
	add_action('wp_ajax_downgrade_subscription', 'ajax_downgrade_subscription');

	function ajax_downgrade_subscription(){
		global $wpdb;
		global $table_prefix;
		$user = wp_get_current_user();
		$new_term = convertTerm($_POST['new_term']);
		$old_term = get_user_meta($user->ID, 'subscription_term', true);
		
		$sql = "SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->id."' AND processed=0 ORDER BY id DESC";
		$res = $wpdb->get_results($sql)[0];
		$affected = $wpdb->update($table_prefix."rg_sagepay_schedule",array('upgrade_term'=>$new_term, 'upgrade_on_expire'=>1),array('id'=>$res->id));
		if ( false === $affected ){
			echo 0;
		} else {
			echo 1;
		}
		//radius updates to do here...
		die;
	}

	add_action('wp_ajax_nopriv_cancel_upgrade_downgrade_subscription', 'cancel_upgrade_downgrade_subscription');
	add_action('wp_ajax_cancel_upgrade_downgrade_subscription', 'cancel_upgrade_downgrade_subscription');

	function cancel_upgrade_downgrade_subscription(){
		global $wpdb;
		global $table_prefix;
		$user = wp_get_current_user();
		$sql = "SELECT * FROM ".$table_prefix."rg_sagepay_schedule WHERE user_id='".$user->id."' AND processed=0 ORDER BY id DESC";
		$res = $wpdb->get_results($sql)[0];
		$affected = $wpdb->update($table_prefix."rg_sagepay_schedule",array('upgrade_term'=>NULL, 'upgrade_on_expire'=>0),array('id'=>$res->id));
		if ( false === $affected ){
			echo 0;
		} else {
			echo 1;
		}
		//radius updates to do here...
		die;
	}

	add_action('wp_ajax_nopriv_get_user_subscription_term', 'get_user_subscription_term');
	add_action('wp_ajax_get_user_subscription_term', 'get_user_subscription_term');

	function get_user_subscription_term(){
		global $wpdb;
		global $table_prefix;
		$user = wp_get_current_user();
		$term = get_user_meta($user->ID, 'subscription_term', true);
		echo $term;
		die;
	}


	//IP FUNCTIONS
	function ip_info() {
	    $xml = simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=".getRealIpAddr());
		$country_code = "";
		$country = "";
		foreach ($xml as $key => $value)
		{
			if($key == "geoplugin_countryCode"){
				$country_code = (string)$value;
			}
			if($key == "geoplugin_countryName"){
				$country = (string)$value;
			}
		}
		$return = array("country_code"=>$country_code, "country"=>$country);
		return $return;
	}

	function getRealIpAddr()
	{
	    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
	    {
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    }
	    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
	    {
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    }
	    else
	    {
	      $ip=$_SERVER['REMOTE_ADDR'];
	    }
	    //return $ip;
	    //uk "82.71.19.52";
	    return "82.71.19.52";
	    //usa "47.89.41.164";
	}


	//ENABLE ajaxurl VARIABLE FOR FRONT OF SITE
	add_action('wp_head', 'my_ajaxurl');

	function my_ajaxurl() {

	   echo '<script type="text/javascript">
	           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
	         </script>';
	}


	//PASSWORD FUNCTIONS
	function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds')
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

	
	//LOGIN PAGE FUNCTIONS
	function redirect_login_page() {
		$login_page  = home_url( '/sign-in/' );
		$page_viewed = basename($_SERVER['REQUEST_URI']);

		if( $page_viewed == "wp-login.php" && $_SERVER['REQUEST_METHOD'] == 'GET') {
			wp_redirect($login_page);
			exit;
		}
	}
	add_action('init','redirect_login_page');

	function login_failed() {
		$login_page  = home_url( '/login/' );
		wp_redirect( $login_page . '?login=failed' );
		exit;
	}
	add_action( 'wp_login_failed', 'login_failed' );
	 
	function verify_username_password( $user, $username, $password ) {
		$login_page  = home_url( '/sign-in/' );
		if( $username == "" || $password == "" ) {
			wp_redirect( $login_page . "?login=empty" );
			exit;
		}
	}
	add_filter( 'authenticate', 'verify_username_password', 1, 3);

	function logout_page() {
		$login_page  = home_url( '/sign-in/' );
		wp_redirect( $login_page );
		exit;
	}
	add_action('wp_logout','logout_page');


	//MENU FUNCTIONS
	add_filter('wp_nav_menu_items', 'add_login_logout_link', 10, 2); 
	function add_login_logout_link($items, $args) {  
		$items_array = wp_get_nav_menu_items("Main Navigation"); 
		if(is_user_logged_in()){
       		$loginoutlink = "<a href='/client-area-landing/'>Client Area</a>";
       		ob_start();
		    wp_loginout('index.php');
		    $loginoutlink2 = ob_get_contents();
		    ob_end_clean();
       	} else {
       		$loginoutlink = "<a href='/sign-in/'>Sign In</a>";
       		$loginoutlink2 = "";
       	}
       	
		$items = "";
		$i=0;
		$total = count($items_array);
		foreach($items_array as $item){
			$items .= "<li id='menu-item-".$item->ID."' class='menu-item menu-item-type-post_type menu-item-object-page menu-item-24'><a href='".$item->url."'>".$item->title."</a></li>";
			$i++;
		}  
		$items .= '<li>'. $loginoutlink .'</li>';
		if($loginoutlink2 != "") { $items .= '<li>'. $loginoutlink2 .'</li>'; }
		return $items; 
	}


	/* AVATAR STUFF */
	function get_member_avatar(){
		$user = wp_get_current_user();
	    $default = get_stylesheet_directory_uri().'/images/avatar_placeholder.png';

	    $img = get_user_meta($user->ID, 'avatar', true);
	    if($img && $img != $default){
	        return $img;
		}
	    return $default;
	}

	include("email_template_functions.php");
?>