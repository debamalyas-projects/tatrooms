<?php
/*
 * Plugin Name: Hotel Listing
 * Description: This plugin is used to hotel listing.
 * Version: 1.0
 * Author: Nlogica technologies
 * Author URI: http://naantam.com
 */
 

// Creates Hotel Listing Custom Post Type
function hotellisting_init() {
    $args = array(
      'label' => 'Hotel Listing',
        'public' => false,
		'exclude_from_search' => false,
		'show_in_nav_menus' => false,
		'publicly_queryable' => true,
        'show_ui' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'rewrite' => array('slug' => 'hotellisting'),
        'query_var' => true,
        'menu_icon' => 'dashicons-admin-page',
        'supports' => array(
            'title',
            'editor',
            'author'
            )
        );
    register_post_type( 'hotellisting', $args );
}
add_action( 'init', 'hotellisting_init' );

function posts_for_current_author($query) {
    global $wpdb;
    global $user_ID;
    global $post_type;
    
    $sync_vendor = $wpdb->get_results($wpdb->prepare("SELECT * from `".$wpdb->prefix."users` where id=%s", $user_ID));
    
    $user_type = $sync_vendor[0]->user_type;
    
    if($user_type=='Vendor' && $post_type=='hotellisting' && $_SERVER['SCRIPT_URL']=='/wp-admin/edit.php'){
        $query->set('author', $user_ID);
    }
    
    return $query;
}
add_filter('pre_get_posts', 'posts_for_current_author');

add_action('wp_dropdown_users_args', 'filter_authors');
function filter_authors( $args ) {
	if ( isset( $args['who'])) {
		$args['role__in'] = ['administrator'];
		unset( $args['who']);
	}
	return $args;
}


add_filter( 'default_content', 'hotellisting_content', 10, 2);
function hotellisting_content($content, WP_Post $post){
    if ($post->post_type == 'hotellisting'){
    $content = '<div class="hotel_lists">
          <div class="col-lg-2 col-md-3 col-sm-12 col-12">
            <div class="hotel_img"><img src="[acf=image|{post_id}}]"></div>
          </div>
          <div class="col-lg-10 col-md-9 col-sm-12 col-12">
            <h3><span>[acf=hotel_name|{post_id}}]</span></h3>
            <p>[acf=hotel_information|{post_id}}]</p>
            <div class="rte red">[acf=currency|{post_id}}] [acf=price|{post_id}}] / [acf=tariff_parameter|{post_id}}]</div>
            <div class="buton"><a href="[acf=link|{post_id}}]" target="_blank" rel="noopener noreferrer">View Details</a></div>
          </div>
</div>';
}else{
    $content = '';
}

return $content;
}

function pagination_output($url,$query,$no_of_records,$records_per_page,$page_no){
    $offset = ($page_no-1) * $records_per_page;
    $total_pages = ceil($no_of_records / $records_per_page);
    
    $query_arr = explode('::',$query);
    
    $new_query = '';
    for($i=0;$i<count($query_arr);$i++){
        $query_kv_arr = explode(':',$query_arr[$i]);
        if($query_kv_arr[0]!='page'){
            $new_query .= $query_kv_arr[0].':'.$query_kv_arr[1].'::';
        }
    }
    
    $new_query = rtrim($new_query,'::');

    $prev_page = $page_no-1;
    $nex_page = $page_no+1;
    
    $page_output = '<div class="container">
      <div class="row">
<nav aria-label="Page navigation example">
  <ul class="pagination">';
  
  if($page_no=='1'){
    $page_output .= '';
  }else{
      $p_new_query = $new_query;
      if($p_new_query==''){
          $p_new_query = 'page:'.$prev_page;
      }else{
          $p_new_query = $p_new_query.'::page:'.$prev_page;
      }
      $page_output .= '<li class="page-item"><a class="page-link" href="'.$url.'?query='.$p_new_query.'">Previous</a></li>';
  }
  
  if($page_no==$total_pages){
      $page_output .= '';
  }else{
      $p_new_query = $new_query;
      if($p_new_query==''){
          $p_new_query = 'page:'.$nex_page;
      }else{
          $p_new_query = $p_new_query.'::page:'.$nex_page;
      }
      $page_output .= '<li class="page-item"><a class="page-link" href="'.$url.'?query='.$p_new_query.'">>Next</a></li>';
  }
  
  $page_output .= '</ul>
</nav>
</div>
</div>';
    echo $page_output;
}


function hotellisting_view(){
	 ob_start();
?>
<?php
$posts = get_posts([
  'post_type' => 'hotellisting',
  'post_status' => 'publish',
  'numberposts' => -1
  // 'order'    => 'ASC'
]);

if(isset($_GET['query'])){
    $query = $_GET['query'];
    $query_arr = explode('::',$query);
    
    $new_query = '';
    $loc_con = 0;
    for($k=0;$k<count($query_arr);$k++){
        $query_kv_arr = explode(':',$query_arr[$k]);
        
        if($query_kv_arr[0]=='location'){
            $new_posts = array();
            $location = $query_kv_arr[1];
            for($i=0;$i<count($posts);$i++){
                $loc = get_field('location',$posts[$i]->ID);
                if($loc==$location){
                    $loc_con = 1;
                    $new_posts[]=$posts[$i];
                }
            }
        }
        
        if($loc_con == 0){
            $new_posts = array();
            for($i=0;$i<count($posts);$i++){
                $new_posts[]=$posts[$i];
            }
        }
        $pag_con = 0;
        if($query_kv_arr[0]=='page'){
            $pag_con = 1;
            $page_no = $query_kv_arr[1];
        }
        
        if($pag_con == 0){
            $page_no = 1;
        }
        
        $no_of_records = count($new_posts);
        $record_per_page = '10';
        $url = get_site_url().'/hotel-list/';
    }
}else{
    $new_posts = array();
    for($i=0;$i<count($posts);$i++){
        $new_posts[]=$posts[$i];
    }
    
    $no_of_records = count($new_posts);
    $page_no = '1';
    $record_per_page = '10';
    $url = get_site_url().'/hotel-list/';
    $query = '';
}
$offset = ($page_no-1) * $record_per_page;
$upto = $offset+10;
for($j=$offset;$j<$upto;$j++){
    if(array_key_exists($j,$new_posts)){
        $content = tag_decoder($new_posts[$j]->post_content);
        echo $content;
    }
}

pagination_output($url,$query,$no_of_records,$record_per_page,$page_no);

?>
<?php
	$html=ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode('cleopatra_hotellisting', 'hotellisting_view');

function hoteldetails_view(){
	ob_start();
	if(isset($_GET['hotel'])){
		$hotel_id = $_GET['hotel'];		
?>
<!--============ slider Section ============-->
<section id="slider">
	<div style="margin:0px auto; max-width:1900px;">
    	<div id="amazingslider-1" style="display:block;position:relative;margin:0px auto;">
			<ul class="amazingslider-slides" style="display:block; text-align:center;">
				<?php
				if( have_rows('banner', $hotel_id) ):
					$out='';
					while( have_rows('banner', $hotel_id) ): the_row();
					
					$out.='<li><img src="'.get_sub_field('image').'" alt="" data-description="" /></li>';
					
					endwhile;
					
				endif;
				echo $out;
				?>
			</ul>
    
    </div>
    <!-- End of body section HTML codes -->  
  </div>
</section>
  <!-- End #slider -->
  

  <section id="hotel_detail_sec">
    <div class="container">
      <div class="row hotel_desc">
			<div class="col-lg-6 col-md-6 col-sm-12 col-12">
				<h2>About Hotel</h2>
				<p><?php echo get_field('hotel_information',$hotel_id); ?></p>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-12">
				<?php echo get_field('hotel_map',$hotel_id); ?>
			</div>
		</div>
		<div class="facility">
			<fieldset>
				<legend>Booking</legend>
		<div class="row">
			<?php
				if( have_rows('facilities', $hotel_id) ):
					$out='';
					$count=1;
					while( have_rows('facilities', $hotel_id) ): the_row();
					
					$out.='<div class="col-lg-3 col-md-4 col-sm-6 col-12">
					<div class="form-group"><input id="facility_'.$count.'" type="checkbox" onclick="check_uncheck_facility(this.value,\'facility_'.$count.'\');" value="'.get_sub_field('facility_price').'||'.get_sub_field('facility_name').'"> '.get_sub_field('facility_name').' (INR. '.get_sub_field('facility_price').')</div>
				</div>';
					$count++;
					endwhile;
					
				endif;
				echo $out;
			?>
		</div>
<?php
global $wpdb;
$table_name = $wpdb->prefix . "nlt_hotel_room_listing";

$rows = $wpdb->get_results("SELECT * from $table_name WHERE `hotel_id`='".$hotel_id."' AND `status`='Active'");

if(isset($_SESSION['search_check_in'])){
    
    if($_SESSION['search_check_in']!='' || $_SESSION['search_check_out']!='' || $_SESSION['search_accommodation']!=''){
        $new_rows = array();
        for($i=0;$i<count($rows);$i++){
            $accommodation = $rows[$i]->room_accomodation;
            $room_id = $rows[$i]->id;
            if($_SESSION['search_accommodation']<=$accommodation){
                $rows_order = $wpdb->get_results("SELECT * from `wp_order` WHERE `room_type_id`='".$room_id."'");
                
                $count_case=array();
                for($k=0;$k<count($rows_order);$k++){
                    $check_in = $rows_order[$k]->check_in_date;
                    $check_out = $rows_order[$k]->check_out_date;
                    
                    $search_check_in = $_SESSION['search_check_in'];
                    $search_check_out = $_SESSION['search_check_out'];
                    
                    $search_check_in_arr = explode('-',$search_check_in);
                    $search_check_in = $search_check_in_arr[2].'-'.$search_check_in_arr[1].'-'.$search_check_in_arr[0];
                    
                    $search_check_out_arr = explode('-',$search_check_out);
                    $search_check_out = $search_check_out_arr[2].'-'.$search_check_out_arr[1].'-'.$search_check_out_arr[0];
                    
                    $check_in=date_create($check_in);
                    $search_check_in=date_create($search_check_in);
                    $check_in_diff_obj=date_diff($check_in,$search_check_in);
                    $check_in_diff = $check_in_diff_obj->format("%R%a");
                    
                    $check_out=date_create($check_out);
                    $search_check_out=date_create($search_check_out);
                    $check_out_diff_obj=date_diff($check_out,$search_check_out);
                    $check_out_diff = $check_out_diff_obj->format("%R%a");
                    
                    if($check_in_diff<0 &&  $check_out_diff<0 || $check_in_diff>0 &&  $check_out_diff>0){
                        $count_case[] = 1;
                    }else{
                        $count_case[] = 0;
                    }
                }
                
                if(!in_array(0,$count_case)){
                    $new_rows[] = $rows[$i];
                }
            }
        }
        
        $rows=$new_rows;
    }
    
    unset($_SESSION['search_check_in']);
    unset($_SESSION['search_check_out']);
    unset($_SESSION['search_accommodation']);
}
?>
<script>
function get_room_by_id(room_id){
	$.post("<?php echo get_site_url(); ?>/ajax/",
	  {
		room_id: room_id,
		channel: "get_room_by_id"
	  },
	  function(data, status){
		data = data.replace('<!-- wp:html -->','');
		data = data.replace('<!-- /wp:html -->','');
		data = JSON.parse(data);
		
		$('#room_description').html(data[0].room_description);
		var room_price = data[0].room_price;
		var discount = data[0].discount;
		room_price = parseFloat(room_price)-parseFloat(discount);
		$('#room_price').val(room_price);
		$('#room_id').val(data[0].id);
		$('#room_from_advance').val(data[0].id+'|'+data[0].room_name);
		$('#room_from_full').val(data[0].id+'|'+data[0].room_name);
		
		show_price();
	  });
}
function show_price(){
    $('#check_in1').val('');
    $('#check_in2').val('');
    $('#check_out1').val('');
    $('#check_out2').val('');
    $('#total_price_hidden1').val('');
    $('#total_price_hidden2').val('');
    $('#gst1').val('');
    $('#gst2').val('');
    $('#total_price_hidden').val('');
    
	var room_price = parseFloat($('#room_price').val());
	
	$('#room_price1').val(room_price);
	$('#room_price2').val(room_price);
	
	var facilities_selected = $('#facilities').val();
	var facilities_selected_arr = facilities_selected.split('|||');
	//alert(facilities_selected_arr);
	if(facilities_selected==''){
		var facility_price = 0;
	}else{
	var facility_price = 0;
		for(var i=0;i<facilities_selected_arr.length;i++){
			var val_arr = facilities_selected_arr[i].split('||');
			facility_price = facility_price+parseFloat(val_arr[0]);
		}
	}
	$('#facility_price1').val(facility_price);
	$('#facility_price2').val(facility_price);
	
	var total_price = room_price+facility_price;
	$('#total_price').html('INR.'+total_price);
	//$('#total_price_hidden').val(total_price);
	//$('#total_price_hidden1').val(total_price);
	//$('#total_price_hidden2').val(total_price);
}
function check_uncheck_facility(val,checkbox_id){
	var checkbox_obj = document.getElementById(checkbox_id);
	if(checkbox_obj.checked){
		var facilities_selected = $('#facilities').val();
		if(facilities_selected==''){
			$('#facilities').val(val);
			$('#facilities_form_advance').val(val);
			$('#facilities_form_full').val(val);
		}else{
			$('#facilities').val(facilities_selected+'|||'+val);
			$('#facilities_form_advance').val(facilities_selected+'|||'+val);
			$('#facilities_form_full').val(facilities_selected+'|||'+val);
		}
	}else{
		var facilities_selected = $('#facilities').val();
		var facilities_selected_arr = facilities_selected.split('|||');
		var new_facilities_selected = '';
		for(var i=0;i<facilities_selected_arr.length;i++){
			if(facilities_selected_arr[i]!=val){
				if(new_facilities_selected==''){
					new_facilities_selected = facilities_selected_arr[i];
				}else{
					new_facilities_selected = new_facilities_selected + '|||' + facilities_selected_arr[i];
				}
			}
		}
		$('#facilities').val(new_facilities_selected);
		$('#facilities_form_advance').val(new_facilities_selected);
		$('#facilities_form_full').val(new_facilities_selected);
	}
	show_price();
}
</script>
		<div class="row room_desc">
			<div class="col-lg-6 col-md-6 col-12 des" id="room_description">
				<?php echo html_entity_decode($rows[0]->room_description); ?>
			</div>
			<div class="col-lg-6 col-md-6 col-12">
				<div class="row">
					<div class="col-lg-6 col-md-6 col-12">
						<div class="form-group">
							<label>Select Rooms</label>
							<select class="form-control" onchange="get_room_by_id(this.value);">
							<?php
								for($i=0;$i<count($rows);$i++){
							?>
								<option value="<?php echo $rows[$i]->id; ?>"><?php echo $rows[$i]->room_name; ?></option>
							<?php
								}
							?>
							</select>
						</div>
					</div>
					<div class="clr"></div>
					<div class="col-lg-6 col-md-6 col-12">
						<label>Room Price</label>
						<?php
							$room_price = $rows[0]->room_price-$rows[0]->discount;
						?>
						<div id="total_price">INR.<?php echo $room_price; ?></div>
						<input type="hidden" id="room_price" name="room_price" value="<?php echo $room_price; ?>">
						<input type="hidden" id="room_id" name="room_id" value="<?php echo $rows[0]->id; ?>">
						<input type="hidden" id="hotel_id" name="hotel_id" value="<?php echo $hotel_id; ?>">
						<input type="hidden" id="facilities" name="facilities" value="">
					</div>
					<div class="ht_20"></div>
							
					<div class="col-lg-12 col-md-12 col-12">
					<?php
						if(isset($_SESSION['user'])){
							if(($_SESSION['user']['type']=='customer')||($_SESSION['user']['type']=='corporate')||($_SESSION['user']['type']=='travelagency')){
                    	
                            global $wpdb;
                            if($_SESSION['user']['type']=='customer'){
								$table_name = $wpdb->prefix . "nlt_customer";
								
								$query = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$_SESSION['user']['user_id']."'"
									);
							}else if($_SESSION['user']['type']=='corporate'){
								$table_name = $wpdb->prefix . "sync_corporate";
								
								$query = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$_SESSION['user']['user_id']."'"
									);
							}else{
								$table_name = $wpdb->prefix . "sync_travelagency";
								
								$query = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$_SESSION['user']['user_id']."'"
									);
							}
							$firstname = $query[0]->full_name;
    				    	$email = $query[0]->email;
    				    	$phone = $query[0]->contact_number;
    				    	$_POST = get_site_url().'/temporary-page/?type=1';
    				    	
    				    	$rows_settings = $wpdb->get_results("SELECT * from `wp_settings`");
    				    	
    				    	$gst_percent = $rows_settings[0]->gst;
    				    		
				    ?>	
				    <script>
				        function calculateTotalPrice(type){
				            var checkin = $('#check_in'+type).val();
				            var checkout = $('#check_out'+type).val();
				            var room_price = $('#room_price'+type).val();
				            var facility_price = $('#facility_price'+type).val();
				            if(checkin!='' && checkout!=''){
				                const date1 = new Date(checkin);
                                const date2 = new Date(checkout);
                                if(Math.abs(date1)>Math.abs(date2)){
                                    alert('Check in date can\'t be greater than check out date,');
                                    return false;
                                }
                                const diffTime = Math.abs(date2 - date1);
                                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))+1; 
                                var total_price = (parseInt(diffDays)*parseInt(room_price))+(parseInt(diffDays)*parseInt(facility_price));
                                var gst = (<?php echo $gst_percent; ?>/100)*total_price;
                                var total_price = total_price + gst;
                                
                                $('#total_price_hidden'+type).val(total_price);
                                $('#gst'+type).val(gst);
                                $('#total_price_hidden').val(total_price);
				            }
				        }
				    </script>
					<?php 
					$message = $_SESSION['message'];
					?>
					<?php 
					if($_SESSION['user']['type']=='customer'){
						$message='';
					?>
				    <div class="alert-msg"><?php echo $_SESSION['message']; ?></div><br>
					    <form action="<?php echo $_POST ?>" method="post">
					        <input type="hidden" name="room_price" id="room_price1" value="<?php echo $room_price; ?>">
					        <input type="hidden" name="facility_price" id="facility_price1" value="0">
						    Check in date : <input type="date" name='check_in' id='check_in1' onblur="calculateTotalPrice('1');" required><br><br>
						    Check out date : <input type="date" name='check_out' onblur="calculateTotalPrice('1');" id='check_out1' required><br><br>
					        Total Amount : <input type="text" id="total_price_hidden1" name="amount" value="" readonly="readonly"><br><br>
					        GST(18%) : <input type="text" name="gst" id="gst1" value="" readonly="readonly"><br><br>
					        Advance Amount : <input type="text" class="form-control" name="adv_amount"><br>
						    <input type="hidden" name="full_name" value="<?php echo $firstname; ?>">
						    <input type="hidden" name="email" value="<?php echo $email; ?>">
						    <input type="hidden" name="phone" value="<?php echo $phone; ?>">
						    <input type="hidden" name="ammount_type" value="Advance">
						    <input type="hidden" id="facilities_form_advance" name="facilities" value="">
						    <input type="hidden" name="hotel" value="<?php echo $hotel_id.'|'.get_field('hotel_name',$hotel_id); ?>">
						    <input type="hidden" name="room" id="room_from_advance" value="<?php echo $rows[0]->id.'|'.$rows[0]->room_name; ?>">
						    <input type="submit" class="btn btn-primary" name='pay_advance' value="Pay Advance"><br>
							
						</form><br>
					    <p style="text-align: center;color: #007bff;font-weight: bolder;font-style: oblique;font-size: large;font-family: sans-serif;background-color: #c4ececa1;"> OR</p><br>
						<form action="<?php echo $_POST ?>" method="post">
						    <input type="hidden" name="room_price" id="room_price2" value="<?php echo $room_price; ?>">
					        <input type="hidden" name="facility_price" id="facility_price2" value="0">
					        Check in date : <input type="date" name='check_in'  id='check_in2' onblur="calculateTotalPrice('2');" required><br><br>
						    Check out date : <input type="date" name='check_out'  id='check_out2' onblur="calculateTotalPrice('2');" required><br><br>
					        <input type="hidden" id="total_price_hidden" name="adv_amount" value="">
						    Total Amount : <input type="text" id="total_price_hidden2" name="amount" value="" readonly="readonly"><br><br>
					        GST(18%) : <input type="text" name="gst" id="gst2" value="" readonly="readonly"><br><br>
						    <input type="hidden" name="ammount_type" value="Full">
						    <input type="hidden" name="full_name" value="<?php echo $firstname; ?>">
						    <input type="hidden" name="email" value="<?php echo $email; ?>">
						    <input type="hidden" name="phone" value="<?php echo $phone; ?>">
						    <input type="hidden" name="hotel" value="<?php echo $hotel_id.'|'.get_field('hotel_name',$hotel_id); ?>">
						    <input type="hidden" name="room" id="room_from_full" value="<?php echo $rows[0]->id.'|'.$rows[0]->room_name; ?>">
						    <input type="hidden" id="facilities_form_full" name="facilities" value="">
						    <input type="submit" class="btn btn-primary" value="Pay Full">
						</form>
						
					<?php
					unset($_SESSION['message']);
    				}else{
						$firstname='';
						$email='';
						$phone='';
						?>
						<div class="alert-msg"><?php echo $_SESSION['message']; ?></div><br>
					    <form action="<?php echo $_POST ?>" method="post">
					        <input type="hidden" name="room_price" id="room_price1" value="<?php echo $room_price; ?>">
					        <input type="hidden" name="facility_price" id="facility_price1" value="0">
							 Full Name : <input type="text" name="full_name" value="<?php echo $firstname; ?>" required="required"><br><br>
						    Email : <input type="text" name="email" value="<?php echo $email; ?>" required="required"><br><br>
						    Mobile Number : <input type="text" name="phone" value="<?php echo $phone; ?>" required="required"><br><br>
						    Check in date : <input type="date" name='check_in' id='check_in1' onblur="calculateTotalPrice('1');" required><br><br>
						    Check out date : <input type="date" name='check_out' onblur="calculateTotalPrice('1');" id='check_out1' required><br><br>
					        Total Amount : <input type="text" id="total_price_hidden1" name="amount" value="" readonly="readonly"><br><br>
					        GST(18%) : <input type="text" name="gst" id="gst1" value="" readonly="readonly"><br><br>
					        Advance Amount : <input type="text" class="form-control" name="adv_amount"><br>
						    <input type="hidden" name="ammount_type" value="Advance">
						    <input type="hidden" id="facilities_form_advance" name="facilities" value="">
						    <input type="hidden" name="hotel" value="<?php echo $hotel_id.'|'.get_field('hotel_name',$hotel_id); ?>">
						    <input type="hidden" name="room" id="room_from_advance" value="<?php echo $rows[0]->id.'|'.$rows[0]->room_name; ?>">
						    <input type="submit" class="btn btn-primary" name='pay_advance' value="Pay Advance"><br>
							
						</form><br>
						OR <br><br>
						<form action="<?php echo $_POST ?>" method="post">
						    <input type="hidden" name="room_price" id="room_price2" value="<?php echo $room_price; ?>">
					        <input type="hidden" name="facility_price" id="facility_price2" value="0">
							 Full Name : <input type="text" name="full_name" value="" required="required"><br><br>
						    Email : <input type="text" name="email" value="" required="required"><br><br>
						    Mobile Number : <input type="text" name="phone" value="" required="required"><br><br>
					        Check in date : <input type="date" name='check_in'  id='check_in2' onblur="calculateTotalPrice('2');" required><br><br>
						    Check out date : <input type="date" name='check_out'  id='check_out2' onblur="calculateTotalPrice('2');" required><br><br>
					        <input type="hidden" id="total_price_hidden" name="adv_amount" value="">
						    Total Amount : <input type="text" id="total_price_hidden2" name="amount" value="" readonly="readonly"><br><br>
					        GST(18%) : <input type="text" name="gst" id="gst2" value="" readonly="readonly"><br><br>
						    <input type="hidden" name="ammount_type" value="Full">
						    <input type="hidden" name="hotel" value="<?php echo $hotel_id.'|'.get_field('hotel_name',$hotel_id); ?>">
						    <input type="hidden" name="room" id="room_from_full" value="<?php echo $rows[0]->id.'|'.$rows[0]->room_name; ?>">
						    <input type="hidden" id="facilities_form_full" name="facilities" value="">
						    <input type="submit" class="btn btn-primary" value="Pay Full">
						</form>
						<?php
						unset($_SESSION['message']);
					}						
							}else{
					?>
						Please login to book rooms.
					<?php
							}
						}else{
					?>
						Please login to book rooms.
					<?php
						}
					?>
					</div>
				</div>
			</div>
				
			</div>
		</fieldset>	
		</div>


		</div>
	
  </section>
<section id="btm_banner">
	<div class="container">
		<div class="row">
			<div class="col-lg-12"><img src="<?php echo get_field('advertisement',$hotel_id); ?>" alt=""></div>
		</div>
	</div>
</section>
<?php
	}else{
	    
		echo 'You are not authorized to view this page.';
	}
	$html=ob_get_contents();
	ob_end_clean();
	return $html;
}

add_shortcode('cleopatra_hoteldetails', 'hoteldetails_view');

function temporary_page(){
    ob_start();
    
    $type = $_GET['type'];
    if($type==1){
		global $wpdb;
    	$check_in = explode('/',$_POST['check_in']); // to convert checking date from string to array and remove the '/' format
	    $check_in = implode('-',$check_in); 	  //  to convert checking date from array to string and change the date format from '/' to '-'

	    $current_date = date("Y-n-d");
	   
	    $check_out = explode('/',$_POST['check_out']);// to convert check out date from string to array and remove the '/' format
	    $check_out = implode('-',$check_out);	//  to convert check out date from array to string and change the date format from '/' to '-'

		$adv_amount = $_POST['adv_amount'];
		$amount = $_POST['amount'];
		$gst = $_POST['gst'];
		$email= $_POST['email'];
	
		$hotel_id = $_POST['hotel'];
		$hotel_id = explode("|",$hotel_id);
		$hotel_id = $hotel_id['0'];
	    // for total ammount calculation
	    $check_in_date = date_create($check_in);						
		$check_out_date = date_create($check_out);	
		
		$current_date_check = date_create($current_date);						
		$date_diff_check_current = date_diff($current_date_check,$check_in_date);
		$total_no_of_days_current_check = $date_diff_check_current->format("%R%a");
		
	    $date_diff = date_diff($check_in_date,$check_out_date);
	    $total_no_of_days = $date_diff->format("%R%a");
		$room_info_arr = explode('|',$_POST['room']);	
		$room_id = $room_info_arr[0];
		$rows_order = $wpdb->get_results("SELECT * from `wp_order` WHERE `room_type_id`='".$room_id."'");

		$count_case=array();
		for($k=0;$k<count($rows_order);$k++){
			$check_in_database = $rows_order[$k]->check_in_date;
			$check_out_database = $rows_order[$k]->check_out_date;
			
			$check_in_database=date_create($check_in_database);
			$check_in_diff_obj=date_diff($check_in_database,$check_in_date);
			$check_in_diff = $check_in_diff_obj->format("%R%a");
			
			$check_out_database=date_create($check_out_database);
			
			$check_out_diff_obj=date_diff($check_out_database,$check_out_date);
			$check_out_diff = $check_out_diff_obj->format("%R%a");
			
			if(!($check_in_diff<0 &&  $check_out_diff<0 || $check_in_diff>0 &&  $check_out_diff>0)){
				$count_case[] = 0;
				
			}
		}
	    if($total_no_of_days<0){
	    	$_SESSION['message'] ="<span style='color: red;'>Please check your check in date and check out date</span>";
			$url = get_site_url().'/hotel-details/?hotel='.$hotel_id;
		    wp_redirect( $url );
	    	// for total ammount calculation
	    }else if($total_no_of_days_current_check<0){
			$_SESSION['message'] = "<span style='color: red;'>Invalid Check In date.</span>";
			$url = get_site_url().'/hotel-details/?hotel='.$hotel_id;
		    wp_redirect( $url );
		}else if(in_array(0,$count_case)){
           $_SESSION['message'] ="<span style='color: red;'>Your selected room is not available in this date.</span> ";
			$url = get_site_url().'/hotel-details/?hotel='.$hotel_id;
			wp_redirect( $url );
        }
		else if($amount==''){
			$_SESSION['message']="<span style='color: red;'>Total amount can not be blank.</span> ";
			$url = get_site_url().'/hotel-details/?hotel='.$hotel_id;
		    wp_redirect( $url );
		}else if($gst==''){
			$_SESSION['message']="<span style='color: red;'>GST can not be blank.</span> ";
			$url = get_site_url().'/hotel-details/?hotel='.$hotel_id;
		    wp_redirect( $url );
		}else if(!(filter_var($email, FILTER_VALIDATE_EMAIL))){
			$_SESSION['message']="<span style='color: red;'>Please provide a valid email address.</span> ";
			$url = get_site_url().'/hotel-details/?hotel='.$hotel_id;
		    wp_redirect( $url );
		}else if(($adv_amount=='')||($adv_amount<=0)){
			$_SESSION['message']="<span style='color: red;'>Advance amount can not be blank and must be greater the 0.</span> ";
			$url = get_site_url().'/hotel-details/?hotel='.$hotel_id;
		    wp_redirect( $url );
		}else{
			$_SESSION['payment']['ammount_type'] = $_POST['ammount_type'];
	    	$_SESSION['payment']['full_name'] = $_POST['full_name'];
		    $_SESSION['payment']['email'] = $_POST['email'];
		    $_SESSION['payment']['phone'] = $_POST['phone'];
		    $_SESSION['payment']['hotel'] = $_POST['hotel'];
		    $_SESSION['payment']['room'] = $_POST['room'];
			$_SESSION['payment']['due_amount']=0;
			
		    $_SESSION['facilities'] =  $_POST['facilities'];
		    
		    $_SESSION['payment']['check_in'] = $_POST['check_in'];
		     $_SESSION['payment']['check_out'] = $_POST['check_out'];
			$_SESSION['payment']['total_amount'] = $_POST['amount'];
			$_SESSION['payment']['amount_per_day'] = $_POST['amount_per_day'];

		    $_SESSION['payment']['adv_amount'] = $_POST['adv_amount'];
			$_SESSION['payment']['facility_price'] = $_POST['facility_price'];
			$_SESSION['payment']['gst'] = $_POST['gst'];
			$_SESSION['payment']['room_price'] = $_POST['room_price'];
			
		    $url = get_site_url().'/payment-details/';
		    wp_redirect( $url );
		}
	}else if($type==4){
		$_SESSION['payment']['ammount_type'] = $_POST['amount_type'];
		$_SESSION['payment']['full_name'] = $_POST['firstname'];
		$_SESSION['payment']['email'] = $_POST['email'];
		$_SESSION['payment']['phone'] = $_POST['phone'];
		$_SESSION['payment']['hotel']=$_POST['hotel'];
		$_SESSION['payment']['room'] = $_POST['room_type_id'].'|test';
		$_SESSION['facilities'] =  $_POST['facilities_ids'];
		$_SESSION['payment']['adv_amount'] = $_POST['amount'];
		
		$_SESSION['payment']['amount_per_day'] = 0;
		$_SESSION['payment']['amount'] = $_POST['amount'];
		$_SESSION['payment']['due_amount'] = $_POST['amount'];
		$_SESSION['payment']['total_amount'] = $_POST['total_amount'];
		$_SESSION['payment']['check_in'] = $_POST['check_in_date'];
		$_SESSION['payment']['check_out'] = $_POST['check_out_date'];
		$_SESSION['payment']['facility_price'] = $_POST['facility_amount'];
		$_SESSION['payment']['gst'] = $_POST['gst'];
		$_SESSION['payment']['room_price'] = $_POST['room_amount'];
		$_SESSION['payment']['gst_percent'] = $_POST['gst_percent'];
	
		$url = get_site_url().'/payment-details/';
		wp_redirect( $url );
	}else if($type==2){

		$amount = $_SESSION['payment']['amount'];
		$ammount_type = $_SESSION['payment']['ammount_type'];
		$total_amount = $_SESSION['payment']['total_amount'];
		$check_in_date =  $_SESSION['payment']['check_in'];
		$check_out_date = $_SESSION['payment']['check_out'];
		$hotel_id = $_SESSION['hotel_id'];
		$room_id = $_SESSION['room_id'];
		$facilities = $_SESSION['facilities'];
		$user_id = $_SESSION['user']['user_id'];
		$user_type = $_SESSION['user']['type'];
		$full_name = $_SESSION['payment']['full_name'];
		$email = $_SESSION['payment']['email'];
		$phone = $_SESSION['payment']['phone'];
		$pay_revenue = 'tatrooms';
		$facility_price = $_SESSION['payment']['facility_price'];
		$gst = $_SESSION['payment']['gst'];
		$gst_percent = $_SESSION['payment']['gst_percent'];
		$room_price = $_SESSION['payment']['room_price'];
		$due_amount = $_SESSION['payment']['due_amount'];
		
		$status=$_POST["status"];
		$firstname=$_POST["firstname"];
		$amount=$_POST["amount"];
		$txnid=$_POST["txnid"];
		$posted_hash=$_POST["hash"];
		$key=$_POST["key"];
		$productinfo=$_POST["productinfo"];
		$email=$_POST["email"];
		$salt="yIEkykqEH3";
		if ($status != 'success') {
	       /*$_SESSION['payu_message'] = "<span style='color: red;'>Invalid Transaction. Please try again</span>";*/
	       echo "Invalid Transaction. Please try again";
		} else if(!(isset($_SESSION['order_id']))){
			global $wpdb;
			$table_name = $wpdb->prefix . "settings";
			$query1 = $wpdb->get_results("SELECT * from $table_name");

			$gst_percent = $query1[0]->gst;
			
			$query = $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."order` (`hotel_id`, `full_name`, `email`, `phone`, `room_type_id`, `facilities_ids`, `amount_paid`, `amount_type`,`facility_amount`,`gst`,`gst_percent`,`room_amount`,`total_amount`, `check_in_date`, `check_out_date`, `type_id`, `type`,`pay_revenue`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", $hotel_id,$full_name,$email,$phone, $room_id,$facilities,$amount,$ammount_type,$facility_price,$gst,$gst_percent,$room_price,$total_amount,$check_in_date,$check_out_date,$user_id,$user_type,$pay_revenue));
			 
			echo "Thank You. Your order status is '". $status ."'.<br>"."Your Transaction ID for this transaction is '".$txnid."'.<br>"."We have received a payment of Rs. '" . $amount;
				unset($_SESSION['payment']['adv_amount']);
				unset($_SESSION['payment']['ammount_type']);
				unset($_SESSION['payment']['total_amount']);
				unset($_SESSION['payment']['check_in']);
				unset($_SESSION['payment']['check_out']);
				unset($_SESSION['hotel_id']);
				unset($_SESSION['room_id']);
				unset($_SESSION['facilities']);
				unset($_SESSION['order_id']);

		}else{
			$order_id = $_SESSION['order_id'];
			$pay_revenue = 'tatrooms';
			$ammount_type = 'Advance-Full';
			
			global $wpdb;
		/*	$table_name = $wpdb->prefix . "settings";
			$query1 = $wpdb->get_results("SELECT * from $table_name");
			$gst_percent = $query1[0]->gst_percent;*/
			
			$query = $wpdb->query($wpdb->prepare("INSERT INTO `".$wpdb->prefix."order` (`hotel_id`, `full_name`, `email`, `phone`, `room_type_id`, `facilities_ids`, `amount_paid`, `amount_type`,`facility_amount`,`gst`,`gst_percent`,`room_amount`,`total_amount`, `check_in_date`, `check_out_date`, `type_id`,`parent_order_id`, `type`,`pay_revenue`) VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)", $hotel_id,$full_name,$email,$phone, $room_id,$facilities,$due_amount,$ammount_type,$facility_price,$gst,$gst_percent,$room_price,$total_amount,$check_in_date,$check_out_date,$user_id,$order_id,$user_type,$pay_revenue));
			
			echo "Thank You. Your order status is '". $status ."'.<br>"."Your Transaction ID for this transaction is '".$txnid."'.<br>"."We have received a payment of Rs. '" . $amount;
				unset($_SESSION['payment']['adv_amount']);
				unset($_SESSION['payment']['ammount_type']);
				unset($_SESSION['payment']['total_amount']);
				unset($_SESSION['payment']['check_in']);
				unset($_SESSION['payment']['check_out']);
				unset($_SESSION['hotel_id']);
				unset($_SESSION['room_id']);
				unset($_SESSION['facilities']);
				unset($_SESSION['order_id']);
		}
	}else if($type==3){
		$status=$_POST["status"];
		$firstname=$_POST["firstname"];
		$amount=$_POST["amount"];
		$txnid=$_POST["txnid"];
		$posted_hash=$_POST["hash"];
		$key=$_POST["key"];
		$productinfo=$_POST["productinfo"];
		$email=$_POST["email"];
		$salt="yIEkykqEH3";
		If (isset($_POST["additionalCharges"])) {
		$additionalCharges=$_POST["additionalCharges"];
		$retHashSeq = $additionalCharges.'|'.$salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
		} else {
		$retHashSeq = $salt.'|'.$status.'|||||||||||'.$email.'|'.$firstname.'|'.$productinfo.'|'.$amount.'|'.$txnid.'|'.$key;
		}
		$hash = hash("sha512", $retHashSeq);
		if ($hash != $posted_hash) {
		echo "Invalid Transaction. Please try again";
		} else {
		echo "Your order status is ". $status ;
		echo "Your transaction id for this transaction is ".$txnid.". You may try making the payment by clicking the link below.";
		}
	}else if($type=5){
	    if($_POST['check_in']==''){
	        $_SESSION['search_message']='Check in date should not be blank in search.';
	        wp_redirect(get_site_url().'/message/');
	    }else if($_POST['check_out']==''){
	        $_SESSION['search_message']='Check out date should not be blank in search.';
	        wp_redirect(get_site_url().'/message/');
	    }else{ 
	        $search_check_in = $_POST['check_in'];
            $search_check_out = $_POST['check_out'];
            
            $search_check_in_arr = explode('-',$search_check_in);
            $search_check_in = $search_check_in_arr[2].'-'.$search_check_in_arr[1].'-'.$search_check_in_arr[0];
            
            $search_check_out_arr = explode('-',$search_check_out);
            $search_check_out = $search_check_out_arr[2].'-'.$search_check_out_arr[1].'-'.$search_check_out_arr[0];
            
            $search_check_in=date_create($search_check_in);
            $search_check_out=date_create($search_check_out);
            $diff_obj=date_diff($search_check_in,$search_check_out);
            $diff = $diff_obj->format("%R%a");
    	    if($diff<0){
    	        $_SESSION['search_message']='Check out date should be greater than or equal to check in date.';
    	        wp_redirect(get_site_url().'/message/');
    	    }else{
    	        date_default_timezone_set("Asia/Calcutta");
    	        $curr_date = date("Y-m-d");
    	        
    	        $search_check_out = $_POST['check_out'];
                
                $search_check_out_arr = explode('-',$search_check_out);
                $search_check_out = $search_check_out_arr[2].'-'.$search_check_out_arr[1].'-'.$search_check_out_arr[0];
                
                $curr_date=date_create($curr_date);
                $search_check_out=date_create($search_check_out);
                $diff_obj=date_diff($curr_date,$search_check_out);
                $diff = $diff_obj->format("%R%a");
                
                if($diff<0){
                    $_SESSION['search_message']='Check out date should be greater than or equal to current date.';
    	            wp_redirect(get_site_url().'/message/');
                }else{
                    if($_POST['accommodation']==''){
                        $_SESSION['search_message']='Guests should not be blank.';
    	                wp_redirect(get_site_url().'/message/');
                    }else{
                        if(!is_numeric($_POST['accommodation'])){
                            $_SESSION['search_message']='Guests should be numeric.';
    	                    wp_redirect(get_site_url().'/message/');
                        }else{
                    	    $_SESSION['search_check_in'] = $_POST['check_in'];
                    	    $_SESSION['search_check_out'] = $_POST['check_out'];
                    	    $_SESSION['search_accommodation'] = $_POST['accommodation'];
                    	    wp_redirect(get_site_url().'/hotel-list/?query=location:'.$_POST['location']);
                        }
                    }
                }
    	    }
	    }
	}
	$html=ob_get_contents();
	ob_end_clean();
	return $html;
}		
add_shortcode('cleopatra_temporary_page', 'temporary_page');

function payment_details(){
    ob_start();
   $hotel_id = $_SESSION['payment']['hotel'];
   $hotel_id = explode("|",$hotel_id);
   $_SESSION['hotel_id'] = $hotel_id['0'];

   $room_id =  $_SESSION['payment']['room'];
   $room_id = explode("|",$room_id);
   $_SESSION['room_id'] = $room_id['0'];
   
   $fecility_id = $_SESSION['payment']['facility'];
   $fecility_id = explode("|",$facility_id);
   $_SESSION['facility_id'] = $fecility_id['0']; 
 //payment gateway
$MERCHANT_KEY = "gnAT3VFw";
$surl =  get_site_url().'/temporary-page/?type=2';
$furl =  get_site_url().'/temporary-page/?type=3';
$SALT = "eQmkslnZoE";
// Change to https://secure.payu.in for LIVE mode
$PAYU_BASE_URL = "https://secure.payu.in";

$action = '';

$posted = array();

if(!empty($_POST)) {
foreach($_POST as $key => $value) {
$posted[$key] = $value;
}
}

$formError = 0;
if(empty($posted['txnid'])) {
// Generate random transaction id
$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);

} else {
$txnid = $posted['txnid'];

}
$hash = '';
// Hash Sequence
$hashSequence = "key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10";

if(empty($posted['hash']) && sizeof($posted) > 0) {
if(
empty($posted['key'])
|| empty($posted['amount'])
|| empty($posted['txnid'])
|| empty($posted['firstname'])
|| empty($posted['email'])
|| empty($posted['phone'])
|| empty($posted['productinfo'])
|| empty($posted['surl'])
|| empty($posted['furl'])
|| empty($posted['service_provider'])
) {

$formError = 1;

	
} else {

$hashVarsSeq = explode('|', $hashSequence);
$hash_string = '';
foreach($hashVarsSeq as $hash_var) {
$hash_string .= isset($posted[$hash_var]) ? $posted[$hash_var] : '';
$hash_string .= '|';
}
$hash_string .= $SALT;

$hash = strtolower(hash('sha512', $hash_string));
$action = $PAYU_BASE_URL . '/_payment';
}
} elseif(!empty($posted['hash'])) {
$hash = $posted['hash'];
$action = $PAYU_BASE_URL . '/_payment';
}
if($formError==1){
	$formError_msg = '<span style="color:red">Please fill all mandatory fields.</span><br/>';
}else{
	$formError_msg = '';
}

if(isset($_SESSION['payu_message'])){
	$formError_msg = $_SESSION['payu_message'];
	unset($_SESSION['payu_message']);
}
?>
<script>
var hash = '<?php echo $hash ?>';
function submitForm() {
if(hash == '') {
return;
}
var payuForm = document.forms.payuForm;
payuForm.submit();
}
</script>
<section id="contact">
	<div class="container">
	  <h2 class="h2 ac">Payment Details</h2>
	  <div class="row">
		<div class="col-lg-12 col-md-12 col-sm-12 col-12 col contact_form">
			<form name="payuForm" method="post" action="<?php echo $action; ?>"  enctype="multipart/form-data">
				<input type="hidden" name="key" value="<?php echo $MERCHANT_KEY ?>" />
				<input type="hidden" name="hash" value="<?php echo $hash ?>"/>
				<input type="hidden" name="txnid" value="<?php echo $txnid ?>" />
				
				<input type="hidden" name="service_provider" value="payu_paisa" size="64" />
				<input type="hidden" name="surl" value="<?php echo $surl; ?>"/>
				<input type="hidden" name="furl" value="<?php echo $furl; ?>" />
			    <div class="form-group">
					Customer name: <?php  echo $_SESSION['payment']['full_name'];?>
					<input type="hidden" name="firstname" value="<?php  echo $_SESSION['payment']['full_name'];?>">
				</div>				
				<div class="form-group">					
					Customer email: <?php  echo $_SESSION['payment']['email'];?>					
					<input type="hidden" name="email" value="<?php  echo $_SESSION['payment']['email'];?>">				
				</div>
				<div class="form-group">					
					Customer mobile no: <?php  echo $_SESSION['payment']['phone'];?>					
					<input type="hidden" name="phone" value="<?php  echo $_SESSION['payment']['phone'];?>">				
				</div>
				<div class="form-group">					
				    <input type="hidden" name="productinfo" value="<?php echo $_SESSION['payment']['hotel_id'];?>"/>
				</div>
				<div class="form-group">					
					<?php						
					$hotel_info_arr = explode('|',$_SESSION['payment']['hotel']);						
					$hotel_name = $hotel_info_arr[1];					
					?>
				    Hotel name: <?php echo $hotel_name; ?>
				    <input type="hidden" name="hotel_name" value="<?php  echo $hotel_name;?>">
				    <input type="hidden" name="productinfo" value="<?php  echo $hotel_name;?>"/>
				</div>
				<div class="form-group">					
					<?php						
					$room_info_arr = explode('|',$_SESSION['payment']['room']);						
					$room_type = $room_info_arr[1];					
					?>
			        Room type: <?php echo $room_type; ?>
					<input type="hidden" name="room_type" value="<?php  echo $room_type; ?>">
				</div>
				<?php
				if($_SESSION['payment']['facility']!=''){
					?>
					<div class="form-group">					
					<?php					
					$facilities_arr = explode('|||',$_SESSION['payment']['facility']);					
					$facility_str = '';					
					for($i=0;$i<count($facilities_arr);$i++){
							$facilities_arr_arr = explode('||',$facilities_arr[$i]);						
							if($i==0){							
								$facility_str = $facilities_arr_arr[1];						
							}else{							
								$facility_str = $facility_str.','.$facilities_arr_arr[1];						}			
								}	
					?>
					Facilities: <?php echo $facility_str; ?>
					<input type="hidden" name="facility" value="<?php  echo $_SESSION['payment']['facility'];?>">
				</div>
				<?php
				}
				if($_SESSION['payment']['check_in']!=''){
				?>
				<div class="form-group">
					Check in date: <?php echo  $_SESSION['payment']['check_in']; ?>
					<input type="hidden" name="check_in" value="<?php  echo $_SESSION['payment']['check_in'];?>">
				</div>
				<div class="form-group">
					Check out date: <?php echo  $_SESSION['payment']['check_out']; ?>
					<input type="hidden" name="check_out" value="<?php  echo $_SESSION['payment']['check_out'];?>">
				</div>
				<div class="form-group">
					Facility Price:<?php  echo $_SESSION['payment']['facility_price'];?>
			    	<input type="hidden" name="facility_price" value="<?php  echo $_SESSION['payment']['facility_price'];?>" >
				</div>
				<div class="form-group">
					Room Price:<?php  echo $_SESSION['payment']['room_price'];?>
			    	<input type="hidden" name="room_price" value="<?php  echo $_SESSION['payment']['room_price'];?>" >
				</div>
				<div class="form-group">
					GST(18%):<?php  echo $_SESSION['payment']['gst'];?>
			    	<input type="hidden" name="gst" value="<?php  echo $_SESSION['payment']['gst'];?>" >
				</div>
				<?php
				}
				if($_SESSION['payment']['due_amount']!=0){
				?>
				<div class="form-group">
					Due Amount: Rs. <?php  echo $_SESSION['payment']['amount'];?>
					<input type="hidden" name="amount" value="<?php  echo $_SESSION['payment']['amount'];?>">
				</div>
				<?php
				}
				if($_SESSION['payment']['amount_per_day'] !=0){
				?>
				<div class="form-group">
					Amount per day: Rs. <?php  echo $_SESSION['payment']['amount_per_day'];?> / day
					<input type="hidden" name="amount_per_day" value="<?php  echo $_SESSION['payment']['amount_per_day'];?>">
				</div>	
				<?php
				}
				?>
				<div class="form-group">	
					Total Amount : <?php  echo $_SESSION['payment']['total_amount'];?> 
					<input type="hidden" name="total_amount" value="<?php  echo $_SESSION['payment']['total_amount'];?>">				
				</div>
				<?php
				if($_SESSION['payment']['adv_amount']!=''){
					?>
					<div class="form-group">
					Amount to pay: Rs. <?php  echo $_SESSION['payment']['adv_amount'];?>
					<input type="hidden" name="amount" value="<?php  echo $_SESSION['payment']['adv_amount'];?>">
					</div>
					<?php
				}
				?>
				    
				    <!--<button type="submit" class="btn btn-primary" name='add_cash'>Pay</button>-->
				    <input type="submit" name="pay" class="genric-btn primary" value="Pay">
				    <a href="">Go Back</a>
			    </div>
			</form>
	   	</div>
	  	</div>    		
		</div>
	</section>
    
<?php

    $html=ob_get_contents();
	ob_end_clean();
	return $html;
}		
add_shortcode('cleopatra_payment_details', 'payment_details');

function order_list(){
	$posts = get_posts([
	  'post_type' => 'hotellisting',
	  'post_status' => 'publish',
	  'numberposts' => -1
	]);
	
	$hotel_array = array();
	for($i=0;$i<count($posts);$i++){
		$hotel_array[$posts[$i]->ID] = $posts[$i]->post_title;
	}
	ob_start();

	if(isset($_SESSION['user']['user_id'])){
	//$user_id = $_SESSION['user']['user_id'];
	global $wpdb;
                            
	$table_name = $wpdb->prefix . "order";

	$query = $wpdb->get_results("SELECT * from $table_name WHERE `type_id`='".$_SESSION['user']['user_id']."' AND `type`='".$_SESSION['user']['type']."'"
	    );
	
	?>
	<head>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
	<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
	</head>
	<body>
	<script>
	$(document).ready(function() {
	    $('#example').DataTable();
	} );
	</script>
	<table id="example" class="table table-striped table-bordered" style="width:100%">
		<thead>
            <tr>
				<th>Order Id</th>
                <th>Hotel Name</th>
                <th>Room Name</th>
                <th>Facilities</th>
                <th>Amount Paid</th>
                <th>Amount Type</th>
				<th>Facility Amount</th>
				<th>GST</th>
				<th>Room Amount</th>
				<th>GST Percent</th>
                <th>Total Amount</th>
                <th>Check In</th>
                <th>Check Out</th>
				<th>Payment</th>
				<th>Print Document</th>
            </tr>
        </thead>
    	<tbody>
	<?php

	for($i=0;$i<count($query);$i++){
		$due_amount = $query[$i]->total_amount-$query[$i]->amount_paid;
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
				<td><?php echo $query[$i]->id; ?></td>
				<td><?php echo $hotel_array[$query[$i]->hotel_id]; ?></td>
				<?php
				$table_name1 = $wpdb->prefix . "nlt_hotel_room_listing";

				$query1 = $wpdb->get_results("SELECT `room_name` from $table_name1 WHERE `id`='".$query[$i]->room_type_id."'");
				
				$query2 = $wpdb->get_results("SELECT * from $table_name WHERE `parent_order_id`='".$query[$i]->id."'");
				?>
				<td><?php echo $query1[0]->room_name; ?></td>
				<td><?php echo $facilities; ?></td>
				<td><?php echo $query[$i]->amount_paid; ?></td>
				<td><?php echo $query[$i]->amount_type; ?></td>
				<td><?php echo $query[$i]->facility_amount; ?></td>
				<td><?php echo $query[$i]->gst; ?></td>
				<td><?php echo $query[$i]->room_amount; ?></td>
				<td><?php echo $query[$i]->gst_percent; ?></td>
				<td><?php echo $query[$i]->total_amount; ?></td>
				<td><?php echo $query[$i]->check_in_date; ?></td>
				<td><?php echo $query[$i]->check_out_date; ?></td>
				<?php
				if((count($query2)!=0)||($query[$i]->amount_type=='Advance-Full')){
					?>
					<td>Full payed</td>
					<?php
				}else if($query[$i]->total_amount-$query[$i]->amount_paid!=0){
						$order_id = $query[$i]->id;
					?>
					<td><a href="https://tatrooms.com/payment-after-order/?order_id=<?php echo $order_id; ?>">Full pay</a></td>
					<?php
				}else{
				?>
					<td>Full payed</td>
				<?php
				}
				?>
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
                    				<td colspan='2' align='center'><h3>Room booked : <?php echo $query1[$i]->room_name; ?></h3></td>
                    				<td align='center' >Rs. <?php echo $query[$i]->room_amount; ?></td>
                    			</tr>
                    			<tr>
                    				<td colspan='2' align='center'><h3><u>Facilities</u><br>
                    				<?php echo $facilities; ?></h3></td>
                    				<td align='center' >Rs. <?php echo $query[$i]->facility_amount; ?></td>
                    			</tr>
                    			<tr>
                    				<td colspan='2' align='center'><h3>Service Tax (<?php echo $query[$i]->gst_percent; ?>%)</h3></td>
                    				<td align='center' >Rs. <?php echo $query[$i]->gst; ?></td>
                    			</tr>
                    			<tr>
                    				<td colspan='2' align='center'><h3>Grand Total</h3></td>
                    				<td align='center' >Rs. <?php echo $query[$i]->total_amount; ?></td>
                    			</tr>
                    			<?php
                    				   if($query[$i]->amount_type=='Advance'){
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
	?>	
	    </tbody>
	</table>
	</body>
	<?php
	
	}else{
		$url = get_site_url().'/customer-login';
		wp_redirect( $url );
	}
	
	$html=ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode('cleopatra_order_listing', 'order_list');


function payment_after_order(){
	ob_start();
$order_id = $_GET['order_id'];
$_SESSION['order_id'] = $order_id;
global $wpdb;
                            
$table_name = $wpdb->prefix . "order";

$query = $wpdb->get_results("SELECT * from $table_name WHERE `id`='".$order_id."'"
	    );

$amount = $query[0]->total_amount-$query[0]->amount_paid;
$firstname = $query[0]->full_name;
$email = $query[0]->email;
$phone = $query[0]->phone;
$hotel_id = $query[0]->hotel_id;
$room_type_id = $query[0]->room_type_id;
$facility_amount = $query[0]->facility_amount;
$gst = $query[0]->gst;
$gst_percent = $query[0]->gst_percent;
$room_amount = $query[0]->room_amount;

$facilities_ids = $query[0]->facilities_ids;
$amount_paid = $query[0]->amount_paid;
$amount_type = $query[0]->amount_type;
$total_amount = $query[0]->total_amount;
$check_in_date = $query[0]->check_in_date;
$check_out_date = $query[0]->check_out_date;
$_POST = get_site_url().'/temporary-page/?type=4';
?>
<form action="<?php echo $_POST ?>" method="post">
						   <input type="hidden" id="total_price_hidden" name="amount" value="<?php echo $amount; ?>">
						   <input type="hidden" id="total_price_hidden" name="hotel_id" value="<?php echo $hotel_id; ?>">
						   <input type="hidden" id="total_price_hidden" name="room_type_id" value="<?php echo $room_type_id; ?>">
						   <input type="hidden" id="total_price_hidden" name="facilities_ids" value="<?php echo $facilities_ids; ?>">
						   <input type="hidden" id="total_price_hidden" name="amount_paid" value="<?php echo $amount_paid; ?>">
						   <input type="hidden" id="total_price_hidden" name="amount_type" value="<?php echo $amount_type; ?>">
						   <input type="hidden" id="total_price_hidden" name="total_amount" value="<?php echo $total_amount; ?>">
						   <input type="hidden" id="total_price_hidden" name="check_in_date" value="<?php echo $check_in_date; ?>">
						   <input type="hidden" id="total_price_hidden" name="check_out_date" value="<?php echo $check_out_date; ?>">
						   <tr>
							<td>Full name: <?php echo $firstname; ?></td><br>
						   </tr>
						    <input type="hidden" id="total_price_hidden2" name="firstname" value="<?php echo $firstname; ?>">
							<tr>
							<td>Amount Due: <?php echo $amount; ?></td><br>

						   </tr>
						    <input type="hidden" id="total_price_hidden2" name="email" value="<?php echo $email; ?>">
							<tr>
							<td>Email: <?php echo $email; ?></td><br>

						   </tr>
							<input type="hidden" id="total_price_hidden2" name="phone" value="<?php echo $phone; ?>">
							<tr>
							<td>Phone: <?php echo $phone; ?></td><br>
						<input type="hidden" id="total_price_hidden2" name="hotel" value="<?php echo $hotel_id; ?>">
						   </tr>
						   <tr>
							<td>Total Amount: <?php echo $total_amount; ?></td><br>
						   </tr>
						   <tr>
							<td>Facility Amount: <?php echo $facility_amount; ?></td><br>
						<input type="hidden" id="total_price_hidden2" name="facility_amount" value="<?php echo $facility_amount; ?>">
						   </tr>
						   <tr>
							<td>GST: <?php echo $gst; ?></td><br>
						<input type="hidden" id="total_price_hidden2" name="gst" value="<?php echo $gst; ?>">
						   </tr><tr>
							<td>GST Percent: <?php echo $gst_percent; ?></td><br>
						<input type="hidden" id="total_price_hidden2" name="gst_percent" value="<?php echo $gst_percent; ?>">
						   </tr>
						   <tr>
							<td>Room Amount: <?php echo $room_amount; ?></td><br>
						<input type="hidden" id="total_price_hidden2" name="room_amount" value="<?php echo $room_amount; ?>">
						   </tr>
						   <input type="hidden" name="hotel" value="<?php echo $hotel_id.'|'.get_field('hotel_name',$hotel_id); ?>">
						    <input type="submit" class="btn btn-primary" value="Pay">
</form>
<?php


$html=ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode('cleopatra_payment_after_order', 'payment_after_order');

function print_document(){
	ob_start();
$data = $_GET['data'];
$data = explode('.',$data);
$id = $data[0];
$type = $data[1];
echo $id.'<br>';
echo $type.'<br>';
$html=ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode('cleopatra_print_document', 'print_document');

function commission_distribution(){
	ob_start();
	global $wpdb;
	$agent = $_SESSION['user'];
	if(isset($_SESSION['user'])){
		$agent_id = $_SESSION['user']['user_id'];
		
		
		$table_name = $wpdb->prefix . "order_claim_part";
		$table_name3 = $wpdb->prefix . "sync_vendor";
		$table_name2 = $wpdb->prefix . "sync_travelagency";
		
		$query = $wpdb->get_results("SELECT * from $table_name WHERE `agent_id`='".$agent_id."'");

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
		
			<head>
			<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
			<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
			<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
			<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
			<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
			</head>
			<body>
			<script>
			$(document).ready(function() {
				$('#example').DataTable();
			} );
			</script>
			<table id="example" class="table table-striped table-bordered" style="width:100%">
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
				<tbody>
				
				<?php
				for($i=0;$i<count($query);$i++){
				$vendor_query = $wpdb->get_results("SELECT * from $table_name3 WHERE `id`='".$query[$i]->vendor_id."'");
				$agent_query = $wpdb->get_results("SELECT * from $table_name2 WHERE `id`='".$query[$i]->agent_id."'");
				?>
						
							<tr>
								<td><?php echo $query[$i]->order_id; ?></td>
								<td><?php echo $vendor_query[0]->full_name; ?></td>
								<td><?php echo $agent_query[0]->full_name; ?></td>
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
?>
		    </tbody>
		</table>
	</body>
	<?php
		}
	}else{
		echo "You are not authorised to view this page";
	}
	
	
	$html=ob_get_contents();
	ob_end_clean();
	return $html;
}
add_shortcode('cleopatra_commission_distribution', 'commission_distribution');

function tag_decoder($content)
	{
		preg_match_all("/\[([^\]]*)\]/", $content, $matches);


		$tags_arr=$matches[1];

		for($i=0;$i<count($tags_arr);$i++)
		{
			$shortcode_decode_arr=explode('=',$tags_arr[$i]);
			
			if($shortcode_decode_arr[0]=='post')
			{
				$post_content_obj=get_post($shortcode_decode_arr[1]);
				$post_content = $post_content_obj->post_content;
				
				$post_content=$this->tag_decoder($post_content);

				$content=str_replace('['.$tags_arr[$i].']',$post_content,$content);
			}
			else if($shortcode_decode_arr[0]=='wp')
			{
				if($shortcode_decode_arr[1]=='header')
				{
					$content=str_replace('['.$tags_arr[$i].']',$this->wordpress_header(),$content);
				}
				else
				{
					$content=str_replace('['.$tags_arr[$i].']',$this->wordpress_footer(),$content);
				}
			}
			else if($shortcode_decode_arr[0]=='contact-form-7 id'){
				$shortcode_content = do_shortcode('['.$tags_arr[$i].']');
				$content=str_replace('['.$tags_arr[$i].']',$shortcode_content,$content);
			}
			else if($shortcode_decode_arr[0]=='shortcode')
			{
				if(count($shortcode_decode_arr)>2){
					$shortcode_string = '';
					for($i=1;$i<count($shortcode_decode_arr);$i++){
						$shortcode_string .= $shortcode_decode_arr[$i].'=';
					}
					$shortcode_string = rtrim($shortcode_string,'=');
					$shortcode_content=do_shortcode('['.$shortcode_string.']');
				}else{
					$shortcode_content=do_shortcode('['.$shortcode_decode_arr[1].']');
				}
				$content=str_replace('['.$tags_arr[$i].']',$shortcode_content,$content);
			}
			else if($shortcode_decode_arr[0]=='acf')
			{
				$field_params=$shortcode_decode_arr[1];
				$fields_params_arr=explode('|',$field_params);
				
				$field_shortcode=$fields_params_arr[0];
				
				if(isset($fields_params_arr[1]))
				{
					$field_post_id=$fields_params_arr[1];
				
					$shortcode_content=get_field($field_shortcode,$field_post_id);
				}
				else
				{
					$shortcode_content=get_field($field_shortcode);
				}
				$content=str_replace('['.$tags_arr[$i].']',$shortcode_content,$content);
			}
			else if($shortcode_decode_arr[0]=='acf_repeater')
			{
				$fields_params=$shortcode_decode_arr[1];
				$fields_params_arr=explode('||',$fields_params);
				
				$repeater_template_shortcode_arr=explode('|',$fields_params_arr[1]);
				
				if(isset($repeater_template_shortcode_arr[1]))
				{
					$repeater_template=get_field($repeater_template_shortcode_arr[0],$repeater_template_shortcode_arr[1]);
				}
				else
				{
					$repeater_template=get_field($repeater_template_shortcode_arr[0]);
				}
				
				$repeater_arr=explode('|',$fields_params_arr[0]);
				$repeater_shortcode=$repeater_arr[0];
				
				if(isset($repeater_arr[1]))
				{
					$field_post_id=$repeater_arr[1];
					
					preg_match_all("/\[([^\]]*)\]/", $repeater_template, $matches_rep);


					$tags_rep_arr=$matches_rep[1];
					
					if( have_rows($repeater_shortcode, $field_post_id) ):
						$out='';
						while( have_rows($repeater_shortcode, $field_post_id) ): the_row();
						
						$out2=$repeater_template;
						
						for($j=0;$j<count($tags_rep_arr);$j++)
						{
							$sub_field=get_sub_field($tags_rep_arr[$j]);
							$out2=str_replace('['.$tags_rep_arr[$j].']',$sub_field,$out2);
						}
						
						$out.=$out2;
						
						endwhile;
						
					endif;
				}
				else
				{
					preg_match_all("/\[([^\]]*)\]/", $repeater_template, $matches_rep);


					$tags_rep_arr=$matches_rep[1];
					
					if( have_rows($repeater_shortcode) ):
						$out='';
						while( have_rows($repeater_shortcode) ): the_row();
						
						$out2=$repeater_template;
						
						for($j=0;$j<count($tags_rep_arr);$j++)
						{
							$sub_field=get_sub_field($tags_rep_arr[$j]);
							$out2=str_replace('['.$tags_rep_arr[$j].']',$sub_field,$out2);
						}
						
						$out.=$out2;
						
						endwhile;
						
					endif;
				}
				
				
				$content=str_replace('['.$tags_arr[$i].']',$out,$content);
				
			}
		}
		
		return $content;
	}
?>
