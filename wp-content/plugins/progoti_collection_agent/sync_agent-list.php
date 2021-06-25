<?php

function sync_agent_list() {
	?>
	<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
	<div class="wrap">
		<h2>Agent List</h2>
		<div class="tablenav top">
			<div class="alignleft actions">
				<a href="<?php echo admin_url('admin.php?page=sync_agent_create'); ?>">Add New Agent</a>
			</div>
			<br class="clear">
		</div>
		<?php
		global $wpdb;
		$table_name = $wpdb->prefix . "progoti_collection_egent";
		
		$rows = $wpdb->get_results("SELECT * from $table_name");
		
		?>
		<table class='wp-list-table widefat fixed striped posts'>
			<tr>
				<th class="manage-column ss-list-width">First Name</th>
				<th class="manage-column ss-list-width">Last Name</th>
				<th class="manage-column ss-list-width">Address</th>
				<th class="manage-column ss-list-width">Contact Number</th>
				<th class="manage-column ss-list-width">Email Address</th>
				<th class="manage-column ss-list-width">Aadhar Card</th>
				<th class="manage-column ss-list-width">Pan Card</th>
				<th class="manage-column ss-list-width">Status</th>
				<th class="manage-column ss-list-width">Action</th>
			</tr>
			<?php foreach ($rows as $row) { 
				$aadharcard_database = $row->aadharcard;
				$pancard_database = $row->pancard;

				$aadharcard = "/wp-content/plugins/progoti_collection_agent/upload_images/".$row->aadharcard;
				$pancard = "/wp-content/plugins/progoti_collection_agent/upload_images/".$row->pancard;
				$base_url = "https://tatrooms.com";
				$aadharcard = $base_url.$aadharcard;
				$pancard = $base_url.$pancard;

				?>
				<tr>
					<td class="manage-column ss-list-width"><?php echo $row->first_name; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->last_name; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->address; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->contact_number; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->email; ?></td>
					<td class="manage-column ss-list-width"><a href="<?php echo $aadharcard; ?>" target="_blank"><?php echo $aadharcard_database; ?></a>
</td>
					<td class="manage-column ss-list-width"><a href="<?php echo $pancard; ?>" target="_blank"><?php echo $pancard_database; ?></a>
</td>
					<td class="manage-column ss-list-width"><?php echo $row->status; ?></td>
					<td><a href="<?php echo admin_url('admin.php?page=sync_agent_update&id=' . $row->id); ?>">Update</a></td>
				</tr>
			<?php } ?>
			
		</table>
	</div>
	<?php
}

?>