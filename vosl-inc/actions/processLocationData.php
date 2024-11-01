<?php
if ( !is_user_logged_in() ) {
	 die("You are not authorized to access this page.");
}
	
	if (!empty($_GET['delete'])) {
		//If delete link is clicked
		if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], "delete-location_".$_GET['delete'])){
			$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TABLE." WHERE id=%d", (int)$_GET['delete'])); 
			
		}
		
		if (!empty($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce'], "delete-tag_".$_GET['delete'])){
			$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TAGS_TABLE." WHERE id=%d", (int)$_GET['delete'])); 
			
		}
	}
	
	if($_REQUEST['deltID'] > 0)
	{	
		$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TAGS_ASSOC_TABLE." WHERE tag_id=%d", (int)$_REQUEST['deltID'])); 
		$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TAGS_TABLE." WHERE id=%d", (int)$_REQUEST['deltID'])); 
		print "<script>location.href = 'admin.php?page=".VOSL_PAGES_DIR.'/manage-tags.php'."';</script>";
	}
	
	if($_REQUEST['delID'] > 0)
	{
		$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TABLE." WHERE id=%d", (int)$_REQUEST['delID'])); 
		$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_CUSTOM_FIELDS_ASSOC_TABLE." WHERE store_id=%d", (int)$_REQUEST['delID']));
		print "<script>location.href = 'admin.php?page=".VOSL_PAGES_DIR.'/locations.php'."';</script>";
	}
	
	
	if (!empty($_POST) && !empty($_POST['vosl_tag_id']) && $_POST['act']!="delete" && $_POST['act']=="voslupdatetags") {
		$field_value_str=""; 
		//print_r($_POST);
		$tag_id = $_POST['vosl_tag_id'];
		foreach ($_POST as $key=>$value) {
			if (preg_match("@\-$tag_id@", $key)) {
				$key=str_replace("-$tag_id", "", $key); // stripping off number at the end (giving problems when constructing address string below)
				
				if (is_array($value)){
					$value=serialize($value); //for arrays being submitted
					$field_value_str.=$key."='$value',";
				} else {
					$value = sanitize_text_field( $value );
					$field_value_str.=$key."=".$wpdb->prepare("%s", trim(vosl_comma(stripslashes($value)))).", "; 
				}
				$_POST["$key"]=$value; 
			}
		}
		
		$field_value_str=substr($field_value_str, 0, strlen($field_value_str)-2);
		extract($_POST);
		
		$wpdb->query($wpdb->prepare("UPDATE ".VOSL_TAGS_TABLE." SET ".str_replace("%", "%%", $field_value_str)." WHERE id=%d", (int)$tag_id));  
		print "<script>location.href = 'admin.php?page=".VOSL_PAGES_DIR.'/manage-tags.php'."';</script>";
	}
	
	//print_r($_POST);
	if (!empty($_POST) && !empty($_POST['vosl_location_id']) && $_POST['act']!="delete" && $_POST['act']!="updatetags" && $_POST['act']=="voslupdatelocation") {
		$field_value_str=""; 
		
		$location_id = $_POST['vosl_location_id'];
		$tags = $_POST['voslSelTags-'.$location_id];
		
		unset($_POST['voslSelTags-'.$location_id]);
		$voslRedirectToTags = $_POST['voslRedirectToTags'];
		unset($_POST['voslRedirectToTags']);
		
		if(!isset($_POST['show_address_publicly-'.$location_id]))
			$_POST['show_address_publicly-'.$location_id] = 0;
		
		$custom_fields = array();
		foreach ($_POST as $key=>$value) {
			if (preg_match("@\-$location_id@", $key)) {
				$key=str_replace("-$location_id", "", $key); // stripping off number at the end (giving problems when constructing address string below)
				
				if (is_array($value)){
					$value=serialize($value); //for arrays being submitted
					$field_value_str.=$key."='$value',";
				} else {
					$value = sanitize_text_field( $value );
					$field_value_str.=$key."=".$wpdb->prepare("%s", trim(vosl_comma(stripslashes($value)))).", "; 
				}
				$_POST["$key"]=$value; 
			}
			
			if(strpos($key,"custom")!==false and strpos($key,"custom_marker_icon")===false)
			{
				$custom_fields[$key] = $value;
			}
		}
		
		$field_value_str=substr($field_value_str, 0, strlen($field_value_str)-2);
		$edit=$location_id; extract($_POST);
		
		$country_text = '';
		
		if($country!='')
		{
			$co = array_search ($country, $tld);
			
			if($co!='')
			  $country_text = ', '.$co;	
		}
		
		
		$the_address="$address, $address2, $city, $state $zip";
		$the_address_geo = "$address, $address2, $city, $state $zip$country_text";
		
		
		if (empty($_POST['no_geocode']) || $_POST['no_geocode']!=1) { //no_geocode sent by addons that manually edit the the coordinates. Prevents sl_do_geocoding() from overwriting the manual edit.
			$sql = $wpdb->prepare("SELECT * FROM ".VOSL_TABLE." WHERE id=%d", (int)$location_id);
			$old_address=$wpdb->get_results($sql, ARRAY_A); 
		}
		
		$wpdb->query($wpdb->prepare("UPDATE ".VOSL_TABLE." SET ".str_replace("%", "%%", $field_value_str)." WHERE id=%d", (int)$location_id));  
		
		//print_r($custom_fields); die;
		
		do_action('vosl_associate_custom_fields_listings',(int)$location_id,$custom_fields);
		//$enable_default_tags = vosl_data('vosl_enable_default_tags');
		do_action('vosl_associate_tags_listings',(int)$location_id,$tags);
		
		if ((empty($_POST['longitude-'.$location_id]) || $_POST['longitude-'.$location_id]==$old_address[0]['longitude']) && (empty($_POST['latitude-'.$location_id]) || $_POST['latitude-'.$location_id]==$old_address[0]['latitude'])) {
			if ($the_address!=$old_address[0]['address']." ".$old_address[0]['address2'].", ".$old_address[0]['city'].", ".$old_address[0]['state']." ".$old_address[0]['zip'] || ($old_address[0]['latitude']==="" || $old_address[0]['longitude']==="")) {
				
				vosl_do_geocoding($the_address_geo,$location_id);
			}
		}
		
		/*if($voslRedirectToTags!='' and $voslRedirectToTags == 'save')
		{
			?>
			<script type="text/javascript">
				location.href = '<?php echo VOSL_MANAGE_LOCATIONS_PAGE."&pg=manage-tags"; ?>';
			</script>
			<?php
		}*/
		
		print "<script>location.href = 'admin.php?page=".VOSL_PAGES_DIR.'/locations.php'."';</script>";
	}
	
	if (!empty($_GET['changeView']) && $_GET['changeView']==1) {
		if ($vosl_vars['location_table_view']=="Normal") {
			$vosl_vars['location_table_view']='Expanded';
			vosl_data('vosl_vars', 'update', $vosl_vars);
			//$tabViewText="Expanded";
		} else {
			$vosl_vars['location_table_view']='Normal';
			vosl_data('vosl_vars', 'update', $vosl_vars);
			//$tabViewText="Normal";
		}
		print "<script>location.replace('".str_replace("&changeView=1", "", $_SERVER['REQUEST_URI'])."');</script>";
	}
?>