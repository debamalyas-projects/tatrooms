<?php
function sync_money_create() {
	global $wpdb;
	if(isset($_POST['insert'])){
		$agent_id = $_POST['agent_id'];
		$loan_taker_id = $_POST['loan_taker_id'];

		if($agent_id==''){
			$message1='<span style="color: #FF0000">Please select one agent.</span>';
		}else if($loan_taker_id==''){
			$message1='<span style="color: #FF0000">Please select one loan taker.</span>';
		}else{
			$query = $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."money_collection` (`agent_id`,`loan_taker_id`) VALUES (%s,%s)",$agent_id,$loan_taker_id));
			$message1='<span style="color: #008080">Loan agent and loan taker saved successfully.</span>';
		}
	}
	$table_name = $wpdb->prefix . "progoti_collection_egent";
	$agent = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name"));

	$table_name2 = $wpdb->prefix . "progati_loan";
	$user = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name2"));
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/money_collection/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add New Agent For Money Collect</h2>
        <?php if (isset($message1)): ?><div class="updated"><p><?php echo $message1; ?></p></div><?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
            <table class='wp-list-table widefat fixed'>
			<tr>
				<th class="ss-th-width">Agent Name</th>
				<td><select name='agent_id' required="required">
					<option value=""><b>Select one option</b></option>
					<?php
						foreach($agent as $agent_list){
							?>
							<option value="<?php echo $agent_list->id; ?>" <?php if($agent_id==$agent_list->id){ echo 'selected="selected"'; }?>><?php echo  $agent_list->first_name.' '.$agent_list->last_name; ?></option>
							<?php
						}
					?>
				</select></td>
                </tr>
				<th class="ss-th-width">Loan taker Name</th>
				<td><select name='loan_taker_id' required="required">
					<option value=""><b>Select one option</b></option>
					<?php
						foreach($user as $user_list){
							?>
							<option value="<?php echo $user_list->id; ?>" <?php if($loan_taker_id==$user_list->id){ echo 'selected="selected"'; }?>><?php echo  $user_list->client_first_name.' '.$user_list->client_last_name; ?></option>
							<?php
						}
					?>
				</select></td>
                </tr>
            </table>
            <input type='submit' name="insert" value='Submit' class='button'>
        </form>
    </div>
    <?php
}