<?php
/*
Plugin Name: Progoti Loan
Description: Plugin for Progoti Loan
Version: 1
Author: naantam.in
Author URI: naantam.in
*/
// function to create the DB / Options / Defaults					
function sync_loan_install() {

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
register_activation_hook(__FILE__, 'sync_loan_install');

//menu items
add_action('admin_menu','sync_loan_modifymenu');
function sync_loan_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Progoti Loan', //page title
	'Progoti Loan', //menu title
	'manage_options', //capabilities
	'sync_loan_list', //menu slug
	'sync_loan_list' //function
	);
	
	//this is a submenu
	add_submenu_page('sync_loan_list', //parent slug
	'Add Progoti Loan', //page title
	'Add Progoti Loan', //menu title
	'manage_options', //capability
	'sync_loan_create', //menu slug
	'sync_loan_create'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Loan', //page title
	'Update', //menu title
	'manage_options', //capability
	'sync_loan_update', //menu slug
	'sync_loan_update'); //function
}
define('tatroomsprogotiloan', plugin_dir_path(__FILE__));
require_once(tatroomsprogotiloan . 'sync_loan-list.php');
require_once(tatroomsprogotiloan . 'sync_loan-create.php');
require_once(tatroomsprogotiloan . 'sync_loan-update.php');