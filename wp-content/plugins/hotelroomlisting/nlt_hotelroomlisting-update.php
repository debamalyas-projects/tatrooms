<?php
function nlt_hotelroomlisting_update() {
    
    $posts = get_posts([
	  'post_type' => 'hotellisting',
	  'post_status' => 'publish',
	  'numberposts' => -1
	]);
	
	$hotel_array = array();
	for($i=0;$i<count($posts);$i++){
		$hotel_array[$posts[$i]->ID] = $posts[$i]->post_title;
	}
    
	global $wpdb;
	$table_name = $wpdb->prefix . "nlt_hotel_room_listing";
	$id = $_GET["id"];
//update
    if (isset($_POST['update'])) {
		$hotel_id = $_POST['hotel_id'];
		$room_name = $_POST['room_name'];
		$room_description = $_POST['room_description'];
		$room_accomodation = $_POST['room_accomodation'];
		$room_price = $_POST['room_price'];
		$discount = $_POST['discount'];
		$status = $_POST["status"];
		$category = $_POST["category"];
		
		if($_POST['hotel_id']=='')				
		{					
			$message1='Please select hotel.<br>';				
		}else if($_POST["category"]==''){
			$message1='Enter room category.<br>';	
		}
		else if($_POST['room_name']=='')				
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
			$wpdb->update(
                $table_name, //table
                array(
					'hotel_id' => $hotel_id,
					'room_name' => $room_name,
					'room_description' => htmlentities($room_description),
					'room_accomodation' => $room_accomodation,
					'room_price' => $room_price,
					'discount' => $discount,
					'status' => $status,
					'room_category_id'=>$category
					), //data
                array('id' => $id), //where
                array(
					'%s', 
					'%s', 
					'%s',
					'%s',
					'%s', 
					'%s', 
					'%s',
					'%s'
					), //data format
                array('%s') //where format
			);
			$message1="Room updated successfully.";
		}
    }
//delete
    else if(isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
    }else{//selecting value to update	
        $nlt_hotelroomlisting = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where id=%s", $id));
        foreach ($nlt_hotelroomlisting as $s) {
            $hotel_id = $s->hotel_id;
			$room_name = $s->room_name;
			$category = $s->room_category_id;
			$room_description = $s->room_description;
			$room_accomodation = $s->room_accomodation;
			$room_price = $s->room_price;
			$discount = $s->discount;
			$status = $s->status;
        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Hotel Room Edit</h2>
        <?php if ($_POST['delete']) { ?>
            <div class="updated"><p>Hotel Room deleted</p></div>
            <a href="<?php echo admin_url('admin.php?page=nlt_hotelroomlisting_list') ?>">&laquo; Back to hotel room list</a>
        <?php }else{ ?>
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
                        <th class="ss-th-width">Room Category</th>
                       <td>
							<select name="category" class="ss-field-width">
							<option value="">Select One</option>
							<?php
							$table_name_category = $wpdb->prefix . "sync_categories";
							$query = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name_category"));
							foreach($query as $value){
								?>
							<option value="<?php echo $value->id; ?>"<?php if($category==$value->id){ echo 'selected="selected"'; }else{ echo ''; } ?>><?php echo $value->category; ?></option>
							<?php
							}
							?>							
							</select>
						</td>
                    </tr>
    				<tr>
                        <th class="ss-th-width">Room Description</th>
                        <td><?php wp_editor( stripslashes(html_entity_decode($room_description)), 'room_description' ); ?></td>
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
					<tr>
						<th class="ss-th-width">Status</th>
						<td>
							<select name="status" class="ss-field-width">
								<option value="Active" <?php if($status=="Active"){ echo 'selected="selected"'; }else{ echo ''; } ?>>Active</option>
								<option value="Inactive" <?php if($status=="Inactive"){ echo 'selected="selected"'; }else{ echo ''; } ?>>Inactive</option>
							</select>
						</td>
					</tr>
                </table>
                <input type='submit' name="update" value='Update' class='button' onclick=""> &nbsp;&nbsp;
                <input type='submit' name="delete" value='Delete' class='button' onclick="return confirm('Are you sure you want to delete this?')">
            </form>
        <?php } ?>

    </div>
    <?php
}
?>