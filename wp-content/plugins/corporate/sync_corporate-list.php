<?php

function sync_corporate_list() {
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/corporate/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Corporate</h2>
        <div class="tablenav top">
            <div class="alignleft actions">
                <a href="<?php echo admin_url('admin.php?page=sync_corporate_create'); ?>">Add New Corporate</a>
            </div>
            <br class="clear">
        </div>
        <?php
        global $wpdb;
        $table_name = $wpdb->prefix . "sync_corporate";

        $rows = $wpdb->get_results("SELECT * from $table_name");
        ?>
        <table class='wp-list-table widefat fixed striped posts'>
            <tr>
                <th class="manage-column ss-list-width">ID</th>
                <th class="manage-column ss-list-width">Full name</th>
				<th class="manage-column ss-list-width">Address</th>
				<th class="manage-column ss-list-width">Contact Number</th>
				<th class="manage-column ss-list-width">Email</th>
				<th class="manage-column ss-list-width">Aadhar Number</th>
				<th class="manage-column ss-list-width">Pan Number</th>
				<th class="manage-column ss-list-width">Status</th>
                <th>&nbsp;</th>
            </tr>
            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td class="manage-column ss-list-width"><?php echo $row->id; ?></td>
                    <td class="manage-column ss-list-width"><?php echo $row->full_name; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->address; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->contact_number; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->email; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->aadhar_number; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->pan_number; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->status; ?></td>
                    <td><a href="<?php echo admin_url('admin.php?page=sync_corporate_update&id=' . $row->id); ?>">Update</a></td>
                </tr>
            <?php } ?>
        </table>
    </div>
    <?php
}