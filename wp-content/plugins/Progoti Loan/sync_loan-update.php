<?php
function sync_loan_update() {
  
	global $wpdb;
	$table_name = $wpdb->prefix . "progati_loan";
	$id = $_GET["id"];
//update
    if (isset($_POST['update'])) {
		$client_first_name = $_POST['client_first_name'];
        $client_last_name = $_POST["client_last_name"];
        $loan_amount = $_POST["loan_amount"];
        $percentage_fine = $_POST["percentage_fine"];
        $filename = $_FILES['filename']['name'];
		$tempfilename = $_FILES['filename']['tmp_name'];

        $allowed = array('gif', 'png', 'jpg','pdf');
		$filename = $_FILES['filename']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if($_POST['client_first_name']==''){					
            $message1='<span style="color: #FF0000">Enter client first name.<br></span>';				
        }else if($_POST['client_last_name']==''){					
			$message1='<span style="color: #FF0000">Enter client last name.<br></span>';				
		}else if($_POST['loan_amount']==''){					
			$message1='<span style="color: #FF0000">Enter loan amount.<br></span>';				
		}else if(!(is_numeric($loan_amount))){
			$message1='<span style="color: #FF0000">Loan amount is only contain numeric value.</span>';
		}
        else{
            if($filename==''){
                $wpdb->update(
                    $table_name, //table
                    array(
                        'client_first_name' => $client_first_name,
                        'client_last_name' => $client_last_name,
                        'loan_amount' => $loan_amount,
                        'percentage_fine' => $percentage_fine
                        ), //data
                    array('id' => $id), //where
                    array(
                        '%s', 
                        '%s',
                        '%s',
                        '%s'
                        ), //data format
                    array('%s') //where format
                );
                $message1='<span style="color: #008080">Loan details updated successfully.</span>';
            }else{
                if (!in_array($ext, $allowed)) {
                    $message1='<span style="color: #FF0000">Document is only support .gif,.jpg,.png,.pdf and .pdf format.</span>';
                }else{
                    move_uploaded_file($tempfilename,"/home/tatrooms/public_html/wp-content/plugins/Progoti Loan/upload/$filename");
                    $wpdb->update(
                        $table_name, //table
                        array(
                            'client_first_name' => $client_first_name,
                            'client_last_name' => $client_last_name,
                            'loan_amount' => $loan_amount,
                            'file_ulpoad'=>$filename,
                            'percentage_fine'=>$percentage_fine
                            ), //data
                        array('id' => $id), //where
                        array(
                            '%s', 
                            '%s',
                            '%s',
                            '%s',
                            '%s'
                            ), //data format
                        array('%s') //where format
                    );
                    $message1='<span style="color: #008080">Loan details updated successfully.</span>';
                }
            }
        }
        $rows = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$id ."'");
		$filename = "/wp-content/plugins/progoti_collection_agent/upload_images/".$rows[0]->file_ulpoad;
		$base_url = "https://tatrooms.com";
		$filename = $base_url.$filename;

		$filename_database = $rows[0]->file_ulpoad;
    }
//delete
    else if(isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
    }else{//selecting value to update	
        $sync_loan = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where id=%s", $id));
        foreach ($sync_loan as $s) {
			$client_first_name = $s->client_first_name;
            $client_last_name = $s->client_last_name;
            $loan_amount = $s->loan_amount;
            $percentage_fine = $s->percentage_fine;
            $filename_database = $s->file_ulpoad;
            
            $filename = "/wp-content/plugins/progoti_collection_agent/upload_images/".$s->file_ulpoad;
			$base_url = "https://tatrooms.com";
			$filename = $base_url.$filename;

        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Loan details Edit</h2>
        <?php if ($_POST['delete']) { ?>
            <div class="updated"><p>Loan details deleted</p></div>
            <a href="<?php echo admin_url('admin.php?page=sync_loan_list') ?>">&laquo; Back to Progoti loan list</a>
        <?php }else{ ?>
            <?php if (isset($message1)): ?><div class="updated"><p><?php echo $message1; ?></p></div><?php endif; ?>
            <form method="post" action="" enctype="multipart/form-data">
                <table class='wp-list-table widefat fixed'>
    				<tr>
                        <th class="ss-th-width">First Name</th>
                        <td><input type="text" name="client_first_name" value="<?php echo $client_first_name; ?>" class="ss-field-width" /></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width">Last Name</th>
                        <td><input type="text" name="client_last_name" value="<?php echo $client_last_name; ?>" class="ss-field-width" /></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width">Loan Amount</th>
                        <td><input type="text" name="loan_amount" value="<?php echo $loan_amount; ?>" class="ss-field-width" /></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width">Percentage Of Fine </th>
                        <td><input type="text" name="percentage_fine" value="<?php echo $percentage_fine; ?>" class="ss-field-width" /></td>
                    </tr>
                    <tr>
                        <th class="ss-th-width">Uploded File Name</th>
                        <td class="manage-column ss-list-width"><a href="<?php echo $filename; ?>" target="_blank"><?php echo $filename_database; ?></a>
                        <td><input type="file" name="filename" value="" class="ss-field-width" /></td>
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