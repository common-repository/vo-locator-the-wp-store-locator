// JavaScript Document
(function( $ ) {
 
    // Add Color Picker to all inputs that have 'color-field' class
    $(function() {
		
		if(jQuery('.vosl_marker_tag_color').length)
		 {
			  jQuery('.vosl_marker_tag_color').wpColorPicker();
		 }
		 
        $('#color-field').wpColorPicker({
							change: function(event, ui) {
								var color = ui.color.toString();
								$('#color-field').val(color);
								// ajax settings upate
								//update_vosl_settings_ajax();
								// update listings UI whenever a change is made
								vosl_update_map_listings_ui_admin();
							}
						});
						
		$('#color-field-text').wpColorPicker({
							change: function(event, ui) {
								var color = ui.color.toString();
								$('#color-field-text').val(color);
								// ajax settings upate
								//update_vosl_settings_ajax();
								vosl_update_map_listings_ui_admin();
							}
						});
						
		$('#color-field-text-bg').wpColorPicker({
							change: function(event, ui) {
								// ajax settings upate
								var color = ui.color.toString();
								$('#color-field-text-bg').val(color);
								//update_vosl_settings_ajax();
								vosl_update_map_listings_ui_admin();
							}
						});
		
		if(jQuery('#vosl_custom_map_popup_bgcolor_settings').length)
		 {
			  jQuery('#vosl_custom_map_popup_bgcolor_settings').wpColorPicker({
							change: function(event, ui) {
								var color = ui.color.toString();
								$('#vosl_custom_map_popup_bgcolor_settings').val(color);
								vosl_update_admin_marker_popup();
								// ajax settings upate
								//update_vosl_settings_ajax();
							}
						});
		 }
		  
		  if(jQuery('#vosl_custom_map_marker_color_settings').length)
		  {
			  jQuery('#vosl_custom_map_marker_color_settings').wpColorPicker({
							change: function(event, ui) {
								var color = ui.color.toString();
								$('#vosl_custom_map_marker_color_settings').val(color);
								vosl_update_admin_marker_popup();
								// ajax settings upate
								//update_vosl_settings_ajax();
								// This function builds map markers
								
							}
						});
		  }
		  
		  if(jQuery('#vosl_custom_map_popup_text_color_settings').length)
		  {
			  jQuery('#vosl_custom_map_popup_text_color_settings').wpColorPicker({
							change: function(event, ui) {
								var color = ui.color.toString();
								$('#vosl_custom_map_popup_text_color_settings').val(color);
								// ajax settings upate
								vosl_update_admin_marker_popup();
								//update_vosl_settings_ajax();
							}
						});
		  }
    });
     
})( jQuery );