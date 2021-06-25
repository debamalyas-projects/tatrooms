<?php

function nlt_hotelroomlisting_list() {
		
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
		}
		?>
		<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
		 <h2>Hotel rooms</h2>
		<div class="tablenav top">
			<div class="alignleft actions">
				<a href="<?php echo admin_url('admin.php?page=nlt_hotelroomlisting_create'); ?>">Add New Hotel Room</a>
			</div>
			<br class="clear">
		</div>
		<?php
		 global $wpdb;
		$table_name = $wpdb->prefix . "nlt_hotel_room_listing";
		$table_name1 = $wpdb->prefix . "sync_categories";
		
		$row = $wpdb->get_results("SELECT * from $table_name");
		?>
		<table class='wp-list-table widefat fixed striped posts'>
			<tr>
				<th class="manage-column ss-list-width">ID</th>
				<th class="manage-column ss-list-width">Hotel Name</th>
				<th class="manage-column ss-list-width">Room Name</th>
				<th class="manage-column ss-list-width">Room Category</th>
				<th class="manage-column ss-list-width">Room Accomodation</th>
				<th class="manage-column ss-list-width">Room Price</th>
				<th class="manage-column ss-list-width">Discount</th>
				<th class="manage-column ss-list-width">Status</th>
				<th class="manage-column ss-list-width">Action</th>
				<th>&nbsp;</th>
			</tr>
			<?php for ($i=0;$i<count($row);$i++) { 
				if(in_array($row[$i]->hotel_id, $array)){?>
				<tr>
					<td class="manage-column ss-list-width"><?php echo $row[$i]->id; ?></td>
					<td class="manage-column ss-list-width"><?php echo $hotel_array[$row[$i]->hotel_id]; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row[$i]->room_name; ?></td>
					<?php
					if($row->room_category_id!=0){
						$category_sql = $wpdb->get_results("SELECT * from $table_name,$table_name1 WHERE $table_name.`room_category_id`=$table_name1.`id` AND $table_name.`id`='".$row->id."'");
						?>
						<td class="manage-column ss-list-width"><?php echo $category_sql[0]->category; ?></td>
						<?php
					}else{?>
						<td class="manage-column ss-list-width"><?php echo "Category-1"; ?></td>
						<?php
					}
					?>
					<td class="manage-column ss-list-width"><?php echo $row[$i]->room_accomodation; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row[$i]->room_price; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row[$i]->discount; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row[$i]->status; ?></td>
					<td><a href="<?php echo admin_url('admin.php?page=nlt_hotelroomlisting_update&id=' . $row->id); ?>">Update</a></td>
				</tr>
			<?php
				}
			}
			?>
		</table>
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
	<link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
	<div class="wrap">
		<h2>Hotel rooms</h2>
		<div class="tablenav top">
			<div class="alignleft actions">
				<a href="<?php echo admin_url('admin.php?page=nlt_hotelroomlisting_create'); ?>">Add New Hotel Room</a>
			</div>
			<br class="clear">
		</div>
		<?php
		global $wpdb;
		$table_name = $wpdb->prefix . "nlt_hotel_room_listing";
		$table_name1 = $wpdb->prefix . "sync_categories";
		
		$rows = $wpdb->get_results("SELECT * from $table_name");
	
		?>
		<table class='wp-list-table widefat fixed striped posts'>
			<tr>
				<th class="manage-column ss-list-width">ID</th>
				<th class="manage-column ss-list-width">Hotel Name</th>
				<th class="manage-column ss-list-width">Room Name</th>
				<th class="manage-column ss-list-width">Room Category</th>
				<th class="manage-column ss-list-width">Room Accomodation</th>
				<th class="manage-column ss-list-width">Room Price</th>
				<th class="manage-column ss-list-width">Discount</th>
				<th class="manage-column ss-list-width">Status</th>
				<th>&nbsp;</th>
			</tr>
			<?php foreach ($rows as $row) { ?>
				<tr>
					<td class="manage-column ss-list-width"><?php echo $row->id; ?></td>
					<td class="manage-column ss-list-width"><?php echo $hotel_array[$row->hotel_id]; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->room_name; ?></td>
					<?php
					if($row->room_category_id!=0){
						$category_sql = $wpdb->get_results("SELECT * from $table_name,$table_name1 WHERE $table_name.`room_category_id`=$table_name1.`id` AND $table_name.`id`='".$row->id."'");
						?>
						<td class="manage-column ss-list-width"><?php echo $category_sql[0]->category; ?></td>
						<?php
					}else{?>
						<td class="manage-column ss-list-width"><?php echo "Category-1"; ?></td>
						<?php
					}
					?>
					<td class="manage-column ss-list-width"><?php echo $row->room_accomodation; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->room_price; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->discount; ?></td>
					<td class="manage-column ss-list-width"><?php echo $row->status; ?></td>
					<td><a href="<?php echo admin_url('admin.php?page=nlt_hotelroomlisting_update&id=' . $row->id); ?>">Update</a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
	<?php
	}

}
?>