var is_mobile = false;
var vosl_single_tag = '';
var vosl_multiple_tags = '';
var showMarkerCluster = 1;
var voslReInitialzedMap = false;
var voslRadiusVal = 0;

jQuery(document).ready( function($) {
	
	showMarkerCluster = $( "#vosl_enable_cluster" ).val();
	showMarkerCluster = parseInt(showMarkerCluster);
	
	$( "#vosl_single_tag" ).change(function() {
 		 vosl_single_tag = $(this).val();
		 $("#btnFind").click();
	});
			
	$("#place_address").keydown(function(event){
		if( event.keyCode == 13 ) {
		  event.preventDefault();
		  $("#btnFind").click();
		  return false;
		}
 	 });							 
								 
    if( $('#map_placeholder').css('display')=='none') {
        is_mobile = true;       
    }

	$("#btnFind").click( function() {
		
		//console.log(vosl_single_tag);
		$("#loadingIcon").show();
		$("#vosl-no-locations").remove();
		
		if($('#vosl_single_tag').length && $("#vosl_single_tag").val() != '')
		{
			vosl_single_tag = $("#vosl_single_tag").val();
		}
		
		if(vosl_single_tag!='')
		{
			var data = {
				action: 'find_locations',
				address: jQuery("#place_address").val(),
				tag_id: vosl_single_tag
			};	
			
		}else if(vosl_multiple_tags!='')
		{
			var data = {
				action: 'find_locations',
				address: jQuery("#place_address").val(),
				multiple_tags: vosl_multiple_tags
			};
			
		}else
		{
			var data = {
				action: 'find_locations',
				address: jQuery("#place_address").val()
			};
		}
		
		if(jQuery('#vosl_instance_id').length)
			data["instance_id"] = jQuery('#vosl_instance_id').val();
			
		data["radius_val"] = voslRadiusVal;	
		
		// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
	 	$.post(the_ajax_script.ajaxurl, data, function(response) {
			
			if(response!='')
			{
				if(response!='NO')
				{
					var defaultLat = '';
					var defaultLong = '';
					
					vosl_clear_all_markers();
					
					if(showMarkerCluster && jQuery("#sl_location_map").val()==1)
					{
						try {
							markerCluster.clearMarkers();
							var vosl_map = jQuery("#map_placeholder").gmap3("get");
							var clusterOptions = { zoomOnClick: true }
							markerCluster = new MarkerClusterer(vosl_map,null,clusterOptions);
						}
						catch(err) {
							console.log(err.message);
						}
					}
					 
					jQuery("#maplist .col-lg-3 .locationlist").remove();
					jQuery( "#load_more_list" ).before( response );
					jQuery( "#load_more_list" ).show();
					
					listItems = $("#maplist .col-lg-3 .locationlist");
					listItems.each(function(idx, li) {
					   	var location_id = jQuery(this).attr('id');
						location_id = location_id.split("_"); 
						location_id = location_id[1];
						
						defaultLat = jQuery("#maplist #location_"+location_id+" .default_center_lat").html();
						defaultLong = jQuery("#maplist #location_"+location_id+" .default_center_long").html();
						
						if(jQuery("#sl_location_map").val()==1)
						{
							et_add_marker(location_id,parseFloat(jQuery("#maplist #location_"+location_id+" .lat").html()),parseFloat(jQuery("#maplist #location_"+location_id+" .long").html()),'<div id="et_marker_'+location_id+'" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">'+jQuery("#maplist #location_"+location_id+" .callout").html()+'</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div>');
						}
										
					});	
					
					if(jQuery("#sl_location_map").val()==1 && voslReInitialzedMap==false && defaultLat!='' && defaultLong!='')
					{
						
						try {
							$('#map_placeholder').gmap3('get').setCenter(new google.maps.LatLng(parseFloat(defaultLat),parseFloat(defaultLong)));
						}
						catch(err) {
							console.log(err.message);
						}
					}
					//
					
					// check to see if all listing is shown, if yes, then hide the load more link
					/*if($("#maplist .col-lg-3 .locationlist").length == $( "#maplist .col-lg-3 .locationlist" ).first().attr('data-count'))
						jQuery( "#load_more_list" ).hide();*/
					if($("#maplist .col-lg-3 .locationlist").length == $( "#maplist .col-lg-3" ).find(".locationlist").eq(0).attr('data-count'))
						jQuery( "#load_more_list" ).hide();	
						
				}else
				{
					vosl_clear_all_markers();
					$( "<div id='vosl-no-locations'>No Locations Found</div>" ).insertBefore( "#maplist #load_more_list" );
					jQuery( "#load_more_list" ).hide();
					jQuery("#maplist .col-lg-3 .locationlist").remove();
				}
			}else
			{
				$( "<div id='vosl-no-locations'>No Locations Found</div>" ).insertBefore( "#maplist #load_more_list" );
				jQuery( "#load_more_list" ).hide();
			}
			
			$("#loadingIcon").hide();
	 	});
	 	return false;
	});
	
	//jQuery( "#load_more_list" ).live( "click", function()
	jQuery(document).on('click', '#load_more_list', function() 
   	{
		$("#loadingIcon").show();	
		//var lastOffset = jQuery("#maplist .col-lg-3 .locationlist").last();
		var lastOffset = jQuery("#maplist .col-lg-3 .locationlist").filter(':last');
		lastOffset = lastOffset.attr('data-offset');
		
		if($('#vosl_single_tag').length && $("#vosl_single_tag").val() != '')
		{
			vosl_single_tag = $("#vosl_single_tag").val();
		}
		
		if(vosl_single_tag!='')
		{
			var data1 = {
				action: 'load_more_locations',
				address: jQuery("#place_address").val(),
				tag_id: vosl_single_tag,
				offset: lastOffset
			};	
			
		}else if(vosl_multiple_tags!='')
		{
			var data1 = {
				action: 'load_more_locations',
				address: jQuery("#place_address").val(),
				multiple_tags: vosl_multiple_tags,
				offset: lastOffset
			};
			
		}else
		{
			var data1 = {
				action: 'load_more_locations',
				address: jQuery("#place_address").val(),
				offset: lastOffset
			};
		}
		
		if(jQuery('#vosl_instance_id').length)
			data1["instance_id"] = jQuery('#vosl_instance_id').val();
			
		data1["radius_val"] = voslRadiusVal;	
		
		$.post(the_ajax_script.ajaxurl, data1, function(response) {
			
			if(response!='')
			{
				var listItems = $("#maplist .col-lg-3 .locationlist");
				jQuery( "#load_more_list" ).before( response );
				
				listItems = $("#maplist .col-lg-3 .locationlist");
				listItems.each(function(idx, li) {
					var currentLoopOffset = jQuery(this).attr('data-offset');											
					var location_id = jQuery(this).attr('id');
					location_id = location_id.split("_"); 
					location_id = location_id[1];
					
					if(jQuery("#sl_location_map").val()==1)
					{
						if(parseInt(currentLoopOffset) > parseInt(lastOffset))
						{
							et_add_marker(location_id,parseFloat(jQuery("#maplist #location_"+location_id+" .lat").html()),parseFloat(jQuery("#maplist #location_"+location_id+" .long").html()),'<div id="et_marker_'+location_id+'" class="et_marker_info"><div class="location-description"> <div class="location-title"><div class="listing-info">'+jQuery("#maplist #location_"+location_id+" .callout").html()+'</div> </div> <div class="location-rating"></div> </div> <!-- .location-description --> </div>');
						}
					}
									
				});
				
				// check to see if all listing is shown, if yes, then hide the load more link
				/*if($("#maplist .col-lg-3 .locationlist").length == $( "#maplist .col-lg-3 .locationlist" ).first().attr('data-count'))
					jQuery( "#load_more_list" ).hide();*/
				if($("#maplist .col-lg-3 .locationlist").length == $( "#maplist .col-lg-3" ).find(".locationlist").eq(0).attr('data-count'))
					jQuery( "#load_more_list" ).hide();	
				
			}else
			{
				jQuery("#load_more_list").hide();
			}
			
			$("#loadingIcon").hide();
	 	});
	 	return false;
	});
	
	//jQuery( "#maplist .col-lg-3 .locationlist" ).live( "click", function()
	jQuery(document).on('click', '#maplist .col-lg-3 .locationlist', function() 
   	{
		jQuery("#maplist .col-lg-3 .locationlist").removeClass("active");	
		jQuery("#maplist .col-lg-3 .locationlist .locationdetails").hide();
		var location_id = jQuery(this).attr('id');
		location_id = location_id.split("_"); 
		location_id = location_id[1];
			
		if(jQuery("#sl_location_map").val()==0)
		{
			if(jQuery("#location_"+location_id+" .locationdetails").is(":hidden"))
			{
				jQuery("#location_"+location_id+" .locationdetails").fadeIn("slow");
				
			}else
			{
				jQuery("#location_"+location_id+" .locationdetails").fadeOut("slow");
			}
			
		}else if(jQuery("#sl_location_map").val()==1 && is_mobile==false)
		{
			var lastMarker = $("#map_placeholder").gmap3({
				get: {
				  id: "et_marker_"+location_id
				}
			  });
			
			// added by manoj milani on 13 April 16 to fix the issue for null lat, long
			if(lastMarker==false)
				return;
			
			jQuery( '.et_marker_info' ).hide();
			var marker_id = 'et_marker_' + location_id;
			jQuery("#map_placeholder").gmap3("get").panToWithOffset(lastMarker.position, 0, -100);
			jQuery( '#' + marker_id ).css( { 'display' : 'block', 'opacity' : 0 } ).stop(true,true).animate( { bottom : '15px', opacity : 1 }, 500 );
			
		}else if(jQuery("#sl_location_map").val()==1 && is_mobile==true)
		{			
			if(jQuery("#location_"+location_id+" .locationdetails").is(":hidden"))
			{
				jQuery("#location_"+location_id+" .locationdetails").fadeIn("slow");
				
			}else
			{
				jQuery("#location_"+location_id+" .locationdetails").fadeOut("slow");
			}
		}
		
		jQuery("#maplist .col-lg-3 #location_"+location_id).addClass("active");
		
   	});
});

function vosl_clear_all_markers()
{
	var listItems = jQuery("#maplist .col-lg-3 .locationlist");
	listItems.each(function(idx, li) {		
	
		var location_id = jQuery(this).attr('id');
		location_id = location_id.split("_"); 
		location_id = location_id[1];
		
		if(jQuery("#sl_location_map").val()==1)
		{
			jQuery("#map_placeholder").gmap3({
				clear: {
				  id: "et_marker_"+location_id
				}
			  });
			  
			jQuery("#et_marker_"+location_id).parent().remove(); 
		}
						
	});	
}
	
function closeMarker(order)
{
	var marker_id = 'et_marker_' + order;
	jQuery( '#' + marker_id ).stop(true,true).animate( { bottom : '50px', opacity : 0 }, 500, function() {
		jQuery(this).css( { 'display' : 'none' } );
	} );
}

function initializeLocationMap(latitude,longitude,zoom_level)
{
	var lat = parseFloat(latitude);
    var long = parseFloat(longitude);
    var newLatLng = new google.maps.LatLng(lat,long);
	
	if(latitude=='')
		newLatLng = new google.maps.LatLng(37.09024, -95.712891);
	
	jQuery('#map_placeholder').gmap3({
	 map:{
		options:{
		 center:newLatLng,
		 zoom:zoom_level,
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
	
	var vosl_map = jQuery("#map_placeholder").gmap3("get");
	
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
		 // some code..
   }else
   {
	   	if(showMarkerCluster)
		{
			MarkerClusterer.prototype.MARKER_CLUSTER_IMAGE_PATH_ = '//cdn.rawgit.com/googlemaps/js-marker-clusterer/gh-pages/images/m';
			var clusterOptions = { zoomOnClick: true }
			markerCluster = new MarkerClusterer(vosl_map,null,clusterOptions);
		}
   }
}

function et_add_marker( marker_order, marker_lat, marker_lng, marker_description ){
			var marker_id = 'et_marker_' + marker_order;
			
			marker_description = marker_description.replace(/\\\//g, "/");
			// added to fix backslash in the callout
			marker_description = marker_description.replace(/\\/g, '');
			// added by manoj milani on 13 April 16 to fix the issue for null lat, long
			if(isNaN(marker_lat))
			{
				return;
			}
			
			var pinImage = "";
			
			/*if(jQuery("#vosl_custom_map_marker_color").val()!='')
			{
				var pinColor = jQuery("#vosl_custom_map_marker_color").val();
				pinColor = pinColor.replace("#", "");
				var pinImage = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
				new google.maps.Size(21, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(10, 34));
			}*/
			if(jQuery("#maplist .col-lg-3 .locationlist#location_"+marker_order+" .vosl_marker_icon").text()!='')
			{
				var pinImage = new google.maps.MarkerImage(jQuery("#maplist .col-lg-3 .locationlist#location_"+marker_order+" .vosl_marker_icon").text());
				
			}else if(jQuery("#maplist .col-lg-3 .locationlist#location_"+marker_order+" .vosl_marker_color").text()!='')
			{
				var pinColor = jQuery("#maplist .col-lg-3 .locationlist#location_"+marker_order+" .vosl_marker_color").text();
				pinColor = pinColor.replace("#", "");
				var pinImage = new google.maps.MarkerImage("//chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
				new google.maps.Size(21, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(10, 34));
				
			}else if(jQuery("#vosl_custom_map_marker_color").val()!='')
			{
				var pinColor = jQuery("#vosl_custom_map_marker_color").val();
				pinColor = pinColor.replace("#", "");
				var pinImage = new google.maps.MarkerImage("//chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
				new google.maps.Size(21, 34),
				new google.maps.Point(0,0),
				new google.maps.Point(10, 34));
			}
			
			jQuery("#map_placeholder").gmap3({
				marker : {
					id : marker_id,
					latLng : [marker_lat, marker_lng],
					options: {
						icon: pinImage
					},
					events : {
						click: function( marker ){
							if ( et_active_marker ){
								et_active_marker.setAnimation( null );
								
							}
							et_active_marker = marker;
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
			
			if(showMarkerCluster)
			{
				var lastMarker = jQuery("#map_placeholder").gmap3({
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
		
function showDrivingDirections(address)
{
	if(confirm("Would you like driving directions from your current location?"))
	{
		window.open(address);
		
	}else
	{
		address += "&saddr="+jQuery( '#place_address' ).val();
		window.open(address);
	}
}