<?php
function sync_update() {
	$message = '';
	global $wpdb;
                            
	$table_name = $wpdb->prefix . "settings";

	$query = $wpdb->get_results("SELECT * from $table_name");

	$gst = $query[0]->gst;
	$tatroom_commission = $query[0]->tatroom_commission;
	$vendor_commission = $query[0]->vendor_commission;
	$agent_commission = $query[0]->agent_commission;
	
	if(isset($_POST['update'])){
		$gst = $_POST['gst'];
		$tatroom_commission = $_POST['tatroom_commission'];
		$vendor_commission = $_POST['vendor_commission'];
		$agent_commission = $_POST['agent_commission'];
		$wpdb->query($wpdb->prepare("UPDATE $table_name SET `gst`='".$gst."',`tatroom_commission`='".$tatroom_commission."',`vendor_commission`='".$vendor_commission."',`agent_commission`='".$agent_commission."'"));
		$message =  "Cart updated successfully.";
	}
	?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/cart/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Cart Edit</h2>
			<?php echo $message; ?>
            <form method="post" action="" enctype="multipart/form-data">
                <table class='wp-list-table widefat fixed'>
                    <tr>
						<th class="ss-th-width">GST Percent</th>
						<td><input type="text" name="gst" value="<?php echo $gst; ?>" class="ss-field-width" /></td>
					</tr>
					<tr>
						<th class="ss-th-width">Tatroom Commission</th>
						<td><input type="text" name="tatroom_commission" value="<?php echo $tatroom_commission; ?>"class="ss-field-width" /></td>
					</tr>
					<tr>
						<th class="ss-th-width">Vendor Commission</th>
						<td><input type="text" name="vendor_commission" value="<?php echo $vendor_commission; ?>" class="ss-field-width" /></td>
					</tr>
					<tr>
						<th class="ss-th-width">Agent Commission</th>
						<td><input type="text" name="agent_commission" value="<?php echo $agent_commission; ?>" class="ss-field-width" /></td>
					</tr>
                </table><br>
                <input type='submit' name="update" value='Update' class='button' onclick="">
            </form>

    </div>
    <?php
}
?>