<?php

function sync_agent_create() {
	
    //insert
    if (isset($_POST['insert'])) {
		
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$address = $_POST['address'];
        $contact_number = $_POST['contact_number'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $cpassword = $_POST['cpassword'];

		$aadharcard = $_FILES['aadharcard']['name'];
		$tempaadharcard = $_FILES['aadharcard']['tmp_name'];
		$pancard = $_FILES['pancard']['name'];
		$temppancard = $_FILES['pancard']['tmp_name'];

		$status = $_POST['status'];
		
		global $wpdb;
        
        $allowed = array('gif', 'png', 'jpg','pdf');
        $aadharcard_ext = $_FILES['aadharcard']['name'];
        $pancard_ext = $_FILES['pancard']['name'];
        $ext_aadhar = pathinfo($aadharcard_ext, PATHINFO_EXTENSION);
        $ext_pan = pathinfo($pancard_ext, PATHINFO_EXTENSION);

		if(!(is_numeric($contact_number))){
			$message1='<span style="color: #FF0000">Contact number contain only numeric value.</span>';
		}else if((strlen($contact_number))!=10){
			$message1='<span style="color: #FF0000">Contact number contain 10 digits.</span>';
        }else if (!(filter_var($email, FILTER_VALIDATE_EMAIL))){
            $message1='<span style="color: #FF0000">Enter a valid email address.</span>';
        }else if($password!=$cpassword){
            $message1='<span style="color: #FF0000">Password and confirm password can not matched.</span>';
        }else if (!in_array($ext_aadhar, $allowed)) {
            $message1='<span style="color: #FF0000">Aadhar card file is only support .gif,.jpg,.png and .pdf format.</span>';
        }else if(!in_array($ext_pan, $allowed))  {
            $message1='<span style="color: #FF0000">Pan card file is only support .gif,.jpg,.png and .pdf format.</span>';
        } else{
			move_uploaded_file($tempaadharcard,"/home/tatrooms/public_html/wp-content/plugins/progoti_collection_agent/upload_images/$aadharcard");

			move_uploaded_file($temppancard,"/home/tatrooms/public_html/wp-content/plugins/progoti_collection_agent/upload_images/$pancard");

			$query = $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."progoti_collection_egent` (`first_name`,`last_name`,`aadharcard`,`pancard`,`address`,`contact_number`,`email`,`password`,`status`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s)",$first_name,$last_name,$aadharcard,$pancard,$address,$contact_number,$email,$password,$status));
			
			$message1="Agent details saved successfully.";
		}
	
    }else{
        $first_name = '';
        $last_name = '';
        $address = '';
        $contact_number = '';
        $email = '';
        $status = '';
	}
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/categorieslisting/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add New Agent</h2>
        <?php if (isset($message1)): ?><div class="updated"><p><?php echo $message1; ?></p></div><?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
            <table class='wp-list-table widefat fixed'>
				<tr>
                    <th class="ss-th-width">Agent First Name</th>
                    <td><input type="text" name="first_name" value="<?php echo $first_name; ?>" class="ss-field-width"  required="required"/></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Agent Last Name</th>
                    <td><input type="text" name="last_name" value="<?php echo $last_name; ?>" class="ss-field-width" required="required"/></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Agent Address</th>
                    <td><textarea name='address' rows="4" cols="50" required="required"><?php echo $address; ?></textarea></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Agent Contact Number</th>
                    <td><input type="text" name="contact_number" value="<?php echo $contact_number; ?>" class="ss-field-width" required="required"/></td>
                </tr>
                <tr>
                    <th class="ss-th-width">Agent Email</th>
                    <td><input type="email" name="email" value="<?php echo $email; ?>" class="ss-field-width" required="required"/></td>
                </tr>
                <tr>
                    <th class="ss-th-width">Agent Password</th>
                    <td><input type="password" name="password" value="" class="ss-field-width" required="required"/></td>
                </tr>
                <tr>
                    <th class="ss-th-width">Agent Confirm Password</th>
                    <td><input type="password" name="cpassword" value="" class="ss-field-width" required="required"/></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Upload Adadhar Card Image</th>
                    <td><input type="file" name="aadharcard" value="" class="ss-field-width" required="required"/></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Upload Pan Card Image</th>
                    <td><input type="file" name="pancard" value="" class="ss-field-width" required="required"/></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Agent Status</th>
                    <td><select name='status' required="required">
						<option value="">Select one option</option>
						<option value="Active" <?php if($status=='Active'){ echo 'selected="selected"'; }?>>Active</option>
						<option value="InActive"  <?php if($status=='InActive'){ echo 'selected="selected"'; }?>>InActive</option>
					</select></td>
                </tr>
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    <?php
}