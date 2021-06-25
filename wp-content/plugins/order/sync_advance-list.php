<?php
function sync_advance_list() {
	
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
		
		
		$array = array();
		for($i=0;$i<count($current_user_posts);$i++){
			$array[$i] = $current_user_posts[$i]->ID;
		}?>
		<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/order/style-admin.css" rel="stylesheet" />
		<div class="wrap">
			<h2>Pay full for advance booking</h2>
		<?php
		$flag='';
		$count=0;
		$message = '';
		$table_name = $wpdb->prefix . "order";
		$query = $wpdb->get_results("SELECT * from $table_name WHERE `amount_type`='Advance'");
		?>
		<form action="" method="post">
			<label for="payment">Make Payment:</label><br>
			<select name="order_id">
				<option value="">Select advance invoice number</option>
		<?php
		for($i=0;$i<count($query);$i++){
			if(in_array($query[$i]->hotel_id, $array)){
				$query1 = $wpdb->get_results("SELECT * from $table_name WHERE `parent_order_id`='".$query[$i]->id."'");
				if(count($query1)==0){
			?>
					  <option value="<?php echo $query[$i]->id; ?>"><?php echo $query[$i]->full_name.'-'.$query[$i]->id; ?></option>
			<?php
				}
			}
		}
		?>
		</select>
		<input type="text" name="pay_amount" value="" placeholder="Amount" required='required'>
			<input type="submit" name="submit" value="Submit"><br>
			</form><br>
		</div>
	<?php
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
			<h2>Pay full for advance booking</h2>
			<?php
			global $wpdb;
			$flag='';
			$count=0;
			$message = '';
			$table_name = $wpdb->prefix . "order";
			$query = $wpdb->get_results("SELECT * from $table_name WHERE `amount_type`='Advance'");
			?>
			<form action="" method="post">
			<label for="payment">Make Payment:</label><br>
			<select name="order_id">
				<option value="">Select advance invoice number</option>
			<?php
			for($i=0;$i<count($query);$i++){
				$query1 = $wpdb->get_results("SELECT * from $table_name WHERE `parent_order_id`='".$query[$i]->id."'");
				if(count($query1)==0){
			?>
					  <option value="<?php echo $query[$i]->id; ?>"><?php echo $query[$i]->full_name.'-'.$query[$i]->id; ?></option>
			<?php
				}
			}
			?>
			</select>
			<input type="text" name="pay_amount" value="" placeholder="Amount" required='required'>
			<input type="submit" name="submit" value="Submit"><br>
			</form><br>
		</div>
		<?php
	}
	
	if(isset($_POST['submit'])){
		$payAmount = $_POST['pay_amount'];
		$order_id = $_POST['order_id'];
		$table_name = $wpdb->prefix . "order";

		$data = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$order_id."'");
		$total_amount = $data[0]->total_amount;
		$amount_paid = $data[0]->amount_paid;
		$dueAmount = $total_amount-$amount_paid;

		$hotel_id = $data[0]->hotel_id;
		$full_name = $data[0]->full_name;
		$email = $data[0]->email;
		$phone = $data[0]->phone;
		$room_id = $data[0]->room_type_id;
		$facilities = $data[0]->facilities_ids;
		$total_amount = $data[0]->amount_paid;
		$ammount_type = 'Advance-Full';
		$facility_price = $data[0]->facility_amount;
		$gst = $data[0]->gst;
		$gst_percent = $data[0]->gst_percent;
		$room_amount = $data[0]->room_amount;
		$total_amount = $data[0]->total_amount;
		$check_in_date = $data[0]->check_in_date;
		$check_out_date = $data[0]->check_out_date;
		$user_id = $data[0]->type_id;
		$user_type = $data[0]->type;
		$pay_revenue = 'hotel';
		if($order_id==''){
			echo "Please select advance invoice number.";
		}else if(!is_numeric($payAmount)){
			echo "Amount should be numeric";
		}else if($dueAmount!=$payAmount){
			echo "Please submit valid amount. Due amount is ₹".$dueAmount;
		}else{
		    global $wpdb;
			$wpdb->insert(
					$table_name, //table
					array(
					'hotel_id' => $hotel_id,
					'full_name' => $full_name,
					'email' => $email,
					'phone' => $phone,
					'room_type_id' => $room_id,
					'facilities_ids' => $facilities,
					'amount_paid' => $payAmount,
					'amount_type' => $ammount_type,
					'facility_amount'=> $facility_price,
					'gst'=> $gst,
					'gst_percent'=>$gst_percent,
					'room_amount'=> $room_amount,
					'total_amount' => $total_amount,
					'check_in_date' => $check_in_date,
					'check_out_date' => $check_out_date,
					'type_id' => $user_id,
					'parent_order_id' => $order_id,
					'type' => $user_type,
					'pay_revenue' => $pay_revenue
					), //data
					array(
					'%s', 
					'%s', 
					'%s',
					'%s', 
					'%s', 
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s',
					'%s'
					) //data format			
			);
			//echo $wpdb->last_query;
			echo 'Amount successfully paid.';
		}
	}
}
?>