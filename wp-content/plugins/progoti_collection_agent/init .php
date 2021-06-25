<?php
/*
Plugin Name: Progoti Collection Agent
Description: Plugin for progoti collection agent
Version: 1
Author: naantam.in
Author URI: naantam.in
*/
// function to create the DB / Options / Defaults					
function sync_agent_install() {

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

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'sync_agent_install');

//menu items
add_action('admin_menu','sync_agent_modifymenu');
function sync_agent_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Progoti Collection Agent', //page title
	'Progoti Collection Agent', //menu title
	'manage_options', //capabilities
	'sync_agent_list', //menu slug
	'sync_agent_list' //function
	);
	
	//this is a submenu
	add_submenu_page('sync_agent_list', //parent slug
	'Add Agent', //page title
	'Add Agent', //menu title
	'manage_options', //capability
	'sync_agent_create', //menu slug
	'sync_agent_create'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Agent', //page title
	'Update', //menu title
	'manage_options', //capability
	'sync_agent_update', //menu slug
	'sync_agent_update'); //function
}
define('tatroomsagent', plugin_dir_path(__FILE__));
require_once(tatroomsagent . 'sync_agent-list.php');
require_once(tatroomsagent . 'sync_agent-create.php');
require_once(tatroomsagent . 'sync_agent-update.php');

// Progoti agent login form

function progotiagent_login_form(){
	ob_start();
	if(isset($_SESSION['user'])){
	    $_SESSION['message'] = 'You are logged in.';
	    wp_redirect(get_site_url().'/message/');
	}
	if(isset($_POST['login'])){
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "progoti_collection_egent";
		
		$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE `email`='".$email."' AND `password`='".$password."'");
		
		if(count($rows)==0){
			$message1 = 'Username/Email and password doesn\'t match.';
		}else{
			$rows_new = $wpdb->get_results("SELECT * FROM $table_name WHERE `email`='".$email."' AND `password`='".$password."' AND `status`='InActive'");
			
			if(count($rows_new)>0){
				$message1 = 'Username/Email is made inactive. Please contact administrator for further details.';
			}else{
				$_SESSION['user'] = array('user_id'=>$rows[0]->id,'type'=>'progotiagent');
				
				if(isset($_SESSION['link_redirect'])){
					wp_redirect($_SESSION['link_redirect']);
					exit;
				}else{
					wp_redirect(get_site_url());
				}
				$message1 = '';
			}
		}
	}else{
		$email = '';
		$password = '';
		$message1 = '';
	}
?>
<section id="contact">
	<div class="container">
	  <h2 class="h2 ac">Progoti Agent Login</h2>
	  <div class="row">
	      <div class="col-lg-12 col-md-12 col-sm-12 col-12 col contact_form">
	          <?php echo $message1; ?>
	      </div>
	  </div>
	  <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-12 col contact_form">
		 <div role="form">
			<form method="post" action="" enctype="multipart/form-data">
				<div class="form-group">
					Username/Email
				</div>
				<div class="form-group">
					<input type="text" name="email" value="<?php echo $email; ?>" class="form-control">
				</div>
				<div class="form-group">
					Password
				</div>
				<div class="form-group">
					<input type="password" name="password" value="<?php echo $password; ?>" class="form-control">
				</div>
				<div class="form-group">
					<input name="login" type="submit" value="Login" class="btn btn_prm"><br>
				</div>
			</form>
		</div> 
	   </div>
	  </div>    		
	</div>
</section>
<?php
	$html=ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode('cleopatra_progotiagent_login', 'progotiagent_login_form');
// Progoti agent logout
function progotiagent_logout(){
	ob_start();
	if(isset($_SESSION['user'])){
		unset($_SESSION['user']);
		$_SESSION['message'] = 'You are successfully logged out.';
        wp_redirect(get_site_url().'/message/');
	}else{
?>
<section id="contact">
	<div class="container">
	  <h2 class="h2 ac" style="color: red !important; font-weight: bold !important;">You are not authorized to view this page.</h2>
	</div>
</section>
<?php
	}
	$html=ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode('cleopatra_progotiagent_logout', 'progotiagent_logout');