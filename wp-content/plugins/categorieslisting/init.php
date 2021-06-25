<?php
/*
Plugin Name: Category Listing
Description: Plugin for category listing
Version: 1
Author: naantam.in
Author URI: naantam.in
*/
// function to create the DB / Options / Defaults					
function sync_categories_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "sync_categories";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `category` varchar(255) CHARACTER SET utf8 NOT NULL,
            `status` ENUM('Active', 'Inactive') DEFAULT 'Active',
            PRIMARY KEY (`id`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'sync_categories_install');

//menu items
add_action('admin_menu','sync_categories_modifymenu');
function sync_categories_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Categories', //page title
	'Categories', //menu title
	'manage_options', //capabilities
	'sync_categories_list', //menu slug
	'sync_categories_list' //function
	);
	
	//this is a submenu
	add_submenu_page('sync_categories_list', //parent slug
	'Add Categories', //page title
	'Add Categories', //menu title
	'manage_options', //capability
	'sync_categories_create', //menu slug
	'sync_categories_create'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Categories', //page title
	'Update', //menu title
	'manage_options', //capability
	'sync_categories_update', //menu slug
	'sync_categories_update'); //function
}
define('tatroomscategories', plugin_dir_path(__FILE__));
require_once(tatroomscategories . 'sync_categories-list.php');
require_once(tatroomscategories . 'sync_categories-create.php');
require_once(tatroomscategories . 'sync_categories-update.php');