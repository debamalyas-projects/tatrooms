<?php
/*
Plugin Name: cart_settings
Description: Plugin for cart_settings
Version: 1
Author: syncxini.com
Author URI: syncxini.com
*/
// function to create the DB / Options / Defaults					
function sync_cart_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "settings";

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
register_activation_hook(__FILE__, 'sync_cart_install');

//menu items
add_action('admin_menu','sync_cart_modifymenu');
function sync_cart_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('cart', //page title
	'Cart Settings', //menu title
	'manage_options', //capabilities
	'sync_update', //menu slug
	'sync_update' //function
	);	
}
define('tatroomscartsettings', plugin_dir_path(__FILE__));
require_once(tatroomscartsettings . 'sync_update.php');

