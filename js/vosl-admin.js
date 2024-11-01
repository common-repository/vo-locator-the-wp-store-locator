// JavaScript Document
var vosl_autocomplete;
var vosl_admin_map_center_changed = false;
var voslAddEditListingMarkersArr = new Array();
var componentForm = {
	street_number: 'short_name',
	route: 'long_name',
	locality: 'long_name',
	sublocality_level_1: 'long_name',
	administrative_area_level_1: 'short_name',
	country: 'short_name',
	//country_short: 'short_name',
	postal_code: 'short_name'
};
  

jQuery(function() {
  // Handler for .ready() called.
  	  $vosl_Ajax = jQuery("body");	
	  initialize_vosl_admin_events();
	  vosl_load_selected_font_settings();
	  initialize_vosl_admin_events_update_settings();
  // update co-ordination action button click
  	
	if( typeof google === 'object' && typeof google.maps === 'object' )
	{
		 google.maps.Map.prototype.panToWithOffset=function(e,a,d){var c=this;var b=new google.maps.OverlayView();b.onAdd=function(){var f=this.getProjection();var g=f.fromLatLngToContainerPixel(e);g.x=g.x+a;g.y=g.y+d;c.panTo(f.fromContainerPixelToLatLng(g))};b.draw=function(){};b.setMap(this)};
		 voslInitAutocomplete();
	}
	 	
});

function closeMarker(order)
{
	var marker_id = 'et_marker_' + order;
	jQuery( '#' + marker_id ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 500, function() {
		jQuery(this).css( { 'display' : 'none' } );
	} );
}

function update_vosl_settings_ajax()
{ 

    
    
	$vosl_Ajax.addClass('vosl_loading');
	
	jQuery.ajax({
				type: "POST",
				url: vosl_admin_ajax_script_url.ajaxurl,
				data: jQuery("#vosl_settings").serialize(),
				success: function(alrt){
							
				// update listings UI whenever a change is made
				//vosl_update_map_listings_ui_admin();
				// update marker cluster
				//vosl_update_admin_marker_cluster();
				// udpate search customizations options
				//vosl_update_search_customization_options();
				$vosl_Ajax.removeClass('vosl_loading');
				location.reload();
				}
				});
}


function vosl_update_admin_marker_popup()
{
	var pinImage = new google.maps.MarkerImage();
	
	if(jQuery("#vosl_custom_map_marker_color_settings").val()!='')
	{
		var pinColor = jQuery("#vosl_custom_map_marker_color_settings").val();
		pinColor = pinColor.replace("#", "");
		var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
		new google.maps.Size(21, 34),
		new google.maps.Point(0,0),
		new google.maps.Point(10, 34));
	}
			
	var listItems=jQuery("#maplist .col-lg-3 .locationlist");listItems.each(function(idx,li){var location_id=jQuery(this).attr('id');location_id=location_id.split("_");location_id=location_id[1];
	
		var lastMarker = jQuery("#map_placeholder_admin").gmap3({
			get: {
			  id: "et_marker_"+location_id
			}
		  });
		  
		  lastMarker.setIcon(pinImage);
	});
	
	// change infowindow background
	if(jQuery('#vosl_custom_map_popup_bgcolor_settings').val()!='')
	{
		jQuery("#map_placeholder_admin .location-description").css('background',jQuery('#vosl_custom_map_popup_bgcolor_settings').val());
		jQuery(".location-rating:before").css('border-top-color', jQuery('#vosl_custom_map_popup_bgcolor_settings').val());
	}
	
	// change infowindow txt color
	if(jQuery('#vosl_custom_map_popup_text_color_settings').val()!='')
	{
		jQuery(".location-description .voslrow, .location-description .voslrow a").css('color',jQuery('#vosl_custom_map_popup_text_color_settings').val());
	}
	
	// change infowindow popup width
	if(jQuery("#vosl_custom_map_popup_width").val()!='')
	{
		var width = jQuery("#vosl_custom_map_popup_width").val() - 160;
		jQuery("#map_placeholder_admin .location-description").css('width',jQuery("#vosl_custom_map_popup_width").val()+"px");
		// change right side width in the infowindow
		jQuery("#map_placeholder_admin .location-description .col-lg-7").css('width',parseInt(width)+"px");
	}
}

function vosl_update_search_customization_options()
{
	if(jQuery(".vosl-menu-search-customizations #enable_default_tags:checked").length)
	{
		jQuery(".vosl_tag_options").show();
		jQuery("#vosl_single_tag > option:first-child").text("--"+jQuery("#vosl_tags_filter_label").val()+"--");
		
	}else
	{
		jQuery(".vosl_tag_options").hide();
	}
	
	if(jQuery(".vosl-menu-search-customizations #vosl_radius_filter:checked").length)
	{
		jQuery(".vosl_radius_max_option").show();
		
	}else
	{
		jQuery(".vosl_radius_max_option").hide();
	}
	
	
	jQuery("#zipcodelookup h2").html(jQuery("#fndLocationText").val());
}

function vosl_update_map_listings_ui_admin()
{
	jQuery(".vosl-menu-search-customizations #vosl-radius-units").html(jQuery("#selMiles option:selected").text());
	jQuery("#vosl_listing_column_admin_settings .locationlist h4 span span").html(jQuery("#selMiles").val());
	jQuery("#vosl_listing_column_admin_settings .locationlist").css("background",jQuery("#color-field-text-bg").val());
	jQuery("#vosl_listing_column_admin_settings .locationlist").css("color",jQuery("#color-field-text").val());
	
	jQuery("#vosl_listing_column_admin_settings .locationlist").mouseenter(function() {
		jQuery(this).css("background",jQuery("#color-field").val())
	}).mouseleave(function() {
		jQuery(this).css("background",jQuery("#color-field-text-bg").val())
	});
}

function update_vosl_settings_ajax_without_spinner()
{
	jQuery.ajax({
		type: "POST",
		url: vosl_admin_ajax_script_url.ajaxurl,
		data: jQuery("#vosl_settings").serialize(),
		success: function(alrt){}
		});
}

function voslInitAutocomplete()
{
	// Create the autocomplete object, restricting the search to geographical
	// location types.
	

	// When the user selects an address from the dropdown, populate the address
	// fields in the form.
	if(jQuery("#find_address").length)
	{
		vosl_autocomplete = new google.maps.places.Autocomplete(
		/** @type {!HTMLInputElement} */(document.getElementById('find_address')),
		{types: ['geocode']});
		vosl_autocomplete.addListener('place_changed', vosl_fillInAddress);
	}
}

function deleteVoslListing(url, message)
{
	if(confirm(message))
	{
		location.href = url;
	}
}
  
 function vosl_fillInAddress() {
        // Get the place details from the autocomplete object.
        var place = vosl_autocomplete.getPlace();
		var Address = '';
        
		jQuery("#locationForm .address").val('');
		jQuery("#locationForm .address2").val('');
		jQuery("#locationForm input[name^='city']").val('');
		jQuery("#locationForm input[name^='state']").val('');
		jQuery("#locationForm input[name^='zip']").val('');
		//console.log(place.address_components);
        // Get each component of the address from the place details
        // and fill the corresponding field on the form.
        for (var i = 0; i < place.address_components.length; i++) {
          var addressType = place.address_components[i].types[0];
		 // console.log(addressType);
		  if (componentForm[addressType]) {
            var val = place.address_components[i][componentForm[addressType]];
          }
		  
		  if(addressType=='street_number')
		  {
			  jQuery("#locationForm .address").val(val);
		  }
		  
		  if(addressType=='route')
		  {
			  jQuery("#locationForm .address").val(jQuery("#locationForm .address").val()+" "+val);
			  //jQuery("#locationForm .address2").val(val);
		  }
		  
		  if(addressType=='locality' || addressType=='sublocality_level_1')
		  {
			  // city
			  jQuery("#locationForm input[name^='city']").val(val);
		  }
		  
		  if(addressType=='administrative_area_level_1')
		  {
			  // state
			   jQuery("#locationForm input[name^='state']").val(val);
		  }
		  
		 if(addressType=='postal_code')
		  {
			  // postcal code
			  jQuery("#locationForm input[name^='zip']").val(val);
		  } 
		  
		  if(addressType=='country')
		  {
			  // postcal code
			 jQuery("#locationForm select[name^='country']").val(val.toLowerCase());
		  } 
		  
		   
        }
		
		voslGetLatLongFromAddressAndPlotMap(jQuery("#find_address").val());
 }
 
function voslGenMarkerId() {
	return '' + (new Date()).getTime();
}
 
 function voslGetLatLongFromAddressAndPlotMap(address)
 {
	var geocoder = new google.maps.Geocoder();
	
	if(voslAddEditListingMarkersArr.length > 0)
	{
		jQuery("#vosl-add-edit-listing-map-admin").gmap3({
			clear: {
			  id: voslAddEditListingMarkersArr[0]
			}
		  });
		 
		 voslAddEditListingMarkersArr.pop(); 
	}
	
	var LatLong = new Array();
	geocoder.geocode( { 'address': address}, function(results, status) {
	
	if (status == google.maps.GeocoderStatus.OK) {
			var latitude = parseFloat(results[0].geometry.location.lat());
			var longitude = parseFloat(results[0].geometry.location.lng());
			jQuery("#locationForm input[name^='latitude']").val(latitude);
			jQuery("#locationForm input[name^='longitude']").val(longitude);
			
			var markerID = voslGenMarkerId();
			voslAddEditListingMarkersArr.push(markerID);
			jQuery("#vosl-add-edit-listing-map-admin").gmap3({
				marker : {
					id : markerID,
					latLng : [latitude, longitude]
				},
			});
			jQuery('#vosl-add-edit-listing-map-admin').gmap3('get').setCenter(new google.maps.LatLng(parseFloat(latitude),parseFloat(longitude)));
			jQuery("#vosl-add-edit-listing-map-admin").gmap3('get').setZoom(14);
		} 
	}); 
 }
 
function initialize_vosl_admin_events_update_settings()
{
	jQuery(".vosl-menu-map-customizations #location_map").change(function () {
		// ajax settings upate
		
		if(jQuery(".vosl-menu-map-customizations #location_map:checked").length)
		{
			jQuery(".vosl_map_dependents").show();
			jQuery(".vosl_map_cust_columns.second").css('display','inline-block');
			jQuery("#map_placeholder_admin").show();
			jQuery("#vosl_listing_column_admin_settings").css('width',jQuery("#vosl_listing_column_width").val()+"px");
			
		}else
		{
			jQuery(".vosl_map_dependents").hide();
			jQuery(".vosl_map_cust_columns.second").css('display','none');
			jQuery("#map_placeholder_admin").hide();
			jQuery("#vosl_listing_column_admin_settings").css('width','97%');
		}
	});
	
	jQuery(document).on('blur', "#vosl_custom_map_popup_width", function () { 
		vosl_update_admin_marker_popup();
	});
	
	jQuery(document).on('blur', "#fndLocationText, #vosl_tags_filter_label", function () { 
		vosl_update_search_customization_options();
	});
	
	jQuery(".vosl-menu-map-customizations #enable_default_cluster").change(function () {
			vosl_update_admin_marker_cluster();
	});
	
	jQuery("#selMiles").change(function () {
			vosl_update_map_listings_ui_admin();
	});
	
	jQuery("#enable_default_tags").change(function () {
			vosl_update_search_customization_options();
	});	
	
	jQuery("#vosl_radius_filter").change(function () {
			vosl_update_search_customization_options();
	});		
}

function initializeAddEditListingsMap(latitude,longitude, zoom)
{
	if(jQuery('#vosl-add-edit-listing-map-admin').length)
	{
		var lat = parseFloat(latitude);
		var long = parseFloat(longitude);
		
		var newLatLng = new google.maps.LatLng(lat,long);
		
		if(latitude=='')
			newLatLng = new google.maps.LatLng(37.09024, -95.712891);
		
		jQuery('#vosl-add-edit-listing-map-admin').gmap3({
		 map:{
			options:{
			 center:newLatLng,
			 zoom:zoom,
			 mapTypeId: google.maps.MapTypeId.ROADMAP,
			 mapTypeControl: true,
			 mapTypeControlOptions: {
			   style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
			 },
			 navigationControl: true,
			 scrollwheel: true,
			 streetViewControl: true
			}
		 }
		});
		
		if(jQuery("#vosl_location_id").length && jQuery("#vosl_location_id").val() > 0 && jQuery("#locationForm input[name^='latitude']").val()!='' && jQuery("#locationForm input[name^='longitude']").val()!='' )
		{
			var markerID = voslGenMarkerId();
			var latitude = parseFloat(jQuery("#locationForm input[name^='latitude']").val());
			var longitude = parseFloat(jQuery("#locationForm input[name^='longitude']").val());
			voslAddEditListingMarkersArr.push(markerID);
			jQuery("#vosl-add-edit-listing-map-admin").gmap3({
				marker : {
					id : markerID,
					latLng : [latitude, longitude]
				},
			});
			jQuery('#vosl-add-edit-listing-map-admin').gmap3('get').setCenter(new google.maps.LatLng(latitude,longitude));
			jQuery("#vosl-add-edit-listing-map-admin").gmap3('get').setZoom(14);
		}
	
	//var vosl_map = jQuery("#vosl-add-edit-listing-map-admin").gmap3("get");
	}
}

function initialize_vosl_admin_events()
{
	initializeAddEditListingsMap('','', 4);
	
	jQuery("#voslRedirectToTags").prev().hide();
	
	jQuery('#upload_image_button').click(function() {
		vosl_image_btn_id = jQuery('#upload_image').attr('id');
		tb_show('', 'media-upload.php?type=image&TB_iframe=true');
		return false;
	});
	
	jQuery('#remove_image_button').click(function() {
		jQuery("#vosl_admin_add_edit_listing_img img").attr('src',jQuery(".vosl_default_hidden_img").html());
		jQuery('#upload_image').val('');
		return false;
	});

	jQuery('.nav-tab-wrapper .nav-tab').click(function(){
		el = jQuery(this);
		elid = el.attr('id');
		jQuery('.vosl-settings-menu-group').hide(); 
		jQuery('.'+elid).show();
	});
	jQuery('.nav-tab-wrapper .nav-tab').click(function(){
		jQuery('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active');
	});
	
	jQuery('.vosladmintab .nav-tab').click(function(){
		voslResizeMapAdminSettings(); 
	});
	
	jQuery( "#vosl_listing_column_admin_settings" ).resizable({
	  resize: function( event, ui ) {
		 var height = Math.round(ui.size.height); 
		 var width = Math.round(ui.size.width);
		 jQuery("#vosl_listing_column_height").val(height);
		 jQuery("#vosl_listing_column_width").val(width);
		 jQuery("#vosl_listing_column_stats").html(width+"x"+height+" px");
	  },
	  stop: function(event, ui) {
		  
	  }
	});
  
   jQuery( "#vosl_search_box_admin_settings" ).resizable({
	  handles: 'e',
	  resize: function( event, ui ) {
		 var height = Math.round(ui.size.height); 
		 var width = Math.round(ui.size.width);
		 jQuery("#vosl_search_box_height").val(height);
		 jQuery("#vosl_search_box_width").val(width);
		 jQuery("#vosl_search_box_stats").html(width+"x"+height+" px");
	  },
	  stop: function(event, ui) {
		  
	  }
	});
	  	  
	  jQuery("#vosl_custom_font_settings").change(function () {
			vosl_load_selected_font_settings();
			// ajax settings upate
			//update_vosl_settings_ajax();
		});
		
	  jQuery( document ).on( "click", ".vosl_admin_listing_delete", function() {
		 		delete_vosl_listing(jQuery(this).attr("listing-id"));
				return false;
		});
		
		jQuery( document ).on( "click", ".vosl_admin_tags_listing_delete", function() {
		 		delete_vosl_tag(jQuery(this).attr("tag-id"));
				return false;
		});
		
		jQuery('#vosl_txtSearchText').keypress(function (e) {
		 var key = e.which;
		 if(key == 13)  // the enter key code
		  {
			vosl_listing_admin_table.search(jQuery("#vosl_txtSearchText").val()).draw() ;
			return false;  
		  }
		});  
		
		jQuery('#vosl_txtSearchTextTags').keypress(function (e) {
		 var key = e.which;
		 if(key == 13)  // the enter key code
		  {
			vosl_tags_admin_table.search(jQuery("#vosl_txtSearchTextTags").val()).draw() ;
			return false;  
		  }
		}); 
		
		jQuery('#vosl_tags_filter').change(function() {
			vosl_listing_admin_table.draw(false);
			return false; 
		}); 	
		
		jQuery( "#vosl_map_custom_center" ).keyup(function() {
		  if(jQuery( "#vosl_map_custom_center" ).val()=='')
		  {
			  jQuery( "#vosl_map_custom_center_lat" ).val('');
			  jQuery( "#vosl_map_custom_center_long" ).val('');
			  // ajax settings upate
			 // update_vosl_settings_ajax();
		  }
		});
		
		jQuery('input[type=radio][name=vosl_map_center_point_type]').change(function() {
			if (this.value == 1) {
				jQuery(".vosl_custom_center_point_container").hide();
			}
			else if (this.value == 0) {
				jQuery(".vosl_custom_center_point_container").show();
			}
			
			// ajax settings upate
			//update_vosl_settings_ajax();
		});
	
	jQuery( document ).on( "click", "#btnSubmitVoslSettings", function() {
		 		update_vosl_settings_ajax();
		});
	
}

function delete_vosl_listing(listing_id)
{
	if(confirm("Do you want to delete this listing?"))
	{
		var data = {
				action: 'vosl_delete_listing',
				loc_id: listing_id
			};
			
		jQuery.post(vosl_admin_ajax_script_url.ajaxurl, data, function(response) {
			
			if(response!='' && response=='OK')
			{
				alert("Listing deleted successfully.");
				vosl_listing_admin_table.search(jQuery("#vosl_txtSearchText").val()).draw() ;
			}
	 	});
	}
}

function delete_vosl_tag(tag_id)
{
	if(confirm("Do you want to delete this tag?"))
	{
		var data = {
				action: 'vosl_delete_tag',
				tag_id: tag_id
			};
			
		jQuery.post(vosl_admin_ajax_script_url.ajaxurl, data, function(response) {
			
			if(response!='' && response=='OK')
			{
				alert("Tag deleted successfully.");
				vosl_tags_admin_table.search(jQuery("#vosl_txtSearchTextTags").val()).draw() ;
			}
	 	});
	}
}

function vosl_load_selected_font_settings()
{
	if(jQuery('#vosl_custom_font_settings').length)
	{
		var str = jQuery('#vosl_custom_font_settings').val();									   
		var res = str.split("::");				
		
		WebFont.load({
			google: {
			  families: [res[0]]
			}
		  });
		
		jQuery("#vosl_sample_preview").css("font-family", res[0]);
		
		// changes in whole UI
		jQuery("#maplist .location-description").css("font-family", res[0]);
		jQuery("#maplist").css("font-family", res[0]);
		jQuery("#zipcodelookup #btnFind").css("font-family", res[0]);
		jQuery("#zipcodelookup #place_address").css("font-family", res[0]);
		jQuery("#zipcodelookup h2").css("font-family", res[0]);
		jQuery(".voslsingletag").css("font-family", res[0]);
		jQuery(".voslsingletag select").css("font-family", res[0]);
	}
}

function voslResizeMapAdminSettings()
{
	var m = jQuery("#map_placeholder_admin").gmap3('get');
	x = m.getZoom();
    c = m.getCenter();
    google.maps.event.trigger(m, 'resize');
    m.setZoom(x);
    m.setCenter(c);
}

function checkToSaveVoslListing()
{
	if(confirm("Do you want to save current listing before going to manage tags?"))
	{
		jQuery("#voslRedirectToTags").val('save');
		
	}else
	{
		jQuery("#voslRedirectToTags").val('nosave');
	}
	
	jQuery("#locationForm").submit();
}

function changeMapCenterAdminSettings() {
  // Get the place details from the autocomplete object.
  geocoder = new google.maps.Geocoder();
  
  geocoder.geocode( { 'address': jQuery("#vosl_map_custom_center").val()}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
        //In this case it creates a marker, but you can get the lat and lng from the location.LatLng
		var map = jQuery('#map_placeholder_admin').gmap3("get");
        map.setCenter(results[0].geometry.location);
      } else {
        alert("Geocode was not successful for the following reason: " + status);
      }
    });
}

function voslBuildDataonAdminMap()
{
	var listItems=jQuery("#maplist .col-lg-3 .locationlist");listItems.each(function(idx,li){var location_id=jQuery(this).attr('id');location_id=location_id.split("_");location_id=location_id[1];
	
		jQuery("#map_placeholder_admin").gmap3({clear:{id:"et_marker_"+location_id}});
		jQuery("#et_marker_"+location_id).parent().remove();
	
	});
	
	listItems=jQuery("#maplist .col-lg-3 .locationlist");listItems.each(function(idx,li){var location_id=jQuery(this).attr('id');location_id=location_id.split("_");location_id=location_id[1];
	
	defaultLat = jQuery("#maplist #location_"+location_id+" .default_center_lat").html();
	defaultLong = jQuery("#maplist #location_"+location_id+" .default_center_long").html();
	
	
	et_add_marker(location_id,parseFloat(jQuery("#maplist #location_"+location_id+" .lat").html()),parseFloat(jQuery("#maplist #location_"+location_id+" .long").html()),'<div id="et_marker_'+location_id+'" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">'+jQuery("#maplist #location_"+location_id+" .callout").html()+'</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div>');
	
	
	});
}

function et_add_marker( marker_order, marker_lat, marker_lng, marker_description ){
			var marker_id = 'et_marker_' + marker_order;
			
			marker_description = marker_description.replace(/\\\//g, "/");
			// added by manoj milani on 13 April 16 to fix the issue for null lat, long
			if(isNaN(marker_lat))
			{
				return;
			}
			
			
			var pinImage = new google.maps.MarkerImage();
			
			if(jQuery("#vosl_custom_map_marker_color_settings").val()!='')
			{
				var pinColor = jQuery("#vosl_custom_map_marker_color_settings").val();
				pinColor = pinColor.replace("#", "");
				var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
				new google.maps.Size(21, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(10, 34));
			}
			
			jQuery("#map_placeholder_admin").gmap3({
				marker : {
					id : marker_id,
					latLng : [marker_lat, marker_lng],
					options: {
						icon: pinImage
					},
					events : {
						click: function( marker ){
							
							jQuery( '.et_marker_info' ).hide();
							jQuery(this).gmap3("get").panToWithOffset( marker.position,0,-100 );
							
							jQuery( '#' + marker_id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 500 );
							jQuery( '#' + marker_id ).css("bottom","15px");
							
						},
						mouseover: function( marker ){
							
						},
						mouseout: function( marker ){
							
						}
					}
				},
				overlay : {
					latLng : [marker_lat, marker_lng],
					options : {
						content : marker_description,
						offset : {
							y:-42,
							x:-203 + parseInt(jQuery('#vosl_hidd_map_popup_offset').val())
						}
					}
				}
			});
			
			if(jQuery(".vosl-menu-map-customizations #enable_default_cluster:checked").length)
			{
				//vosl_update_admin_marker_cluster();
				var lastMarker = jQuery("#map_placeholder_admin").gmap3({
					get: {
					  id: marker_id
					}
				  });
				
				if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
										 // some code..
				}else
				{
					markerCluster.addMarker(lastMarker);
				}
			}
}

function voslInitializeSettingsMapAdmin(latitude,longitude, zoom)
{
	var lat = parseFloat(latitude);
    var long = parseFloat(longitude);
    var newLatLng = new google.maps.LatLng(lat,long);
	
	if(latitude=='')
		newLatLng = new google.maps.LatLng(37.09024, -95.712891);
	
	var map = jQuery('#map_placeholder_admin').gmap3({
	 map:{
		options:{
		 center:newLatLng,
		 zoom:zoom,
		 mapTypeId: google.maps.MapTypeId.ROADMAP,
		 mapTypeControl: true,
		 mapTypeControlOptions: {
		   style: google.maps.MapTypeControlStyle.DROPDOWN_MENU
		 },
		 navigationControl: true,
		 scrollwheel: false,
		 streetViewControl: false
		}
	 }
	});
	
	var map = jQuery('#map_placeholder_admin.voslmapsettingsholder').gmap3("get");
	map.addListener('center_changed', function() {										 
		  var c = map.getCenter();
		  var lat = c.lat();
		  var long = c.lng();
		  
		  jQuery("#vosl_map_custom_center_lat").val(lat);
		  jQuery("#vosl_map_custom_center_long").val(long);
		  
		   // ajax settings upate
		  // flag set so that the ajax function only gets called once in 3 seconds
		  /*if(vosl_admin_map_center_changed==false)
		  {
			  vosl_admin_map_center_changed = true;
			  update_vosl_settings_ajax_without_spinner();
			  setTimeout(function(){  vosl_admin_map_center_changed = false; }, 3000);
		  } */
		  
	  });
	
	map.addListener('zoom_changed', function() {
		var vosl_zoom = map.getZoom();											 	
		jQuery("#vosl_map_zoom_level").val(vosl_zoom);
		jQuery(".vosl_map_stats .vosl_zoom_stats span").html(vosl_zoom);
		 // ajax settings upate
		  // flag set so that the ajax function only gets called once in 3 seconds
		  /*if(vosl_admin_map_center_changed==false)
		  {
			  vosl_admin_map_center_changed = true;
			  update_vosl_settings_ajax_without_spinner();
			  setTimeout(function(){  vosl_admin_map_center_changed = false; }, 3000);
		  } */
  	});
	
	jQuery( "#map_placeholder_admin.voslmapsettingsholder" ).resizable({
	  resize: function( event, ui ) {
		 voslResizeMapAdminSettings();
		 var height = Math.round(ui.size.height); 
		 var width = Math.round(ui.size.width);
		 jQuery("#vosl_map_size_height").val(height);
		 jQuery("#vosl_map_size_width").val(width);
		 jQuery(".vosl_map_stats .vosl_size_stats span").html(width+"x"+height+" (Pixels)");
	  },
	  stop: function(event, ui) {
		  // ajax settings upate
		  //update_vosl_settings_ajax();
	  }
	});
	
	
	// update admin marker cluster
	vosl_update_admin_marker_cluster();
	// This function builds map markers on load
	voslBuildDataonAdminMap();
}

function vosl_update_admin_marker_cluster()
{
	var map = jQuery('#map_placeholder_admin').gmap3("get");
	if(typeof markerCluster == "undefined")
	{
		MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_ = '//cdn.rawgit.com/googlemaps/js-marker-clusterer/gh-pages/images/m';
		var clusterOptions = { zoomOnClick: true }
		markerCluster = new MarkerClusterer(map,null,clusterOptions);
		//markerCluster.clearMarkers();
		//voslBuildDataonAdminMap();
		
	}else if (typeof markerCluster != "undefined" && jQuery(".vosl-menu-map-customizations #enable_default_cluster:checked").length)
	{
		markerCluster.clearMarkers();
		voslBuildDataonAdminMap();
		
	}else if (typeof markerCluster != "undefined" && !jQuery(".vosl-menu-map-customizations #enable_default_cluster:checked").length)
	{
		markerCluster.clearMarkers();
		voslBuildDataonAdminMap();
	}
}