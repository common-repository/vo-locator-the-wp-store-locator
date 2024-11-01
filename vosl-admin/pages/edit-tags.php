<?php
if (!defined("VOSL_INCLUDES_PATH")) { include("../vosl-define.php"); }

global $wpdb;
$tag_id = $_REQUEST['id'];
global $vosl_hooks, $wpdb;

require_once(VOSL_ACTIONS_PATH."/processLocationData.php");

$value=$wpdb->get_row("SELECT * FROM ".VOSL_TAGS_TABLE." WHERE id = ".$tag_id, ARRAY_A);
$edit_link = 'admin.php?page='.VOSL_PAGES_DIR.'/edit-tags.php'; 
?>
<div class='wrap'>
<table cellpadding='' cellspacing='0' style='width:100%' class='manual_edit_table'><tr>
<td style='padding-top:0px; width:50%' valign='top'>

<tr style='background-color:$bgcol' id='sl_tr_data-<?=$value[id]?>'>
			
	<td><form name='manualAddForm' method=post>
	<table cellpadding='0' class='widefat'>
    <thead><tr><th><?=__("Edit Tag", VOSL_TEXT_DOMAIN)?></th></tr></thead>
	<tr>
		<td style='vertical-align:top !important; width:30%'>
        
        <div style="display:inline-block;float: left;width: 100px;"><?=__("Name of Tag", VOSL_TEXT_DOMAIN)?></div>
        <div style="display:inline-block;float: left;"><input name='tag_name-<?=$value['id']?>' id='tag-<?=$value['id']?>' value='<?=$value['tag_name']?>' size=30 type='text'></div>
        <br />
		<div style="display:inline-block;float: left;clear: both;width: 100px; margin-top:10px;"><?=__("Tag Color", VOSL_TEXT_DOMAIN)?></div>
        <div style="display:inline-block;float: left; margin-top:10px;"><input type="text" class="vosl_marker_tag_color" id="tag-<?=$value['id']?>" name='tag_color-<?=$value['id']?>' value='<?php echo esc_html($value['tag_color']);?>' /></div><br />
		
		
        <?php $cancel_onclick = "location.href=\"admin.php?page=".VOSL_PAGES_DIR.'/manage-tags.php'."\""; ?>
		<nobr style="float:left; margin-top:15px;"><input type='submit' value='<?=__("Update", VOSL_TEXT_DOMAIN)?>' class='button-primary'>&nbsp;&nbsp;<input type='button' class='button' value='<?=__("Cancel", VOSL_TEXT_DOMAIN)?>' onclick='<?=$cancel_onclick?>'></nobr>
		</td><td style='width:60%; vertical-align:top !important;'>
		
		</td><td style='vertical-align:top !important; width:40%'>
	</td></tr>
    <?php if($tag_id > 0){ ?>
        <tr>
        <td colspan="3"><a href="#" onClick="deleteVoslListing('<?=$edit_link."&deltID=".$tag_id;?>','<?=__("Do you really want to delete this tag?", VOSL_TEXT_DOMAIN);?>'); return false;" class="button-red" style="float:right;"><?=__("Delete Tag", VOSL_TEXT_DOMAIN)?></a></td>
        </tr>
        <?php } ?>
	</table>
	<input type='hidden' name='act' value='voslupdatetags' />
    <input type="hidden" name="vosl_tag_id" id="vosl_tag_id" value="<?=$tag_id?>" />
</form>
</td>

</tr>

</td>
<td style='padding-top:0px;' valign='top'>
</td>
</tr>
</table>
</div>