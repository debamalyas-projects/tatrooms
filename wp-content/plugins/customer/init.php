<?php
/*
Plugin Name: Customer
Description: Plugin for customer
Version: 1
Author: naantam.in
Author URI: naantam.in
*/
// function to create the DB / Options / Defaults					
function nlt_customer_install() {

    global $wpdb;

    $table_name = $wpdb->prefix . "nlt_customer";
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
    dbDelta($sql);
}

// run the install scripts upon plugin activation
register_activation_hook(__FILE__, 'nlt_customer_install');

//menu items
add_action('admin_menu','nlt_customer_modifymenu');
function nlt_customer_modifymenu() {
	
	//this is the main item for the menu
	add_menu_page('Customer', //page title
	'Customer', //menu title
	'manage_options', //capabilities
	'nlt_customer_list', //menu slug
	'nlt_customer_list' //function
	);
	
	//this is a submenu
	add_submenu_page('nlt_customer_list', //parent slug
	'Add New Customer', //page title
	'Add New Customer', //menu title
	'manage_options', //capability
	'nlt_customer_create', //menu slug
	'nlt_customer_create'); //function
	
	//this submenu is HIDDEN, however, we need to add it anyways
	add_submenu_page(null, //parent slug
	'Update Customer', //page title
	'Update', //menu title
	'manage_options', //capability
	'nlt_customer_update', //menu slug
	'nlt_customer_update'); //function
}
define('tatroomscustomer', plugin_dir_path(__FILE__));
require_once(tatroomscustomer . 'nlt_customer-list.php');
require_once(tatroomscustomer . 'nlt_customer-create.php');
require_once(tatroomscustomer . 'nlt_customer-update.php');

//Frontend Customer Registration Form
function customer_registration_form(){
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
		$aadhar_number = $_POST["aadhar_number"];
		$pan_number = $_POST["pan_number"];
		$username = $_POST["email"];
		$password = $_POST["password"];
		$confirm_password = $_POST["confirm_password"];
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "nlt_customer";
		
		$rows_email = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `email`='".$email."'");
		
		$rows_email_count = $rows_email[0]->n;
		
		$rows_aadhar_number = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `aadhar_number`='".$aadhar_number."'");
		
		$rows_aadhar_number_count = $rows_aadhar_number[0]->n;
		
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
		else if($rows_aadhar_number_count>0)
		{
		    $message1='<div style="color: red !important;">Aadhar number already exists.</div><br>';	
		}
		else if($_POST['aadhar_number']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Aadhar Number.</div><br>';				
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
					'aadhar_number' => $aadhar_number,
					'pan_number' => $pan_number,
					'username' => $username,
					'password' => $password,
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
					'%s',
					'%s'
					) //data format			
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
	  <h2 class="h2 ac">Customer Registration</h2>
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
					Aadhar Number
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

add_shortcode('cleopatra_customer_registration', 'customer_registration_form');

function customer_account_edit(){
    ob_start();
    if(!isset($_SESSION['user'])){
	    $_SESSION['message'] = 'You are not logged in.';
	    wp_redirect(get_site_url().'/message/');
	}
	global $wpdb;
	$table_name = $wpdb->prefix . "nlt_customer";
	$id = $_SESSION['user']['user_id'];
//update
    if (isset($_POST['update'])) {
		$full_name = $_POST["full_name"];
		$address = $_POST["address"];
		$contact_number = $_POST["contact_number"];
		$email = $_POST["email"];
		$aadhar_number = $_POST["aadhar_number"];
		$pan_number = $_POST["pan_number"];
		
		$rows_email = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `email`='".$email."' AND `id`!='".$id."'");
		
		$rows_email_count = $rows_email[0]->n;
		
		$rows_aadhar_number = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `aadhar_number`='".$aadhar_number."' AND `id`!='".$id."'");
		
		$rows_aadhar_number_count = $rows_aadhar_number[0]->n;
		
		$rows_pan_number = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `pan_number`='".$pan_number."' AND `id`!='".$id."'");
		
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
		    $message1='<div style="color: red !important;">Email already associated with another account.</div><br>';	
		}
		else if($_POST['email']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Email.</div><br>';				
		}
		else if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))				
		{					
			$message1='<div style="color: red !important;">Enter your valid email address.</div><br>';				
		}
		else if($rows_aadhar_number_count>0)
		{
		    $message1='<div style="color: red !important;">Aadhar number already associated with another account.</div><br>';	
		}
		else if($_POST['aadhar_number']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Aadhar Number.</div><br>';				
		}
		else if($rows_pan_number_count>0)
		{
		    $message1='<div style="color: red !important;">Pan number already associated with another account.</div><br>';	
		}
		else if($_POST['pan_number']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your Pan Number.</div><br>';				
		}
		else
		{
			$wpdb->update(
                $table_name, //table
                array(
					'full_name' => $full_name,
					'address' => $address,
					'contact_number' => $contact_number,
					'email' => $email,
					'aadhar_number' => $aadhar_number,
					'pan_number' => $pan_number,
					'username' => $email
					), //data
                array('id' => $id), //where
                array(
					'%s', 
					'%s', 
					'%s',
					'%s', 
					'%s', 
					'%s',
					'%s', 
					'%s'
					), //data format
                array('%s') //where format
			);
			$message1="<div style='color: green !important;'>Your information updated successfully.</div>";
		}
    }else{
        $nlt_customer = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where id=%s", $id));
        
        foreach ($nlt_customer as $s) {
            $full_name = $s->full_name;
			$address = $s->address;
			$contact_number = $s->contact_number;
			$email = $s->email;
			$aadhar_number = $s->aadhar_number;
			$pan_number = $s->pan_number;
        }
    }
?>
<section id="contact">
	<div class="container">
	  <h2 class="h2 ac">Edit my Account</h2>
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
					Aadhar Number
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
					<input name="update" type="submit" value="Edit Account" class="btn btn_prm">
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

add_shortcode('cleopatra_customer_account_edit', 'customer_account_edit');

function customer_login_form(){
	ob_start();
	if(isset($_SESSION['user'])){
	    $_SESSION['message'] = 'You are logged in.';
	    wp_redirect(get_site_url().'/message/');
	}
	if(isset($_POST['login'])){
		$email = $_POST['email'];
		$password = $_POST['password'];
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "nlt_customer";
		
		$rows = $wpdb->get_results("SELECT * FROM $table_name WHERE `email`='".$email."' AND `password`='".$password."'");
		
		if(count($rows)==0){
			$message1 = 'Username/Email and password doesn\'t match.';
		}else{
			$rows_new = $wpdb->get_results("SELECT * FROM $table_name WHERE `email`='".$email."' AND `password`='".$password."' AND `status`='0'");
			
			if(count($rows_new)>0){
				$message1 = 'Username/Email is made inactive. Please contact administrator for further details.';
			}else{
				$_SESSION['user'] = array('user_id'=>$rows[0]->id,'type'=>'customer');
				
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
	  <h2 class="h2 ac">Customer Login</h2>
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
					<a href="<?php echo get_site_url(); ?>/customer-registration/">Register</a> | <a href="javascript:void(0);">Forget Passoword</a>
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
add_shortcode('cleopatra_customer_login', 'customer_login_form');

function customer_logout(){
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
add_shortcode('cleopatra_customer_logout', 'customer_logout');

function customer_change_password(){
    ob_start();
    if(!isset($_SESSION['user'])){
	    $_SESSION['message'] = 'You are not logged in.';
	    wp_redirect(get_site_url().'/message/');
	}
	global $wpdb;
	$table_name = $wpdb->prefix . "nlt_customer";
	$id = $_SESSION['user']['user_id'];
//update
    if (isset($_POST['updatepassword'])) {
		$password = $_POST["password"];
		$confirm_password = $_POST["confirm_password"];
		
		if($_POST['password']=='')				
		{					
			$message1='<div style="color: red !important;">Enter your new password.</div><br>';				
		}
		else if($_POST['confirm_password']=='')				
		{					
			$message1='<div style="color: red !important;">Please confirm your password.</div><br>';				
		}
		else if($_POST['confirm_password']!=$_POST['password'])				
		{					
			$message1='<div style="color: red !important;">Password and confirm password should match.</div><br>';				
		}
		else
		{
			$wpdb->update(
                $table_name, //table
                array(
					'password' => $password
					), //data
                array('id' => $id), //where
                array(
					'%s'
					), //data format
                array('%s') //where format
			);
			$message1="<div style='color: green !important;'>Your password updated successfully.</div>";
		}
    }else{
        $password = '';
        $confirm_password = '';
        $message1 = '';
    }
?>
<section id="contact">
	<div class="container">
	  <h2 class="h2 ac">Change Password</h2>
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
					Password
				</div>
				<div class="form-group">
					<input type="password" name="password" value="<?php echo $password; ?>" class="form-control">
				</div>
				<div class="form-group">
					Confirm Password
				</div>
				<div class="form-group">
					<input type="text" name="confirm_password" value="<?php echo $confirm_password; ?>" class="form-control">
				</div>
				<div class="form-group">
					<input name="updatepassword" type="submit" value="Change Password" class="btn btn_prm">
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

add_shortcode('cleopatra_customer_change_password', 'customer_change_password');