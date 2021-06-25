<?php
/*
Plugin Name: Vendor
Description: Plugin for vendor
Version: 1
Author: naantam.in
Author URI: naantam.in
*/
// function to create the DB / Options / Defaults					
function sync_vendor_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "sync_vendor";
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
            `id` bigint(20) NOT NULL AUTO_INCREMENT,
            `full_name` varchar(255) CHARACTER SET utf8 NOT NULL,
			`address` longtext CHARACTER SET utf8 NOT NULL,
			`contact_number` varchar(255) CHARACTER SET utf8 NOT NULL,
			`email` varchar(255) CHARACTER SET utf8 NOT NULL,
			`tin_number` varchar(255) CHARACTER SET utf8 NOT NULL,
			`pan_number` varchar(255) CHARACTER SET utf8 NOT NULL,
			`username` varchar(255) CHARACTER SET utf8 NOT NULL,
			`status` ENUM('Active', 'Inactive') DEFAULT 'Active',
            PRIMARY KEY (`id`)
          ) $charset_collate; ";
    $sql_user_user_type = "ALTER TABLE `".$wpdb->prefix ."users` ADD `user_type` ENUM('Vendor','Administrator') DEFAULT 'Administrator';";
	$sql_user_user_type_id = "ALTER TABLE `".$wpdb->prefix ."users` ADD `user_type_id` bigint(20) NOT NULL;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta($sql);
	$wpdb->query($sql_user_user_type);
	$wpdb->query($sql_user_user_type_id);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'sync_vendor_install');

//menu items
add_action('admin_menu','sync_vendor_modifymenu');
function sync_vendor_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Vendor', //page title
	'Vendor', //menu title
	'manage_options', //capabilities
	'sync_vendor_list', //menu slug
	'sync_vendor_list' //function
	);
	
	//this is a submenu
	add_submenu_page('sync_vendor_list', //parent slug
	'Add New Vendor', //page title
	'Add New Vendor', //menu title
	'manage_options', //capability
	'sync_vendor_create', //menu slug
	'sync_vendor_create'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Vendor', //page title
	'Update', //menu title
	'manage_options', //capability
	'sync_vendor_update', //menu slug
	'sync_vendor_update'); //function
}
define('tatroomsvendor', plugin_dir_path(__FILE__));
require_once(tatroomsvendor . 'sync_vendor-list.php');
require_once(tatroomsvendor . 'sync_vendor-create.php');
require_once(tatroomsvendor . 'sync_vendor-update.php');

//Remove Admin Backend Menus for all users except admin
function remove_menus () {
    error_reporting(0);
    global $wpdb;
    global $user_ID;
    
    $sync_vendor = $wpdb->get_results($wpdb->prepare("SELECT * from `".$wpdb->prefix."users` where id=%s", $user_ID));
    
    $user_type = $sync_vendor[0]->user_type;
        
    if($user_type=='Vendor'){
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        //$('#menu-dashboard').remove();
        $('#menu-dashboard > ul').remove();
        $('#menu-posts').remove();
        $('#menu-media').remove();
        $('#menu-pages').remove();
        $('#menu-comments').remove();
        //$('#menu-posts-hotellisting').remove();
        $('#menu-posts-templatio').remove();
        $('#toplevel_page_flamingo').remove();
        $('#toplevel_page_wpcf7').remove();
        $('#menu-appearance').remove();
        $('#menu-plugins').remove();
        $('#menu-users').remove();
        $('#menu-tools').remove();
        $('#menu-settings').remove();
        $('#toplevel_page_edit-post_type-acf').remove();
        $('#toplevel_page_wp-mail-smtp').remove();
        $('#toplevel_page_nlt_customer_list').remove();
        //$('#toplevel_page_nlt_hotelroomlisting_list').remove();
        $('#toplevel_page_sync_vendor_list').remove();
        $('#toplevel_page_acf-options').remove();
        $('#wp-admin-bar-updates').remove();
        $('#wp-admin-bar-wp-logo').remove();
        $('#wp-admin-bar-comments').remove();
        $('#wp-admin-bar-new-content').remove();
        $('#show-settings-link').remove();
        $('#contextual-help-link').remove();
        $('.update-nag').remove();
        $('.notice-error').remove();
        $('#dashboard-widgets-wrap').remove();
        $('#wpfooter').remove();
        $('#toplevel_page_sync_corporate_list').remove();
        $('#toplevel_page_sync_travelagency_list').remove();
    });
</script>
<?php
        
    }
}
add_action('admin_menu', 'remove_menus');

//Frontend Vendor Registration Form
function vendor_registration_form(){
	ob_start();
	if(isset($_SESSION['user'])){
	    $_SESSION['message'] = 'You are logged in. Logout first to do a new registration.';
	    wp_redirect(get_site_url().'/message/');
	}
	if (isset($_POST['register'])) {
		$full_name = $_POST["full_name"];
		$address = $_POST["address"];
		$contact_number = $_POST["contact_number"];
		$email = $_POST["email"];
		$tin_number = $_POST["tin_number"];
		$pan_number = $_POST["pan_number"];
		$username = $_POST["email"];
		$password = $_POST["password"];
		$confirm_password = $_POST["confirm_password"];
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "sync_vendor";
		
		$rows_email = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `email`='".$email."'");
		
		$rows_email_count = $rows_email[0]->n;
		
		$rows_tin_number = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `tin_number`='".$tin_number."'");
		
		$rows_tin_number_count = $rows_tin_number[0]->n;
		
		$rows_pan_number = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `pan_number`='".$pan_number."'");
		
		$rows_pan_number_count = $rows_pan_number[0]->n;
		
		
		if($_POST['full_name']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Full Name.</div><br>';				
		}
		else if($_POST['address']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Full Address.</div><br>';				
		}
		else if($_POST['contact_number']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Contact Number.</div><br>';				
		}
		else if($rows_email_count>0)
		{
		    $message1='<div style="color: red !important;">Email already exists.</div><br>';	
		}
		else if($_POST['email']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Email.</div><br>';				
		}
		else if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))				
		{					
			$message1='<div style="color: red !important;">Enter your valid email address.</div><br>';				
		}
		else if($rows_tin_number_count>0)
		{
		    $message1='<div style="color: red !important;">Tin number already exists.</div><br>';	
		}
		else if($_POST['tin_number']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Tin Number.</div><br>';				
		}
		else if($rows_pan_number_count>0)
		{
		    $message1='<div style="color: red !important;">Pan number already exists.</div><br>';	
		}
		else if($_POST['pan_number']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Pan Number.</div><br>';				
		}
		else if($_POST['password']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Password.</div><br>';				
		}
		else if($_POST['confirm_password']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Confirm Password.</div><br>';				
		}
		else if($_POST['confirm_password']!=$_POST['password'])				
		{					
			$message1='<div style="color: red !important;">Password and confirm password should match.</div><br>';
		}
		else
		{
			$wpdb->insert(
					$table_name, //table
					array(
					'full_name' => $full_name,
					'address' => $address,
					'contact_number' => $contact_number,
					'email' => $email,
					'tin_number' => $tin_number,
					'pan_number' => $pan_number,
					'username' => $username,
					'status' => 'Active'
					), //data
					array(
					'%s', 
					'%s', 
					'%s',
					'%s', 
					'%s', 
					'%s',
					'%s', 
					'%s'
					) //data format			
			);
			$user_type_id = $wpdb->insert_id;
			$userdata = array(
					'user_login' => $email,
					'user_pass' => $password,
					'user_nicename' => $full_name,
					'user_email' => $email,
					'user_url' => '',
					'user_registered' => date('Y-m-d H:i:s'),
					'user_activation_key' => '',
					'display_name' => $full_name
					);
			$user_id = wp_insert_user($userdata);
			$wpdb->update(
                $wpdb->prefix . 'users', //table
                array(
					'user_type' => 'Vendor',
					'user_type_id' => $user_type_id
					), //data
                array('ID' => $user_id), //where
                array(
					'%s', 
					'%s'
					), //data format
                array('%s') //where format
			);
			$wpdb->update(
                $wpdb->prefix . 'usermeta', //table
                array(
					'meta_value' => 'a:1:{s:13:"administrator";b:1;}'
					), //data
                array(
                    'user_id' => $user_id,
                    'meta_key' => 'wp_capabilities'
                    ), //where
                array(
					'%s'
					), //data format
                array(
                    '%s',
                    '%s'
                    ) //where format
			);
			$body = '
			<pre>
			Dear '.$full_name.'
			      You are successfully registered with Tatrooms with the following credentials.
		    Username/email : '.$email.'
			Password : '.$password.'
			
			Hope to service you better!
			
		    Thanks & regards,
			Tatrooms Admin Team.
			</pre>
			';
			wp_mail($email,'Successful Tatrooms registration',$body);
			$message1="<div style='color: green !important;'>You are registered successfully.</div>";
			$full_name = '';
    		$address = '';
    		$contact_number = '';
    		$email = '';
    		$aadhar_number = '';
    		$pan_number = '';
    		$username = '';
    		$password = '';
    		$confirm_password = '';
		}
    }else{
		$full_name = '';
		$address = '';
		$contact_number = '';
		$email = '';
		$aadhar_number = '';
		$pan_number = '';
		$username = '';
		$password = '';
		$confirm_password = '';
		$message1 = '';
	}
?>
<section id="contact">
	<div class="container">
	  <h2 class="h2 ac">Vendor Registration</h2>
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
					Full name
				</div>
				<div class="form-group">
					<input type="text" name="full_name" value="<?php echo $full_name; ?>" class="form-control">
				</div>
				<div class="form-group">
					Address
				</div>
				<div class="form-group">
					<input type="text" name="address" value="<?php echo $address; ?>" class="form-control">
				</div>
				<div class="form-group">
					Contact Number
				</div>
				<div class="form-group">
					<input type="text" name="contact_number" value="<?php echo $contact_number; ?>" class="form-control">
				</div>
				<div class="form-group">
					Email
				</div>
				<div class="form-group">
					<input type="text" name="email" value="<?php echo $email; ?>" class="form-control">
				</div>
				<div class="form-group">
					Tin Number
				</div>
				<div class="form-group">
					<input type="text" name="aadhar_number" value="<?php echo $aadhar_number; ?>" class="form-control">
				</div>
				<div class="form-group">
					Pan Number
				</div>
				<div class="form-group">
					<input type="text" name="pan_number" value="<?php echo $pan_number; ?>" class="form-control">
				</div>
				<div class="form-group">
					Password
				</div>
				<div class="form-group">
					<input type="password" name="password" value="<?php echo $password; ?>" class="form-control">
				</div>
				<div class="form-group">
					Confirm Password
				</div>
				<div class="form-group">
					<input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>" class="form-control">
				</div>
				<div class="form-group">
					<input name="register" type="submit" value="Register" class="btn btn_prm">
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

add_shortcode('cleopatra_vendor_registration', 'vendor_registration_form');
