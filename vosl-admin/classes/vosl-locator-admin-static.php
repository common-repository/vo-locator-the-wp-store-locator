<?php
class VoStoreLocator_AdminStatic
{
	public static function initHooks() {
        add_action( 'vosl_populate_tags_dropdown', array(__class__, 'vosl_populate_tags_dropdown_ui'), 50, 2 );
	    add_action( 'vosl_associate_tags_listings', array(__class__, 'vosl_associate_tags_listings_ui'), 50, 2 );
		add_action( 'vosl_listing_custom_marker_icon', array(__class__, 'vosl_listing_custom_marker_icon'), 50, 2 );
		add_filter( 'vosl_filter_front_tags', array(__class__, 'vosl_filter_front_tags'), 50, 1 );
		// add shortcode to visual composer/wp bakery
		add_action( 'vc_before_init', array(__class__, 'vosl_vc_shortcode_render') );
		// add shortcode to default worpress tinymce editor
		add_action('admin_head', array(__class__, 'vosl_shortcode_mce_button') );
		add_action('vosl_add_custom_marker_icon_table_heading', array(__class__, 'vosl_add_custom_marker_icon_table_heading') );
		add_filter('vosl_get_custom_marker_icon', array(__class__, 'vosl_get_custom_marker_icon') );
		add_action('vosl_show_custom_marker_icon_field', array(__class__, 'vosl_show_custom_marker_icon_field') );
		add_action('vosl_general_settings_fields', array(__class__, 'vosl_general_settings_fields'), 50, 2 );
		add_action( 'vosl_associate_custom_fields_listings', array(__class__, 'vosl_associate_custom_fields_listings'), 50, 2 );
		add_action('vosl_add_custom_fields_listing_front', array(__class__, 'vosl_add_custom_fields_listing_front') );
		add_filter( 'vosl_get_listing_load_size', array(__class__, 'vosl_get_listing_load_size'), 50, 1 );
    }
	
	public static function vosl_get_listing_load_size($instance_id)
	{ 
		$vosl_listing_load_size = vosl_data('vosl_listing_load_size');
		
		if($vosl_listing_load_size=='')
			$vosl_listing_load_size = 100;
			
		return $vosl_listing_load_size;	
	}
	
	public static function vosl_add_custom_fields_listing_front($row)
	{
		global $vosl_base;
		
		 if($row['description']!=''){ ?>
	<div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/description.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><?php echo $row['description']?></div></div>
	<?php } 
    
		if($row['url']!=''){ ?>
        <div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/URL.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><a target="_blank" href="<?php echo $row['url']?>"><?php echo $row['url_text']?></a></div></div>
        <?php } ?>
        <?php if($row['phone']!=''){ ?>
        <div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/phone.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><?php echo $row['phone']?></div></div>
        <?php } ?>
        <?php if($row['fax']!=''){ ?>
        <div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/fax.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><?php echo $row['fax']?></div></div>
        <?php } ?>
        <?php if($row['email']!=''){ ?>
        <div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/email.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><a href="mailto:<?php echo $row['email']?>"><?php echo $row['email']?></a></div></div>
        <?php } ?>
        <?php if($row['hours']!=''){ ?>
        <div class="voslrow"><div class="col-md-1 col-sm-1"><strong><img src='<?php echo $vosl_base?>/images/icons/hours.png' /></strong></div><div class="col-md-10 col-sm-10" style="padding-left:0px;"><?php echo $row['hours']?></div></div>
        <?php }
	}
	
	public static function vosl_general_settings_fields($vosl_map_api_key,$vosl_show_love)
	{
		?>
        <tr valign="top">
        <th scope="row"><?php echo __("Google Maps API Key", VOSL_TEXT_DOMAIN); ?></th>
        <td>
        <input type="text" value="<?php echo esc_html($vosl_map_api_key);?>" id="vosl_map_api_key" name="vosl_map_api_key" style="width:350px;" />
        <span class="description" style="display:block;"><a href="http://www.vitalorganizer.com/vo-locator-documentation/#mapapi" target="_blank"><?php echo __("Show me how", VOSL_TEXT_DOMAIN); ?></a> | <a href="https://console.developers.google.com/flows/enableapi?apiid=maps_backend,geocoding_backend,places_backend&keyType=CLIENT_SIDE&reusekey=true" target="_blank"><?php echo __("Get Api Key", VOSL_TEXT_DOMAIN); ?></a></span>
        </fieldset></td>
        </tr>
        
        <?php /*?><tr valign="top">
        <th scope="row"><?php echo __("Google Maps Server API Key", VOSL_TEXT_DOMAIN); ?></th>
        <td>
        <input type="text" value="<?php echo esc_html($vosl_google_map_server_api_key);?>" id="vosl_google_map_server_api_key" name="vosl_google_map_server_api_key" style="width:350px;" />
        <span class="description" style="display:block;"><a href="http://www.vitalorganizer.com/vo-locator-documentation/#mapapi" target="_blank"><?php echo __("Show me how", VOSL_TEXT_DOMAIN); ?></a> | <a href="https://console.developers.google.com/flows/enableapi?apiid=geocoding_backend&keyType=SERVER_SIDE&reusekey=true" target="_blank"><?php echo __("Get Server Api Key", VOSL_TEXT_DOMAIN); ?></a></span>
        </fieldset></td>
        </tr><?php */?>
        
        <tr valign="top">
        <th scope="row"><?php echo __("Show some love", VOSL_TEXT_DOMAIN); ?></th>
        <td> <fieldset><legend class="screen-reader-text"><span></span></legend><label for="users_can_register">
        <input type="checkbox" value="1" id="vosl_show_love" name="vosl_show_love" <?php if($vosl_show_love==1){ ?> checked="checked" <?php } ?>>
        <?php echo __("Enable branding on front-end", VOSL_TEXT_DOMAIN); ?></label>
        </fieldset></td>
        </tr>
        <?php
	}
	
	public static function vosl_show_custom_marker_icon_field($listing_id)
	{
		?>
        <tr><td style='padding-left:0px' class='nobottom' colspan="3">
		<strong><?=__("Custom Marker Pin", VOSL_TEXT_DOMAIN)?></strong><br />  <a href="http://www.vitalorganizer.com/product/vo-store-locator-pro-add-on?utm_source=plugin&amp;utm_medium=pluginUI&amp;utm_campaign=custommarkerpin" target="_blank"><?=__("Need Custom Pins? Get the Pro-Add-On", VOSL_TEXT_DOMAIN)?></a></td></tr>
        <?php
	}
	
	public static function vosl_get_custom_marker_icon($listing_id)
	{
		global $wpdb;
		
		$output = self::vosl_listing_custom_marker_icon($listing_id);
		$output['marker_color'] = '<div style="background:'.$output['marker_color'].'; width:20px; height: 20px; margin:0 auto;"></div>';
		
		return $output['marker_color'];
	}
	
	public static function vosl_add_custom_marker_icon_table_heading()
	{
		?>
		<th><?php echo __("Pin Color", VOSL_TEXT_DOMAIN); ?></th>
        <?php
	}
	
	public static function vosl_shortcode_mce_button() {

	    if ( !current_user_can( 'edit_posts' ) and !current_user_can( 'edit_pages' ) ) {
			return;
		  }
		  // Check if WYSIWYG is enabled
		  if ( 'true' == get_user_option( 'rich_editing' ) ) {
			add_filter( 'mce_external_plugins', array(__class__, 'vosl_shorcode_tinymce_plugin') );
			add_filter( 'mce_buttons', array(__class__, 'vosl_shorcode_register_mce_button') );
		  }
	}
	
	public static function vosl_shorcode_tinymce_plugin( $plugin_array ) {
	  global $vosl_base;	
	  $plugin_array['vosl_custom_mce_button'] = $vosl_base .'/js/vosl_mce_editor_plugin.js';
	  return $plugin_array;
	}
	
	public static function vosl_shorcode_register_mce_button( $buttons ) {
	  array_push( $buttons, 'vosl_custom_mce_button' );
	  return $buttons;
	}
	
	public static function vosl_vc_shortcode_render() {

	    global $vosl_base;
	
	    vc_map( array(
		  "name" => __( "VO Locator", VOSL_TEXT_DOMAIN ),
		  "base" => "VO-LOCATOR",
		  "class" => "",
		  "category" => __( "Content", VOSL_TEXT_DOMAIN),
		  'show_settings_on_create' => false,
		  "icon" => $vosl_base . "/images/logo.small.png", // Simply pass url to your icon here
	   ) );
	}
	
	public static function vosl_filter_front_tags($tags)
	{
		if(!is_array($tags) and is_int($tags))
		{
			return " AND id IN (SELECT store_id FROM ".VOSL_TAGS_ASSOC_TABLE." WHERE tag_id = ".(int)$tags.") ";
		}
	}
	
	public static function vosl_listing_custom_marker_icon($listing_id)
	{
		global $wpdb;
		
		$vosl_custom_map_marker_color = vosl_data("vosl_custom_map_marker_color");
	
		// we only assign single tag in our free version, so check to see if their is marker color for tag
		$row = $wpdb->get_row( $wpdb->prepare( "SELECT t.tag_color FROM ".VOSL_TAGS_TABLE." t JOIN ".VOSL_TAGS_ASSOC_TABLE." tl ON (tl.tag_id = t.id) WHERE tl.store_id = %d ", $listing_id ), ARRAY_A );
		
		$output = array();
		$output['marker_color'] = '';
		
		if($vosl_custom_map_marker_color!='')
		{
			$output['marker_color'] = $vosl_custom_map_marker_color;
		}
		
		if($row['tag_color']!='')
		{
			$output['marker_color'] = $row['tag_color'];	
		}
		
		$output['marker_icon'] = '';
		return $output;
	}
	
	/*public static function reInitializeVoslMap()
	{
		$vosl_lat = '';
		$vosl_long = '';
		$vosl_map_zoom_level = '';
		
		$location_value = vosl_data('sl_location_map');
		$vosl_map_zoom_level = vosl_data('vosl_map_zoom_level');
		$vosl_map_size_width = vosl_data('vosl_map_size_width');
		$vosl_map_size_height = vosl_data('vosl_map_size_height');
		$vosl_listing_column_width = vosl_data('vosl_listing_column_width');
		$vosl_listing_column_height = vosl_data('vosl_listing_column_height');
		$vosl_map_custom_center = vosl_data('vosl_map_custom_center');
		$vosl_search_box_width = vosl_data('vosl_search_box_width');
		$current_location_lookup = vosl_data('sl_current_location_lookup');
		
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
			
		if($vosl_map_zoom_level=='')
			$vosl_map_zoom_level = 4;
			
		$json = vosl_data('vosl_map_custom_center_coordinates');
		$json_array = json_decode($json, true);
		
		if(is_array($json_array) and !empty($json_array))
		{
			$vosl_lat = $json_array['lat'];
			$vosl_long = $json_array['long'];
		}
		
		if($current_location_lookup==1)
		{
			// default to null
			$vosl_lat = '';
			$vosl_long = '';
		}
		
		?>
        <?php if($location_value==1){ ?>
        <script type="text/javascript">
		<?php if($vosl_lat!='' and $vosl_long!=''){ ?>
		var vosl_map_object = {"lat":"<?=$vosl_lat?>","long":"<?=$vosl_long?>"};
		<?php } ?>
		jQuery(document).ready( function($) {
			var map = jQuery("#map_placeholder").gmap3('get');
			var newLatLng = map.getCenter();
			
			<?php if($vosl_lat!='' and $vosl_long!=''){ ?>
				newLatLng = new google.maps.LatLng(parseFloat(vosl_map_object.lat), parseFloat(vosl_map_object.long));	
			<?php } ?>
			
			
			jQuery('#map_placeholder').gmap3({
			 map:{
				options:{
				 center:newLatLng,
				 zoom: parseInt(<?=$vosl_map_zoom_level?>),
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
        });
        </script>
        <?php } ?>
        <style type="text/css">
        
		@media (min-width: 1025px) {
			#maplist .overflowscroll{ height: <?=$vosl_listing_column_height?>px !important;width: <?=$vosl_listing_column_width?>px !important; max-height: <?=$vosl_listing_column_height?>px !important; }
			.voslpmapcontainer{ height: <?=$vosl_map_size_height?>px !important;width: <?=$vosl_map_size_width?>px !important; }
			#map_placeholder{height: <?=$vosl_map_size_height?>px !important;width: <?=$vosl_map_size_width?>px !important;}
			.voslpsearch{ width: <?=$vosl_search_box_width?>px !important; }
		}
        </style>
        <?php
	}*/
	
    public static function vosl_populate_tags_dropdown_ui($tags = array(), $store_id)
	{
		global $wpdb;
		$rows = $wpdb->get_results( "SELECT id, tag_name FROM ".VOSL_TAGS_TABLE." order by tag_name", ARRAY_A );
		
		if($store_id > 0)
			$extra = '-'.$store_id;
		else
			$extra = '';		
		
		$html = '<select name="voslSelTags'.$extra.'">
			<option value="">--'.__("Select Tag", VOSL_TEXT_DOMAIN).'--</option>';
		foreach($rows as $row){ 
			
			$selected = '';
			
			if(!empty($tags) and in_array($row['id'],$tags))
			{
				$selected = ' selected="selected" ';
			}
		
			$html .= '<option value="'.$row['id'].'" '.$selected.'>'.$row['tag_name'].'</option>';
		 }     
		$html .= '</select>';
		
		return $html;
	}
	
	public static function vosl_associate_tags_listings_ui($store_id, $tag_id)
	{
		global $wpdb;
	
		$wpdb->query($wpdb->prepare("DELETE FROM ".VOSL_TAGS_ASSOC_TABLE." WHERE store_id=%d", (int)$store_id));
		if($tag_id > 0)
		{
			$q = $wpdb->prepare("INSERT INTO ".VOSL_TAGS_ASSOC_TABLE." (store_id, tag_id) VALUES (%d, %d)", $store_id, $tag_id); 
			$wpdb->query($q);
		}
	}
	
	public static function vosl_associate_custom_fields_listings($store_id, $custom_fields_input)
	{
		global $wpdb, $vosl_admin_classes_dir;
		
		$vosl_locator_admin = new VoStoreLocator_Admin();
		$vosl_custom_fields = $vosl_locator_admin->get_store_custom_fields();
	
		foreach($vosl_custom_fields as $field)
		{
			if(isset($custom_fields_input['custom_'.$field['field_name']]))
			{
				// add
				$wpdb->query($wpdb->prepare("INSERT INTO ".VOSL_CUSTOM_FIELDS_ASSOC_TABLE." (custom_field_id, custom_field_value, store_id) VALUES (%d, %s, %d)", $field['field_id'],$custom_fields_input['custom_'.$field['field_name']],$store_id));
				
			}else if( isset($custom_fields_input['custom_'.$field['field_name']."_".$field['field_id']]) )
			{
				$row = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM ".VOSL_CUSTOM_FIELDS_ASSOC_TABLE." WHERE custom_field_id = %d AND store_id = %d ", $field['field_id'], $store_id ), ARRAY_A );
				
				// update
				if($row['id'] > 0)
				{
					$wpdb->query( $wpdb->prepare( "UPDATE ".VOSL_CUSTOM_FIELDS_ASSOC_TABLE." SET custom_field_value = %s WHERE custom_field_id = %d AND store_id = %d ", $custom_fields_input['custom_'.$field['field_name']."_".$field['field_id']], $field['field_id'], $store_id ) );
					
				}else
				{
					// if new custom field is added later
					$wpdb->query($wpdb->prepare("INSERT INTO ".VOSL_CUSTOM_FIELDS_ASSOC_TABLE." (custom_field_id, custom_field_value, store_id) VALUES (%d, %s, %d)", $field['field_id'],$custom_fields_input['custom_'.$field['field_name']."_".$field['field_id']],$store_id));
				}
			}
		}
	}
}
?>