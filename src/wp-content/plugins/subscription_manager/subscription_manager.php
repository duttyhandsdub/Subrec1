<?php
/*
Plugin Name: WP Subscription Manager
Plugin URI: http://www.accentdesign.co.uk/
Description: Subscription Manager Plugin for Gravity Forms SagePay Payment Gateway
Version: 0.0.1
Author: Nick Thompson
Author URI: http://www.accentdesign.co.uk

------------------------------------------------------------------------

Copyright 2009-2016 Accent Design Group Ltd.

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
*/

define( 'SUBSCRIPTION_MANAGER_VERSION', '1.0.0' );
define( 'SUBSCRIPTION_MANAGER_PATH', plugin_dir_path(__FILE__));


GFForms::include_feed_addon_framework();

if ( class_exists( 'GF_Field' ) ) {
	require_once( 'class_gf_field_coupon.php' );
}

add_action('init', 'subscription_manager');

function subscription_manager(){
	include('classes/wp_subscription_manager.php');
	$WP_Subscription_Manger = new WP_Subscription_Manger();
	include('admin/admin_pages.php');
	//get all options for comment notification
	$options = get_option('wp_subscription_manager');
	//check for value of checkbox, if it does not exist then we know options is new and default.
	if(!$options['wp_subscriptions_manager_max_rows']){
		//set up options table entry
		$options = array(
			'wp_subscriptions_manager_log'=>true, 
			'wp_subscriptions_manager_max_rows'=>''
			);
		add_option('wp_subscription_manager', $options);
	} else {
		//do nothing as is already set up
	}
}

add_action( 'admin_enqueue_scripts', 'wp_subscription_manager_enqueue_admin_styles');

function wp_subscription_manager_enqueue_admin_styles(){

	wp_enqueue_style( 'prefix-style', plugins_url('/admin/admin_styles.css', __FILE__) );
	wp_enqueue_script( 'admin-js', plugins_url('/admin/admin_scripts.js', __FILE__), array('jquery'), '1.0.0', true  );
	
}

add_action( 'wp_enqueue_scripts', 'wp_subscription_manager_enqueue_public_scripts');

function wp_subscription_manager_enqueue_public_scripts(){
	wp_enqueue_script( 'gform_vouchers-js', plugins_url('/admin/gform_vouchers.js', __FILE__), array('jquery'), '1.0.0', true  );
}

/**
 * return the wp_subscription_manager class instance.
 * 
 * @return wp_subscription_manager class instance
 */
function wp_subscription_manager(){
	
	return WP_Subscription_Manger::get_instance();
}

function wp_subscription_manager_register_session(){
    if( !session_id() ){
        session_start();
    }
}
add_action('init','wp_subscription_manager_register_session');

function wp_subscription_manager_install_options(){
	global $wpdb;
    $sql = "";
    global $table_prefix;
    if(!isset($table_prefix)){
        $table_prefix = "wp_";
    }
    if($wpdb->get_var("show tables like '".$table_prefix."wp_subscription_vouchers'") != $table_prefix.'wp_subscription_vouchers')
    {
    	$sql .= "CREATE TABLE `".$table_prefix."wp_subscription_vouchers` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `type` varchar(255) NOT NULL,
                    `active` tinyint(1) DEFAULT 1,
					`expire_date` datetime DEFAULT NULL,
					`single_usage` tinyint(1) DEFAULT 1,
					`user_id` int(11) DEFAULT NULL,
					`max_usage` int(11) DEFAULT NULL,
					`current_usage` int(11) DEFAULT NULL,
					`code` varchar(255) NOT NULL,
					`name` varchar(255) NOT NULL,
                    PRIMARY KEY (`id`)
                    );";
    }
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}
register_activation_hook(__FILE__,'wp_subscription_manager_install_options');

