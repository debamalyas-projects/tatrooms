<?php
/*
Plugin Name: Money Collection
Description: Plugin for  Money Collection
Version: 1
Author: naantam.in
Author URI: naantam.in
*/
// function to create the DB / Options / Defaults					
function sync_money_install() {

    global $wpdb;

    /*$table_name = $wpdb->prefix . "sync_categories";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `category` varchar(255) CHARACTER SET utf8 NOT NULL,
            `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
            PRIMARY KEY (`id`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);*/
}

// run the install scripts upon plugin activation*/
register_activation_hook(__FILE__, 'sync_money_install');

//menu items
add_action('admin_menu','sync_money_modifymenu');
function sync_money_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Money Collection', //page title
	'Money Collection', //menu title
	'manage_options', //capabilities
	'sync_money_list', //menu slug
	'sync_money_list' //function
	);
	
	//this is a submenu
	add_submenu_page('sync_money_list', //parent slug
	'Add Money Collection Agent', //page title
	'Add Money Collection Agent', //menu title
	'manage_options', //capability
	'sync_money_create', //menu slug
	'sync_money_create'); //function
}
define('tatroomsmoneycollection', plugin_dir_path(__FILE__));
require_once(tatroomsmoneycollection . 'sync_money-list.php');
require_once(tatroomsmoneycollection . 'sync_money-create.php');