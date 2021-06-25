<?php

function nlt_hotelroomlisting_create() {
	global $wpdb;
	
	$posts = get_posts([
	  'post_type' => 'hotellisting',
	  'post_status' => 'publish',
	  'numberposts' => -1
	]);
	
	$hotel_array = array();
	for($i=0;$i<count($posts);$i++){
		$hotel_array[$posts[$i]->ID] = $posts[$i]->post_title;
	}
	
    //insert
    if (isset($_POST['insert'])) {
		
		$hotel_id = $_POST['hotel_id'];
		$room_name = $_POST['room_name'];
		$room_description = $_POST['room_description'];
		$room_accomodation = $_POST['room_accomodation'];
		$room_price = $_POST['room_price'];
		$discount = $_POST['discount'];
		$category = $_POST['category'];
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "nlt_hotel_room_listing";
		
		if($_POST['hotel_id']=='')				
		{					
			$message1='Please select hotel.<br>';				
		}else if($_POST['category']=='')				
		{					
			$message1='Please select room category.<br>';				
		}else if($_POST['room_name']=='')				
		{					
			$message1='Enter room name.<br>';				
		}
		else if($_POST['room_description']=='')				
		{					
			$message1='Enter description.<br>';				
		}
		else if($_POST['room_accomodation']=='')				
		{					
			$message1='Enter room accomodation.<br>';				
		}
		else if(!is_numeric($_POST['room_accomodation']))				
		{					
			$message1='Room accomodation should be numeric.<br>';				
		}
		else if($_POST['room_price']=='')				
		{					
			$message1='Enter room price.<br>';				
		}
		else if(!is_numeric($_POST['room_price']))				
		{					
			$message1='Room price should be numeric.<br>';				
		}
		else if($_POST['discount']=='')				
		{					
			$message1='Enter discount.<br>';				
		}
		else if(!is_numeric($_POST['discount']))				
		{					
			$message1='Discount should be numeric.<br>';				
		}
		else
		{
			$wpdb->insert(
					$table_name, //table
					array(
					'hotel_id' => $hotel_id,
					'room_name' => $room_name,
					'room_description' => htmlentities($room_description),
					'room_accomodation' => $room_accomodation,
					'room_price' => $room_price,
					'discount' => $discount,
					'room_category_id' => $category,
					), //data
					array(
					'%s', 
					'%s', 
					'%s',
					'%s',
					'%s', 
					'%s', 
					'%s'
					) //data format			
			);
			$message1="Room saved successfully.";
		}
    }else{
		$hotel_id = '';
		$room_name = '';
		$room_description = '';
		$room_accomodation = '';
		$room_price = '';
		$category='';
		$discount = '0';
	}
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add New Hotel Room</h2>
        <?php if (isset($message1)): ?><div class="updated"><p><?php echo $message1; ?></p></div><?php endif; ?>
        <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>" enctype="multipart/form-data">
            <table class='wp-list-table widefat fixed'>
                <tr>
                    <th class="ss-th-width">Select Hotel</th>
                    <td>
						<select name="hotel_id" class="ss-field-width">
							<option value="">Select One</option>
						<?php
							foreach($hotel_array AS $key=>$val){
						?>
							<option value="<?php echo $key; ?>" <?php if($hotel_id==$key){ echo 'selected="selected"'; }else{ echo ''; } ?>><?php echo $val; ?></option>
						<?php
						}
						?>
						</select>
					</td>
                </tr>
				<tr>
                    <th class="ss-th-width">Room Name</th>
                    <td><input type="text" name="room_name" value="<?php echo $room_name; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Room Description</th>
                    <td><?php wp_editor( stripslashes(html_entity_decode($room_description)), 'room_description' ); ?></td>
                </tr>
				<tr>
				<?php
					$table_name = $wpdb->prefix . "sync_categories";
					$query = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name"));
					
				?>
					<th class="ss-th-width">Room Category</th>
					<td>
						<select name="category" class="ss-field-width">
						<option value="">Select One</option>
						<?php
						foreach($query as $value){
							?>
							<option value="<?php echo $value->id; ?>" <?php if($category==$value->id){ echo 'selected="selected"'; }else{ echo ''; } ?>><?php echo $value->category; ?></option>
							<?php
						}
						?>
						</select>
					</td>
				</tr>
				<tr>
                    <th class="ss-th-width">Room Accomodation</th>
                    <td><input type="text" name="room_accomodation" value="<?php echo $room_accomodation; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Room Price</th>
                    <td><input type="text" name="room_price" value="<?php echo $room_price; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Discount (in Rupees)</th>
                    <td><input type="text" name="discount" value="<?php echo $discount; ?>" class="ss-field-width" /></td>
                </tr>
				
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    <?php
}