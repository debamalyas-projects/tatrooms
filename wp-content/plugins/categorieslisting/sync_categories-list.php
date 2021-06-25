<?php

function sync_categories_list() {
	?>
	<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
	<div class="wrap">
		<h2>Categories</h2>
		<div class="tablenav top">
			<div class="alignleft actions">
				<a href="<?php echo admin_url('admin.php?page=sync_categories_create'); ?>">Add New Category</a>
			</div>
			<br class="clear">
		</div>
		<?php
		global $wpdb;
		$table_name = $wpdb->prefix . "sync_categories";
		
		$rows = $wpdb->get_results("SELECT * from $table_name");
		
		?>
		<table class='wp-list-table widefat fixed striped posts'>
			<tr>
				<th class="manage-column ss-list-width">Category Id</th>
				<th class="manage-column ss-list-width">Category Name</th>
				<th class="manage-column ss-list-width">Category Status</th>
				<th class="manage-column ss-list-width">Action</th>
			</tr>
			<?php foreach ($rows as $row) { ?>
				<tr>
					<td class="manage-column ss-list-width"><?php echo $row->id; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->category; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->status; ?></td>
					<td><a href="<?php echo admin_url('admin.php?page=sync_categories_update&id=' . $row->id); ?>">Update</a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php
}

?>