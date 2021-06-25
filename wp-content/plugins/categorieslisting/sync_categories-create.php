<?php

function sync_categories_create() {
	
    //insert
    if (isset($_POST['insert'])) {
		
		$categories = $_POST['categories'];
		$status = $_POST['status'];
		
		global $wpdb;
		
		if($_POST['categories']=='')				
		{					
			$message1='Enter categorie name.<br>';				
		}
		else
		{
			$query = $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."sync_categories` (`category`,`status`) VALUES (%s,%s)",$categories,$status));
			
			$message1="Categories saved successfully.";
		}
    }else{
		$categories = '';
	}
    ?>
    <link type="text/css" href="<?php echo WP_PLUGIN_URL; ?>/categorieslisting/style-admin.css" rel="stylesheet" />
    <div class="wrap">
        <h2>Add New Category</h2>
        <?php if (isset($message1)): ?><div class="updated"><p><?php echo $message1; ?></p></div><?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
            <table class='wp-list-table widefat fixed'>
				<tr>
                    <th class="ss-th-width">Category</th>
                    <td><input type="text" name="categories" value="<?php echo $categories; ?>" class="ss-field-width" /></td>
                </tr>
				<tr>
                    <th class="ss-th-width">Status</th>
					<td>
						<select name="status" class="ss-field-width">
							<option value="Active">Active</option>
							<option value="Inactive">Inactive</option>
						</select>
					</td>
                </tr>
            </table>
            <input type='submit' name="insert" value='Save' class='button'>
        </form>
    </div>
    <?php
}