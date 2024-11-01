<?php
class VoStoreLocator_Admin
{	
	public function vosl_update_settings()
	{
		$this->save_admin_settings($_POST);
	}
	
	public function vo_find_more_locations_process_request()
	{
		global $wpdb, $vosl_base; 
		$current_location_lookup = vosl_data('sl_current_location_lookup');
		$vosl_map_api_key = vosl_data('vosl_map_api_key');
		/*$vosl_listing_load_size = vosl_data('vosl_listing_load_size');
		
		if($vosl_listing_load_size=='')
			$vosl_listing_load_size = 100;*/
		
		// first check if data is being sent and that it is the data we want
		$offset = 0;
		if(isset($_POST['offset']))
			$offset = (int)$_POST['offset'] + 1;
			
		$google_map_domain = vosl_data('vosl_google_map_domain');
		$map_domain=(!empty($google_map_domain))? $google_map_domain : "maps.google.com" ;	
		
		$tag_filters = '';
		if(isset($_POST['tag_id']) and (int)$_POST['tag_id'] > 0)
		{
			// single filter
			$tag_filters = apply_filters( 'vosl_filter_front_tags',(int)$_POST['tag_id']);
		}
		
		if(isset($_POST['multiple_tags']) and $_POST['multiple_tags'] != '')
		{
			// multiple filter
			$tag_filters = apply_filters( 'vosl_filter_front_tags',$_POST['multiple_tags']);
		}
		
		$vosl_listing_load_size = apply_filters( 'vosl_get_listing_load_size',(int)$_POST['instance_id']);
		
		$radius_filter = '';
		
		// issue fixed, no location found issue fixed in v 3.2.6
		if($_POST['radius_val'] > 0)
			$radius_filter = apply_filters( 'vosl_filter_radius_range',$_POST['radius_val']);
			
		if ( isset( $_POST["address"] ) ) {
			// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
			$data = trim($_POST["address"]);
			// send the response back to the front end
			if($data!='')
			{
				$address = $data;
				$address = urlencode($address);
				$address = str_replace(" ", "%20", $address);
				
				$map_region = vosl_data('vosl_map_region');
				$region=(!empty($map_region))? "&region=".$map_region : "" ;
				$map_api_key=(!empty($vosl_map_api_key))? "&key=".$vosl_map_api_key : "" ;
				$component = '';
				
				if($this->validate_zipcode($data,$map_region))
				{
					$component = "&components=country:".strtoupper(trim($map_region));
				}
				
				$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$region.$component.$map_api_key;	
				//$url = urlencode($url);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
				//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
				$resp = json_decode($response, true);
				
				$status = $resp['status']; //$status = "";
				$lat = (!empty($resp['results'][0]['geometry']['location']['lat']))? $resp['results'][0]['geometry']['location']['lat'] : "" ;
				$lng = (!empty($resp['results'][0]['geometry']['location']['lng']))? $resp['results'][0]['geometry']['location']['lng'] : "" ;
				
				// 6371 - km
				// 3959 - mi
				$kmmiles = vosl_data('vosl_km');
				
				if(isset($_POST['instance_id']) and (int)$_POST['instance_id'] > 0)
				{
					$kmmiles = apply_filters( 'vosl_get_instance_kmmiles',(int)$_POST['instance_id']);
				}
				
				if($kmmiles=='')
					$kmmiles = 'mi';
				
				if($kmmiles=='mi')
					$sql = $wpdb->prepare("SELECT *, ( 3959 * acos( cos( radians(%f) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( latitude ) ) ) ) AS distance FROM ".VOSL_TABLE." WHERE NOT ISNULL(latitude) $tag_filters $radius_filter order by distance ASC limit ".$offset.", ".$vosl_listing_load_size, $lat,$lng,$lat);
				else
					$sql = $wpdb->prepare("SELECT *, ( 6371 * acos( cos( radians(%f) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( latitude ) ) ) ) AS distance FROM ".VOSL_TABLE." WHERE NOT ISNULL(latitude) $tag_filters $radius_filter order by distance ASC limit ".$offset.", ".$vosl_listing_load_size, $lat,$lng,$lat);	
				
				
				
			}else
			{
				$sql = "SELECT * FROM ".VOSL_TABLE." WHERE NOT ISNULL(latitude) $tag_filters order by store_name ASC limit ".$offset.", ".$vosl_listing_load_size;
			}
			
			
			
			$result_row = $wpdb->get_results($sql,ARRAY_A);
			
			$vosl_listings_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".VOSL_TABLE." WHERE 1 $tag_filters ");
			
			//echo $response;
			if(!empty($result_row))
			{
				if($default_center_lat=='')
				{
					$vosl_map_custom_center_coordinates = vosl_data('vosl_map_custom_center_coordinates');
					if((int)$current_location_lookup == 0)
					{
						// if fixed center point
						$json_array = json_decode($vosl_map_custom_center_coordinates, true);
						
						if(is_array($json_array) and !empty($json_array))
						{
							$default_center_lat = $json_array['lat'];
							$default_center_long = $json_array['long'];
						}
					}
				}
				
				$cc = $offset;
				foreach($result_row as $row){ 
					
					if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
						//enable secure connection
						if( strpos($row['image'],"https://") !== true )
						{
							$row['image'] = str_replace("http://","https://",$row['image']);
						}
					}

					// added this to apply custom color per tag if found for the listing, added in 3.1 version, pass instance ID
					$listing_icon = apply_filters( 'vosl_listing_custom_marker_icon',(int)$row['id'], (int)$_POST['instance_id']);
					
					$custom_fields = $this->get_store_custom_fields($row['id']);
					
					foreach($custom_fields as $cfield)
					{
						$row[$cfield['field_name']] = $cfield['custom_field_value'];
					}
					
					$address = '';
					$address .= (!empty($row['address']))?$row['address']:'';
					$address .= (!empty($row['address2']))?", ".$row['address2']:'';
					$address .= (!empty($row['city']))?", ".$row['city']:'';
					$address .= (!empty($row['state']))?", ".$row['state']:'';
					$address .= (!empty($row['zip']))?" ".$row['zip']:'';
					
					$add = str_replace("","%20", $address);
					//$addr_link_src = "http://maps.google.com/maps?saddr=&daddr=".$add;
					$addr_link_src = "http://".$map_domain."/maps?daddr=".$add;	
					
					// check for the url if it has http or not
					if(strpos($row['url'],'http://')===false and strpos($row['url'],'https://')===false and $row['url']!='')
						$row['url'] = 'http://'.$row['url'];
						
					$row['url_text'] = str_replace(array("http://",'https://'),array('',''),$row['url']);	
					
					// changed by manoj on 11th Nov 2016 due to map popup size customizations
					$callout = "<h4 style='float:left;word-wrap: break-word;'>".addslashes($row['store_name'])." <a href='#' style='float:right;' onclick='closeMarker(".$row['id']."); return false;'>X</a></h4>";
					
					/*if($row['show_address_publicly']==1)
						$callout .= "<p><img src='".$vosl_base."/images/icons/address.png' /> <a href='".$addr_link_src."' target='_blank'>".$address."</a></p>";*/
						
					if($row['show_address_publicly']==1)	
						$callout .= "<p><img src='".$vosl_base."/images/icons/address.png' /> <a href='#' onclick=\"showDrivingDirections('".addslashes($addr_link_src)."'); return false;\">".addslashes($address)."</a></p>";
					
					if($row['url']!='')
						$callout .= "<p><img src='".$vosl_base."/images/icons/URL.png' /> <a target=\'_blank\' href='".$row['url']."'>".$row['url_text']."</a></p>";
					
					if($row['phone']!='')	
						$callout .= "<p><img src='".$vosl_base."/images/icons/phone.png' /> ".$row['phone']."</p>";
					
					if($row['fax']!='')
						$callout .= "<p><img src='".$vosl_base."/images/icons/fax.png' /> ".$row['fax']."</p>";
					
					if($row['email']!='')	
						$callout .= "<p><img src='".$vosl_base."/images/icons/email.png' /> <a href='mailto:".$row['email']."'>".$row['email']."</a></p>";
						
					if($row['hours']!='')	
						$callout .= "<p><img src='".$vosl_base."/images/icons/hours.png' /> ".$row['hours']."</p>";
						
					if($row['description']!='')	
						$callout .= "<p><img src='".$vosl_base."/images/icons/description.png' /> ".addslashes($row['description'])."</p>";
						
					// updates the callout to add custom fields	
					$row = apply_filters( 'vosl_update_map_callout_fields', $row );
					
					// updates the callout to add custom fields
					if($row['new_callout']!='')
						$callout = $row['new_callout'];		
						
					if($row['image']!='')
						$image = $row['image'];
					else
						$image = $vosl_base."/images/locationimg.jpg";	
					
					$htm = '<div class="voslrow"><div class="col-lg-5"><div class="img_placeholder"><img src="'.$image.'" class="img-responsive" /><a href="#" onclick="showDrivingDirections(\''.addslashes($addr_link_src).'\'); return false;">'.__('Get Directions', VOSL_TEXT_DOMAIN).'</a></div></div><div class="col-lg-7">'.$callout.'</div></div>';		
				
					//$address="$row[address], $row[address2], $row[city], $row[state] $row[zip]"; ?>
	<div class="voslrow locationlist" id="location_<?php echo $row['id']?>" data-offset="<?php echo $cc?>" data-count="<?=$vosl_listings_count?>">
	<div class="lat" style="display:none"><?php echo $row['latitude']?></div>
	<div class="long" style="display:none"><?php echo $row['longitude']?></div>
    <div class="vosl_marker_color" style="display:none;"><?php echo $listing_icon['marker_color']?></div>
    <div class="vosl_marker_icon" style="display:none;"><?php echo $listing_icon['marker_icon']?></div>
	<div class="callout" style="display:none"><?php echo $htm?></div>
	<h4><?php echo $row['store_name']?>
	<?php if($row['distance']!=''){ 
				$distance = number_format ( $row['distance'], 2 );
				?>
	<span><?php echo $distance?> <?php echo $kmmiles?></span>
	<?php } ?>
	</h4>
	
	<div class="locationdetails">
	<div class="voslrow">
	<?php if($row['image']!=''){ ?>
	<div class="col-md-2 col-sm-2">
	<div class="voslrow imagerow"><div class="img_placeholder"><img src="<?php echo $row['image']?>" class="img-responsive" /></div></div>
	</div>
	<?php } ?>
	<div class="col-md-10 col-sm-10" style="padding-left:0px; font-size:14px;">
	<div class="voslrow mainrow">
	<?php /*?><?php if($row['description']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/description.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><?php echo $row['description']?></div></div>
	<?php } ?><?php */?>
	<?php if($row['show_address_publicly']==1){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/address.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><a href='#' onclick="showDrivingDirections('<?php echo addslashes($addr_link_src)?>');return false"><?php echo addslashes($address)?></a></div></div>
	<?php } ?>
    
    <?php do_action('vosl_add_custom_fields_listing_front', $row); ?>
    
	<?php /*?><?php if($row['url']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/URL.png' /></strong></div><div class="col-md-10 col-sm-10"><a target="_blank" href="<?php echo $row['url']?>"><?php echo $row['url_text']?></a></div></div>
	<?php } ?>
	<?php if($row['phone']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/phone.png' /></strong></div><div class="col-md-10 col-sm-10"><?php echo $row['phone']?></div></div>
	<?php } ?>
	<?php if($row['fax']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/fax.png' /></strong></div><div class="col-md-10 col-sm-10"><?php echo $row['fax']?></div></div>
	<?php } ?>
	<?php if($row['email']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/email.png' /></strong></div><div class="col-md-10 col-sm-10"><a href="mailto:<?php echo $row['email']?>"><?php echo $row['email']?></a></div></div>
	<?php } ?>
	<?php if($row['hours']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/hours.png' /></strong></div><div class="col-md-10 col-sm-10"><?php echo $row['hours']?></div></div>
	<?php } ?><?php */?>
    
    
	</div>
	</div>
	</div>
	</div>
	</div>
	<?php $cc = $cc + 1; } 
				
			}
			die();
		}
		
		die;
	}
	
	private function validate_zipcode($zip, $country_code)
	{
		$zip_postal=$zip;
		
		if($country_code=='')
			$country_code = 'us';
		
		$ZIPREG=array(
		 "us"=>"^\d{5}([\-]?\d{4})?$",
		 "uk"=>"^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$",
		 "de"=>"\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b",
		 "ca"=>"^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$",
		 "fr"=>"^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$",
		 "it"=>"^(V-|I-)?[0-9]{5}$",
		 "au"=>"^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$",
		 "nl"=>"^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$",
		 "es"=>"^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$",
		 "dk"=>"^([D-d][K-k])?( |-)?[1-9]{1}[0-9]{3}$",
		 "se"=>"^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$",
		 "be"=>"^[1-9]{1}[0-9]{3}$"
		);
		
		if ($ZIPREG[$country_code]) {
 
		 if (!preg_match("/".$ZIPREG[$country_code]."/i",$zip_postal)){
			 //Validation failed, provided zip/postal code is not valid.
			 return false;
		 } else {
			 //Validation passed, provided zip/postal code is valid.
			 return true;
		 }
		 
		} else {
		 
		 //Validation not available
		 return false;
		 
		}
	}
	
	public function vo_find_locations_process_request() {
		global $wpdb, $vosl_base; 
		
		$google_map_domain = vosl_data('vosl_google_map_domain');
		$map_domain=(!empty($google_map_domain))? $google_map_domain : "maps.google.com";
		$current_location_lookup = vosl_data('sl_current_location_lookup');
		$vosl_map_api_key = vosl_data('vosl_map_api_key');
		/*$vosl_listing_load_size = vosl_data('vosl_listing_load_size');
		
		if($vosl_listing_load_size=='')
			$vosl_listing_load_size = 100;*/
			
		// first check if data is being sent and that it is the data we want
		if ( isset( $_POST["address"] ) ) {
			// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
			$data = trim($_POST["address"]);
			
			$address = $data;
			$address = urlencode($address);
			$address = str_replace(" ", "%20", $address);
			
			if($_POST["lat"]!='' and $_POST["long"]!='')
			{
				$status = 'OK';
				$lat = $_POST["lat"];
				$lng = $_POST["long"];
				
				$default_center_lat = $lat;
				$default_center_long = $lng;
				
			}else
			{
				$map_region = vosl_data('vosl_map_region');
				$region=(!empty($map_region))? "&region=".$map_region : "" ;
				$map_api_key=(!empty($vosl_map_api_key))? "&key=".$vosl_map_api_key : "" ;
				$component = '';
				
				// zipcode search
				if($this->validate_zipcode($data,$map_region))
				{
					$component = "&components=country:".strtoupper(trim($map_region));
				}
				
				$url = "https://maps.google.com/maps/api/geocode/json?address=$address&sensor=false".$region.$component.$map_api_key;
				
				//$url = urlencode($url);
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url);
				curl_setopt($ch, CURLOPT_HEADER,0); //Change this to a 1 to return headers
				curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER["HTTP_USER_AGENT"]);
				//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				$response = curl_exec($ch);
				curl_close($ch);
				$resp = json_decode($response, true);
				
				$status = $resp['status']; //$status = "";
				$lat = (!empty($resp['results'][0]['geometry']['location']['lat']))? $resp['results'][0]['geometry']['location']['lat'] : "" ;
				$lng = (!empty($resp['results'][0]['geometry']['location']['lng']))? $resp['results'][0]['geometry']['location']['lng'] : "" ;
			}	
			
			$tag_filters = '';
			if(isset($_POST['tag_id']) and (int)$_POST['tag_id'] > 0)
			{
				// single filter
				$tag_filters = apply_filters( 'vosl_filter_front_tags',(int)$_POST['tag_id']);
			}
			
			if(isset($_POST['multiple_tags']) and $_POST['multiple_tags'] != '')
			{
				// multiple filter
				$tag_filters = apply_filters( 'vosl_filter_front_tags',$_POST['multiple_tags']);
			}
			
			$vosl_listing_load_size = apply_filters( 'vosl_get_listing_load_size',(int)$_POST['instance_id']);
			
			// radius 
			$radius_filter = '';
			
			// issue fixed, no location found issue fixed in v 3.2.6
			if($_POST['radius_val'] > 0)
				$radius_filter = apply_filters( 'vosl_filter_radius_range',$_POST['radius_val']);
			
			//echo $status;
			if (strcmp($status, "OK") == 0) {
				
				$default_center_lat = $lat;
				$default_center_long = $lng;
				
				// 6371 - km
				// 3959 - mi
				// write a hook to get km/miles from hook as per instance or global settings
				$kmmiles = vosl_data('vosl_km');
				
				if(isset($_POST['instance_id']) and (int)$_POST['instance_id'] > 0)
				{
					$kmmiles = apply_filters( 'vosl_get_instance_kmmiles',(int)$_POST['instance_id']);
				}
				
				if($kmmiles=='')
					$kmmiles = 'mi';
				
				if($kmmiles=='mi')
					$sql = $wpdb->prepare("SELECT *, ( 3959 * acos( cos( radians(%f) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( latitude ) ) ) ) AS distance FROM ".VOSL_TABLE." WHERE NOT ISNULL(latitude) $tag_filters $radius_filter order by distance ASC limit 0, ".$vosl_listing_load_size, $lat,$lng,$lat);	
				else
					$sql = $wpdb->prepare("SELECT *, ( 6371 * acos( cos( radians(%f) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(%f) ) + sin( radians(%f) ) * sin( radians( latitude ) ) ) ) AS distance FROM ".VOSL_TABLE." WHERE NOT ISNULL(latitude) $tag_filters $radius_filter order by distance ASC limit 0, ".$vosl_listing_load_size, $lat,$lng,$lat);	
					
			}else
			{
				$sql = "SELECT * FROM ".VOSL_TABLE." WHERE NOT ISNULL(latitude) $tag_filters order by store_name ASC limit 0, ".$vosl_listing_load_size;		
			}
			
			
			//echo $sql;
				
			$result_row = $wpdb->get_results($sql,ARRAY_A);
			$vosl_listings_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".VOSL_TABLE." WHERE 1 $tag_filters ");
			
			if(!empty($result_row))
			{
				if($default_center_lat=='')
				{
					$vosl_map_custom_center_coordinates = vosl_data('vosl_map_custom_center_coordinates');
					if((int)$current_location_lookup == 0)
					{
						// if fixed center point
						$json_array = json_decode($vosl_map_custom_center_coordinates, true);
						
						if(is_array($json_array) and !empty($json_array))
						{
							$default_center_lat = $json_array['lat'];
							$default_center_long = $json_array['long'];
						}
					}
				}
				
				// for tracking visitor
				do_action('vosl_track_visitor', (int)$_POST['instance_id'], $data, $lat, $lng);
					
				$cc = 0;
				foreach($result_row as $row){ 
					
					if( (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443 ){
						//enable secure connection
						if( strpos($row['image'],"https://") !== true )
						{
							$row['image'] = str_replace("http://","https://",$row['image']);
						}
					}
					
					// added this to apply custom color per tag if found for the listing, added in 3.1 version, pass instance ID
					$listing_icon = apply_filters( 'vosl_listing_custom_marker_icon',(int)$row['id'], (int)$_POST['instance_id']);
					$custom_fields = $this->get_store_custom_fields($row['id']);
					
					foreach($custom_fields as $cfield)
					{
						$row[$cfield['field_name']] = $cfield['custom_field_value'];
					}
				
					$address = '';
					$address .= (!empty($row['address']))?$row['address']:'';
					$address .= (!empty($row['address2']))?", ".$row['address2']:'';
					$address .= (!empty($row['city']))?", ".$row['city']:'';
					$address .= (!empty($row['state']))?", ".$row['state']:'';
					$address .= (!empty($row['zip']))?" ".$row['zip']:'';
					
					$add = str_replace("","%20", $address);
					//$addr_link_src = "http://maps.google.com/maps?saddr=&daddr=".$add;
					$addr_link_src = "http://".$map_domain."/maps?daddr=".$add;
					
					// check for the url if it has http or not
					if(strpos($row['url'],'http://')===false and strpos($row['url'],'https://')===false and $row['url']!='')
						$row['url'] = 'http://'.$row['url'];
						
					$row['url_text'] = str_replace(array("http://",'https://'),array('',''),$row['url']);
					
					// changed by manoj on 11th Nov 2016 due to map popup size customizations
					$callout = "<h4 style='float:left;word-wrap: break-word;'>".addslashes($row['store_name'])." <a href='#' style='float:right;' onclick='closeMarker(".$row['id']."); return false;'>X</a></h4>";
					
					if($row['show_address_publicly']==1)
						$callout .= "<p><img src='".$vosl_base."/images/icons/address.png' /> <a href='#' onclick=\"showDrivingDirections('".addslashes($addr_link_src)."'); return false;\">".addslashes($address)."</a></p>";
					
					if($row['url']!='')
						$callout .= "<p><img src='".$vosl_base."/images/icons/URL.png' /> <a target=\'_blank\' href='".$row['url']."'>".$row['url_text']."</a></p>";
					
					if($row['phone']!='')	
						$callout .= "<p><img src='".$vosl_base."/images/icons/phone.png' /> ".$row['phone']."</p>";
					
					if($row['fax']!='')
						$callout .= "<p><img src='".$vosl_base."/images/icons/fax.png' /> ".$row['fax']."</p>";
					
					if($row['email']!='')	
						$callout .= "<p><img src='".$vosl_base."/images/icons/email.png' /> <a href='mailto:".$row['email']."'>".$row['email']."</a></p>";
						
					if($row['hours']!='')	
						$callout .= "<p><img src='".$vosl_base."/images/icons/hours.png' /> ".$row['hours']."</p>";
						
					if($row['description']!='')	
						$callout .= "<p><img src='".$vosl_base."/images/icons/description.png' /> ".addslashes($row['description'])."</p>";
						
					// updates the callout to add custom fields	
					$row = apply_filters( 'vosl_update_map_callout_fields', $row );
					
					// updates the callout to add custom fields
					if($row['new_callout']!='')
						$callout = $row['new_callout'];
							
						
					if($row['image']!='')
						$image = $row['image'];
					else
						$image = $vosl_base."/images/locationimg.jpg";	
					
					$htm = '<div class="voslrow"><div class="col-lg-5"><div class="img_placeholder"><img src="'.$image.'" class="img-responsive" /><a href="#" onclick="showDrivingDirections(\''.addslashes($addr_link_src).'\'); return false;">'.__('Get Directions', VOSL_TEXT_DOMAIN).'</a></div></div><div class="col-lg-7">'.$callout.'</div></div>';		
					
	 ?>
	<div class="voslrow locationlist" id="location_<?php echo $row['id']?>" data-offset="<?php echo $cc?>" data-count="<?=$vosl_listings_count?>">
	<div class="default_center_lat" style="display:none;"><?php echo $default_center_lat?></div>
	<div class="default_center_long" style="display:none;"><?php echo $default_center_long?></div>
	<div class="lat" style="display:none;"><?php echo $row['latitude']?></div>
	<div class="long" style="display:none;"><?php echo $row['longitude']?></div>
    <div class="vosl_marker_color" style="display:none;"><?php echo $listing_icon['marker_color']?></div>
    <div class="vosl_marker_icon" style="display:none;"><?php echo $listing_icon['marker_icon']?></div>
	<div class="callout" style="display:none;"><?php echo $htm?></div>
	<h4><?php echo $row['store_name']?>
	<?php if($row['distance']!=''){ 
				$distance = number_format ( $row['distance'], 2 );
				?>
	<span><?php echo $distance?> <?php echo $kmmiles?></span>
	<?php } ?>
	</h4>
	<?php /*?> <?php if($row['show_address_publicly']){ ?>
				<p><?php echo $address?></p>
				<?php } ?><?php */?>
	<div class="locationdetails">
	<div class="voslrow">
	<?php if($row['image']!=''){ ?>
	<div class="col-md-2 col-sm-2">
	<div class="voslrow imagerow"><div class="img_placeholder"><img src="<?php echo $row['image']?>" class="img-responsive" /></div></div>
	</div>
	<?php } ?>
	<div class="col-md-10 col-sm-10" style="padding-left:0px; font-size:14px;">
	<div class="voslrow mainrow">
	<?php /*?><?php if($row['description']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/description.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><?php echo $row['description']?></div></div>
	<?php } ?><?php */?>
	<?php if($row['show_address_publicly']==1){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/address.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><a href='#' onclick="showDrivingDirections('<?php echo addslashes($addr_link_src)?>');return false"><?php echo addslashes($address)?></a></div></div>
	<?php } ?>
    
    <?php do_action('vosl_add_custom_fields_listing_front', $row); ?>
    
	<?php /*?><?php if($row['url']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/URL.png' /></strong></div><div class="col-md-10 col-sm-10"><a target="_blank" href="<?php echo $row['url']?>"><?php echo $row['url_text']?></a></div></div>
	<?php } ?>
	<?php if($row['phone']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/phone.png' /></strong></div><div class="col-md-10 col-sm-10"><?php echo $row['phone']?></div></div>
	<?php } ?>
	<?php if($row['fax']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/fax.png' /></strong></div><div class="col-md-10 col-sm-10"><?php echo $row['fax']?></div></div>
	<?php } ?>
	<?php if($row['email']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/email.png' /></strong></div><div class="col-md-10 col-sm-10"><a href="mailto:<?php echo $row['email']?>"><?php echo $row['email']?></a></div></div>
	<?php } ?>
	<?php if($row['hours']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/hours.png' /></strong></div><div class="col-md-10 col-sm-10"><?php echo $row['hours']?></div></div>
	<?php } ?><?php */?>
    
    
	</div>
	</div>
	</div>
	</div>
	</div>
	<?php $cc = $cc + 1; } 
				
			}else
			{
				echo "NO";
			}
			
			// for tracking visitor
			//do_action('vosl_track_visitor', (int)$_POST['instance_id'], $data, $lat, $lng);
			die();
		}
		
		die;
	}
	
	private function vosl_get_google_fonts() {
			// Variable to hold fonts;
			$fonts = array();
			$json  = array();

			// Check if transient is set
			if ( false === get_transient( 'vosl_font_google_fonts_list' ) ) {

				$fonts_from_repo = wp_remote_fopen( "https://plugins.svn.wordpress.org/vo-locator-the-wp-store-locator/trunk/assets/fonts/webfonts.json", array( 'sslverify' => false ) );
				$json            = $fonts_from_repo;
				$font_output = json_decode( $json, true );

				foreach ( $font_output['items'] as $item ) {

					$urls = array();

					// Get font properties from json array.
					foreach ( $item['variants'] as $variant ) {

						$name = str_replace( ' ', '+', $item['family'] );
						$urls[ $variant ] = "https://fonts.googleapis.com/css?family={$name}:{$variant}";

					}

					$atts = array(
						'name'         => $item['family'],
						'category'     => $item['category'],
						'font_type'    => 'google',
						'font_weights' => $item['variants'],
						'subsets'      => $item['subsets'],
						'files'        => $item['files'],
						'urls'         => $urls
					);

					// Add this font to the fonts array
					$id           = strtolower( str_replace( ' ', '_', $item['family'] ) );
					$fonts[ $id ] = $atts;

				}

				// Filter to allow us to modify the fonts array before saving the transient
				$fonts = apply_filters( 'vosl_font_google_fonts_array', $fonts );

				// Set transient for google fonts
				set_transient( 'vosl_font_google_fonts_list', $fonts, 14 * DAY_IN_SECONDS );

			} else {
				$fonts = get_transient( 'vosl_font_google_fonts_list' );
			}

			return apply_filters( 'vosl_font_get_google_fonts', $fonts );
	}
	
	private function vosl_get_default_fonts() {
			if ( false === get_transient( 'vosl_font_default_fonts' ) ) {

				// Declare default font list
				$font_list = array(
						'Arial'               => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Century Gothic'      => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Courier New'         => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Georgia'             => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Helvetica'           => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Impact'              => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Lucida Console'      => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Lucida Sans Unicode' => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Palatino Linotype'   => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'sans-serif'          => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'serif'               => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Tahoma'              => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Trebuchet MS'        => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
						'Verdana'             => array( 'weights' => array( '400', '400italic', '700', '700italic' ) ),
				);

				// Build font list to return
				$fonts = array();
				foreach ( $font_list as $font => $attributes ) {

					$urls = array();

					// Get font properties from json array.
					foreach ( $attributes['weights'] as $variant ) {
						$urls[ $variant ] = "";
					}

					// Create a font array containing it's properties and add it to the $fonts array
					$atts = array(
							'name'         => $font,
							'font_type'    => 'default',
							'font_weights' => $attributes['weights'],
							'subsets'      => array(),
							'files'        => array(),
							'urls'         => $urls,
					);

					// Add this font to all of the fonts
					$id           = strtolower( str_replace( ' ', '_', $font ) );
					$fonts[ $id ] = $atts;
				}

				// Filter to allow us to modify the fonts array before saving the transient
				$fonts = apply_filters( 'vosl_font_default_fonts_array', $fonts );

				// Set transient for google fonts (for 2 weeks)
				set_transient( 'vosl_font_default_fonts', $fonts, 14 * DAY_IN_SECONDS );

			} else {
				$fonts = get_transient( 'vosl_font_default_fonts' );
			}

			// Return the font list
			return apply_filters( 'vosl_font_get_default_fonts', $fonts );
		}
		
	public function vosl_get_all_fonts()
	{
		$default_fonts = $this->vosl_get_default_fonts();
		$google_fonts  = $this->vosl_get_google_fonts();

		if ( ! $default_fonts ) {
			$default_fonts = array();
		}

		if ( ! $google_fonts ) {
			$google_fonts = array();
		}

		return array_merge( $default_fonts, $google_fonts );
	}
	
	public function save_admin_settings($post)
	{   

		if(isset($post['enable_default_cluster']) )
		{	
			$cluster_value = sanitize_text_field($post['enable_default_cluster']);
			
		}else if(!isset($post['enable_default_cluster']))
		{
			$cluster_value = 0;
			
		}else
		{
			$cluster_value = 1;
		}	
		
		vosl_data('vosl_enable_default_cluster', 'update', $cluster_value);
		
		if(isset($post['vosl_custom_font']) and $post['vosl_custom_font']!='')
		{	
			$vosl_custom_font = sanitize_text_field($post['vosl_custom_font']);
			
		}else
		{
			$vosl_custom_font = '';
		}
		
		vosl_data('vosl_custom_font', 'update', $vosl_custom_font);
		//custom style //
        if(isset($post['vosl_custom_style']) and $post['vosl_custom_style']!='')
		{	
			$vosl_custom_style = $post['vosl_custom_style'];
			
		}else
		{
			$vosl_custom_style = '';
		}
		
		vosl_data('vosl_custom_style', 'update', $vosl_custom_style);
		
        //   
		if(isset($post['search_box_placeholder_text']) and $post['search_box_placeholder_text']!='')
		{	
			$search_box_placeholder_text = sanitize_text_field($post['search_box_placeholder_text']);
			
		}else
		{
			$search_box_placeholder_text = '';
		}
		
		vosl_data('search_box_placeholder_text', 'update', $search_box_placeholder_text);
		
		if(!isset($post['location_map']))
			$set_value = 0;
		else
			$set_value = sanitize_text_field($post['location_map']);
				
		vosl_data('sl_location_map', 'update', $set_value);
		
		$set_value = sanitize_text_field($post['vosl_map_center_point_type']);
			
		vosl_data('sl_current_location_lookup', 'update', $set_value);
		
		if(!isset($post['vosl_show_love']))
			$set_value = 0;
		else
			$set_value = sanitize_text_field($post['vosl_show_love']);
			
		vosl_data('vosl_show_love', 'update', $set_value);	
		
		if(!isset($post['enable_default_tags']))
			$set_value = 0;
		else
			$set_value = sanitize_text_field($post['enable_default_tags']);
			
		vosl_data('vosl_enable_default_tags', 'update', $set_value);
		
		if($post['fndLocationText']!='')
		{
			$fndLocationText = sanitize_text_field( $post['fndLocationText'] );
			vosl_data('sl_find_location_text', 'update', $fndLocationText);
			
		}else
		{
			vosl_data('sl_find_location_text', 'update', '');
		}	
		
		if($post['vosl_tags_filter_label']!='')
		{
			$vosl_tags_filter_label = sanitize_text_field( $post['vosl_tags_filter_label'] );
			vosl_data('vosl_tags_filter_label', 'update', $vosl_tags_filter_label);
			
		}else
		{
			vosl_data('vosl_tags_filter_label', 'update', '');
		}
		
		if($post['vosl_map_api_key']!='')
		{
			$vosl_map_api_key = sanitize_text_field( $post['vosl_map_api_key'] );
			vosl_data('vosl_map_api_key', 'update', $vosl_map_api_key);
			
		}else
		{
			vosl_data('vosl_map_api_key', 'update', '');
		}
		
		if(!isset($post['vosl_radius_filter']))
			$vosl_radius_filter = 0;
		else
			$vosl_radius_filter = sanitize_text_field($post['vosl_radius_filter']);
		
		if($post['color-field']!='')
		{
			$colorfield = sanitize_text_field( $post['color-field'] );
			vosl_data('sl_highlight_color', 'update', $colorfield);
		}	
			
		if($post['color-field-text']!='')
		{
			$colorfieldtext = sanitize_text_field( $post['color-field-text'] );
			vosl_data('sl_highlight_text_color', 'update', $colorfieldtext);
		}	
			
		if($post['color-field-text-bg']!='')
		{
			$colorfieldtextbg = sanitize_text_field( $post['color-field-text-bg'] );
			vosl_data('sl_listing_bg_color', 'update', $colorfieldtextbg);	
		}
		
		if($post['selMiles']!='')
		{
			$selMiles = sanitize_text_field( $post['selMiles'] );
			vosl_data('vosl_km', 'update', $selMiles);	
		}
		
		if($post['map_region']!='')
		{
			$vosl_map_region_arr=explode(":", $post['map_region']);
			vosl_data('vosl_google_map_country', 'update', $vosl_map_region_arr[0]);	
			vosl_data('vosl_google_map_domain', 'update', $vosl_map_region_arr[1]);
			vosl_data('vosl_map_region', 'update', $vosl_map_region_arr[2]);
		}
		
		
		// save more settings
		//do_action("vosl_setting_options");
		$get_vosl_map_custom_center = vosl_data('vosl_map_custom_center');
		$vosl_map_zoom_level = sanitize_text_field( $post['vosl_map_zoom_level'] );
		$vosl_map_size_width = sanitize_text_field( $post['vosl_map_size_width'] );
		$vosl_map_size_height = sanitize_text_field( $post['vosl_map_size_height'] );
		$vosl_listing_column_width = sanitize_text_field( $post['vosl_listing_column_width'] );
		$vosl_listing_column_height = sanitize_text_field( $post['vosl_listing_column_height'] );
		$vosl_search_box_width = sanitize_text_field( $post['vosl_search_box_width'] );
		$vosl_search_box_height = sanitize_text_field( $post['vosl_search_box_height'] );
		$vosl_map_custom_center = sanitize_text_field( $post['vosl_map_custom_center'] );
		$vosl_custom_map_popup_width = sanitize_text_field( $post['vosl_custom_map_popup_width'] );
		$vosl_custom_map_popup_bgcolor = sanitize_text_field( $post['vosl_custom_map_popup_bgcolor'] );
		$vosl_custom_map_marker_color = sanitize_text_field( $post['vosl_custom_map_marker_color'] );
		$vosl_custom_map_popup_textcolor = sanitize_text_field( $post['vosl_custom_map_popup_textcolor'] );
		$vosl_radius_filter_max = sanitize_text_field( $post['vosl_radius_filter_max'] );
		$vosl_radius_distance_label_text = sanitize_text_field( $post['vosl_radius_distance_label_text'] );
		$vosl_radius_label_text = sanitize_text_field( $post['vosl_radius_label_text'] );
		$vosl_default_tags_filter = sanitize_text_field( $post['vosl_default_tags_filter'] );
		$vosl_listing_load_size = sanitize_text_field( $post['vosl_listing_load_size'] );
		
		if($vosl_listing_load_size!='' and !is_numeric($vosl_listing_load_size) )
		{
			$vosl_listing_load_size = '';
			
		}else if( $vosl_listing_load_size!='' and is_numeric($vosl_listing_load_size) and (int)$vosl_listing_load_size > 1000 )
		{
			$vosl_listing_load_size = 1000;
		}
		
		//echo $vosl_listing_load_size;
		
		vosl_data('vosl_map_zoom_level', 'update', $vosl_map_zoom_level);
		vosl_data('vosl_map_size_width', 'update', $vosl_map_size_width);
		vosl_data('vosl_map_size_height', 'update', $vosl_map_size_height);
		vosl_data('vosl_listing_column_width', 'update', $vosl_listing_column_width);
		vosl_data('vosl_listing_column_height', 'update', $vosl_listing_column_height);
		vosl_data('vosl_search_box_width', 'update', $vosl_search_box_width);
		vosl_data('vosl_search_box_height', 'update', $vosl_search_box_height);
		vosl_data('vosl_map_custom_center', 'update', $vosl_map_custom_center);
		vosl_data('vosl_custom_map_popup_width', 'update', $vosl_custom_map_popup_width);
		vosl_data('vosl_custom_map_popup_bgcolor', 'update', $vosl_custom_map_popup_bgcolor);
		vosl_data('vosl_custom_map_marker_color', 'update', $vosl_custom_map_marker_color);
		vosl_data('vosl_custom_map_popup_textcolor', 'update', $vosl_custom_map_popup_textcolor);
		vosl_data('vosl_radius_filter', 'update', $vosl_radius_filter);
		vosl_data('vosl_radius_distance_label_text', 'update', $vosl_radius_distance_label_text);
		vosl_data('vosl_radius_label_text', 'update', $vosl_radius_label_text);
		vosl_data('vosl_default_tags_filter', 'update', $vosl_default_tags_filter);
		vosl_data('vosl_listing_load_size', 'update', $vosl_listing_load_size);
		
		if($vosl_radius_filter==1 and $vosl_radius_filter_max=='')
			$vosl_radius_filter_max = 300;
			
		vosl_data('vosl_radius_filter_max', 'update', $vosl_radius_filter_max);
	
		/*if($vosl_map_custom_center!='' and $get_vosl_map_custom_center!=$vosl_map_custom_center)
		{
			$result_array = $this->vosl_pro_do_geocoding_find($vosl_map_custom_center);
			
			if(is_array($result_array) and !empty($result_array))
			{
				$vosl_lattitude = $result_array[0];
				$vosl_longitude = $result_array[1];
				$json = array("lat" => $vosl_lattitude, 'long' => $vosl_longitude);
				$json = json_encode($json);
				vosl_data('vosl_map_custom_center_coordinates', 'update', $json);
			}
			
		}*/
		
		if($post['vosl_map_custom_center_lat']!='' and $post['vosl_map_custom_center_long']!='' and $post['vosl_map_custom_center_lat']!='46.60207' and $post['vosl_map_custom_center_long']!="-120.505898")
		{
			$json = array("lat" => $post['vosl_map_custom_center_lat'], 'long' => $post['vosl_map_custom_center_long']);
			$json = json_encode($json);
			vosl_data('vosl_map_custom_center_coordinates', 'update', $json);
			
			// if textbox for custom map center is null, then only search for formatted address from lat, long
			if($vosl_map_custom_center=='')
			{
				$vosl_map_api_key = vosl_data('vosl_map_api_key');
				$geocode=file_get_contents('https://maps.google.com/maps/api/geocode/json?latlng='.$post['vosl_map_custom_center_lat'].",".$post['vosl_map_custom_center_long'].'&key='.$vosl_map_api_key);
				$output= json_decode($geocode, true);	
				
				if($output['results'][0]['formatted_address']!='')
				{
					vosl_data('vosl_map_custom_center', 'update', sanitize_text_field($output['results'][0]['formatted_address']));
				}
			}	
		}else
		{
			vosl_data('vosl_map_custom_center_coordinates', 'delete');
		}
	}
	
	public function vosl_pro_do_geocoding_find($address) {
	  global $wpdb, $text_domain, $vosl_vars;
	  
		// Fixed on for servers without SSL certificate, moved to http from https 19th Nov 15 
		$base_url = "http://maps.googleapis.com/maps/api/geocode/json?";
	
		if ($sensor!="" && !empty($sensor) && ($sensor === "true" || $sensor === "false" )) {$base_url .= "sensor=".$sensor;} else {$base_url .= "sensor=false";}
		
		$locale_info = get_locale();
		$locale_info = explode("_",$locale_info);
		$locale_info = $locale_info[0];	
		
		if($locale_info!='')
			$base_url .= "&language=".$locale_info;
			
		$map_region = vosl_data('vosl_map_region');
		$region=(!empty($map_region))?$map_region : "" ;
		
		if($region!='')
			$base_url .= "&region=".$region;	
		
		// Iterate through the rows, geocoding each address
		$request_url = $base_url . "&address=" . urlencode(trim($address)); //print($request_url );
		
		//New code to accomdate those without 'file_get_contents' functionality for their server - added by Manoj M - thank you
		if (extension_loaded("curl") && function_exists("curl_init")) {
			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, $request_url);
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
			$resp_json = curl_exec($cURL);
			curl_close($cURL);  
		}else{
			$resp_json = file_get_contents($request_url) or die("url not loading");
		}
		//End of new code
	
		$resp = json_decode($resp_json, true); //var_dump($resp);
		$status = $resp['status']; //$status = "";
		$lat = (!empty($resp['results'][0]['geometry']['location']['lat']))? $resp['results'][0]['geometry']['location']['lat'] : "" ;
		$lng = (!empty($resp['results'][0]['geometry']['location']['lng']))? $resp['results'][0]['geometry']['location']['lng'] : "" ;
		//die("<br>compare: ".strcmp($status, "OK")."<br>status: $status<br>");
		if (strcmp($status, "OK") == 0) {
			// successful geocode
			$geocode_pending = false;
			$lat = $resp['results'][0]['geometry']['location']['lat'];
			$lng = $resp['results'][0]['geometry']['location']['lng'];
			return array($lat,$lng);
		} 
	 }
	
	/*public function vosl_show_tabscontent()
	{
		
	}*/
	
	public function vosl_delete_listing()
	{
		global $wpdb;
		$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TABLE." WHERE id=%d", (int)$_REQUEST['loc_id']));
		echo "OK";
		die;
	}
	
	public function vosl_delete_tag()
	{
		global $wpdb;
		$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TAGS_TABLE." WHERE id=%d", (int)$_REQUEST['tag_id']));
		$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TAGS_ASSOC_TABLE." WHERE tag_id=%d", (int)$_REQUEST['tag_id']));
		echo "OK";
		die;
	}
	
	public function vo_wp_gear_manager_admin_scripts() {
		wp_enqueue_script('media-upload');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('jquery');
	}
	
	public function vo_wp_gear_manager_admin_styles() {
		global $vosl_base;
		wp_enqueue_style('thickbox');
		
		
		if($_GET['page']=='vo-locator-the-wp-store-locator/vosl-admin/pages/settings.php')
		{
			wp_enqueue_style('vosl-admin-custom', $vosl_base."/css/custom.css", array(), VOSL_VERSION);
		}
	}
	
	public function vosl_enqueue_admin_scripts(){
		global $vosl_base, $vosl_version;
		
		//echo $vosl_version; die;
		$admin_data = wp_get_current_user();
		wp_enqueue_script('vosl-admin', $vosl_base.'/js/vosl-admin.js?t='.time(), array('jquery')); //jQuery will load as dependency
		wp_localize_script( 'vosl-admin', 'vosl_admin_ajax_script_url', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
		 
		if($_SERVER["HTTPS"] != "on" ) {
			$httpvar = 'http';
		}else {
			$httpvar = 'https';
		}
		
		if( isset($_GET['page']) and strpos($_GET['page'],"vo-locator-the-wp-store-locator")!==false )
		{
			wp_enqueue_script('vosl-media-uploader', $vosl_base.'/js/vosl-media-uploader.js', array('jquery')); //jQuery will load as dependency
		}
		
		wp_enqueue_script('vosl-admin-ui', $httpvar.'://code.jquery.com/ui/1.11.4/jquery-ui.js', array('jquery')); //jQuery will load as dependency
		wp_enqueue_style('vosl-admin-uicss', $httpvar.'://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css', array());
		wp_enqueue_style('vosl-admin-datatablecss', $vosl_base.'/css/jquery.dataTables.min.css', array());
		wp_enqueue_style('vosl-admin', $vosl_base.'/css/admin.css', array(),(string)$vosl_version);
		wp_enqueue_style('vosl-admin-fa', $vosl_base.'/lib/font-awesome/css/font-awesome.min.css', array());
		wp_enqueue_script('vosl-google-fonts', $httpvar.'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js');
		wp_enqueue_script('vosl-admin-datatablejs', $vosl_base.'/js/jquery.dataTables.min.js', array());
		wp_enqueue_script('vosl-admin-lightboxvideojs', $vosl_base.'/lib/lightboxvideo/src/videoLightning.js', array('jquery'));
		wp_enqueue_script('vosl-admin-jquery-confirm-js', $vosl_base.'/lib/jquery-confirm/jquery-confirm.min.js', array('jquery'));
		wp_enqueue_style('vosl-admin-jquery-confirm-css', $vosl_base.'/lib/jquery-confirm/jquery-confirm.min.css', array());

			
	}
	
	public function vo_add_color_picker( $hook ) {
 		global $vosl_base;
		if( is_admin() ) { 
		 
			// Add the color picker css file       
			wp_enqueue_style( 'wp-color-picker' ); 
			 
			// Include our custom jQuery file with WordPress Color Picker dependency
			wp_enqueue_script( 'custom-script-handle', $vosl_base . '/js/color-script.js', array( 'wp-color-picker' ), false, true ); 
		}
	}
	
	public function vosl_admin_notices()
	{ 
		$vosl_google_map_domain = vosl_data('vosl_google_map_domain');
		$vosl_map_api_key = vosl_data('vosl_map_api_key');
		//$vosl_google_map_server_api_key = vosl_data('vosl_google_map_server_api_key');
		
		if($vosl_google_map_domain=='')
		{
			echo '<div class="updated">
			   <p>'.__('VO Locator: We have added regions within the settings page of the plugin. Please take a look at your settings for selecting appropriate region and save it.', VOSL_TEXT_DOMAIN).'</p>    
			</div>'; 
		}
		
		if($vosl_map_api_key=='')
		{
			printf( '<div class="%1$s"><h4 style="margin:10px 0 10px;">%2$s</h4>%4$s<p><a href="%3$s" target="_blank">'.__( 'Please visit here', VOSL_TEXT_DOMAIN ).'</a> %5$s</p></div>', 'notice notice-error is-dismissible', __( 'VO Locator - Google Maps API Key Missing', VOSL_TEXT_DOMAIN ), 'https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,places_backend&keyType=CLIENT_SIDE&reusekey=true', __( 'As of June 22, 2016 Google Maps requires an API key to function properly.', VOSL_TEXT_DOMAIN ),__( 'to set up a free Google Maps API key', VOSL_TEXT_DOMAIN ) ); 
		}
		
		if($out = get_transient( get_current_user_id().'proerror' ) ) {
			delete_transient( get_current_user_id().'proerror' );
			
			$class = 'notice notice-error is-dismissible';
		
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, __( $out, VOSLP_TEXT_DOMAIN ) ); 
		}
		
		if($out = get_transient( get_current_user_id().'prosuccess' ) ) {
			delete_transient( get_current_user_id().'prosuccess' );
			
			$class = 'notice notice-success  is-dismissible';
		
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, __( $out, VOSLP_TEXT_DOMAIN ) ); 
		}
		
		// geocoding error
		if($out = get_transient( get_current_user_id().'voslgeocodeerror' ) ) {
			delete_transient( get_current_user_id().'voslgeocodeerror' );
			
			$class = 'notice notice-error is-dismissible';
			printf( '<div class="%1$s"><p>%2$s</p></div>', $class, __( $out, VOSL_TEXT_DOMAIN ) ); 
		}
		
		$review_ignore = add_query_arg( array( VOSL_TEXT_DOMAIN . '_admin_notice_ignore' => 'review' ) );
		
		$notices['review'] = array(
          'title' => __('Leave A Review?', VOSL_TEXT_DOMAIN),
          'msg' => sprintf(__('We hope you\'ve enjoyed using WordPress %s! Would you consider leaving us a review on WordPress.org?', VOSL_TEXT_DOMAIN), 'VO Store Locator'),
          'link' => '<li><span class="dashicons dashicons-external"></span><a href="https://wordpress.org/support/plugin/vo-locator-the-wp-store-locator/reviews?filter=5&rate=5#new-post" target="_blank">' . __('Sure! I\'d love to!', VOSL_TEXT_DOMAIN) . '</a></li>
                         <li><span class="dashicons dashicons-smiley"></span><a href="' . $review_ignore . '"> ' . __('I\'ve already left a review', VOSL_TEXT_DOMAIN) . '</a></li>
                         <li><span class="dashicons dashicons-dismiss"></span><a href="' . $review_ignore . '">' . __('Never show again', VOSL_TEXT_DOMAIN) . '</a></li>'
        );

        $this->admin_notice($notices);
	}
	
	public function vosl_admin_notice() {
        $vosl_disable_review_notices = vosl_data('vosl_disable_review_notices');
      
        if ( $vosl_disable_review_notices == '' ) {
            if ( current_user_can( 'manage_options' ) ) {
                return true;
            }
        }
        return false;
    }
	
	// Ignore function that gets ran at admin init to ensure any messages that were dismissed get marked
    public function vosl_admin_notice_ignore() {
       
        // If user clicks to ignore the notice, update the option to not show it again
        if ( isset( $_GET[VOSL_TEXT_DOMAIN . '_admin_notice_ignore'] ) ) {
            vosl_data('vosl_disable_review_notices', 'update', 1);
            $query_str = remove_query_arg( VOSL_TEXT_DOMAIN . '_admin_notice_ignore' );
            wp_redirect( $query_str );
            exit;
        }
    }

	public function admin_notice( $admin_notices ) {
       
        // Check options
        if ( !$this->vosl_admin_notice() ) {
            return false;
        }
		
        foreach ( $admin_notices as $slug => $admin_notice ) {

                $admin_display_msg = ( isset($admin_notices[$slug]['msg'] ) ? $admin_notices[$slug]['msg'] : '' );
                $admin_display_title = ( isset($admin_notices[$slug]['title'] ) ? $admin_notices[$slug]['title'] : '' );
                $admin_display_link = ( isset( $admin_notices[$slug]['link'] ) ? $admin_notices[$slug]['link'] : '' );
                $output_css = false;

               $query_str = ( isset( $admin_notices[$slug]['later_link'] ) ? $admin_notices[$slug]['later_link'] : esc_url(add_query_arg( VOSL_TEXT_DOMAIN . '_admin_notice_ignore', $slug ) ) );
			   echo '<div class="update-nag vosl-admin-notice">
						<div class="vosl-notice-logo" style="background-image: url(' . VOSL_BASE.'/images/logo.small.png);"></div>
						<p class="vosl-notice-title">' . $admin_display_title . '</p>
						<p class="vosl-notice-body">' . $admin_display_msg . '</p>
						<ul class="vosl-notice-body vosl-blue">' . $admin_display_link . '</ul>
					  </div>';
        }
    }
	
	public function edit_vosl_tag()
	{
		global $wpdb;
		include(VOSL_PAGES_PATH."/edit-tags.php");
	}
	
	public function add_vosl_tags()
	{
		global $wpdb;
		include(VOSL_PAGES_PATH."/add-tags.php");
	}
	
	public function manage_vosl_tags()
	{
		global $wpdb;
		include(VOSL_PAGES_PATH."/manage-tags.php");
	}
	
	public function edit_vosl_location()
	{
		global $wpdb;
		include(VOSL_PAGES_PATH."/edit-locations.php");
	}
	
	public function add_vosl_location()
	{
		global $wpdb;
		include(VOSL_PAGES_PATH."/add-locations.php");
	}
	
	public function manage_vosl_locations()
	{
		global $wpdb;
		include(VOSL_PAGES_PATH."/manage-locations.php");
	}
	
	public function manage_vosl_settings()
	{
		global $wpdb;
		include(VOSL_PAGES_PATH."/settings.php");
	}
	
	public function vosl_get_listings_admin()
	{
		global $wpdb, $vosl_base; 
		
		$requestData= $_REQUEST;
		
		$columns = array(
		// datatable column index  => database column name
			0 =>'id',
			2 => 'store_name',
			3 => 'address',
			4 => 'city',
			5 => 'state',
			6 => 'zip',
			7 => 'latitude'
		);
		
		// filter to update columns sorting for pro addon
		$columns = apply_filters( 'vosl_change_admin_listing_sorting_columns', $columns );

		// getting total number records without any search
		$sql = "SELECT COUNT(s.id) ";
		$sql.=" FROM ".VOSL_TABLE." s ";
		
		// join on tags
		if( (int)$requestData['tags_filter'] > 0)
		{
			$sql.= " JOIN ".VOSL_TAGS_ASSOC_TABLE." ta ON (ta.store_id = s.id AND ta.tag_id = ".(int)$requestData['tags_filter']." ) ";
		}
		
		$totalData = $wpdb->get_var( $sql );
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		//$sql = "SELECT id, store_name, address, city, state, zip ";
		$sql = "SELECT count(s.id) ";
		$sql.=" FROM ".VOSL_TABLE." s ";
		
		// join on tags
		if( (int)$requestData['tags_filter'] > 0)
		{
			$sql.= " JOIN ".VOSL_TAGS_ASSOC_TABLE." ta ON (ta.store_id = s.id AND ta.tag_id = ".(int)$requestData['tags_filter']." ) ";
		}
		
		$sql.= " WHERE 1=1 ";
		
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			if($requestData['filter_by']=='')
			{
				$sql.=" AND ( s.store_name LIKE '%".$requestData['search']['value']."%' ";    
				$sql.=" OR s.address LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR s.state LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR s.zip LIKE '%".$requestData['search']['value']."%' ";
				$sql.=" OR s.city LIKE '%".$requestData['search']['value']."%' )";
				
			}else
			{
				$sql.=" AND ( s.`".$requestData['filter_by']."` LIKE '%".$requestData['search']['value']."%' ) "; 
				
			}
			
			// add filter for custom fields here to modify original query
			$sql = apply_filters('vosl_add_custom_fields_search_filter', $sql, $requestData, 'count');
			
			$count = $wpdb->get_var( $sql );
			$totalFiltered = $count; // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		}
		
		$sql = "SELECT s.id, s.store_name, s.address, s.city, s.state, s.zip, s.latitude, s.longitude ";
		$sql.=" FROM ".VOSL_TABLE." s ";
		
		// join on tags
		if( (int)$requestData['tags_filter'] > 0)
		{
			$sql.= " JOIN ".VOSL_TAGS_ASSOC_TABLE." ta ON (ta.store_id = s.id AND ta.tag_id = ".(int)$requestData['tags_filter']." ) ";
		}
		
		$sql.= " WHERE 1=1 ";
		
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			if($requestData['filter_by']=='')
				{
					$sql.=" AND ( s.store_name LIKE '%".$requestData['search']['value']."%' ";    
					$sql.=" OR s.address LIKE '%".$requestData['search']['value']."%' ";
					$sql.=" OR s.state LIKE '%".$requestData['search']['value']."%' ";
					$sql.=" OR s.zip LIKE '%".$requestData['search']['value']."%' ";
					$sql.=" OR s.city LIKE '%".$requestData['search']['value']."%' )"; 
					
				}else
				{
					$sql.=" AND ( s.`".$requestData['filter_by']."` LIKE '%".$requestData['search']['value']."%' ) "; 
					
				}
		}
		
		//echo $sql;
		
		// add filter for custom fields here to modify original query
		$sql = apply_filters('vosl_add_custom_fields_search_filter', $sql, $requestData, 'result');
		
		//echo $sql;
		
		// by default sort the listings so that missing co-ordinates be at the top
		if($requestData['order'][0]['column']==0)
		{
			$sql.=" ORDER BY latitude asc LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
			
		}else
		{
			$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		}
		
		$data = array();
		$results = $wpdb->get_results( $sql, ARRAY_A );
		foreach($results as $row) {  // preparing an array
			$nestedData=array(); 
			
			if($row["latitude"]=='')
				$latlong = '';
			else
				$latlong = $row["latitude"].",".$row["longitude"];
			
			$custom_marker_icon = '';	
			$custom_checkbox = '';
			$custom_marker_icon = apply_filters('vosl_get_custom_marker_icon', $row["id"]);
			$custom_checkbox = apply_filters('vosl_get_admin_listing_checkbox', $row["id"]);
			
			$nestedData[] = $row["id"];
			
			// It means filter not found
			if($custom_marker_icon==$row['id'])
				$custom_marker_icon = '';
				
			// It means filter not found
			if($custom_checkbox==$row['id'])
				$custom_checkbox = '';	
			
			if($custom_checkbox!='')
				$nestedData[] = $custom_checkbox;
			
			//$nestedData[] = $row["custom_marker_color"];
			$nestedData[] = $custom_marker_icon;
					
				
			$nestedData[] = $row["store_name"];
			$nestedData[] = $row["address"];
			$nestedData[] = $row["city"];
			$nestedData[] = $row["state"];
			$nestedData[] = $row["zip"];
			$nestedData[] = $latlong;
			
			
			
			//$nestedData[] = '<a title="Edit Listing" class="fa fa-pencil-square-o vosl_admin_listing_edit" href="admin.php?page='.VOSL_PAGES_DIR.'/edit-locations.php'.'&id='.$row["id"].'"></a>&nbsp;&nbsp;<a href="#" listing-id="'.$row["id"].'" id="vosl_admin_listing_del_'.$row['id'].'" class="fa fa-trash-o vosl_admin_listing_delete" title="Delete Listing"></a>';
			$data[] = $nestedData;
		}
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data   // total data array
					);
		
		echo json_encode($json_data);  // send data as json format
		die;
	}
	
	public function vosl_get_tags_listings_admin()
	{
		global $wpdb, $vosl_base; 
		
		$requestData= $_REQUEST;
		
		$columns = array(
		// datatable column index  => database column name
			0 =>'id',
			2 => 'tag_name'
		);

		// getting total number records without any search
		$sql = "SELECT COUNT(id) ";
		$sql.=" FROM ".VOSL_TAGS_TABLE;
		$totalData = $wpdb->get_var( $sql );
		$totalFiltered = $totalData;  // when there is no search parameter then total number rows = total number filtered rows.
		
		
		//$sql = "SELECT id, store_name, address, city, state, zip ";
		$sql = "SELECT count(id) ";
		$sql.=" FROM ".VOSL_TAGS_TABLE." WHERE 1=1";
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( tag_name LIKE '%".$requestData['search']['value']."%') ";    
			$count = $wpdb->get_var( $sql );
			$totalFiltered = $count; // when there is a search parameter then we have to modify total number filtered rows as per search result. 
		}
		
		$sql = "SELECT id, tag_name, tag_color ";
		$sql.=" FROM ".VOSL_TAGS_TABLE." WHERE 1=1";
		if( !empty($requestData['search']['value']) ) {   // if there is a search parameter, $requestData['search']['value'] contains search parameter
			$sql.=" AND ( tag_name LIKE '%".$requestData['search']['value']."%') ";    
		}
	
		$sql.=" ORDER BY ". $columns[$requestData['order'][0]['column']]."   ".$requestData['order'][0]['dir']."  LIMIT ".$requestData['start']." ,".$requestData['length']."   ";
		
		$data = array();
		$results = $wpdb->get_results( $sql, ARRAY_A );
		foreach($results as $row) {  // preparing an array
			$nestedData=array(); 
			
			$tag_color = '';
			
			if($row['tag_color']!='')
			{
				$tag_color = 'background: '.$row['tag_color'];
			}
			
			$nestedData[] = $row["id"];
			
			$nestedData[] = '<div style="width:20px; height:20px; margin:0 auto; '.$tag_color.'"><div>';
			$nestedData[] = $row["tag_name"];
			//$nestedData[] = '<a title="Edit Tag" class="fa fa-pencil-square-o vosl_admin_tags_listing_edit" href="admin.php?page='.VOSL_PAGES_DIR.'/edit-tags.php'.'&id='.$row["id"].'"></a>&nbsp;&nbsp;<a href="#" tag-id="'.$row["id"].'" id="vosl_admin_tag_del_'.$row['id'].'" class="fa fa-trash-o vosl_admin_tags_listing_delete" title="Delete Tag"></a>';
			$data[] = $nestedData;
		}
		
		$json_data = array(
					"draw"            => intval( $requestData['draw'] ),   // for every request/draw by clientside , they send a number as a parameter, when they recieve a response/data they first check the draw number, so we are sending same number in draw. 
					"recordsTotal"    => intval( $totalData ),  // total number of records
					"recordsFiltered" => intval( $totalFiltered ), // total number of records after searching, if there is no searching then totalFiltered = totalData
					"data"            => $data   // total data array
					);
		
		echo json_encode($json_data);  // send data as json format
		die;
	}
	
	public function get_store_tags($store_id = 0)
	{
		global $wpdb;
		
		if($store_id==0)
		{
			$sql = "SELECT id, tag_name FROM ".VOSL_TAGS_TABLE." order by tag_name ASC ";
		}
		
		$rows = $wpdb->get_results($sql,ARRAY_A);
		return $rows;
	}
	
	public function get_store_custom_fields($store_id = 0)
	{
		global $wpdb;
		
		if($store_id==0)
		{
			$sql = "SELECT c.id as field_id, c.label, c.field_name, c.fa_icon, c.field_type FROM ".VOSL_CUSTOM_FIELDS." c  order by c.`order` ASC ";
			
		}else
		{
			$sql = $wpdb->prepare("SELECT c.id as field_id , c.field_type,  c.label, c.field_name, ca.id as store_custom_field_id, ca.custom_field_value, c.fa_icon FROM ".VOSL_CUSTOM_FIELDS." c LEFT JOIN ".VOSL_CUSTOM_FIELDS_ASSOC_TABLE." ca ON (ca.custom_field_id = c.id AND ca.store_id = %d) order by c.`order` ASC", $store_id);
		}
		
		$rows = $wpdb->get_results($sql,ARRAY_A);
		return $rows;
	}
}
?>