<?php
ob_start();
global $vosl_admin_classes_dir, $vosl_listing_load_size;
require_once $vosl_admin_classes_dir . '/vosl-locator-admin.php';
$vosl_locator_admin = new VoStoreLocator_Admin();

$location_value = vosl_data('sl_location_map');
$current_location_lookup = vosl_data('sl_current_location_lookup');
$find_location_text = vosl_data('sl_find_location_text');
$enable_default_tags = vosl_data('vosl_enable_default_tags');
$vosl_show_love = vosl_data('vosl_show_love');
$vosl_map_api_key = vosl_data('vosl_map_api_key');
$enable_default_cluster = vosl_data("vosl_enable_default_cluster");
$vosl_custom_font = vosl_data("vosl_custom_font");
$vosl_custom_style = vosl_data("vosl_custom_style");

$vosl_tags_filter_label = vosl_data('vosl_tags_filter_label');
$vosl_custom_map_marker_color = vosl_data('vosl_custom_map_marker_color');
$vosl_custom_map_popup_width = vosl_data('vosl_custom_map_popup_width');
$vosl_custom_map_popup_width = (empty($vosl_custom_map_popup_width)?420:$vosl_custom_map_popup_width);
$vosl_popup_rightside_width = $vosl_custom_map_popup_width - 105;
$vosl_custom_map_popup_bgcolor = vosl_data('vosl_custom_map_popup_bgcolor');
$vosl_custom_map_popup_bgcolor = (empty($vosl_custom_map_popup_bgcolor)?"#FFFFFF":$vosl_custom_map_popup_bgcolor);
$vosl_custom_map_popup_textcolor = vosl_data('vosl_custom_map_popup_textcolor');
$vosl_custom_map_popup_textcolor = (empty($vosl_custom_map_popup_textcolor)?"#000000":$vosl_custom_map_popup_textcolor);
$search_box_placeholder_text = vosl_data('search_box_placeholder_text');
$vosl_default_tags_filter = vosl_data('vosl_default_tags_filter');
$vosl_listing_load_size = vosl_data('vosl_listing_load_size');

// additional settings that were moved from pro addon
$vosl_map_zoom_level = vosl_data('vosl_map_zoom_level');
$vosl_map_size_width = vosl_data('vosl_map_size_width');
$vosl_map_size_height = vosl_data('vosl_map_size_height');
$vosl_listing_column_width = vosl_data('vosl_listing_column_width');
$vosl_listing_column_height = vosl_data('vosl_listing_column_height');
$vosl_map_custom_center = vosl_data('vosl_map_custom_center');
$vosl_search_box_width = vosl_data('vosl_search_box_width');

if($vosl_listing_load_size=='')
	$vosl_listing_load_size = 100;

if($vosl_map_size_width=='')
	$vosl_map_size_width = 800;
	
if($vosl_map_size_height=='')
	$vosl_map_size_height = 350;
	
if($vosl_listing_column_width=='')
	$vosl_listing_column_width = 267;
	
if($vosl_listing_column_height=='')
	$vosl_listing_column_height = 350;
	
if($vosl_search_box_width=='')
	$vosl_search_box_width = 336;		
	
if($vosl_map_zoom_level=='')
	$vosl_map_zoom_level = 4;
// additional settings that were moved from pro addon

$vosl_offset = 0;

if($vosl_custom_map_popup_width > 420)
{
	$vosl_offset = -1 * abs(($vosl_custom_map_popup_width-420));
	$vosl_offset = round($vosl_offset/2);
	
}else if($vosl_custom_map_popup_width < 420)
{
	$vosl_offset = round(abs((420-$vosl_custom_map_popup_width))/2);
}

if($vosl_tags_filter_label=='')
	$vosl_tags_filter_label = 'All Types';
		
if($enable_default_cluster=='')
	$enable_default_cluster = 1;
	
if($vosl_show_love=='')
	$vosl_show_love = 1;	

if($current_location_lookup=='')
	$current_location_lookup = 0;
	
if($location_value=='')
	$location_value = 1;		

if($current_location_lookup==0)
{
	$sql = $wpdb->prepare("SELECT * FROM ".VOSL_TABLE." order by store_name ASC limit 0, %d", $vosl_listing_load_size);
	$result_voslrow = $wpdb->get_results($sql,ARRAY_A);
	$vosl_listings_count = $wpdb->get_var( "SELECT COUNT(*) FROM ".VOSL_TABLE );
	$vosl_map_custom_center_coordinates = vosl_data('vosl_map_custom_center_coordinates');
	
	if($vosl_map_custom_center_coordinates!='')
	{
		$json_array = json_decode($vosl_map_custom_center_coordinates, true);
		
		if(is_array($json_array) and !empty($json_array))
		{
			$vosl_lattitude = $json_array['lat'];
			$vosl_longitude = $json_array['long'];
		}
		
	}else
	{
		// default to null, browser will find it out
		$vosl_lattitude = '';
		$vosl_longitude = '';
	}
	
}else if($current_location_lookup==1)
{
	// default to null, browser will find it out
	$vosl_lattitude = '';
	$vosl_longitude = '';
}

$bg_highlight_color = vosl_data('sl_highlight_color');
$bg_highlight_text_color = vosl_data('sl_highlight_text_color');
$bg_listing_color = vosl_data('sl_listing_bg_color');

if($bg_listing_color=='')
	$bg_listing_color = '#FFFFFF';
	
if($bg_highlight_text_color=='')
	$bg_highlight_text_color = '#000000';
	
if($bg_highlight_color=='')
	$bg_highlight_color = '#3DA1D9';		

$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		
if($lang=='')
	$lang = 'en';

$api_key = (!empty($vosl_map_api_key))? "&key=".$vosl_map_api_key : "" ;	

$map_region = vosl_data('vosl_map_region');
$region=(!empty($map_region))? "&region=".$map_region : "" ;
$google_map_domain = vosl_data('vosl_google_map_domain');
$map_domain=(!empty($google_map_domain))? $google_map_domain : "maps.google.com" ;
	
?>
<?php if($vosl_custom_font!=''){ 
$vosl_font = explode("::",$vosl_custom_font);
?>
<?php if($vosl_font[1]!=''){ 
$google_font = str_replace(" ","+",$vosl_font[1]);
 ?>
<link href='<?php echo $google_font; ?>' rel='stylesheet' type='text/css'>
<?php } ?>
<style type="text/css">
#maplist .location-description, #maplist, #zipcodelookup #btnFind, #zipcodelookup #place_address, #zipcodelookup h2, .voslsingletag select{ font-family:'<?php echo $vosl_font[0]; ?>'; }
</style>
<?php } ?>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link href="<?php echo $vosl_base?>/css/custom.css" rel="stylesheet">

<script src="<?php echo $vosl_base?>/js/gmap3.min.js"></script>
<script type="text/javascript" src="<?php echo $vosl_base?>/js/markerclusterer.js"></script>
<?php /*?><script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=places&language=<?php echo $lang?><?php echo $region?><?php echo $api_key?>"></script><?php */?>

<div id="zipcodelookup" class="voslrow">
<?php if(trim($find_location_text)!=''){ ?>
<h2><?php echo $find_location_text?>:</h2>
<?php } ?>
<form id="retailFinder" name="retailFinder" method="post">
<input type="hidden" value="<?php echo $location_value?>" id="sl_location_map" />
<input type="hidden" value="<?php echo $enable_default_cluster?>" id="vosl_enable_cluster" />
<input type="hidden" value="<?php echo $vosl_custom_map_marker_color?>" id="vosl_custom_map_marker_color" />
<input type="hidden" value="<?php echo $vosl_offset; ?>" id="vosl_hidd_map_popup_offset" />
<div class="voslrow">
<div class="col-lg-4 voslpsearch">
<div class="vosl-input-group voslpsearch">
<?php if($search_box_placeholder_text!=''){ $placeholder_text = $search_box_placeholder_text;  }else{ $placeholder_text = __("Enter address or zipcode", VOSL_TEXT_DOMAIN); } ?>
<input type="text" class="vosl-form-control" name="place_address" id="place_address" placeholder="<?php echo $placeholder_text; ?>">
<span class="vosl-input-group-btn">
<button class="btn btn-default" type="button" id="btnFind"><?php echo __("Go!", VOSL_TEXT_DOMAIN); ?></button>
</span>
</div>
</div>

<?php if($enable_default_tags){ 

$result_tags = $vosl_locator_admin->get_store_tags();
/*$sql = "SELECT * FROM ".VOSL_TAGS_TABLE." order by tag_name ASC ";
$result_tags = $wpdb->get_results($sql,ARRAY_A);*/

?>

<div class="col-lg-2 voslsingletag">
<div class="vosl-input-group">
<select name="vosl_single_tag" id="vosl_single_tag">
	<option value="">--<?php echo __($vosl_tags_filter_label, VOSL_TEXT_DOMAIN); ?>--</option>
    <?php foreach($result_tags as $vosl_tag){ ?>
    <option value="<?php echo $vosl_tag['id']?>" <?php if( (int)$vosl_default_tags_filter == $vosl_tag['id'] ){ ?> selected <?php } ?> ><?php echo $vosl_tag['tag_name']?></option>
    <?php } ?>
</select>
</div>
</div>
<?php } ?>

<?php do_action('vosl_show_radius_range_filter', 0); ?>

<div class="col-lg-8">
<img id="loadingIcon" src="<?php echo $vosl_base?>/images/loading.gif" />
</div>
</div>
</form>
<div style="clear:both"></div>
</div>

<script type="text/javascript">
var markerCluster;
var autocomplete;
var timedOut=false;

<?php if($vosl_lattitude!='' and $vosl_longitude!=''){ ?>
var vosl_map_object = {"lat":"<?=$vosl_lattitude?>","long":"<?=$vosl_longitude?>"};
<?php } ?>
		
jQuery(document).ready(function() {
	
	<?php /*?>if (typeof google === 'object' && typeof google.maps === 'object')
	{
		vosl_HandleApiReady();
		
	}else
	{
		var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "https://maps.google.com/maps/api/js?libraries=places&callback=vosl_HandleApiReady&language=<?php echo $lang?><?php echo $region?><?php echo $api_key?>";
        document.body.appendChild(script);
	}<?php */?>
	
	var script = document.createElement("script");
        script.type = "text/javascript";
        script.src = "https://maps.google.com/maps/api/js?libraries=places&callback=vosl_HandleApiReady&language=<?php echo $lang?><?php echo $region?><?php echo $api_key?>";
        document.body.appendChild(script);
});

function vosl_HandleApiReady()
{
	if(jQuery("#sl_location_map").val()==1)
	{
		google.maps.Map.prototype.panToWithOffset=function(e,a,d){var c=this;var b=new google.maps.OverlayView();b.onAdd=function(){var f=this.getProjection();var g=f.fromLatLngToContainerPixel(e);g.x=g.x+a;g.y=g.y+d;c.panTo(f.fromContainerPixelToLatLng(g))};b.draw=function(){};b.setMap(this)};
	
		var c;et_active_marker=null;
		initializeLocationMap('<?php echo $vosl_lattitude?>','<?php echo $vosl_longitude?>',<?=$vosl_map_zoom_level?>);
		var j=jQuery("#maplist").width();var h=jQuery("#maplist .col-lg-3").width();var f=j-h-50;jQuery("#map_placeholder").css("width",f+"px");
	}
	
	<?php if($current_location_lookup==1){ ?>
		
		timedOut=false;
		timerId=setTimeout(function(){timedOut=true;jQuery("#btnFind").click();jQuery("#loadingIcon").hide();},10000);
		
		jQuery("#loadingIcon").show();
		if(navigator.geolocation){
		bvoslrowserSupportFlag=true;
		navigator.geolocation.getCurrentPosition(function(position){ voslBrowserGeolocationSuccess(position, jQuery); },function(error){ voslBrowserGeolocationFail(error, jQuery); });}
		else{ bvoslrowserSupportFlag=false;handleNoGeolocation(bvoslrowserSupportFlag);}
		
	<?php } ?>
	
	autocomplete=new google.maps.places.Autocomplete((document.getElementById('place_address')),{types:['geocode']});
}
</script>
<div class="voslrow" id="maplist">
<div class="col-lg-3<?php if($location_value==1){ ?> overflowscroll<?php } ?>">
<?php if($current_location_lookup==0){ ?>
<?php $cc = 0; foreach($result_voslrow as $voslrow){ 
	
	$address = '';
	$address .= (!empty($voslrow['address']))?$voslrow['address']:'';
	$address .= (!empty($voslrow['address2']))?", ".$voslrow['address2']:'';
	$address .= (!empty($voslrow['city']))?", ".$voslrow['city']:'';
	$address .= (!empty($voslrow['state']))?", ".$voslrow['state']:'';
	$address .= (!empty($voslrow['zip']))?" ".$voslrow['zip']:'';
	
	$add = str_replace("","%20", $address);
	//$addr_link_src = "http://maps.google.com/maps?saddr=&daddr=".$add;
	$addr_link_src = "http://".$map_domain."/maps?daddr=".$add;	
	
	// check for the url if it has http or not
	if(strpos($voslrow['url'],'http://')===false and strpos($voslrow['url'],'https://')===false and $voslrow['url']!='')
		$voslrow['url'] = 'http://'.$voslrow['url'];
		
	$voslrow['url_text'] = str_replace(array("http://",'https://'),array('',''),$voslrow['url']);	
	
?>
<div class="voslrow locationlist" id="location_<?php echo $voslrow['id']?>" data-offset="<?php echo $cc?>">
<h4><?php echo $voslrow['store_name']?></h4>
<div class="locationdetails">
<div class="voslrow">
<?php if($voslrow['image']!=''){ ?>
<div class="col-md-2 col-sm-2">
<div class="voslrow imagevoslrow"><div class="img_placeholder"><img src="<?php echo $voslrow['image']?>" class="img-responsive" /></div></div>
</div>
<?php } ?>
<div class="col-md-10 col-sm-10"><div class="voslrow mainvoslrow">
<?php if($voslrow['description']!=''){ ?>
<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src="<?php echo $vosl_base?>/images/icons/description.png" /></strong></div><div class="col-md-10 col-sm-10"><?php echo $voslrow['description']?></div></div>
<?php } ?>
<?php if($voslrow['show_address_publicly']==1){ ?>
<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src="<?php echo $vosl_base?>/images/icons/address.png" /></strong></div><div class="col-md-10 col-sm-10"><a href='#' onclick="showDrivingDirections('<?php echo addslashes($addr_link_src)?>');return false"><?php echo addslashes($address)?></a></div></div>
<?php } ?>
<?php if($voslrow['url']!=''){ ?>
<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src="<?php echo $vosl_base?>/images/icons/URL.png" /></strong></div><div class="col-md-10 col-sm-10"><a target="_blank" href="<?php echo $voslrow['url']?>"><?php echo $voslrow['url_text']?></a></div></div>
<?php } ?>
<?php if($voslrow['phone']!=''){ ?>
<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src="<?php echo $vosl_base?>/images/icons/phone.png" /></strong></div><div class="col-md-10 col-sm-10"><?php echo $voslrow['phone']?></div></div>
<?php } ?>
<?php if($voslrow['fax']!=''){ ?>
<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src="<?php echo $vosl_base?>/images/icons/fax.png" /></strong></div><div class="col-md-10 col-sm-10"><?php echo $voslrow['fax']?></div></div>
<?php } ?>
<?php if($voslrow['email']!=''){ ?>
<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src="<?php echo $vosl_base?>/images/icons/email.png" /></strong></div><div class="col-md-10 col-sm-10"><a href="mailto:<?php echo $voslrow['email']?>"><?php echo $voslrow['email']?></a></div></div>
<?php } ?>
<?php if($voslrow['hours']!=''){ ?>
<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src="<?php echo $vosl_base?>/images/icons/hours.png" /></strong></div><div class="col-md-10 col-sm-10"><?php echo $voslrow['hours']?></div></div>
<?php } ?>
</div></div>
</div>
</div>
</div>
<?php $cc = $cc + 1; } ?>
<?php } ?>
<div id="load_more_list"><?php echo __("Load More", VOSL_TEXT_DOMAIN); ?></div>
</div>
<div class="col-lg-9 voslpmapcontainer">
<?php if($location_value==1){ ?>
<script type="text/javascript">/*<![CDATA[*/jQuery(document).ready(function(e){/*var c;et_active_marker=null;*/

//initializeLocationMap('<?php echo $vosl_lattitude?>','<?php echo $vosl_longitude?>');


<?php if($current_location_lookup==0){

 ?><?php foreach($result_voslrow as $voslrow){ 
		if($voslrow['latitude']!='' and $voslrow['longitude']!='')
		{
		
		$address = '';
		$address .= (!empty($voslrow['address']))?$voslrow['address']:'';
		$address .= (!empty($voslrow['address2']))?", ".$voslrow['address2']:'';
		$address .= (!empty($voslrow['city']))?", ".$voslrow['city']:'';
		$address .= (!empty($voslrow['state']))?", ".$voslrow['state']:'';
		$address .= (!empty($voslrow['zip']))?" ".$voslrow['zip']:'';
		$add = str_replace("","%20", $address);
		//$addr_link_src = "http://maps.google.com/maps?saddr=&daddr=".$add;	
		$addr_link_src = "http://".$map_domain."/maps?daddr=".$add;
		
		// check for the url if it has http or not
		if(strpos($voslrow['url'],'http://')===false and strpos($voslrow['url'],'https://')===false and $voslrow['url']!='')
			$voslrow['url'] = 'http://'.$voslrow['url'];
			
		$voslrow['url_text'] = str_replace(array("http://",'https://'),array('',''),$voslrow['url']);	
	
		//$callout = "<h4 style=\'width:90%; float:left;\'>".addslashes($voslrow['store_name'])."</h4><h4><a href=\'#\' style=\'float:right;\' onclick=\'closeMarker(".$voslrow['id']."); return false;\'>X</a></h4>";
		// changed by manoj on 11th Nov 2016 due to map popup size customizations
		$callout = "<h4 style=\' float:left;word-wrap: break-word;\'>".addslashes($voslrow['store_name'])." <a href=\'#\' style=\'float:right;\' onclick=\'closeMarker(".$voslrow['id']."); return false;\'>X</a></h4>";
		
		if($voslrow['show_address_publicly']==1)
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/address.png\' /> <a href=\'#\' onclick=\"showDrivingDirections(\'".addslashes($addr_link_src)."\'); return false;\">".addslashes($address)."</a></p>";
		
		if($voslrow['url']!='')
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/URL.png\' /> <a target=\'_blank\' href=\'".$voslrow['url']."\'>".$voslrow['url_text']."</a></p>";
		
		if($voslrow['phone']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/phone.png\' /> ".$voslrow['phone']."</p>";
		
		if($voslrow['fax']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/fax.png\' /> ".$voslrow['fax']."</p>";
		
		if($voslrow['email']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/email.png\' /> <a href=\'mailto:".$voslrow['email']."\'>".$voslrow['email']."</a></p>";
		
		if($voslrow['hours']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/hours.png\' /> ".$voslrow['hours']."</p>";
			
		if($voslrow['description']!='')	
			$callout .= "<p><img src=\'".$vosl_base."/images/icons/description.png\' /> ".$voslrow['description']."</p>";	
			
		if($voslrow['image']!='')
			$image = $voslrow['image'];
		else
			$image = $vosl_base."/images/locationimg.jpg";
		
		$htm = '<div class="voslrow"><div class="col-lg-5"><div class="img_placeholder"><img src="'.$image.'" class="img-responsive" /></div></div><div class="col-lg-7">'.$callout.'</div></div>';	
		
		
		?><?php /*?>et_add_marker(<?php printf( '"%1$s", %2$s, %3$s, \'<div id="et_marker_%1$s" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">%4$s</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div> <!-- .et_marker_info -->\'',
				$voslrow['id'],
				$voslrow['latitude'],
				$voslrow['longitude'],
				$htm
			); ?>);<?php */?><?php } } ?><?php } ?>/*var j=jQuery("#maplist").width();var h=jQuery("#maplist .col-lg-3").width();var f=j-h-50;jQuery("#map_placeholder").css("width",f+"px")*/});/*]]>*/</script>
<div id="map_placeholder"></div>
<?php }else{ ?>
<style type="text/css">#maplist .col-lg-3{width:100%}</style>
<div id="details_placeholder">
</div>
<?php } ?>
<?php if($current_location_lookup==1){ ?>
<script type="text/javascript">/*<![CDATA[*/
function codeLatLng(lat,long){

geocoder=new google.maps.Geocoder();
var lat=parseFloat(lat);
var lng=parseFloat(long);
var latlng=new google.maps.LatLng(lat,lng);
geocoder.geocode({'latLng':latlng},function(results,status){

	if(status==google.maps.GeocoderStatus.OK){
		if(results[1]){
			jQuery("#place_address").val(results[1].formatted_address);
		}else{alert('No results found');}
	}else{
		alert('Geocoder failed due to: '+status);
	}
});

}

function voslApiGeolocationSuccess(position, $) {
	voslBrowserGeolocationSuccess(position, $);
	//alert("API geolocation success!\n\nlat = " + position.coords.latitude + "\nlng = " + position.coords.longitude);
};

function voslTryAPIGeolocation($) {
	jQuery("#loadingIcon").show();
	
	$.post( "https://www.googleapis.com/geolocation/v1/geolocate?key=<?=$vosl_map_api_key?>", function(success) {
		jQuery("#loadingIcon").hide();
		timedOut=false;
		voslApiGeolocationSuccess({coords: {latitude: success.location.lat, longitude: success.location.lng}}, $);
  })
  .fail(function(err) {
	jQuery("#loadingIcon").hide();   
	console.log(err);
    alert("API Geolocation error! \n\n");
  });
}

function voslBrowserGeolocationFail(error, $) {
	
  clearTimeout(timerId);
  $("#loadingIcon").hide();
	  
  switch (error.code) {
    case error.TIMEOUT:
      alert("Browser geolocation error !\n\nTimeout.");
	  
      break;
    case error.PERMISSION_DENIED:
      if(error.message.indexOf("Only secure origins are allowed") == 0) {
        voslTryAPIGeolocation($);
      }else
	  {
		  // show default listing
		  $("#btnFind").click();
	  }
      break;
    case error.POSITION_UNAVAILABLE:
      //alert("Browser geolocation error !\n\nPosition unavailable.");
	  voslTryAPIGeolocation($);
      break;
  }
};

function voslBrowserGeolocationSuccess(position, $) {
	
	if(timedOut==false){clearTimeout(timerId);var data={action:'find_locations',address:"",lat:position.coords.latitude,long:position.coords.longitude};

	console.log(position);
	codeLatLng(position.coords.latitude,position.coords.longitude);
	
	$.post(the_ajax_script.ajaxurl,data,function(response){if(response!='')
	{if(response!='NO')
	{
	
	var defaultLat = '';
	var defaultLong = '';
	
	var listItems=$("#maplist .col-lg-3 .locationlist");listItems.each(function(idx,li){var location_id=jQuery(this).attr('id');location_id=location_id.split("_");location_id=location_id[1];
	
	<?php if($location_value==1){ ?>
	jQuery("#map_placeholder").gmap3({clear:{id:"et_marker_"+location_id}});
	jQuery("#et_marker_"+location_id).parent().remove();
	<?php } ?>
	
	});
	jQuery("#maplist .col-lg-3 .locationlist").remove();
	jQuery("#load_more_list").before(response);
	jQuery("#load_more_list").show();listItems=$("#maplist .col-lg-3 .locationlist");listItems.each(function(idx,li){var location_id=jQuery(this).attr('id');location_id=location_id.split("_");location_id=location_id[1];
	
	defaultLat = jQuery("#maplist #location_"+location_id+" .default_center_lat").html();
	defaultLong = jQuery("#maplist #location_"+location_id+" .default_center_long").html();
	
	<?php if($location_value==1){ ?>
	et_add_marker(location_id,parseFloat(jQuery("#maplist #location_"+location_id+" .lat").html()),parseFloat(jQuery("#maplist #location_"+location_id+" .long").html()),'<div id="et_marker_'+location_id+'" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">'+jQuery("#maplist #location_"+location_id+" .callout").html()+'</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div>');
	<?php } ?>
	
	});
	
	if(typeof(vosl_map_object) == 'undefined')
	{
		try {
			$('#map_placeholder').gmap3('get').setCenter(new google.maps.LatLng(parseFloat(defaultLat),parseFloat(defaultLong)));
		}
		catch(err) {
			console.log(err.message);
		}
	}	
	
	// check to see if all listing is shown, if yes, then hide the load more link
	if($("#maplist .col-lg-3 .locationlist").length == $( "#maplist .col-lg-3 .locationlist" ).first().attr('data-count'))
		jQuery( "#load_more_list" ).hide();
	
	}else
	{jQuery("#load_more_list").hide();jQuery("#maplist .col-lg-3 .locationlist").remove();}}else
	{jQuery("#load_more_list").hide();}
	$("#loadingIcon").hide();});return false;$("#loadingIcon").hide();}
};

function handleNoGeolocation(error){
	
	clearTimeout(timerId);
	$("#loadingIcon").hide();
	$("#btnFind").click();
}
</script>
<?php }else{ ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	setTimeout(function(){$("#btnFind").click();},1000);
});
</script>
<?php } ?>
<style type="text/css">/*<![CDATA[*/
#maplist .col-lg-3 .voslrow.locationlist, #maplist .col-lg-3{background:<?php echo $bg_listing_color?>!important}
#maplist .col-lg-3,#maplist .col-lg-3 a{color:<?php echo $bg_highlight_text_color?>!important}#maplist .col-lg-3 .voslrow:hover{background:<?php echo $bg_highlight_color?>!important}#maplist .col-lg-3 .locationlist.active{background:<?php echo $bg_highlight_color?>!important}#maplist .col-lg-3 .locationlist strong{color:<?php echo $bg_highlight_text_color?>!important}/*]]>*/
.voslpmapcontainer #map_placeholder .location-description{ width:<?php echo $vosl_custom_map_popup_width; ?>px; }
.voslpmapcontainer #map_placeholder .location-description .col-lg-5{ width:125px; }
.voslpmapcontainer #map_placeholder .location-description .col-lg-7{ width:<?php echo $vosl_popup_rightside_width; ?>px; word-break:break-all; }
.voslpmapcontainer #map_placeholder .location-description { background:<?php echo $vosl_custom_map_popup_bgcolor; ?>; }
.location-rating:before{ border-top-color:<?php echo $vosl_custom_map_popup_bgcolor; ?>; }
.location-description .voslrow, .location-description .voslrow a{ color:<?php echo $vosl_custom_map_popup_textcolor; ?>; }
<?php if($vosl_listings_count==count($result_voslrow)){ ?>
#maplist.voslrow #load_more_list{ display:none; }
.voslpmapcontainer .location-description .listing-info .col-lg-7 p{ clear: left;  }
<?php } ?>
</style>
</div>
</div>

<?php do_action('vosl_add_bLinks'); ?>
<script type="text/javascript">/*<![CDATA[*//*var autocomplete;*/jQuery(document).ready(function(){/*autocomplete=new google.maps.places.Autocomplete((document.getElementById('place_address')),{types:['geocode']});*/});function fillInAddress(){var place=autocomplete.getPlace();for(var component in componentForm){document.getElementById(component).value='';document.getElementById(component).disabled=false;}
for(var i=0;i<place.address_components.length;i++){var addressType=place.address_components[i].types[0];if(componentForm[addressType]){var val=place.address_components[i][componentForm[addressType]];document.getElementById(addressType).value=val;}}}/*]]>*/
</script>
<style type="text/css">
	@media (min-width: 1025px) {
#maplist .overflowscroll{ height: <?=$vosl_listing_column_height?>px !important;width: <?=$vosl_listing_column_width?>px !important; max-height: <?=$vosl_listing_column_height?>px !important; }
.voslpmapcontainer{ height: <?=$vosl_map_size_height?>px !important;width: <?=$vosl_map_size_width?>px !important; }
#map_placeholder{height: <?=$vosl_map_size_height?>px !important;width: <?=$vosl_map_size_width?>px !important;}
.voslpsearch{ width: <?=$vosl_search_box_width?>px !important; }

}
<?php echo $vosl_custom_style;?>

</style>



<?php
//do_action('vosl_track_visitor');
//require_once($vosl_admin_classes_dir."/vosl-locator-admin-static.php");
//VoStoreLocator_AdminStatic::reInitializeVoslMap();
$form = ob_get_clean();
?>
