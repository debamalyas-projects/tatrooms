<?php
function sync_categories_update() {
  
	global $wpdb;
	$table_name = $wpdb->prefix . "sync_categories";
	$id = $_GET["id"];
//update
    if (isset($_POST['update'])) {
		$category = $_POST['category'];
		$status = $_POST["status"];
		
		if($_POST['category']=='')				
		{					
			$message1='Enter category name.<br>';				
		}
		else
		{
			$wpdb->update(
                $table_name, //table
                array(
					'category' => $category,
					'status' => $status
					), //data
                array('id' => $id), //where
                array(
					'%s', 
					'%s'
					), //data format
                array('%s') //where format
			);
			$message1="Category updated successfully.";
		}
    }
//delete
    else if(isset($_POST['delete'])) {
        $wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE id = %s", $id));
    }else{//selecting value to update	
        $sync_categories = $wpdb->get_results($wpdb->prepare("SELECT * from $table_name where id=%s", $id));
        foreach ($sync_categories as $s) {
			$category = $s->category;
            $status = $s->status;
        }
    }
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/hotelroomlisting/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Category Edit</h2>
        <?php if ($_POST['delete']) { ?>
            <div class="updated"><p>Category deleted</p></div>
            <a href="<?php echo admin_url('admin.php?page=sync_categories_list') ?>">&laquo; Back to category list</a>
        <?php }else{ ?>
            <?php if (isset($message1)): ?><div class="updated"><p><?php echo $message1; ?></p></div><?php endif; ?>
            <form method="post" action="" enctype="multipart/form-data">
                <table class='wp-list-table widefat fixed'>
    				<tr>
                        <th class="ss-th-width">Category Name</th>
                        <td><input type="text" name="category" value="<?php echo $category; ?>" class="ss-field-width" /></td>
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