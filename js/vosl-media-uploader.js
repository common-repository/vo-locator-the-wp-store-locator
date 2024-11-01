jQuery(function() {
	window.send_to_editor = function(html) {	
		imgurl = jQuery("<div>" + html + "</div>").find('img').attr('src');
		
		if(typeof vosl_image_btn_id != 'undefined')
			jQuery('#'+vosl_image_btn_id).val(imgurl);
		
		if(typeof vosl_image_btn_id != 'undefined' && vosl_image_btn_id=='vosl_custom_listing_marker_icon')
		{
			// display image for pro addon
			vosl_display_custom_marker_icon_admin(imgurl);
			
		}else if(typeof vosl_image_btn_id != 'undefined' && vosl_image_btn_id=='vosl_bulk_update_marker_select')
		{
			vosl_bulk_update_marker_select(imgurl);
			
		}else if(typeof vosl_image_btn_id != 'undefined' && vosl_image_btn_id=='upload_image')
		{
			jQuery("#vosl_admin_add_edit_listing_img img").attr('src',imgurl);
		}
		
		tb_remove();
	}
});