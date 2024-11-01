<?php
if (!empty($_GET['pg']) && isset($wpdb) && $_GET['pg']=='add-locations') { /*include_once(SL_INCLUDES_PATH."/top-nav.php"); */}

global $vosl_admin_classes_dir, $vosl_base;
include(VOSL_INCLUDES_PATH."/countries-regions.php");
unset($tld["United States"]);
$tld["United States"] = "us";
ksort($tld);
$vosl_locator_admin = new VoStoreLocator_Admin();
$vosl_custom_fields = $vosl_locator_admin->get_store_custom_fields();
//ini_set("display_errors", "1");
//error_reporting(E_ALL);

if (!defined("VOSL_INCLUDES_PATH")) { include("../vosl-define.php"); }
echo  $view_link;
print "<div class='wrap'>";

global $wpdb, $vosl_base;

$tags_output = apply_filters( 'vosl_populate_tags_dropdown', array(), 0);
$vosl_map_api_key = vosl_data('vosl_map_api_key');

// Inserting addresses by manual input

if (!empty($_POST['store_name']) && (empty($_GET['mode']) || $_GET['mode']!="pca")) {
	if (!empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], "add-location_single"))
	{
		$btnVoslAddListingClose = $_POST['btnVoslAddListingClose'];

		vosl_add_location();
		if (isset($btnVoslAddListingClose)) {
			print "<script>location.href = 'admin.php?page=".VOSL_PAGES_DIR.'/locations.php'."';</script>";
		}
		
		print "<div class='sl_admin_success'>".__("Successful Addition",VOSL_TEXT_DOMAIN).". <a href='admin.php?page=".VOSL_PAGES_DIR."/locations.php"."'>".__("Manage Locations", $text_domain)."</a> <script>setTimeout(function(){jQuery('.sl_admin_success').fadeOut('slow');}, 6000);</script></div> <!--meta http-equiv='refresh' content='0'-->"; 
	} else {
		print "<div class='sl_admin_warning'>".__("Unsucessful addition due to security check failure",VOSL_TEXT_DOMAIN).". $view_link</div>"; 
	}
}else
 {
   $btnVoslAddListingAndClose = $_POST['btnVoslAddListingClose'];
   $btnVoslAddlisting = $_POST['btnVoslAddlisting'];
   if(isset($btnVoslAddListingAndClose) || isset($btnVoslAddlisting)){
		echo "<div style='color: red;font-size: 15px;font-weight: 400; margin-bottom: 5px;'>".__("Please enter name for the listing",VOSL_TEXT_DOMAIN)."</div>";
	}
}

?>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?=$vosl_map_api_key?>"></script>
<script src="<?php echo $vosl_base?>/js/gmap3.min.js"></script>
<style type="text/css">
.vosl-tags-table{ display:inline-block; margin-left:20px; vertical-align:top; }
</style>
<div class="vosl_default_hidden_img"><?=$vosl_base."/images/locationimg.jpg";?></div>


<form name='manualAddForm' method='post' id='locationForm'>
	
	<table cellpadding='' cellspacing='0' style='width:100%' class='manual_add_table widefat'>
	<thead><tr><th><?=__("Add Listing", VOSL_TEXT_DOMAIN)?></th></tr></thead>
	<tr>
		<td style="vertical-align:top !important; width:40%" class="vosl-add-edit-list left">
		
		<span id='format' style='display:none'><i><?=__("Name of Listing", VOSL_TEXT_DOMAIN)?><br>
		<?=__("Address (Street - Line1)", VOSL_TEXT_DOMAIN)?><br>
		<?=__("Address (Street - Line2 - optional)", VOSL_TEXT_DOMAIN)?><br>
		<?=__("City, State Zip", VOSL_TEXT_DOMAIN)?></i></span>
		<?=__("Name of Listing", VOSL_TEXT_DOMAIN)?><br><input name='store_name' size=50 type='text'><br><br>
        <b><?php echo __("Find Address", VOSL_TEXT_DOMAIN); ?></b><br><input name='find_address' id='find_address' value='' size='50' type='text' autocomplete="off">
        <br /><br />
		<?=__("Address", VOSL_TEXT_DOMAIN)?><br><input name='address' class="address" size=35 type='text'>&nbsp;<small>(<?=__("Street - Line1", VOSL_TEXT_DOMAIN)?>)</small><br>
		<input name='address2' class="address2" size=35 type='text'>&nbsp;<small>(<?=__("Street - Line 2 - optional", VOSL_TEXT_DOMAIN)?>)</small><br>
		<table cellpadding='0px' cellspacing='0px'><tr>
        <td style='padding-left:0px; width:100px;' class='nobottom'><input name='city' size='15' type='text'><br><small><?=__("City", VOSL_TEXT_DOMAIN)?></small></td>
		<td style="width:85px;"><input name='state' size='10' type='text'><br><small><?=__("State", VOSL_TEXT_DOMAIN)?></small></td>
		<td><input name='zip' size='6' type='text'><br><small><?=__("Zip", VOSL_TEXT_DOMAIN)?></small></td></tr>
        
        <tr>
        	<td colspan="3" style="padding:0px;">
            	<select name="country">
                <option value=""><?=__("--Select Country--", VOSL_TEXT_DOMAIN)?></option>
                <?php foreach($tld as $key=>$value){ ?>
                <option value="<?=$value?>"><?=$key?></option>
                <?php } ?>
                </select>
                <br><small><?=__("Country", VOSL_TEXT_DOMAIN)?></small>
            </td>
        </tr>
        
        <tr>
        	<td colspan="3" style="padding:0px;"><div id="vosl-add-edit-listing-map-admin"></div></td>
        </tr>
        </table>
        
        <table cellpadding='0px' cellspacing='0px'>
        <tr>
		<td style="padding-left:0px;"><input name='latitude' size='20' type='text'><br><small><?=__("Lattitude", VOSL_TEXT_DOMAIN)?></small></td>
		<td><input name='longitude' size='20' type='text'><br><small><?=__("Longitude", VOSL_TEXT_DOMAIN)?></small></td></tr>
        </table>
        
        <table cellpadding='0px' cellspacing='0px'>
        
		<tr><td style='padding-left:0px' class='nobottom'>
		<input name='show_address_publicly' type='checkbox' value='1' checked='checked' >&nbsp;<small><?=__("Share Address Publicly", VOSL_TEXT_DOMAIN)?></small></td></tr>
        <?php /*?><tr>
        <td colspan="3" style="padding-left:0px;">Tags/Pin  <?=$tags_output?></td>
        </tr>
        <?php do_action('vosl_show_custom_marker_icon_field',0); ?><?php */?>
		</table><br>
		
        <td style="vertical-align:top !important; width:60%" class="vosl-add-edit-list right">
        
        <div style="width:100%; display:inline-block; margin-bottom:20px;">
        
        <div style="display:inline-block; ">
        <input id='upload_image' type='hidden' name='image' size='35'>
        <div id="vosl_admin_add_edit_listing_img">
        	<img src="<?=$vosl_base."/images/locationimg.jpg";?>" />
        </div>
        
        <input type='button' value='<?=__("Upload", VOSL_TEXT_DOMAIN)?>' id='upload_image_button' class='button' style="margin-top:5px; font-size:12px;" />
        <input type='button' value='<?=__("Remove", VOSL_TEXT_DOMAIN);?>' class='button' id='remove_image_button' style="margin-left:5px; margin-top:5px;font-size:12px;" />
        </div>
        
        <table class="vosl-tags-table">
        <tr>
        <td colspan="3" style="padding-left:0px;">Tags/Pin<br />  <?=$tags_output?></td>
        </tr>
        <?php do_action('vosl_show_custom_marker_icon_field',0); ?>
        </table>
        
        </div>
        
		<?php /*?><?=__("Additional Information", VOSL_TEXT_DOMAIN)?><br><?php */?>
       <?php /*?> <small><?=__("Description", VOSL_TEXT_DOMAIN)?></small><br>
		<textarea name='description' rows='8' cols='50'></textarea>
        
        
        
        <br>
		<input name='url' type='text' size='35'>&nbsp;<small><?=__("URL", VOSL_TEXT_DOMAIN)?></small><br>
		<input name='phone' type='text' size='35'>&nbsp;<small><?=__("Phone", VOSL_TEXT_DOMAIN)?></small><br>
		<input name='fax' type='text' size='35'>&nbsp;<small><?=__("Fax", VOSL_TEXT_DOMAIN)?></small><br>
		<input name='email' type='text' size='35'>&nbsp;<small><?=__("Email", VOSL_TEXT_DOMAIN)?></small><br>
        <input name='hours' type='text' size='35'>&nbsp;<small><?=__("Hours", VOSL_TEXT_DOMAIN)?></small><br /><?php */?>
        
        <?php foreach($vosl_custom_fields as $field){
			
				$label = $field['label'];
				$label = apply_filters( 'vosl_link_custom_field_label',$label, $field);
				$icon = apply_filters( 'vosl_link_custom_field_icon','', $field);
			?>
            
            <?php if($field['field_type']=='text' or $field['field_type']=='url'){ ?>
            <small><?=$label?></small><br>
            <input name='custom_<?=$field['field_name']?>' type='text' size='35'><?=$icon?><br>
            <?php }else if($field['field_type']=='textarea'){ ?>
            <small><?=$label?></small>&nbsp;<?=$icon?><br>
            <textarea name='custom_<?=$field['field_name']?>' rows='8' cols='50'></textarea><br>
            <?php } ?>
            <?php	
		}
		?>
        
        
        
		<?php /*?><input id='upload_image' type='hidden' name='image' size='35'>
        <div id="vosl_admin_add_edit_listing_img">
        	<img src="<?=$vosl_base."/images/locationimg.jpg";?>" />
        </div>
        
        <input type='button' value='<?=__("Upload", VOSL_TEXT_DOMAIN)?>' id='upload_image_button' class='button' style="margin-top:5px;" />
        <input type='button' value='<?=__("Remove", VOSL_TEXT_DOMAIN);?>' class='button' id='remove_image_button' style="margin-left:5px; margin-top:5px;" /><?php */?>
		
		

		<?=wp_nonce_field("add-location_single", "_wpnonce", true, false);?>
		<br><?php $cancel_onclick = "location.href=\"admin.php?page=".VOSL_PAGES_DIR."/locations.php"."\""; ?>
	<input type='submit' name='btnVoslAddlisting' value='<?=__("Add Listing", VOSL_TEXT_DOMAIN)?>'  class='button-primary'>&nbsp;
	<input type='submit' name='btnVoslAddListingClose' value='<?=__("Add Listing & Close", VOSL_TEXT_DOMAIN)?>' class='button-primary'>
	&nbsp;&nbsp;<input type='button' class='button' value='<?php echo __("Cancel", VOSL_TEXT_DOMAIN); ?>' onclick='<?=$cancel_onclick?>'>
	</td>
	</td>
		</tr>
    <?php /*?><tr>
    	<td colspan="2"><div id="vosl-add-edit-listing-map-admin"></div></td>
    </tr> <?php */?>   
	</table>
    
    
</form>


</div>