<?php
global $vosl_admin_classes_dir, $vosl_path, $vosl_base;

require_once $vosl_admin_classes_dir . '/vosl-locator-admin.php';
$vosl_locator_admin = new VoStoreLocator_Admin();

if(isset($_POST['btnSubmit']))
{
	$vosl_locator_admin->save_admin_settings($_POST);
}

$location_value = vosl_data('sl_location_map');
$location_current = vosl_data('sl_current_location_lookup');
$enable_default_tags = vosl_data('vosl_enable_default_tags');
$vosl_show_love = vosl_data('vosl_show_love');
$vosl_map_api_key = vosl_data('vosl_map_api_key');
//$vosl_google_map_server_api_key = vosl_data('vosl_google_map_server_api_key');
$kmmiles = vosl_data('vosl_km');
$enable_default_cluster = vosl_data("vosl_enable_default_cluster");
$vosl_custom_font = vosl_data("vosl_custom_font"); 
$vosl_custom_style=vosl_data("vosl_custom_style");

$vosl_map_zoom_level = vosl_data('vosl_map_zoom_level');
$vosl_map_size_width = vosl_data('vosl_map_size_width');
$vosl_map_size_height = vosl_data('vosl_map_size_height');
$vosl_listing_column_width = vosl_data('vosl_listing_column_width');
$vosl_listing_column_height = vosl_data('vosl_listing_column_height');
$vosl_map_custom_center = vosl_data('vosl_map_custom_center');
$vosl_map_custom_center_coordinates = vosl_data('vosl_map_custom_center_coordinates');
$vosl_search_box_width = vosl_data('vosl_search_box_width');
$vosl_search_box_height = vosl_data('vosl_search_box_height');
$vosl_custom_map_popup_width = vosl_data("vosl_custom_map_popup_width");
$vosl_custom_map_popup_bgcolor = vosl_data("vosl_custom_map_popup_bgcolor");
$vosl_custom_map_popup_textcolor = vosl_data("vosl_custom_map_popup_textcolor");
$vosl_custom_map_marker_color = vosl_data("vosl_custom_map_marker_color");
$show_radius_filter = vosl_data("vosl_radius_filter");
$radius_filter_max = vosl_data('vosl_radius_filter_max');
$search_box_placeholder_text = vosl_data('search_box_placeholder_text');
$vosl_radius_distance_label_text = vosl_data('vosl_radius_distance_label_text');
$vosl_radius_label_text = vosl_data('vosl_radius_label_text');
$vosl_default_tags_filter = vosl_data('vosl_default_tags_filter');
$vosl_listing_load_size = vosl_data('vosl_listing_load_size');

$fonts = $vosl_locator_admin->vosl_get_all_fonts();
uasort($fonts,  function($a, $b) { return strcasecmp($a["name"], $b["name"]); });

$map_region = vosl_data('vosl_map_region');
$region=(!empty($map_region))? "&amp;region=".$map_region : "" ;
$api_key = (!empty($vosl_map_api_key))? "&amp;key=".$vosl_map_api_key : "";

$vosl_custom_map_popup_bgcolor = (empty($vosl_custom_map_popup_bgcolor)?"#FFFFFF":$vosl_custom_map_popup_bgcolor);
$vosl_custom_map_popup_width = (empty($vosl_custom_map_popup_width)?420:$vosl_custom_map_popup_width);
$vosl_custom_map_popup_textcolor = (empty($vosl_custom_map_popup_textcolor)?"#000000":$vosl_custom_map_popup_textcolor);
$vosl_popup_rightside_width = $vosl_custom_map_popup_width - 160;

if($enable_default_cluster=='')
	$enable_default_cluster = 1;

if($kmmiles=='')
{
	$kmmiles = 'mi';
}
	
if($vosl_show_love=='')
	$vosl_show_love = 1;	

if($location_value=='')
	$location_value = 1;
	
if(vosl_data('sl_find_location_text')=='')
	$location_text = '';
else
	$location_text = vosl_data('sl_find_location_text');	
	
if(vosl_data('vosl_tags_filter_label')=='')
	$vosl_tags_filter_label = '';
else
	$vosl_tags_filter_label = vosl_data('vosl_tags_filter_label');	
	
if(vosl_data('sl_highlight_color')=='')
	$vosl_highlight_color = '#3DA1D9';
else
	$vosl_highlight_color = vosl_data('sl_highlight_color');
	
if(vosl_data('sl_highlight_text_color')=='')
	$vosl_highlight_text_color = '#000000';
else
	$vosl_highlight_text_color = vosl_data('sl_highlight_text_color');		
	
if(vosl_data('sl_listing_bg_color')=='')
	$vosl_listing_bg_color = '#FFFFFF';
else
	$vosl_listing_bg_color = vosl_data('sl_listing_bg_color');		
	
/*if(vosl_data('sl_listing_bg_color')=='')
	$vosl_listing_bg_color = '#FFFFFF';
else
	$vosl_listing_bg_color = vosl_data('sl_listing_bg_color');*/				

$vosl_offset = 0;

if($vosl_custom_map_popup_width > 420)
{
	$vosl_offset = -1 * abs(($vosl_custom_map_popup_width-420));
	$vosl_offset = round($vosl_offset/2);
	
}else if($vosl_custom_map_popup_width < 420)
{
	$vosl_offset = round(abs((420-$vosl_custom_map_popup_width))/2);
}

include(VOSL_INCLUDES_PATH."/countries-regions.php");
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
<style type="text/css">
.voslsingletag .vosl-input-group{ margin-right:20px; }

</style>

<?php /*?><style type="text/css">
#maplist .col-lg-3 .voslrow.locationlist{background:<?php echo $vosl_listing_bg_color?>}
#maplist .col-lg-3,#maplist .col-lg-3 a{color:<?php echo $vosl_highlight_text_color?>}#maplist .col-lg-3 .voslrow:hover{background:<?php echo $vosl_highlight_color?>}#maplist .col-lg-3 .locationlist.active{background:<?php echo $vosl_highlight_color?>}#maplist .col-lg-3 .locationlist strong{color:<?php echo $vosl_highlight_text_color?>}
#map_placeholder_admin .location-description{ width:<?php echo $vosl_custom_map_popup_width; ?>px; }

.form-table{ margin-top:0px; }
.form-table th{ padding:15px 10px 15px 0; }
.form-table td{ padding:5px 10px; }
.vosl-menu-documentation, .vosl-menu-map-customizations, .vosl-menu-search-customizations, .vosl-menu-listing-customizations{ display:none; }
.form-table th{ width:210px; }
</style><?php */?>
<div class="wrap">
<div class="voslmodal"></div>

<div class="icon32" id="icon-options-general"><br></div><h2><?php echo __("VO Locator Settings", VOSL_TEXT_DOMAIN); ?></h2>
<div style="width:100%;float:left">

<span class="vosl-video-link button vosl-button-green" data-video-id="y-_6Zm_9hONKo" data-video-width="720px" data-video-height="400px" data-video-autoplay="1" ><?php echo __('Getting Started with VO Store Locator',VOSL_TEXT_DOMAIN); ?></span>

<h2 class="nav-tab-wrapper vosladmintab">
    <a href="#general" id="vosl-menu-general" class="nav-tab nav-tab-active"><?php echo __('General',VOSL_TEXT_DOMAIN); ?></a>
    <?php // removed and added functinality in free version
		  //do_action('vosl_tabs'); ?>
    <?php /*?><a href="#ui" id="vosl-menu-ui" class="nav-tab"><?php echo __('User Interface',VOSL_TEXT_DOMAIN); ?></a>   <?php */?>  
    <a href="#map-customizations" id="vosl-menu-map-customizations" class="nav-tab"><?php echo __('Map Customizations',VOSL_TEXT_DOMAIN); ?></a> 
    <a href="#search-customizations" id="vosl-menu-search-customizations" class="nav-tab"><?php echo __('Search Customizations',VOSL_TEXT_DOMAIN); ?></a>    <a href="#listing-customizations" id="vosl-menu-listing-customizations" class="nav-tab"><?php echo __('Listing Customizations',VOSL_TEXT_DOMAIN); ?></a>
    
    <a href="#documentation" id="vosl-menu-documentation" class="nav-tab">
    	<?php echo __('Documentation',VOSL_TEXT_DOMAIN); ?>
    </a>

</h2>
<form method="post" id="vosl_settings">

<?php require ( $vosl_path.'/templates/general_settings.php' ); ?>

<div style="width:100%;float:left;padding-left:10px" class="vosl-menu-documentation vosl-settings-menu-group">
<p>
	<?php echo __("To use this plugin within pages and posts you simply will have to insert this shortcode", VOSL_TEXT_DOMAIN); ?> [VO-LOCATOR] <?php echo __("within your post/page content", VOSL_TEXT_DOMAIN); ?></p>
<p>
	<?php echo __("If you need to use this plugin in php code you will need to call php function as belows:", VOSL_TEXT_DOMAIN); ?></p>
<p>
<strong>
	if(function_exists("volocator_func"))<br/>
	{<br/>
	echo volocator_func();<br/>
	}
</strong>
</p>
<p><?php echo __("Or else you can do as follows:", VOSL_TEXT_DOMAIN); ?></p><strong>
<p>echo do_shortcode( '[VO-LOCATOR]' ); </p></strong>
<p><?php echo __("For more information please visit our website:", VOSL_TEXT_DOMAIN); ?> <a href="http://www.vitalorganizer.com/vo-locator-wordpress-store-locator-plugin/" target="_blank"><?php echo __("Click Here", VOSL_TEXT_DOMAIN); ?></a>&nbsp;|&nbsp;<a href="http://www.vitalorganizer.com/vo-locator-documentation/" target="_blank"><?php echo __("Documentation", VOSL_TEXT_DOMAIN); ?></a>&nbsp;|&nbsp;<a href="http://www.vitalorganizer.com/product/vo-store-locator-pro-add-on?utm_source=plugin&amp;utm_medium=pluginUI&amp;utm_campaign=settings" target="_blank"><?php echo __("Get VO Locator PRO", VOSL_TEXT_DOMAIN); ?></a></p>
<p><a href="https://wordpress.org/support/plugin/vo-locator-the-wp-store-locator"><?php echo __("Ask for Support", VOSL_TEXT_DOMAIN); ?></a>&nbsp;|&nbsp;<a href="https://wordpress.org/support/view/plugin-reviews/vo-locator-the-wp-store-locator"><?php echo __("Review Us", VOSL_TEXT_DOMAIN); ?></a></p>
</div>

<?php 
	  require ( $vosl_path. '/templates/user_interface_settings.php' );
	 // $vosl_locator_admin->vosl_show_tabscontent();
 ?>

<?php /*?><p class="submit"><input type="submit" value="<?php echo __("Save Changes", VOSL_TEXT_DOMAIN); ?>" class="button button-primary" id="submit" name="btnSubmit"></p><?php */?>
<input type="hidden" name="action" value="vosl_update_settings" />
<input type="hidden" value="<?php echo $vosl_offset; ?>" id="vosl_hidd_map_popup_offset" />
</form>
</div>
</div>

<div style="float:left; width:98%; padding:10px 5px; font-style:italic;"><b><?php echo __("Note:", VOSL_TEXT_DOMAIN); ?></b> <?php echo __("You can use the controls on the map to change zoom, center, resize and then save to make effect. Additionally you can now change the size of the listings box and search box. To resize the boxes, you can drag the boxes using the right bottom corner or right corner.", VOSL_TEXT_DOMAIN); ?></div>

<!--<div style="float:left; width:100%; margin-bottom:15px;">
	<div id="vosl_search_box_admin_settings"></div>
 </div>-->
 
 <div id="zipcodelookup" class="voslrow">
	
    <h2><?php echo $location_text?>:</h2>
   
    <form id="retailFinder" name="retailFinder" method="post">
    <div class="voslrow">
    <div class="col-lg-4 voslpsearch">
    <div class="vosl-input-group voslpsearch">
    <?php if($search_box_placeholder_text!=''){ $placeholder_text = $search_box_placeholder_text;  }else{ $placeholder_text = __("Enter address or zipcode", VOSL_TEXT_DOMAIN); } ?>
    <input type="text" class="vosl-form-control-cstm" name="place_address" id="vosl_search_box_admin_settings" placeholder="<?php echo $placeholder_text; ?>">
    
    </div>
    </div>
    
    <?php // if($enable_default_tags){ 
    $result_tags = $vosl_locator_admin->get_store_tags();
   // $sql = "SELECT * FROM ".VOSL_TAGS_TABLE." order by tag_name ASC ";
   // $result_tags = $wpdb->get_results($sql,ARRAY_A);
    
    ?>
    <div class="col-lg-4 voslsingletag vosl_tag_options">
    <div class="vosl-input-group">
    <select name="vosl_single_tag" id="vosl_single_tag">
        <option value="">--<?php echo __($vosl_tags_filter_label, VOSL_TEXT_DOMAIN); ?>--</option>
        <?php foreach($result_tags as $vosl_tag){ ?>
        <option value="<?php echo $vosl_tag['id']?>"><?php echo $vosl_tag['tag_name']?></option>
        <?php } ?>
    </select>
    </div>
    </div>
    <?php //} ?>
    
    
    </div>
    </form>
    <div style="clear:both"></div>
 </div>
 
 
<div style="float:left; width:100%; margin-bottom:15px;" id="maplist">
	<div id="vosl_listing_column_admin_settings" class="col-lg-3">
   	
    
    <div class="voslrow locationlist" id="location_31" data-offset="4" data-count="8">
	<div class="default_center_lat" style="display:none;">19.9953784</div>
	<div class="default_center_long" style="display:none;">73.8016876</div>
	<div class="lat" style="display:none;">40.724554</div>
	<div class="long" style="display:none;">-73.9953845</div>
	<div class="callout" style="display:none;"><div class="voslrow"><div class="col-lg-5"><div class="img_placeholder"><img src="https://vitalorganizer.com/wp-content/uploads/2015/08/index-150x150.jpg" class="img-responsive"></div></div><div class="col-lg-7"><h4 style="float:left;word-wrap: break-word;">Puck Building (PUCK) <a href="#" style="float:right;" onclick="closeMarker(31); return false;">X</a></h4><p><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/icons/address.png"> <a href="#" onclick="showDrivingDirections('http://maps.google.com/maps?daddr=295 Lafayette Street, New York, NY 10012'); return false;">295 Lafayette Street, New York, NY 10012</a></p><p><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/icons/phone.png"> 123-456-7890</p><p><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/icons/fax.png"> 1-617-485-1376</p><p><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/icons/email.png"> <a href="mailto:test@email">test@email</a></p><p><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/icons/description.png"> Robert F. Wagner Graduate School of Public Service</p></div></div></div>
	<h4>Puck Building (PUCK)		<span>7,759.94 <span style="margin:0px;margin-left:5px;"><?=$kmmiles?></span></span>
		</h4>
		
	</div> 
   
		<div class="voslrow locationlist active" id="location_32" data-offset="1" data-count="8">
	
	
	<h4>Boston University		<span>7,675.76 <span style="margin:0px;margin-left:5px;"><?=$kmmiles?></span></span>
		</h4>
        
        <div class="default_center_lat" style="display:none;">19.9953784</div>
	<div class="default_center_long" style="display:none;">73.8016876</div>
	<div class="lat" style="display:none;">42.3508702</div>
	<div class="long" style="display:none;">-71.1037428</div>
	<div class="callout" style="display:none;"><div class="voslrow"><div class="col-lg-5"><div class="img_placeholder"><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/locationimg.jpg" class="img-responsive"></div></div><div class="col-lg-7"><h4 style="float:left;word-wrap: break-word;">Boston University <a href="#" style="float:right;" onclick="closeMarker(32); return false;">X</a></h4><p><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/icons/address.png"> <a href="#" onclick="showDrivingDirections('http://maps.google.com/maps?daddr=233 Bay State Road, Boston, MA 2215'); return false;">233 Bay State Road, Boston, MA 2215</a></p><p><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/icons/phone.png"> +1 617-353-2000</p></div></div></div>
		
	</div>
		<div class="voslrow locationlist" id="location_30" data-offset="2" data-count="8">
	
	
	<h4>State University of New York Library		<span >7,735.03 <span style="margin:0px;margin-left:5px;"><?=$kmmiles?></span></span>
		</h4>
        <div class="default_center_lat" style="display:none;">19.9953784</div>
        <div class="default_center_long" style="display:none;">73.8016876</div>
        <div class="lat" style="display:none;">42.6859115</div>
        <div class="long" style="display:none;">-73.8265279</div>
        <div class="callout" style="display:none;"><div class="voslrow"><div class="col-lg-5"><div class="img_placeholder"><img src="https://vitalorganizer.com/wp-content/uploads/2015/06/Brown_university_robinson_hall_2009a-150x150.jpg" class="img-responsive"></div></div><div class="col-lg-7"><h4 style="float:left;word-wrap: break-word;">State University of New York Library <a href="#" style="float:right;" onclick="closeMarker(30); return false;">X</a></h4><p><img src="https://www.vitalorganizer.com/wp-content/plugins/vo-locator-the-wp-store-locator/images/icons/address.png"> <a href="#" onclick="showDrivingDirections('http://maps.google.com/maps?daddr=1400 Washington Ave, Albany, NY 12222'); return false;">1400 Washington Ave, Albany, NY 12222</a></p></div></div></div>       
	</div>
		
    
    </div>
	<div id="map_placeholder_admin" class="voslmapsettingsholder"></div>
</div>

<p><input style="float:left; margin-top:20px;" type="button" value="<?php echo __("Update Settings", VOSL_TEXT_DOMAIN); ?>" class="button button-primary" id="btnSubmitVoslSettings" name="btnSubmitVoslSettings"></p>

<script type="text/javascript">

jQuery(function() {
	var autocomplete = new google.maps.places.Autocomplete((jQuery("#vosl_map_custom_center")[0]),
	{types: ['geocode']});
	autocomplete.addListener('place_changed', changeMapCenterAdminSettings);
	
	jQuery(".vosl-video-link").jqueryVideoLightning({
		autoplay: 1,
		backdrop_color: "#ddd",
		backdrop_opacity: 0.6,
		glow: 20,
		glow_color: "#000"
	});
	
	if(window.console && console.error){
		var old = console.error;
		console.error = function(){
			if(arguments[0].indexOf('Google Maps API error')!=-1){
				//alert('Bad Google API Key - '+ arguments[0]);
				
				vosl_jc = jQuery.alert({
                            title: '<?php echo __('Google API Error', VOSL_TEXT_DOMAIN); ?>',
							boxWidth: '550px',
							useBootstrap: false,
                            icon: 'fa fa-warning',
                            type: 'red',
                            content: arguments[0]+'<p><?php echo __('You can use the above link to see more details or you can watch our Getting Started with VO Store Locator Video below', VOSL_TEXT_DOMAIN); ?>: </p><p><iframe width="550" height="315" src="https://www.youtube.com/embed/_6Zm_9hONKo" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe></p>',
                        });
				
			}
			Array.prototype.unshift.call(arguments);
	
			old.apply(this, arguments);
		}
	}

});	

voslInitializeSettingsMapAdmin('<?=$vosl_lattitude?>','<?=$vosl_longitude?>',<?=$vosl_map_zoom_level?>);
</script>