<?php
function sync_vendor_update() {
	global $wpdb;
	$table_name = $wpdb->prefix . "sync_vendor";
	$id = $_GET["id"];
//update
    if (isset($_POST['update'])) {
		$full_name = $_POST["full_name"];
		$address = $_POST["address"];
		$contact_number = $_POST["contact_number"];
		$email = $_POST["email"];
		$tin_number = $_POST["tin_number"];
		$pan_number = $_POST["pan_number"];
		$status = $_POST["status"];
		
		$rows_email = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `email`='".$email."' AND `id`!='".$id."'");
		
		$rows_email_count = $rows_email[0]->n;
		
		$rows_tin_number = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `tin_number`='".$tin_number."' AND `id`!='".$id."'");
		
		$rows_tin_number_count = $rows_tin_number[0]->n;
		
		$rows_pan_number = $wpdb->get_results("SELECT count(*) AS `n` from $table_name WHERE `pan_number`='".$pan_number."' AND `id`!='".$id."'");
		
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
		    $message1='Email already associated with another account.<br>';	
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
		    $message1='Tin number already associated with another account.<br>';	
		}
		else if($_POST['tin_number']=='')				
		{					
			$message1='Enter your Tin Number.<br>';				
		}
		else if($rows_pan_number_count>0)
		{
		    $message1='Pan number already associated with another account.<br>';	
		}
		else if($_POST['pan_number']=='')				
		{					
			$message1='Enter your Pan Number.<br>';				
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
					'tin_number' => $tin_number,
					'pan_number' => $pan_number,
					'username' => $email,
					'status' => $status
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
			$wpdb->update(
                $wpdb->prefix . 'users', //table
                array(
					'user_login' => $email,
					'user_nicename' => $full_name,
					'user_email' => $email,
					'display_name' => $full_name,
					'user_type' => 'Vendor'
					), //data
                array('user_type_id' => $id), //where
                array(
					'%s', 
					'%s', 
					'%s',
					'%s', 
					'%s'
					), //data format
                array('%s') //where format
			);
			$message1="Vendor updated successfully.";
		}
    }
//delete
    else if(isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
		$wpdb->query($wpdb->prepare("DELETE FROM ". $wpdb->prefix . "users WHERE user_type_id = %s", $id));
    }else{//selecting value to update	
        $sync_vendor = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where id=%s", $id));
        foreach ($sync_vendor as $s) {
            $full_name = $s->full_name;
			$address = $s->address;
			$contact_number = $s->contact_number;
			$email = $s->email;
			$tin_number = $s->tin_number;
			$pan_number = $s->pan_number;
			$status = $s->status;
        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/vendor/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Vendor Edit</h2>
        <?php if ($_POST['delete']) { ?>
            <div class="updated"><p>Vendor deleted</p></div>
            <a href="<?php echo admin_url('admin.php?page=sync_vendor_list') ?>">&laquo; Back to vendor list</a>
        <?php }else{ ?>
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
						<th class="ss-th-width">Status</th>
						<td>
							<select name="status" class="ss-field-width">
								<option value="Active" <?php if($status=="Active"){ echo 'selected="selected"'; }else{ echo ''; } ?>>Active</option>
								<option value="Inactive" <?php if($status=="Inactive"){ echo 'selected="selected"'; }else{ echo ''; } ?>>Inactive</option>
							</select>
						</td>
					</tr>
                </table>
                <input type='submit' name="update" value='Update' class='button' onclick=""> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Are you sure you want to delete this?')">
            </form>
        <?php } ?>

    </div>
    <?php
}
?>