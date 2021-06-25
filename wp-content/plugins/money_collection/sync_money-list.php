<?php

function sync_money_list() {
	?>
	<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/money_collection/style-admin.css" rel="stylesheet" />
	<div class="wrap">
		<h2>Agent and loan taker listing</h2>
		<div class="tablenav top">
			<div class="alignleft actions">
				<a href="<?php echo admin_url('admin.php?page=sync_money_create'); ?>">Add New Agent For Money Collect</a>
			</div>
			<br class="clear">
		</div>
		<?php
		global $wpdb;
		$table_name1 = $wpdb->prefix . "money_collection";
		$table_name2 = $wpdb->prefix . "progati_loan";
		$table_name3 = $wpdb->prefix . "progoti_collection_egent";
		
		$rows = $wpdb->get_results("SELECT * from $table_name1,$table_name2,$table_name3 WHERE $table_name1.`loan_taker_id`=$table_name2.`id` AND $table_name1.`agent_id`=$table_name3.`id`");

		?>
		<table class='wp-list-table widefat fixed striped posts'>
			<thead>
				<th class="manage-column ss-list-width">Agent name</th>
				<th class="manage-column ss-list-width">Loan Taker name</th>
			</thead>
			<?php foreach ($rows as $row) { ?>
				<tr>
					<td class="manage-column ss-list-width"><?php echo $row->first_name.' '.$row->last_name; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->client_first_name.' '.$row->client_last_name; ?></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php
}

?>