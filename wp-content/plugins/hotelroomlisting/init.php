<?php
/*
Plugin Name: Hotel Room Listing
Description: Plugin for hotel room listing
Version: 1
Author: naantam.in
Author URI: naantam.in
*/
// function to create the DB / Options / Defaults					
function nlt_hotelroomlisting_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "nlt_hotel_room_listing";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `room_name` varchar(255) CHARACTER SET utf8 NOT NULL,
			`room_description` longtext CHARACTER SET utf8 NOT NULL,
			`hotel_id` varchar(255) CHARACTER SET utf8 NOT NULL,
			`room_accomodation` varchar(255) CHARACTER SET utf8 NOT NULL,
			`status` ENUM('Active', 'Inactive') DEFAULT 'Active',
			`room_price` varchar(255) CHARACTER SET utf8 NOT NULL,
			`discount` varchar(255) CHARACTER SET utf8 NOT NULL,
			`room_category_id` bigint(20),
			FOREIGN KEY (room_category_id) REFERENCES wp_sync_categories(id),
            PRIMARY KEY (`id`)
          ) $charset_collate; ";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'nlt_hotelroomlisting_install');

//menu items
add_action('admin_menu','nlt_hotelroomlisting_modifymenu');
function nlt_hotelroomlisting_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Hotel Rooms', //page title
	'Hotel Rooms', //menu title
	'manage_options', //capabilities
	'nlt_hotelroomlisting_list', //menu slug
	'nlt_hotelroomlisting_list' //function
	);
	
	//this is a submenu
	add_submenu_page('nlt_hotelroomlisting_list', //parent slug
	'Add New hotel room', //page title
	'Add New hotel room', //menu title
	'manage_options', //capability
	'nlt_hotelroomlisting_create', //menu slug
	'nlt_hotelroomlisting_create'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update hotel room', //page title
	'Update', //menu title
	'manage_options', //capability
	'nlt_hotelroomlisting_update', //menu slug
	'nlt_hotelroomlisting_update'); //function
}
define('tatroomshotelroomlisting', plugin_dir_path(__FILE__));
require_once(tatroomshotelroomlisting . 'nlt_hotelroomlisting-list.php');
require_once(tatroomshotelroomlisting . 'nlt_hotelroomlisting-create.php');
require_once(tatroomshotelroomlisting . 'nlt_hotelroomlisting-update.php');