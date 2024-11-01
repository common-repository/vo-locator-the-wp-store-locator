<?php
global $wpdb, $vosl_base;
		
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);

if($lang=='')
	$lang = 'en';

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
	
if($vosl_search_box_height=='')
	$vosl_search_box_height = 36;	
	
if($vosl_map_zoom_level=='')
	$vosl_map_zoom_level = 4;	
	
$vosl_lattitude = '';
$vosl_longitude = '';

if($vosl_map_custom_center_coordinates!='')
{
	$json = vosl_data('vosl_map_custom_center_coordinates');
	$json_array = json_decode($json, true);
	
	if(is_array($json_array) and !empty($json_array))
	{
		$vosl_lattitude = $json_array['lat'];
		$vosl_longitude = $json_array['long'];
	}
	$custom_center_point_found = 1;
	
}else
{
	// default to null, browser will find it out
	$vosl_lattitude = '';
	$vosl_longitude = '';
	
	$custom_center_point_found = 0;
}

?>
<style type="text/css">
.vosl-input-group .vosl-form-control-cstm {
  position: relative;
  z-index: 2;
  float: left;
  width: 100%;
  margin-bottom: 0;
}
.vosl-input-group .vosl-form-control-cstm{display:table-cell !important;}
.vosl-input-group .vosl-form-control-cstm {
  position: relative;
  z-index: 2;
  float: left;
  width: 100%;
  margin-bottom: 0 !important;
}
.voslpsearch .ui-resizable-e{ right:10px; }
#vosl_search_box_admin_settings{ width:<?=$vosl_search_box_width?>px; height:<?=$vosl_search_box_height?>px; background:#FFFFFF; border:1px solid #999999; float:left; padding-right:0px !important; }
#vosl_listing_column_admin_settings{ width:<?=$vosl_listing_column_width?>px; height:<?=$vosl_listing_column_height?>px; background:#FFFFFF; border:1px solid #999999; float:left; }

.vosl_map_cust_columns{ width:49%; display:inline-block; vertical-align:top; }

<?php if(!$enable_default_tags){ ?>
.vosl_tag_options{ display:none; }
<?php } ?>

<?php if(!$show_radius_filter){ ?>
.vosl_radius_max_option{ display:none; }
<?php } ?>

<?php if($location_value==0){ ?>
.vosl_map_dependents, #map_placeholder_admin, .vosl_map_cust_columns.second{ display:none; }
#vosl_listing_column_admin_settings{ width:97%; }
<?php } ?>

<?php if($location_current==1){ ?>
.vosl_custom_center_point_container{ display:none; }
<?php } ?>
.vosl_custom_center_point_container{ font-size:12px; }
#map_placeholder_admin{ width:<?=$vosl_map_size_width?>px; height:<?=$vosl_map_size_height?>px; margin-left:15px; float:left; }

#maplist .col-lg-3 .voslrow.locationlist{background:<?php echo $vosl_listing_bg_color?>}
#maplist .col-lg-3,#maplist .col-lg-3 a{color:<?php echo $vosl_highlight_text_color?>}#maplist .col-lg-3 .voslrow:hover{background:<?php echo $vosl_highlight_color?>}#maplist .col-lg-3 .locationlist.active{background:<?php echo $vosl_highlight_color?>}#maplist .col-lg-3 .locationlist strong{color:<?php echo $vosl_highlight_text_color?>}
#map_placeholder_admin .location-description{ width:<?php echo $vosl_custom_map_popup_width; ?>px; }
#map_placeholder_admin .location-description .col-lg-5{ width:180px; }
#map_placeholder_admin .location-description .col-lg-7{ width:<?php echo $vosl_popup_rightside_width; ?>px; }
#map_placeholder_admin .location-description { background:<?php echo $vosl_custom_map_popup_bgcolor; ?>; }
.location-rating:before{ border-top-color:<?php echo $vosl_custom_map_popup_bgcolor; ?>; }
.location-description .voslrow, .location-description .voslrow a{ color:<?php echo $vosl_custom_map_popup_textcolor; ?>; }
#map_placeholder_admin .location-description .col-lg-7 h4 {
    width: 100%;
}

.form-table{ margin-top:0px; }
.form-table th{ padding:15px 10px 15px 0; }
.form-table td{ padding:5px 10px; }
.vosl-menu-documentation, .vosl-menu-map-customizations, .vosl-menu-search-customizations, .vosl-menu-listing-customizations{ display:none; }
.form-table th{ width:210px; }
<?php echo $vosl_custom_style; ?>
</style>

<script src="<?=$vosl_base?>/js/gmap3.min.js"></script>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?libraries=places&language=<?php echo $lang; ?><?php echo $region; ?><?php echo $api_key; ?>"></script>
<script type="text/javascript" src="<?php echo $vosl_base?>/js/markerclusterer.js"></script>
<div class="vosl-menu-map-customizations vosl-settings-menu-group">
<?php /*?><div style="float:left; width:100%; padding:10px 5px; font-style:italic;"><b><?php echo __("Note:", VOSL_TEXT_DOMAIN); ?></b> <?php echo __("You can use the controls on the map to change zoom, center, resize and then save to make effect. Additionally you can now change the size of the listings box and search box. To resize the boxes, you can drag the boxes using the right bottom corner or right corner.", VOSL_TEXT_DOMAIN); ?></div><?php */?>
<input type="hidden" name="vosl_map_custom_center_lat" id="vosl_map_custom_center_lat" <?php if($vosl_map_custom_center_coordinates!=''){ ?> value="<?=$vosl_lattitude?>" <?php } ?> />
<input type="hidden" name="vosl_map_custom_center_long" id="vosl_map_custom_center_long" <?php if($vosl_map_custom_center_coordinates!=''){ ?> value="<?=$vosl_longitude?>" <?php } ?> />
<input type="hidden" name="vosl_map_zoom_level" id="vosl_map_zoom_level" value="<?=$vosl_map_zoom_level?>" />
<input type="hidden" name="vosl_listing_column_width" id="vosl_listing_column_width" value="<?=$vosl_listing_column_width?>" />
<input type="hidden" name="vosl_listing_column_height" id="vosl_listing_column_height" value="<?=$vosl_listing_column_height?>" />
<input type="hidden" name="vosl_search_box_width" id="vosl_search_box_width" value="<?=$vosl_search_box_width?>" />
<input type="hidden" name="vosl_search_box_height" id="vosl_search_box_height" value="<?=$vosl_search_box_height?>" />
<input type="hidden" name="vosl_custom_center_point_found" id="vosl_custom_center_point_found" value="<?=$vosl_custom_center_point_found?>" />

 <!--<div style="float:left; width:100%; margin-bottom:15px;">
	<div id="vosl_search_box_admin_settings"></div>
 </div>
 
<div style="float:left; width:100%;">
	<div id="vosl_listing_column_admin_settings"></div>
	<div id="map_placeholder_admin" class="voslmapsettingsholder"></div>
</div>-->

<?php /*?><div class="metabox-holder" style="float:left; width:100%; padding-top:0px; margin-top:20px;">
<div id="vosl_map_customizations_section" class="postbox">
<h2 class="hndle ui-sortable-handle"><span><?php echo __("Map Customizations", VOSL_TEXT_DOMAIN); ?></span></h2>
<div class="inside">
	<div class="main">
	
        
        
	</div>
		
	</div>
</div>
</div><?php */?>

<div class="vosl_map_cust_columns">

<table class="form-table">
<tbody>

<tr valign="top">
<th scope="row"><?php echo __("Show Map", VOSL_TEXT_DOMAIN); ?></th>
<td> <fieldset><legend class="screen-reader-text"><span><?php echo __("Show Location with Map", VOSL_TEXT_DOMAIN); ?></span></legend><label for="users_can_register">
<input type="checkbox" value="1" id="location_map" name="location_map" <?php if($location_value==1){ ?> checked="checked" <?php } ?>>
<?php echo __("Enable showing Map with listing", VOSL_TEXT_DOMAIN); ?></label>
</fieldset></td>
</tr>

<tr valign="top" class="vosl_map_dependents">
<th scope="row"><?php echo __("Map Zoom Level", VOSL_TEXT_DOMAIN); ?></th>
<td><div class="vosl_map_stats"><div class="vosl_zoom_stats"><span><?=$vosl_map_zoom_level?></span></div></div></td>
</tr>

<tr valign="top" class="vosl_map_dependents">
<th scope="row"><?php echo __("Map Width/Height", VOSL_TEXT_DOMAIN); ?></th>
<td><div class="vosl_map_stats"><div class="vosl_size_stats"><span><?=$vosl_map_size_width?>x<?=$vosl_map_size_height?> <?php echo __("(Pixels)", VOSL_TEXT_DOMAIN); ?></span></div></div></td>
</tr>


 
<tr valign="top" class="vosl_map_dependents">
<th scope="row"><?php echo __("Map Center Point", VOSL_TEXT_DOMAIN); ?></th>
<td >
<fieldset><legend class="screen-reader-text"><span>Date Format</span></legend>
<label><input type="radio" name="vosl_map_center_point_type" value="1" <?php if($location_current==1){ ?> checked="checked" <?php } ?>> <span class="date-time-text format-i18n"><?php echo __("Browser's Current Location Lookup", VOSL_TEXT_DOMAIN); ?></span></label><br>
<label><input type="radio" name="vosl_map_center_point_type" value="0" <?php if($location_current==0){ ?> checked="checked" <?php } ?>> <span class="date-time-text format-i18n"><?php echo __("Fixed Center Point", VOSL_TEXT_DOMAIN); ?></span></label><br>

<div class="vosl_custom_center_point_container">
	<input type="text" value="<?=$vosl_map_custom_center?>" name="vosl_map_custom_center" id="vosl_map_custom_center" style="width:250px;" />
</div>

</fieldset>
</td>
</tr>

<tr valign="top" class="vosl_map_dependents">
<th scope="row"><?php echo __("Region", VOSL_TEXT_DOMAIN); ?></th>
<td>
<select name='map_region' id="map_region">
<?php 
foreach ($tld as $key=>$value) {
	$selected=($map_region==$value)?" selected " : "";
	$your_location_select.="<option value='$key:{$the_domain[$key]}:$value' $selected>$key</option>\n";
}
echo $your_location_select;
?></select>
</fieldset></td>
</tr>


</tbody>
</table>

</div>

<div class="vosl_map_cust_columns second">
		
        <table class="form-table">
		<tbody>
        
        <tr valign="top">
            <th scope="row"><?php echo __("Enable Marker Clusterer", VOSL_TEXT_DOMAIN); ?></th>
            <td> <fieldset><legend class="screen-reader-text"><span></span></legend><label for="users_can_register">
            <input type="checkbox" value="1" id="enable_default_cluster" name="enable_default_cluster" <?php if($enable_default_cluster==1){ ?> checked="checked" <?php } ?>>
            <?php echo __("Enable marker clusterer on the map.", VOSL_TEXT_DOMAIN); ?></label>
            </fieldset></td>
        </tr>
        
        <tr valign="top">
    <th scope="row"><?php echo __("Default Map Marker Color", VOSL_TEXT_DOMAIN); ?></th>
    <td>
    <input type="text" value="<?php echo esc_html($vosl_custom_map_marker_color);?>" id="vosl_custom_map_marker_color_settings" name="vosl_custom_map_marker_color" />
    </fieldset></td>
</tr>

<tr valign="top">
    <th scope="row"><?php echo __("Marker InfoWindow Width", VOSL_TEXT_DOMAIN); ?></th>
    <td><input type="text" style="width:50px;" value="<?php echo esc_html($vosl_custom_map_popup_width);?>" id="vosl_custom_map_popup_width" name="vosl_custom_map_popup_width" />&nbsp;<?php echo __("Pixels", VOSL_TEXT_DOMAIN); ?></td>
</tr>

<tr valign="top">
    <th scope="row"><?php echo __("Marker InfoWindow Background", VOSL_TEXT_DOMAIN); ?></th>
    <td>
    <input type="text" value="<?php echo esc_html($vosl_custom_map_popup_bgcolor);?>" id="vosl_custom_map_popup_bgcolor_settings" name="vosl_custom_map_popup_bgcolor" />
    </fieldset></td>
</tr>

<tr valign="top">
    <th scope="row"><?php echo __("Marker InfoWindow Text Color", VOSL_TEXT_DOMAIN); ?></th>
    <td>
    <input type="text" value="<?php echo esc_html($vosl_custom_map_popup_textcolor);?>" id="vosl_custom_map_popup_text_color_settings" name="vosl_custom_map_popup_textcolor" />
    </fieldset></td>
</tr>



        
        </tbody>
        </table>

</div>

<input type="hidden" name="vosl_map_size_width" id="vosl_map_size_width" value="<?=$vosl_map_size_width?>" />
<input type="hidden" name="vosl_map_size_height" id="vosl_map_size_height" value="<?=$vosl_map_size_height?>" />
</div>

<div class="vosl-menu-search-customizations vosl-settings-menu-group">

<table class="form-table">
			<tbody>
            
            	<tr valign="top">
                <th scope="row"><?php echo __("Search Box Size", VOSL_TEXT_DOMAIN); ?></th>
                    <td>
                    <div id="vosl_search_box_stats" style="display:inline-block;"><?=$vosl_search_box_width?>x<?=$vosl_search_box_height?> px</div></td>
                </tr>
                
            	<tr valign="top">
                <th scope="row"><?php echo __("Search Box Heading Text", VOSL_TEXT_DOMAIN); ?></th>
                    <td>
                    <input type="text" value="<?php echo esc_html($location_text);?>" id="fndLocationText" name="fndLocationText" style="width:250px;" />
                    </td>
                </tr>
                
                <tr valign="top">
                <th scope="row"><?php echo __("Search Box Placeholder Text", VOSL_TEXT_DOMAIN); ?></th>
                    <td>
                    <input type="text" value="<?php echo esc_html($search_box_placeholder_text);?>" id="search_box_placeholder_text" name="search_box_placeholder_text" style="width:250px;" />
                    </td>
                </tr>
                
                <tr valign="top">
<th scope="row"><?php echo __("Show Tags", VOSL_TEXT_DOMAIN); ?></th>
<td> <fieldset><legend class="screen-reader-text"><span><?php echo __("Enable Current Location Lookup on Load", VOSL_TEXT_DOMAIN); ?>?</span></legend><label for="users_can_register">
<input type="checkbox" value="1" id="enable_default_tags" name="enable_default_tags" <?php if($enable_default_tags==1){ ?> checked="checked" <?php } ?>>
<?php echo __("Enable filtering by tags.", VOSL_TEXT_DOMAIN); ?></label>
</fieldset></td>
</tr>

<tr valign="top" class="vosl_tag_options">
<th scope="row"><?php echo __("Tags Filter Label", VOSL_TEXT_DOMAIN); ?></th>
<td>
<input type="text" value="<?php echo esc_html($vosl_tags_filter_label);?>" id="vosl_tags_filter_label" name="vosl_tags_filter_label" style="width:250px;" />
</fieldset></td>
</tr>
<?php $result_tags = $vosl_locator_admin->get_store_tags(); ?>
<tr valign="top" class="vosl_tag_options">
<th scope="row"><?php echo __("Default Tags Filter", VOSL_TEXT_DOMAIN); ?></th>
<td>
<select name="vosl_default_tags_filter" id="vosl_default_tags_filter">
	<option value="">--<?php echo __("Select Tag", VOSL_TEXT_DOMAIN); ?>--</option>
	<?php foreach($result_tags as $vosl_tag){ ?>
    <option value="<?php echo $vosl_tag['id']?>" <?php if( (int)$vosl_default_tags_filter == $vosl_tag['id'] ){ ?> selected <?php } ?> ><?php echo $vosl_tag['tag_name']?></option>
    <?php } ?>
</select>
<?php echo __("This will enable filtering the listing by selected tag on front-end by default.", VOSL_TEXT_DOMAIN); ?>
</fieldset></td>
</tr>

<?php do_action('vosl_search_customization_instance_fields',$instance_details);
	  do_action('vosl_search_customization_radius_option'); ?>

            </tbody>
            </table>

</div>

<div class="vosl-menu-listing-customizations vosl-settings-menu-group">
	<table class="form-table">
			<tbody>
            
            <tr valign="top">
                <th scope="row"><?php echo __("Listing Box Size", VOSL_TEXT_DOMAIN); ?></th>
                <td>
               <div style="display:inline-block;" id="vosl_listing_column_stats"><?=$vosl_listing_column_width?>x<?=$vosl_listing_column_height?> px</div>
                </td>
            </tr>
            
            

<tr valign="top">
<th scope="row"><?php echo __("Listing background Color", VOSL_TEXT_DOMAIN); ?></th>
<td>
<input type="text" value="<?php echo esc_html($vosl_listing_bg_color);?>" id="color-field-text-bg" name="color-field-text-bg" />
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php echo __("Listing Text Color", VOSL_TEXT_DOMAIN); ?></th>
<td>
<input type="text" value="<?php echo esc_html($vosl_highlight_text_color);?>" id="color-field-text" name="color-field-text" />
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php echo __("Listing Highlight Color", VOSL_TEXT_DOMAIN); ?></th>
<td>
<input type="text" value="<?php echo esc_html($vosl_highlight_color);?>" id="color-field" name="color-field" />
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php echo __("Distance in (Mi/Km)", VOSL_TEXT_DOMAIN); ?></th>
<td>
<select name="selMiles" id="selMiles">
	<option value="mi" <?php if($kmmiles=='mi'){ ?> selected="selected" <?php } ?>><?php echo __("Miles", VOSL_TEXT_DOMAIN); ?></option>
	<option value="km" <?php if($kmmiles=='km'){ ?> selected="selected" <?php } ?>><?php echo __("Kilometers", VOSL_TEXT_DOMAIN); ?></option>
</select>
</fieldset></td>
</tr>

<tr valign="top">
<th scope="row"><?php echo __("Listings Loaded Per Request", VOSL_TEXT_DOMAIN); ?></th>
<td>
<input type="number" max="1000" value="<?php echo esc_html($vosl_listing_load_size);?>" id="vosl_listing_load_size" name="vosl_listing_load_size" />
</fieldset></td>
</tr>
            	
            </tbody>
            </table>
</div>