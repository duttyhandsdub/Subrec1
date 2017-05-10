<?php
/*
    
    Plugin Name: Accent URL
    Plugin URI: 
    Description: Update URL's in the DB when switching between domains
    Version: 1.0
    Author: Taylor
    Author URI: 
    License: GPL

*   
*/
function set_site_url(){
    $current_site   = get_option('siteurl');
    $current_home   = get_option('home');
	if (isset($_SERVER['HTTPS']) && $_SERVER["HTTPS"] == "on")
	{
		$connection = "https://";
		$port = '';
	}
	else{
		$connection = "http://";
		$port = ($_SERVER['SERVER_PORT'] != 80 ? ':' . $_SERVER['SERVER_PORT'] : '');
	}

    $server_name    = $connection . $_SERVER["SERVER_NAME"] . $port;
    $this_url       = $server_name . $_SERVER["REQUEST_URI"];

    if ($current_site != $server_name || $current_home != $server_name){
        global $wpdb;
        $wpdb->query("UPDATE " . $wpdb->prefix . "options SET option_value = REPLACE(option_value, '$current_site', '$server_name')");
        $wpdb->query("UPDATE " . $wpdb->prefix . "postmeta SET meta_value = REPLACE(meta_value, '$current_site', '$server_name')");
        $wpdb->query("UPDATE " . $wpdb->prefix . "posts SET post_content = REPLACE(post_content, '$current_site', '$server_name')");
        $wpdb->query("UPDATE " . $wpdb->prefix . "posts SET post_excerpt = REPLACE(post_excerpt, '$current_site', '$server_name')");
        $wpdb->query("UPDATE " . $wpdb->prefix . "posts SET guid = REPLACE(guid, '$current_site', '$server_name')");
        wp_redirect($this_url);
        die();
    }
}
add_action('init', 'set_site_url');

?>