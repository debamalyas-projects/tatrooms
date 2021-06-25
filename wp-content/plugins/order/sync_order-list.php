<?php
function sync_order_list() {
	
	$user = get_current_user_id();
	
	global $wpdb;
	
	$table_name = $wpdb->prefix . "users";

	$query = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$user."'");
	
	$user_type = $query[0]->user_type;
	
	if($user_type=='Vendor'){       

		$posts = get_posts([
		  'post_type' => 'hotellisting',
		  'post_status' => 'publish',
		  'numberposts' => -1
		]);
	
		$hotel_array = array();
		for($i=0;$i<count($posts);$i++){
			$hotel_array[$posts[$i]->ID] = $posts[$i]->post_title;
		}

		$args = array(
		  'author'        =>  $user, 
		  'post_type' => 'hotellisting',
		  'post_status' => 'publish',
		  'numberposts' => -1
		  );
		
		$current_user_posts = get_posts( $args );
		
		/*print_r($current_user_posts);
		die();*/
		/*for($i=0;i<count($current_user_posts);$i++){
				echo $current_user_posts[$i]->ID;
		}
		*/
		
		$array = array();
		for($i=0;$i<count($current_user_posts);$i++){
			$array[$i] = $current_user_posts[$i]->ID;
		}
		//print_r($array);
		$table_name = $wpdb->prefix . "order";
		$query1 = $wpdb->get_results("SELECT * from $table_name");
		?>
		<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/order/style-admin.css" rel="stylesheet" />
		<div class="wrap">
			<h2>Order</h2>
			<div style="overflow-x: auto;">
			<table class='wp-list-table widefat fixed striped posts'>
				<thead>
					<tr>
						<th class="manage-column ss-list-width">Customer Name</th>
						<th class="manage-column ss-list-width">Customer Type</th>
						<th class="manage-column ss-list-width">Hotel Name</th>
						<th class="manage-column ss-list-width">Room Name</th>
						<th class="manage-column ss-list-width">Email Id</th>
						<th class="manage-column ss-list-width">Mobile Number</th>
						<th class="manage-column ss-list-width">Facilities</th>
						<th class="manage-column ss-list-width">Amount Paid</th>
						<th class="manage-column ss-list-width">Amount Type</th>
						<th class="manage-column ss-list-width">Facility Amount</th>
						<th class="manage-column ss-list-width">GST</th>
						<th class="manage-column ss-list-width">Room Amount</th>
						<th class="manage-column ss-list-width">Total Amount</th>
						<th class="manage-column ss-list-width">Check In</th>
						<th class="manage-column ss-list-width">Check Out</th>
						<th class="manage-column ss-list-width">Order Id</th>
						<th class="manage-column ss-list-width">Parent Order Id</th>
						<th class="manage-column ss-list-width">Invoice</th>
					</tr>
				</thead>
				
	<?php
	
		for($i=0;$i<count($query1);$i++){
			if(in_array($query1[$i]->hotel_id, $array)){
				$gst_percent = $query1[$i]->gst_percent;
					$amount_type = $query1[$i]->amount_type;
					$room_id = $query1[$i]->room_type_id;
					$due_amount = $query1[$i]->total_amount-$query1[$i]->amount_paid;
					$checkin=$query1[$i]->check_in_date;
					$checkout=$query1[$i]->check_out_date;
					$date1=date_create($checkin);
					$date2=date_create($checkout);
					$diff=date_diff($date1,$date2);
					$nodays=$diff->format("%R%a");
					if($nodays==0){
						$nodays=1;
					}
					$room_amount_total = $query1[$i]->room_amount*($nodays);
					$facility_amount_total = $query1[$i]->facility_amount*($nodays);
					 $table_name = $wpdb->prefix . "nlt_hotel_room_listing";
					$sql = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$room_id."'");
					$facilities_ids = $query1[$i]->facilities_ids;
					$facilities_ids = explode('||',$facilities_ids);
					//print_r($facilities_ids);
					for($j=0;$j<count($facilities_ids);$j++){
						if($j%2==1){
							if($j==1){
								$facilities = $facilities_ids[$j];
							}else{
								$facilities = $facilities.','.$facilities_ids[$j];
							}
						}
					}
				?>
				<tr>
						<td><?php echo $query1[$i]->full_name; ?></td>
						<td><?php echo $query1[$i]->type; ?></td>
						<td><?php echo $hotel_array[$query1[$i]->hotel_id]; ?></td>
						<td><?php echo $sql[0]->room_name; ?></td>
						<td><?php echo $query1[$i]->email; ?></td>
						<td><?php echo $query1[$i]->phone; ?></td>
						<td><?php echo $facilities; ?></td>
						<td><?php echo $query1[$i]->amount_paid; ?></td>
						<td><?php echo $query1[$i]->amount_type; ?></td>
						<td><?php echo $query1[$i]->facility_amount; ?></td>
						<td><?php echo $query1[$i]->gst; ?></td>
						<td><?php echo $query1[$i]->room_amount; ?></td>
						<td><?php echo $query1[$i]->total_amount; ?></td>
						<td><?php echo $query1[$i]->check_in_date; ?></td>
						<td><?php echo $query1[$i]->check_out_date; ?></td>
						<td><?php echo $query1[$i]->id; ?></td>
						<td><?php echo $query1[$i]->parent_order_id; ?></td>
						<td>
							<a href="javascript:void(0);" onclick="printInvoice('order-<?php echo $query1[$i]->id; ?>')">Print</a>
							<div id="order-<?php echo $query1[$i]->id; ?>" style="display: none;">
								<table border='1'>
									<tr>
										<td colspan='3' align='center'><img src="<?php echo get_site_url(); ?>/wp-content/uploads/2019/12/tatrooms-logo.png" alt="" title=""></td>
									</tr>
									<tr>
										<td colspan='3' align='center'><h3><u>Invoice</u></h3></td>
									</tr>
									<tr>
										<td align='center'><h3>Customer name</h3></td>
										<td colspan='2' align='center'><h3><?php echo $query1[$i]->full_name; ?></h3></td>
									</tr>
									<tr>
										<td align='center'><h3>Hotel booked</h3></td>
										<td colspan='2' align='center' ><h3><?php echo $hotel_array[$query1[$i]->hotel_id]; ?></h3></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Product</h3></td>
										<td align='center' ><h3>Price</h3></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Room booked : <?php echo $sql[0]->room_name; ?></h3></td>
										<td align='center' >Rs. <?php echo $room_amount_total; ?></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3><u>Facilities</u><br>
										<?php echo $facilities; ?></h3></td>
										<td align='center' >Rs. <?php echo $facility_amount_total; ?></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Service Tax (<?php echo $gst_percent; ?>%)</h3></td>
										<td align='center' >Rs. <?php echo $query1[$i]->gst; ?></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Grand Total</h3></td>
										<td align='center' >Rs. <?php echo $query1[$i]->total_amount; ?></td>
									</tr>
									<?php
										   if($amount_type=='Advance'){
										?>
									<tr>
										<td colspan='2' align='center'><h3>Paid Amount</h3></td>
										<td align='center' >Rs. <?php echo $query1[$i]->amount_paid; ?></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Due Amount</h3></td>
										<td align='center' >Rs. <?php echo $due_amount; ?></td>
									</tr>
									<?php
										   }   
									?>
								</table>
							</div>
						</td>
					</tr>
					<?php $facilities=''; } ?>
					<script>
					function printInvoice(id){
						var prtContent = document.getElementById(id);
						var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
						WinPrint.document.write(prtContent.innerHTML);
						WinPrint.document.close();
						WinPrint.focus();
						WinPrint.print();
						WinPrint.close();
					}
					</script>
					
					<?php
			
		}
	
	}else{
		$posts = get_posts([
		  'post_type' => 'hotellisting',
		  'post_status' => 'publish',
		  'numberposts' => -1
		]);
	
		$hotel_array = array();
		for($i=0;$i<count($posts);$i++){
			$hotel_array[$posts[$i]->ID] = $posts[$i]->post_title;
		}
		?>
		<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/order/style-admin.css" rel="stylesheet" />
		<div class="wrap">
			<h2>Order</h2>
			<?php
			$table_name = $wpdb->prefix . "order";

			$query = $wpdb->get_results("SELECT * from $table_name");
			?>
			<div style="overflow-x: auto;">
			<table class='wp-list-table widefat fixed striped posts'>
				<thead>
					<tr>
						<th class="manage-column ss-list-width">Customer Name</th>
						<th class="manage-column ss-list-width">Customer Type</th>
						<th class="manage-column ss-list-width">Hotel Name</th>
						<th class="manage-column ss-list-width">Room Name</th>
						<th class="manage-column ss-list-width">Email Id</th>
						<th class="manage-column ss-list-width">Mobile Number</th>
						<th class="manage-column ss-list-width">Facilities</th>
						<th class="manage-column ss-list-width">Amount Paid</th>
						<th class="manage-column ss-list-width">Amount Type</th>
						<th class="manage-column ss-list-width">Facility Amount</th>
						<th class="manage-column ss-list-width">GST</th>
						<th class="manage-column ss-list-width">Room Amount</th>
						<th class="manage-column ss-list-width">Total Amount</th>
						<th class="manage-column ss-list-width">Check In</th>
						<th class="manage-column ss-list-width">Check Out</th>
						<th class="manage-column ss-list-width">Order Id</th>
						<th class="manage-column ss-list-width">Parent Order Id</th>
						<th class="manage-column ss-list-width">Invoice</th>
					</tr>
				</thead>
				
				<?php
				
				for($i=0;$i<count($query);$i++){ 
					$gst_percent = $query[$i]->gst_percent;
					$amount_type = $query[$i]->amount_type;
					$room_id = $query[$i]->room_type_id;
					$due_amount = $query[$i]->total_amount-$query[$i]->amount_paid;
					$checkin=$query[$i]->check_in_date;
					$checkout=$query[$i]->check_out_date;
					$date1=date_create($checkin);
					$date2=date_create($checkout);
					$diff=date_diff($date1,$date2);
					$nodays=$diff->format("%R%a");
					if($nodays==0){
						$nodays=1;
					}
					$room_amount_total = $query[$i]->room_amount*($nodays);
					$facility_amount_total = $query[$i]->facility_amount*($nodays);
				?>
					<?php
					 $table_name = $wpdb->prefix . "nlt_hotel_room_listing";
					$sql = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$room_id."'");
					$facilities_ids = $query[$i]->facilities_ids;
					$facilities_ids = explode('||',$facilities_ids);
					//print_r($facilities_ids);
					for($j=0;$j<count($facilities_ids);$j++){
						if($j%2==1){
							if($j==1){
								$facilities = $facilities_ids[$j];
							}else{
								$facilities = $facilities.','.$facilities_ids[$j];
							}
						}
					}
					?>
					<tr>
						<td><?php echo $query[$i]->full_name; ?></td>
						<td><?php echo $query[$i]->type; ?></td>
						<td><?php echo $hotel_array[$query[$i]->hotel_id]; ?></td>
						<td><?php echo $sql[0]->room_name; ?></td>
						<td><?php echo $query[$i]->email; ?></td>
						<td><?php echo $query[$i]->phone; ?></td>
						<td><?php echo $facilities; ?></td>
						<td><?php echo $query[$i]->amount_paid; ?></td>
						<td><?php echo $query[$i]->amount_type; ?></td>
						<td><?php echo $query[$i]->facility_amount; ?></td>
						<td><?php echo $query[$i]->gst; ?></td>
						<td><?php echo $query[$i]->room_amount; ?></td>
						<td><?php echo $query[$i]->total_amount; ?></td>
						<td><?php echo $query[$i]->check_in_date; ?></td>
						<td><?php echo $query[$i]->check_out_date; ?></td>
						<td><?php echo $query[$i]->id; ?></td>
						<td><?php echo $query[$i]->parent_order_id; ?></td>
						<td>
							<a href="javascript:void(0);" onclick="printInvoice('order-<?php echo $query[$i]->id; ?>')">Print</a>
							<div id="order-<?php echo $query[$i]->id; ?>" style="display: none;">
								<table border='1'>
									<tr>
										<td colspan='3' align='center'><img src="<?php echo get_site_url(); ?>/wp-content/uploads/2019/12/tatrooms-logo.png" alt="" title=""></td>
									</tr>
									<tr>
										<td colspan='3' align='center'><h3><u>Invoice</u></h3></td>
									</tr>
									<tr>
										<td align='center'><h3>Customer name</h3></td>
										<td colspan='2' align='center'><h3><?php echo $query[$i]->full_name; ?></h3></td>
									</tr>
									<tr>
										<td align='center'><h3>Hotel booked</h3></td>
										<td colspan='2' align='center' ><h3><?php echo $hotel_array[$query[$i]->hotel_id]; ?></h3></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Product</h3></td>
										<td align='center' ><h3>Price</h3></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Room booked : <?php echo $sql[0]->room_name; ?></h3></td>
										<td align='center' >Rs. <?php echo $room_amount_total; ?></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3><u>Facilities</u><br>
										<?php echo $facilities; ?></h3></td>
										<td align='center' >Rs. <?php echo $facility_amount_total; ?></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Service Tax (<?php echo $gst_percent; ?>%)</h3></td>
										<td align='center' >Rs. <?php echo $query[$i]->gst; ?></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Grand Total</h3></td>
										<td align='center' >Rs. <?php echo $query[$i]->total_amount; ?></td>
									</tr>
									<?php
										   if($amount_type=='Advance'){
										?>
									<tr>
										<td colspan='2' align='center'><h3>Paid Amount</h3></td>
										<td align='center' >Rs. <?php echo $query[$i]->amount_paid; ?></td>
									</tr>
									<tr>
										<td colspan='2' align='center'><h3>Due Amount</h3></td>
										<td align='center' >Rs. <?php echo $due_amount; ?></td>
									</tr>
									<?php
										   }
									?>
								</table>
							</div>
						</td>
					</tr>
					
				<?php $facilities=''; } ?>
			</table>
			</div>
		</div>
		<script>
			function printInvoice(id){
				var prtContent = document.getElementById(id);
				var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
				WinPrint.document.write(prtContent.innerHTML);
				WinPrint.document.close();
				WinPrint.focus();
				WinPrint.print();
				WinPrint.close();
			}
		</script>
		<?php
	}
}
?>