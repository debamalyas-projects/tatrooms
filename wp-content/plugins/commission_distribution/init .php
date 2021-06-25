<?php
/*
Plugin Name: commission_distribution
Description: Plugin for commission_distribution
Version: 1
Author: syncxini.com
Author URI: syncxini.com
*/
// function to create the DB / Options / Defaults					
function sync_commission_distribution_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "order_claim_part";

	/*$query = $wpdb->get_results("SELECT * from $table_name WHERE `user_id`='".$_SESSION['user']['user_id']."'"
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `full_name` varchar(255) CHARACTER SET utf8 NOT NULL,
			`address` longtext CHARACTER SET utf8 NOT NULL,
			`contact_number` varchar(255) CHARACTER SET utf8 NOT NULL,
			`email` varchar(255) CHARACTER SET utf8 NOT NULL,
			`aadhar_number` varchar(255) CHARACTER SET utf8 NOT NULL,
			`pan_number` varchar(255) CHARACTER SET utf8 NOT NULL,
			`username` varchar(255) CHARACTER SET utf8 NOT NULL,
			`password` varchar(255) CHARACTER SET utf8 NOT NULL,
			`status` ENUM('Active', 'Inactive') DEFAULT 'Active',
            PRIMARY KEY (`id`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);*/
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'sync_commission_distribution_install');

//menu items
add_action('admin_menu','sync_commission_distribution_modifymenu');
function sync_commission_distribution_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('commission', //page title
	'Commission Distribution', //menu title
	'manage_options', //capabilities
	'sync_listing', //menu slug
	'sync_listing' //function
	);	
}
define('tatroomscommissiondistribution', plugin_dir_path(__FILE__));
require_once(tatroomscommissiondistribution . 'sync_listing.php');

