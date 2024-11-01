<div class="vosl-menu-general vosl-settings-menu-group">
<table class="form-table" style="width:730px; display:inline-block;">
<tbody>
<?php do_action('vosl_general_settings_fields',$vosl_map_api_key,$vosl_show_love); ?>
<?php do_action('vosl_general_settings_instance_fields',$instance_details); ?>
<?php if(!empty($fonts)){
		?>
        
         <tr valign="top">
            <th scope="row"><?php echo __("Custom Font", VOSL_TEXT_DOMAIN); ?></th>
            <td>
            <select name="vosl_custom_font" id="vosl_custom_font_settings">
                <option value="">Default Font</option>	
                <?php foreach($fonts as $key => $value){ $url = '';
				
				if($value['font_type']=='google')
					$url = $value['urls']['regular'];
				
				$full_name = $value['name']."::".$url;
				 ?>
                    <option value="<?php echo $value['name']."::".$url; ?>" <?php if($vosl_custom_font==$full_name){ ?> selected="selected" <?php } ?>><?php echo $value['name']; ?></option>	
                <?php } ?>
            </select>
            &nbsp;&nbsp;<div id="vosl_sample_preview"><?php echo __("Sample Preview Text", VOSL_TEXT_DOMAIN); ?></div>
            </td>
        </tr>
        <?php
		} 
?>

 <tr>
  
    <th scope="row"><?php echo __("Custom Css", VOSL_TEXT_DOMAIN); ?></th> 
    <td><textarea name="vosl_custom_style" class="codemirror-area" id="vosl_custom_style" rows="13" cols="55"><?php echo $vosl_custom_style;?></textarea></td>
 </tr>


</tbody></table>
   
<?php if (!defined('VOSLP_VERSION')) { ?>    
<table style="width: 350px; display: inline-block; vertical-align: top;margin: 20px;border: 1px solid #cccccc;"  border="0" cellspacing="0" cellpadding="10">
  <tr>
    <td bgcolor="#FFFFFF" style="color:#008EC2;"><div align="center" style="font-size:14px; font-weight:bold;"><?php echo __("Need More Tools?", VOSL_TEXT_DOMAIN); ?></div></td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" style="color:#008EC2;">
      <ul style="list-style:disc; margin-left:20px; color:#444; margin-top:0px;">
      <li><?php echo __("Import/Export Listings", VOSL_TEXT_DOMAIN); ?></div></li>
      <li><?php echo __("Custom Pins", VOSL_TEXT_DOMAIN); ?></li>
      <li><?php echo __("Custom Instances", VOSL_TEXT_DOMAIN); ?></li>
      <li><?php echo __("Visitor Stats", VOSL_TEXT_DOMAIN); ?></li>
      </ul>
    <p align="center"><a href="http://www.vitalorganizer.com/product/vo-store-locator-pro-add-on?utm_source=plugin&amp;utm_medium=pluginUI&amp;utm_campaign=settings" target="_blank"><?php echo __("Grab Your PRO ADDON Now", VOSL_TEXT_DOMAIN); ?></a></p></td>
  </tr>
</table>
<?php } ?>

<?php /*?><p class="submit"><input type="submit" value="<?php echo __("Save Changes", VOSL_TEXT_DOMAIN); ?>" class="button button-primary" id="submit" name="btnSubmit"></p><?php */?>
</div>

<link href="<?php echo $vosl_base; ?>/lib/codemirror/lib/codemirror.css" rel="stylesheet">
<script src="<?php echo $vosl_base;?>/lib/codemirror/lib/codemirror.js"></script> 
<script src="<?php echo $vosl_base;?>/lib/codemirror/mode/javascript/javascript.js"></script> 
<script src="<?php echo $vosl_base;?>/lib/codemirror/addon/mode/simple.js"></script> 
<script type="text/javascript">
jQuery( document ).ready(function() {
 var editor = CodeMirror.fromTextArea(document.getElementById("vosl_custom_style"), {
        tabMode: "indent",
        matchBrackets: true,
        lineNumbers: true,
        lineWrapping: true,
        textWrapping: true,
        mode: "html"
    });
    function updateTextArea() {
        editor.save();
    }
    editor.on('change', updateTextArea);
    editor.replaceRange("\n", {line: Infinity});
});
</script>