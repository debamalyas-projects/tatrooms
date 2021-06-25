<?php
function sync_agent_update() {
  
	global $wpdb;
	$table_name = $wpdb->prefix . "progoti_collection_egent";
	$id = $_GET["id"];
//update
    if (isset($_POST['update'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $address = $_POST['address'];
		$contact_number = $_POST['contact_number'];
		$email = $_POST['email'];

        $aadharcard = $_FILES['aadharcard']['name'];
		$tempaadharcard = $_FILES['aadharcard']['tmp_name'];
        $pancard = $_FILES['pancard']['name'];
		$temppancard = $_FILES['pancard']['tmp_name'];

		$status = $_POST["status"];
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
        }else{
            if($aadharcard=='' && $pancard==''){
				$wpdb->update(
					$table_name, //table
					array(
						'first_name' => $first_name,
						'last_name' => $last_name,
						'address' => $address,
						'contact_number' => $contact_number,
						'email' => $email,
						'status'=>$status
						), //data
					array('id' => $id), //where
					array(
						'%s', 
						'%s',
						'%s',
						'%s',
						'%s',
						'%s'
						), //data format
					array('%s') //where format
				);
				$message1='<span style="color: #008080">Agent details updated successfully.</span>';
			}else if($aadharcard==''){
				if(!in_array($ext_pan, $allowed))  {
					$message1='<span style="color: #FF0000">Pan card file is only support .gif,.jpg,.png and .pdf format.</span>';
				}else{
					move_uploaded_file($temppancard,"/home/tatrooms/public_html/wp-content/plugins/progoti_collection_agent/upload_images/$pancard");

					$wpdb->update(
						$table_name, //table
						array(
							'first_name' => $first_name,
							'last_name' => $last_name,
							'address' => $address,
							'contact_number' => $contact_number,
							'email' => $email,
							'pancard' => $pancard,
							'status'=>$status
							), //data
						array('id' => $id), //where
						array(
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
					$message1='<span style="color: #008080">Agent details updated successfully.</span>';
					
				}

			}else if($pancard==''){
				if(!in_array($ext_aadhar, $allowed)) {
					$message1='<span style="color: #FF0000">Aadhar card file is only support .gif,.jpg,.png and .pdf format.</span>';
				}else{
					move_uploaded_file($tempaadharcard,"/home/tatrooms/public_html/wp-content/plugins/progoti_collection_agent/upload_images/$aadharcard");

					$wpdb->update(
						$table_name, //table
						array(
							'first_name' => $first_name,
							'last_name' => $last_name,
							'address' => $address,
							'contact_number' => $contact_number,
							'email' => $email,
							'aadharcard' => $aadharcard,
							'status'=>$status
							), //data
						array('id' => $id), //where
						array(
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
					$message1='<span style="color: #008080">Agent details updated successfully.</span>';
				}
			}else{
				if(!in_array($ext_aadhar, $allowed)) {
					$message1='<span style="color: #FF0000">Aadhar card file is only support .gif,.jpg,.png and .pdf format.</span>';
				}else if(!in_array($ext_pan, $allowed))  {
					$message1='<span style="color: #FF0000">Pan card file is only support .gif,.jpg,.png and .pdf format.</span>';
				}else{
					move_uploaded_file($tempaadharcard,"/home/tatrooms/public_html/wp-content/plugins/progoti_collection_agent/upload_images/$aadharcard");

					move_uploaded_file($temppancard,"/home/tatrooms/public_html/wp-content/plugins/progoti_collection_agent/upload_images/$pancard");

					$wpdb->update(
						$table_name, //table
						array(
							'first_name' => $first_name,
							'last_name' => $last_name,
							'address' => $address,
							'contact_number' => $contact_number,
							'email' => $email,
							'aadharcard' => $aadharcard,
							'pancard' => $pancard,
							'status'=>$status
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
					$message1='<span style="color: #008080">Agent details updated successfully.</span>';
				}
			}
		}
		$rows = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$id ."'");
		$aadharcard = "/wp-content/plugins/progoti_collection_agent/upload_images/".$rows[0]->aadharcard;
		$pancard = "/wp-content/plugins/progoti_collection_agent/upload_images/".$rows[0]->pancard;
		$base_url = "https://tatrooms.com";
		$aadharcard = $base_url.$aadharcard;
		$pancard = $base_url.$pancard;

		$aadharcard_database = $rows[0]->aadharcard;
		$pancard_database = $rows[0]->pancard;
    }
//delete
    else if(isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
    }else{//selecting value to update	
        $sync_categories = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where id=%s", $id));
        foreach ($sync_categories as $s) {
            $first_name = $s->first_name;
            $last_name = $s->last_name;
            $address = $s->address;
			$contact_number = $s->contact_number;
			$email = $s->email;
			
			$aadharcard_database = $s->aadharcard;
			$pancard_database = $s->pancard;

			$aadharcard = "/wp-content/plugins/progoti_collection_agent/upload_images/".$s->aadharcard;
			$pancard = "/wp-content/plugins/progoti_collection_agent/upload_images/".$s->pancard;
			$base_url = "https://tatrooms.com";
			$aadharcard = $base_url.$aadharcard;
			$pancard = $base_url.$pancard;

            $status = $s->status;
        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Agent Details Edit</h2>
        <?php if ($_POST['delete']) { ?>
            <div class="updated"><p>Agent Details deleted</p></div>
            <a href="<?php echo admin_url('admin.php?page=sync_agent_list') ?>">&laquo; Back to agent list</a>
        <?php }else{ ?>
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
                    <th class="ss-th-width">Agent Email Address</th>
                    <td><input type="email" name="email" value="<?php echo $email; ?>" class="ss-field-width" required="required"/></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Upload Adadhar Card</th>
                    <td class="manage-column ss-list-width"><a href="<?php echo $aadharcard; ?>" target="_blank"><?php echo $aadharcard_database; ?></a>
</td>
					<td><input type="file" name="aadharcard" value="" class="ss-field-width"/></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Upload Pan Card</th>
					<td class="manage-column ss-list-width"><a href="<?php echo $pancard; ?>" target="_blank"><?php echo $pancard_database; ?></a>
</td>
					<td><input type="file" name="pancard" value="" class="ss-field-width" /></td>
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
                <input type='submit' name="update" value='Update' class='button' onclick=""> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Are you sure you want to delete this?')">
            </form>
        <?php } ?>

    </div>
    <?php
}
?>