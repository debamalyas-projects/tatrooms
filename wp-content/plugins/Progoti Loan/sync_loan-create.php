<?php

function sync_loan_create() {
	if(isset($_POST['insert'])){
		$client_first_name = $_POST['fname'];
		$client_last_name = $_POST['lname'];
		$loan_amount = $_POST['amount'];
		$emi = $_POST['emi'];
		$percentage_fine = $_POST['percentage_fine'];

		$amount_notpaid = $_POST['amount'];
		$emi_due = $_POST['emi'];
		$filename = $_FILES['file_name']['name'];
		$tempfilename = $_FILES['file_name']['tmp_name'];

		$allowed = array('gif', 'png', 'jpg','pdf');
		$filename = $_FILES['file_name']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);

		global $wpdb;
		
		if($client_first_name==''){
			$message1='<span style="color: #FF0000">Clinet first name can not be blank.</span>';
		}else if($client_last_name==''){
			$message1='<span style="color: #FF0000">Clinet last name can not be blank.</span>';
		}else if($loan_amount==''){
			$message1='<span style="color: #FF0000">Loan amount can not be blank.</span>';
		}else if(!(is_numeric($loan_amount))){
			$message1='<span style="color: #FF0000">Loan amount is only contain numeric value.</span>';
		}else if($emi==''){
			$message1='<span style="color: #FF0000">EMI can not be blank.</span>';
		}else if($percentage_fine==''){
			$message1='<span style="color: #FF0000">Fine percentage can not be blank.</span>';
		}else if(!(is_numeric($emi))){
			$message1='<span style="color: #FF0000">EMI is only contain numeric value.</span>';
		}else if (!in_array($ext, $allowed)) {
			$message1='<span style="color: #FF0000">Document is only support .gif,.jpg and .png format.</span>';
		}else{
			move_uploaded_file($tempfilename,"/home/tatrooms/public_html/wp-content/plugins/Progoti Loan/upload/$filename");

			$query = $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."progati_loan` (`client_first_name`,`client_last_name`,`loan_amount`,`emi`,`amount_notpaid`,`file_ulpoad`,`emi_due`,`percentage_fine`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s)",$client_first_name,$client_last_name,$loan_amount,$emi,$amount_notpaid,$filename,$emi_due,$percentage_fine));
			
			$folder = "/home/tatrooms/public_html/wp-content/plugins/Progoti Loan/upload/".$filename;
			$message1='<span style="color: #008080">Loan details saved successfully.</span>';
			
		}
	}
   
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/Progoti Loan/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add New Loan</h2>
        <?php if (isset($message1)): ?><div class="updated"><p><?php echo $message1; ?></p></div><?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
            <table class='wp-list-table widefat fixed'>
				<tr>
                    <th class="ss-th-width">Client First Name</th>
                    <td><input type="text" name="fname" value="<?php echo $client_first_name; ?>" class="ss-field-width" /></td>
				</tr>
				<tr>
                    <th class="ss-th-width">Client Last Name</th>
                    <td><input type="text" name="lname" value="<?php echo $client_last_name; ?>" class="ss-field-width" /></td>
				</tr>
				<tr>
                    <th class="ss-th-width">Loan Amount</th>
                    <td><input type="text" name="amount" value="<?php echo $loan_amount; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Loan EMI</th>
                    <td><input type="text" name="emi" value="<?php echo $emi; ?>" class="ss-field-width" /></td>
				</tr>
				<tr>
                    <th class="ss-th-width">Percentage Of Fine</th>
                    <td><input type="text" name="percentage_fine" value="<?php echo $percentage_fine; ?>" class="ss-field-width" /></td>
				</tr>
				<tr>
                    <th class="ss-th-width">Upload Document</th>
                    <td><input type="file" name="file_name" value="<?php echo $folder; ?>" required="required" class="ss-field-width" /></td>
                </tr>
            </table>
            <input type='submit' name="insert" value='Submit' class='button'>
        </form>
    </div>
    <?php
}