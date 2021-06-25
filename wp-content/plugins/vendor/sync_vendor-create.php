<?php

function sync_vendor_create() {
    //insert
    if (isset($_POST['insert'])) {
		
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
			$message1='Enter your Full Name.<br>';				
		}
		else if($_POST['address']=='')				
		{					
			$message1='Enter your Full Address.<br>';				
		}
		else if($_POST['contact_number']=='')				
		{					
			$message1='Enter your Contact Number.<br>';				
		}
		else if($rows_email_count>0)
		{
		    $message1='Email already exists.<br>';	
		}
		else if($_POST['email']=='')				
		{					
			$message1='Enter your Email.<br>';				
		}
		else if(!filter_var($_POST['email'],FILTER_VALIDATE_EMAIL))				
		{					
			$message1='Enter your valid email address.<br>';				
		}
		else if($rows_tin_number_count>0)
		{
		    $message1='Tin number already exists.<br>';	
		}
		else if($_POST['tin_number']=='')				
		{					
			$message1='Enter your Tin Number.<br>';				
		}
		else if($rows_pan_number_count>0)
		{
		    $message1='Pan number already exists.<br>';	
		}
		else if($_POST['pan_number']=='')				
		{					
			$message1='Enter your Pan Number.<br>';				
		}
		else if($_POST['password']=='')				
		{					
			$message1='Enter your Password.<br>';				
		}
		else if($_POST['confirm_password']=='')				
		{					
			$message1='Enter your Confirm Password.<br>';				
		}
		else if($_POST['confirm_password']!=$_POST['password'])				
		{					
			$message1='Password and confirm password should match.<br>';				
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
			/*$wpdb->insert(
					$wpdb->prefix . 'users', //table
					array(
					'user_login' => $email,
					'user_pass' => wp_hash_password($password),
					'user_nicename' => $full_name,
					'user_email' => $email,
					'user_url' => '',
					'user_registered' => date('Y-m-d H:i:s'),
					'user_activation_key' => '',
					'user_status' => '0',
					'display_name' => $full_name,
					'user_type' => 'Vendor',
					'user_type_id' => $user_type_id
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
					'%s',
					'%s',
					'%s'
					) //data format			
			);*/
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
			$message1="Vendor registered successfully.";
		}
    }else{
		$full_name = '';
		$address = '';
		$contact_number = '';
		$email = '';
		$tin_number = '';
		$pan_number = '';
		$username = '';
		$password = '';
		$confirm_password = '';
	}
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/vendor/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add New Vendor</h2>
        <?php if (isset($message1)): ?><div class="updated"><p><?php echo $message1; ?></p></div><?php endif; ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
            <table class='wp-list-table widefat fixed'>
                <tr>
                    <th class="ss-th-width">Full name</th>
                    <td><input type="text" name="full_name" value="<?php echo $full_name; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Address</th>
                    <td><textarea name="address" class="ss-field-width"><?php echo $address; ?></textarea></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Contact Number</th>
                    <td><input type="text" name="contact_number" value="<?php echo $contact_number; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Email</th>
                    <td><input type="text" name="email" value="<?php echo $email; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Tin Number</th>
                    <td><input type="text" name="tin_number" value="<?php echo $tin_number; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Pan Number</th>
                    <td><input type="text" name="pan_number" value="<?php echo $pan_number; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Password</th>
                    <td><input type="text" name="password" value="<?php echo $password; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Confirm Password</th>
                    <td><input type="text" name="confirm_password" value="<?php echo $confirm_password; ?>" class="ss-field-width" /></td>
                </tr>
				
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    <?php
}