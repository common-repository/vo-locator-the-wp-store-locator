<?php
if (!defined("VOSL_INCLUDES_PATH")) { include("../vosl-define.php"); }

print "<div class='wrap'>";

global $wpdb;

//Inserting addresses by manual input
if (!empty($_POST['tag_name']) && (empty($_GET['mode']) || $_GET['mode']!="pca")) {
	
	if (!empty($_POST['_wpnonce']) && wp_verify_nonce($_POST['_wpnonce'], "add-tag_single")){
		
		$output = vosl_check_existing_tags($_POST['tag_name']);
		if($output['msg']=='OK')
		{
			vosl_add_tag();
			print "<div class='sl_admin_success'>".__("Successful Addition",VOSL_TEXT_DOMAIN).". <a href='admin.php?page=".VOSL_PAGES_DIR."/manage-tags.php"."'>".__("Manage Tags", VOSL_TEXT_DOMAIN)."</a> <script>setTimeout(function(){jQuery('.sl_admin_success').fadeOut('slow');}, 6000);</script></div>"; 
			
		}else
		{
			print "<div class='sl_admin_warning'>".__($output['msg'],VOSL_TEXT_DOMAIN).".</div>"; 
		}
		
	} else {
		print "<div class='sl_admin_warning'>".__("Unsucessful addition due to security check failure",VOSL_TEXT_DOMAIN).". $view_link</div>"; 
	}
}else
 {
   $btnVoslAddTag = $_POST['btnVoslAddTag'];
   if(isset($btnVoslAddTag)){
		print "<div style='color: red;font-size: 15px;font-weight: 400; margin-bottom: 5px;'>".__("Please enter name for the tag",VOSL_TEXT_DOMAIN)."</div>";

	}
}

?>
<table cellpadding='' cellspacing='0' style='width:100%' class='manual_add_table'><tr>
<td style='padding-top:0px; width:50%' valign='top'>
	<form name='manualAddForm' method='post'>
	<table cellpadding='0' class='widefat'>
	<thead><tr><th><?=__("Add Tag", VOSL_TEXT_DOMAIN)?></th></tr></thead>
	<tr>
		<td>
		<div style='display:inline; width:50%'>
        <div style="display:inline-block;float: left;width: 100px;"><?=__("Name of Tag", VOSL_TEXT_DOMAIN)?>
        </div>
        <div style="display:inline-block;float: left;"><input name='tag_name' size=40 type='text'></div>
        <br />
		<div style="display:inline-block;float: left;clear: both;width: 100px; margin-top:10px;"><?=__("Tag Color", VOSL_TEXT_DOMAIN)?></div>
        <div style="display:inline-block;float: left; margin-top:10px;"><input type="text" value="" class="vosl_marker_tag_color" id="vosl_marker_tag_color" name="tag_color" /></div>
        
		</div><div style='display:inline; width:50%; float:left; clear:both;'>
		<?=wp_nonce_field("add-tag_single", "_wpnonce", true, false);?>
		<br>
		<input type='submit' name="btnVoslAddTag" value='<?=__("Add Tag", VOSL_TEXT_DOMAIN)?>' class='button-primary'>&nbsp;&nbsp;<input type='button' class='button' value='<?php echo __("Cancel", VOSL_TEXT_DOMAIN); ?>' onclick="location.href='admin.php?page=<?=VOSL_PAGES_DIR."/manage-tags.php";?>';">
        
	</div>
	</td>
		</tr>
	</table>
</form>
</td>
<td style='padding-top:0px;' valign='top'>
</td>
</tr>
</table>
</div>