<?php
function sync_listing() {
	$message = '';
	global $wpdb;
	$user = get_current_user_id();
	$table_name = $wpdb->prefix . "users";
	$query = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$user."'");
	$user_type = $query[0]->user_type;
	$user_id = $query[0]->user_type_id;

	if($user_type=='Vendor'){
		$table_name = $wpdb->prefix . "order_claim_part";
		$table_name3 = $wpdb->prefix . "sync_vendor";
		$table_name2 = $wpdb->prefix . "sync_travelagency";
		
		$query = $wpdb->get_results("SELECT * from $table_name WHERE `vendor_id`='".$user_id."'");
		
		if(count($query)==0){
			?>
<section id="contact">
				<div class="container">
				  <h2 class="h2 ac" style="color: red !important; font-weight: bold !important;">You have no commision distribution records in database.</h2>
				</div>
			</section>
<?php
		}else{
			?>
			<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/order/style-admin.css" rel="stylesheet" />
			<div class="wrap">
				<h2>Commision Distribution</h2>
				<div style="overflow-x: auto;">
				<table class='wp-list-table widefat fixed striped posts'>
					<thead>
						<tr>
							<th class="manage-column ss-list-width">Order Id</th>
							<th class="manage-column ss-list-width">Vendor Name</th>
							<th class="manage-column ss-list-width">Agent Name</th>
							<th class="manage-column ss-list-width">Tatrooms Payment</th>
							<th class="manage-column ss-list-width">Tatrooms Payment Percent</th>
							<th class="manage-column ss-list-width">Vendor Payment</th>
							<th class="manage-column ss-list-width">Vendor Payment Percent</th>
							<th class="manage-column ss-list-width">Agent Payment</th>
							<th class="manage-column ss-list-width">Agent Payment Percent</th>
							<th class="manage-column ss-list-width">GST</th>
							<th class="manage-column ss-list-width">GST Order Percent</th>
							<th class="manage-column ss-list-width">Date</th>
						</tr>
					</thead>
			<?php
			$query = $wpdb->get_results("SELECT * from $table_name WHERE `vendor_id`='".$user_id."'");
			
			for($i=0;$i<count($query);$i++){
				$vendor_query = $wpdb->get_results("SELECT * from $table_name3 WHERE `id`='".$query[$i]->vendor_id."'");
				$agent_query = $wpdb->get_results("SELECT * from $table_name2 WHERE `id`='".$query[$i]->agent_id."'");
				?>
				   <tr>
								<td><?php echo $query[$i]->order_id; ?></td>
								<td><?php echo $vendor_query[0]->full_name; ?></td>
								<?php
									if($query[$i]->agent_id==0){
										?>
										<td>No Agent</td>
										<?php
									}else{
										?>
										<td><?php echo $agent_query[0]->full_name; ?></td>
										<?php
									}
								?>
								<td><?php echo $query[$i]->tatrooms_payment; ?></td>
								<td><?php echo $query[$i]->tatrooms_payment_percent; ?></td>
								<td><?php echo $query[$i]->vendor_payment; ?></td>
								<td><?php echo $query[$i]->vendor_payment_percent; ?></td>
								<td><?php echo $query[$i]->agent_payment; ?></td>
								<td><?php echo $query[$i]->agent_payment_percent; ?></td>
								<td><?php echo $query[$i]->gst; ?></td>
								<td><?php echo $query[$i]->gst_order_percent; ?></td>
								<td><?php echo $query[$i]->date; ?></td>
							</tr>
				<?php
			}
		}
			
	}else{
		$query = $wpdb->get_results("SELECT * from $table_name");
		
		if(count($query)==0){
			?>
<section id="contact">
				<div class="container">
				  <h2 class="h2 ac" style="color: red !important; font-weight: bold !important;">You have no commision distribution records in database.</h2>
				</div>
			</section>
<?php
		}else{
			$table_name = $wpdb->prefix . "order_claim_part";
			$table_name2 = $wpdb->prefix . "sync_travelagency";
			$table_name3 = $wpdb->prefix . "sync_vendor";
		
			?>
			<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/order/style-admin.css" rel="stylesheet" />
				<div class="wrap">
					<h2>Commision Distribution</h2>
					<div style="overflow-x: auto;">
					<table class='wp-list-table widefat fixed striped posts'>
						<thead>
							<tr>
								<th class="manage-column ss-list-width">Order Id</th>
								<th class="manage-column ss-list-width">Vendor Name</th>
								<th class="manage-column ss-list-width">Agent Name</th>
								<th class="manage-column ss-list-width">Tatrooms Payment</th>
								<th class="manage-column ss-list-width">Tatrooms Payment Percent</th>
								<th class="manage-column ss-list-width">Vendor Payment</th>
								<th class="manage-column ss-list-width">Vendor Payment Percent</th>
								<th class="manage-column ss-list-width">Agent Payment</th>
								<th class="manage-column ss-list-width">Agent Payment Percent</th>
								<th class="manage-column ss-list-width">GST</th>
								<th class="manage-column ss-list-width">GST Order Percent</th>
								<th class="manage-column ss-list-width">Date</th>
							</tr>
						</thead>
			<?php
			$query = $wpdb->get_results("SELECT * from $table_name");
			for($i=0;$i<count($query);$i++){
				$agent_query = $wpdb->get_results("SELECT * from $table_name2 WHERE `id`='".$query[$i]->agent_id."'");
				$vendor_query = $wpdb->get_results("SELECT * from $table_name3 WHERE `id`='".$query[$i]->vendor_id."'");
				?>
				   <tr>
								<td><?php echo $query[$i]->order_id; ?></td>
								<td><?php echo $vendor_query[0]->full_name; ?></td>
								<?php
									if($query[$i]->agent_id==0){
										?>
										<td>No Agent</td>
										<?php
									}else{
										?>
										<td><?php echo $agent_query[0]->full_name; ?></td>
										<?php
									}
								?>
								<td><?php echo $query[$i]->tatrooms_payment; ?></td>
								<td><?php echo $query[$i]->tatrooms_payment_percent; ?></td>
								<td><?php echo $query[$i]->vendor_payment; ?></td>
								<td><?php echo $query[$i]->vendor_payment_percent; ?></td>
								<td><?php echo $query[$i]->agent_payment; ?></td>
								<td><?php echo $query[$i]->agent_payment_percent; ?></td>
								<td><?php echo $query[$i]->gst; ?></td>
								<td><?php echo $query[$i]->gst_order_percent; ?></td>
								<td><?php echo $query[$i]->date; ?></td>
							</tr>
				<?php
			}
		}
	}
}
?>