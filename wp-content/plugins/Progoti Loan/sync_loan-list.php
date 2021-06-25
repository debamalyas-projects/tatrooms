<?php

function sync_loan_list() {
	?>
	<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
	<div class="wrap">
		<h2>Categories</h2>
		<div class="tablenav top">
			<div class="alignleft actions">
				<a href="<?php echo admin_url('admin.php?page=sync_loan_create'); ?>">Add New Loan details</a>
			</div>
			<br class="clear">
		</div>
		<?php
		global $wpdb;
		$table_name = $wpdb->prefix . "progati_loan";
		
		$rows = $wpdb->get_results("SELECT * from $table_name");
		?>
		<table class='wp-list-table widefat fixed striped posts'>
			<tr>
				<th class="manage-column ss-list-width">First Name</th>
				<th class="manage-column ss-list-width">Last Name</th>
				<th class="manage-column ss-list-width">Loan Amount</th>
				<th class="manage-column ss-list-width">EMI Amount</th>
				<th class="manage-column ss-list-width">EMI Due</th>
				<th class="manage-column ss-list-width">Amount Paid</th>
				<th class="manage-column ss-list-width">Amount Not Paid</th>
				<th class="manage-column ss-list-width">Percentage Of Fine</th>
				<th class="manage-column ss-list-width">Uploaded File Name</th>
				<th class="manage-column ss-list-width">Action</th>
			</tr>
			<?php foreach ($rows as $row) { 
				$file_ulpoad_database = $row->file_ulpoad;
				$file_ulpoad = "/wp-content/plugins/Progoti Loan/upload/".$row->file_ulpoad;
				$base_url = "https://tatrooms.com";
				$file_ulpoad = $base_url.$file_ulpoad;?>
				<tr>
					<td class="manage-column ss-list-width"><?php echo $row->client_first_name; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->client_last_name; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->loan_amount; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->emi; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->emi_due; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->amount_paid; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->amount_notpaid; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->percentage_fine; ?></td>
					<td class="manage-column ss-list-width"><a href="<?php echo $file_ulpoad; ?>" target="_blank"><?php echo $file_ulpoad_database; ?></a>
					</td>
					<td><a href="<?php echo admin_url('admin.php?page=sync_loan_update&id=' . $row->id); ?>">Update</a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php
}

?>