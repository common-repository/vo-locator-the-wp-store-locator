<?php
if (!empty($_GET['pg']) && isset($wpdb) && $_GET['pg']=='add-locations') { /*include_once(SL_INCLUDES_PATH."/top-nav.php"); */}

if (!defined("VOSL_INCLUDES_PATH")) { include("../vosl-define.php"); }

global $wpdb, $vosl_hooks, $vosl_base, $vosl_admin_classes_dir;
include(VOSL_INCLUDES_PATH."/countries-regions.php");
unset($tld["United States"]);
$tld["United States"] = "us";
ksort($tld);

$vosl_locator_admin = new VoStoreLocator_Admin();
$location_id = $_REQUEST['id'];
$vosl_custom_fields = $vosl_locator_admin->get_store_custom_fields($location_id);

require_once(VOSL_ACTIONS_PATH."/processLocationData.php");
$vosl_map_api_key = vosl_data('vosl_map_api_key');

$value=$wpdb->get_row("SELECT * FROM ".VOSL_TABLE." WHERE id = ".$location_id, ARRAY_A);

if($value['image']=='')
	$value['image'] = $vosl_base."/images/locationimg.jpg";

$rows_tag = $wpdb->get_results( $wpdb->prepare( "SELECT tag_id FROM ".VOSL_TAGS_ASSOC_TABLE." WHERE store_id = %d ",(int)$location_id ), ARRAY_A );

$tags_selected = array();

foreach($rows_tag as $tag)
{
	$tags_selected[] = $tag['tag_id'];
}
//$enable_default_tags = vosl_data('vosl_enable_default_tags');
	
///if($enable_default_tags)	
$tags_output = apply_filters( 'vosl_populate_tags_dropdown',$tags_selected, (int)$location_id);
$edit_link = 'admin.php?page='.VOSL_PAGES_DIR.'/edit-locations.php'; 
?>
<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=<?=$vosl_map_api_key?>"></script>
<script src="<?php echo $vosl_base?>/js/gmap3.min.js"></script>
<style type="text/css">
.vosl-tags-table{ display:inline-block; margin-left:20px; vertical-align:top; }
</style>
<div class='wrap'>
<div class="vosl_default_hidden_img"><?=$vosl_base."/images/locationimg.jpg";?></div>

<?php if($value['show_address_publicly']==1)
			$show_directions = ' checked="checked" ';
			
	?>	
    

    
    <form name='manualAddForm' id='locationForm' method=post>
    <input type="hidden" name="act" id="act" value="voslupdatelocation" />
    <input type="hidden" name="vosl_location_id" id="vosl_location_id" value="<?=$location_id?>" />
	<table cellpadding='0' class='widefat manual_edit_table'>
    <thead><tr><th><?=__("Edit Listing", VOSL_TEXT_DOMAIN)?></th></tr></thead>
	<tr>
		<td style='vertical-align:top !important; width:40%' class="vosl-add-edit-list left"><b><?php echo __("Name of Listing", VOSL_TEXT_DOMAIN); ?></b><br><input name='store_name-<?=$location_id?>' id='store-<?=$location_id?>' value='<?=$value['store_name']?>' size=50 type='text'><br><br>
        <b><?php echo __("Find Address", VOSL_TEXT_DOMAIN); ?></b><br><input name='find_address' id='find_address' value='' size='50' type='text' autocomplete="off">
        <br /><br />
		<b><?php echo __("Address", VOSL_TEXT_DOMAIN); ?></b><br><input class="address" name='address-<?=$value['id']?>' id='address-<?=$value['id']?>' value='<?=$value['address']?>' size='35' type='text'>&nbsp;<small>("<?php echo __("Street - Line1", VOSL_TEXT_DOMAIN); ?> ")</small><br>
		<input name='address2-<?=$value['id']?>' class="address2" id='address2-<?=$value['id']?>' value='<?=$value['address2']?>' size='35' type='text'>&nbsp;<small>("<?php echo __("Street - Line 2 - optional", VOSL_TEXT_DOMAIN); ?>")</small><br>
		
        <table cellpadding='0px' cellspacing='0px'><tr>
        <td style='padding-left:0px; width:100px;' class='nobottom'><input name='city-<?=$value['id']?>' id='city-<?=$value['id']?>' value='<?=$value['city']?>' size='15' type='text'><br><small><?php echo __("City", VOSL_TEXT_DOMAIN); ?></small></td>
		<td style="width:85px;"><input name='state-<?=$value['id']?>' id='state-<?=$value['id']?>' value='<?=$value['state']?>' size='10' type='text'><br><small><?php echo __("State", VOSL_TEXT_DOMAIN); ?></small></td>
		<td><input name='zip-<?=$value['id']?>' id='zip-<?=$value['id']?>' value='<?=$value['zip']?>' size='6' type='text'><br><small><?php echo __("Zip", VOSL_TEXT_DOMAIN); ?></small>
		</td></tr>
        
        <tr>
        	<td colspan="3" style="padding:0px;">
            	<select name="country-<?=$value['id']?>">
                <option value=""><?=__("--Select Country--", VOSL_TEXT_DOMAIN)?></option>
                <?php foreach($tld as $key=>$selvalue){ ?>
                <option value="<?=$selvalue?>" <?php if($selvalue==$value['country']){ ?> selected <?php } ?>><?=$key?></option>
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
		<td style="padding-left:0px;"><input name='latitude-<?=$value['id']?>' value='<?=$value['latitude']?>' size='20' type='text'><br><small><?=__("Lattitude", VOSL_TEXT_DOMAIN)?></small></td>
		<td><input name='longitude-<?=$value['id']?>' value='<?=$value['longitude']?>' size='20' type='text'><br><small><?=__("Longitude", VOSL_TEXT_DOMAIN)?></small></td></tr>
        </table>
        
        <table cellpadding='0px' cellspacing='0px'>
        
		<tr><td style='padding-left:0px; padding-top:20px;' class='nobottom'><input name='show_address_publicly-<?=$value['id']?>' <?=$show_directions?> id='show_address_publicly-<?=$value['id']?>' value='1' type='checkbox'>&nbsp;<small><?php echo __("Share Address Publicly", VOSL_TEXT_DOMAIN); ?></small></td></tr>
        <?php /*?><tr>
        <td colspan="3" style="padding-left:0px;"><?php echo __("Tags/Pin", VOSL_TEXT_DOMAIN); ?>  <?=$tags_output?></td>
        </tr>
        <?php do_action('vosl_show_custom_marker_icon_field',$location_id); ?><?php */?>
        </table>
		
		
		</td><td style='width:60%; vertical-align:top !important;' class="vosl-add-edit-list right">
		<?php /*?><b><?php echo __("Additional Information", VOSL_TEXT_DOMAIN); ?></b><br><?php */?>
		
        
        <div style="width:100%; display:inline-block; margin-bottom:20px;">
            <div style="display:inline-block;">
                <input id='upload_image' name='image-<?=$value['id']?>' id='image-<?=$value['id']?>' value='<?=$value['image']?>' size='35' type='hidden'>
                
                <div id="vosl_admin_add_edit_listing_img">
                    <img src="<?=$value['image']?>" />
                </div>
                
                <input type='button' value='<?=__("Upload", VOSL_TEXT_DOMAIN);?>' class='button' id='upload_image_button' style="margin-top:5px;" />
                <input type='button' value='<?=__("Remove", VOSL_TEXT_DOMAIN);?>' class='button' id='remove_image_button' style="margin-left:5px; margin-top:5px;" />
            </div>
            
            <table class="vosl-tags-table">
                <tr>
                	<td colspan="3" style="padding-left:0px;"><?php echo __("Tags/Pin", VOSL_TEXT_DOMAIN); ?><br />  <?=$tags_output?></td>
                </tr>
                <?php do_action('vosl_show_custom_marker_icon_field',$location_id); ?>
            </table>
        
        </div>
        
        <?php /*?><small><?=__("Description", VOSL_TEXT_DOMAIN)?></small><br>
        <textarea name='description-<?=$value['id']?>' id='description-<?=$value['id']?>' rows='8' cols='50'><?=$value['description']?></textarea>
        
        <br>		
		<input name='url-<?=$value['id']?>' id='url-<?=$value['id']?>' value='<?=$value['url']?>' size='35' type='text'>&nbsp;<small><?php echo __("URL", VOSL_TEXT_DOMAIN); ?></small><br>
		<input name='phone-<?=$value['id']?>' id='phone-<?=$value['id']?>' value='<?=$value['phone']?>' size='35' type='text'>&nbsp;<small><?php echo __("Phone", VOSL_TEXT_DOMAIN); ?></small><br>
		<input name='fax-<?=$value['id']?>' id='fax-<?=$value['id']?>' value='<?=$value['fax']?>' size='35' type='text'>&nbsp;<small><?php echo __("Fax", VOSL_TEXT_DOMAIN); ?></small><br>
		<input name='email-<?=$value['id']?>' id='email-<?=$value['id']?>' value='<?=$value['email']?>' size='35' type='text'>&nbsp;<small><?php echo __("Email", VOSL_TEXT_DOMAIN); ?></small><br>
        <input name='hours-<?=$value['id']?>' id='hours-<?=$value['id']?>'  type='text' value='<?=$value['hours']?>' size='35'>&nbsp;<small><?=__("Hours", VOSL_TEXT_DOMAIN);?></small><br /><?php */?>
        <?php foreach($vosl_custom_fields as $field){
			
				$label = $field['label'];
				$label = apply_filters( 'vosl_link_custom_field_label',$label, $field);
				$icon = apply_filters( 'vosl_link_custom_field_icon','', $field);
				
			?>
            
            <?php if($field['field_type']=='text' or $field['field_type']=='url'){ ?>
            <small><?=$label?></small><br>
            <input name='custom_<?=$field['field_name']?>_<?=$field['field_id']?>' type='text' size='35' value='<?=$field['custom_field_value']?>'><?=$icon?><br>
            <?php }else if($field['field_type']=='textarea'){ ?>
            <small><?=$label?></small>&nbsp;<?=$icon?><br>
            <textarea name='custom_<?=$field['field_name']?>_<?=$field['field_id']?>' rows='8' cols='50'><?=$field['custom_field_value']?></textarea><br>
            <?php } ?>
            <?php	
		}
		?>
        
		<?php /*?><input id='upload_image' name='image-<?=$value['id']?>' id='image-<?=$value['id']?>' value='<?=$value['image']?>' size='35' type='hidden'>
        
        <div id="vosl_admin_add_edit_listing_img">
        	<img src="<?=$value['image']?>" />
        </div>
        
        <input type='button' value='<?=__("Upload", VOSL_TEXT_DOMAIN);?>' class='button' id='upload_image_button' style="margin-top:5px;" />
        <input type='button' value='<?=__("Remove", VOSL_TEXT_DOMAIN);?>' class='button' id='remove_image_button' style="margin-left:5px; margin-top:5px;" /><?php */?>
		
        <?php $cancel_onclick = "location.href=\"admin.php?page=".VOSL_PAGES_DIR.'/locations.php'."\"";
		
		$show_directions = ''; ?>
		
		<br>
		<nobr><input type='submit' value='<?php echo __("Update", VOSL_TEXT_DOMAIN); ?>' class='button-primary'>&nbsp;&nbsp;<input type='button' class='button' value='<?php echo __("Cancel", VOSL_TEXT_DOMAIN); ?>' onclick='<?=$cancel_onclick?>'></nobr>
        
		</td></tr>
        <?php if($location_id > 0){ ?>
        <tr>
        <td colspan="2"><a href="#" onClick="deleteVoslListing('<?=$edit_link."&delID=".$location_id;?>','<?=__("Do you really want to delete this listing?", VOSL_TEXT_DOMAIN);?>'); return false;" class="button-red" style="float:right;"><?=__("Delete Listing", VOSL_TEXT_DOMAIN);?></a></td>
        </tr>
        <?php } ?>
	</table>
    
</form>

</div>