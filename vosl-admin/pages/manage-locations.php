<?php
//error_reporting(E_ALL ^ E_NOTICE);  
global $vosl_admin_classes_dir;

require_once $vosl_admin_classes_dir . '/vosl-locator-admin.php';
$vosl_locator_admin = new VoStoreLocator_Admin();
$vosl_tags = $vosl_locator_admin->get_store_tags(); 

if( isset($_POST['dele']) and $_POST['dele']==1)
{
	$wpdb->query("TRUNCATE TABLE ".VOSL_TABLE); 
	do_action("voslp_clean_all_custom_fields");	
}

// action for checking if the import file is submitted and file is correct
do_action("vosl_import_link");
do_action("vosl_export_process");	
?>
<div class='wrap'>
<div style="float:left; width:100%; "><div class="vosllogo"><img src="<?php echo VOSL_BASE.'/images/logo.small.png'; ?>" /></div><h1 style="display:inline-block; margin-left:10px; padding-top:0px;"><?php echo __("VO Locator", VOSL_TEXT_DOMAIN)?></h1></div>

<div style="padding-left: 7px; float:left; width:100%; margin-bottom:10px;"><a href="http://vitalorganizer.com?utm_source=plugin&amp;utm_medium=pluginUI&amp;utm_campaign=logoIcon" target="_blank">Learn more about Vital Organizer</a>
<?php do_action("vosl_download_import_template_link"); ?>
</div>

<?php if(empty($_GET['edit'])){ 

print "<input type='button' value='".__("Add Listing", VOSL_TEXT_DOMAIN)."' class='button-primary' onclick=\"location.href='".VOSL_ADD_LOCATIONS_PAGE."'\">";

if (!defined('VOSLP_VERSION')) {
?>
<div class="voslpromotelink"><a href="http://www.vitalorganizer.com/product/vo-store-locator-pro-add-on?utm_source=plugin&amp;utm_medium=pluginUI&amp;utm_campaign=listings" target="_blank"><?php echo __("Need To Import? Check out our Pro Add-On.", VOSL_TEXT_DOMAIN); ?></a></div>
<?php
}

// action for export button
do_action("vosl_export_button_show");
do_action('vosl_import_button_show');

?>
<h2><div style="float:left;"><?php echo __("Listings", VOSL_TEXT_DOMAIN); ?></div><div class="listingssearchbox"><form method="post" name="frmSearch" action="<?php echo str_replace(array("&paged=".$_REQUEST['paged'],"&al=1"), array('',''),$_SERVER['REQUEST_URI']); ?>"><input type="text" name="vosl_txtSearchText" id="vosl_txtSearchText" value="<?php echo $_SESSION['vosl-txtSearchText']?>" />&nbsp;&nbsp;<input type="button" value="<?php echo __("Search", VOSL_TEXT_DOMAIN); ?>" name="btnSearch" id="btnSearch" class='button-primary' /></form></div></h2>
<?php }else if(!empty($_GET['edit'])){ ?>
<h2><?php echo __("Edit Listing", VOSL_TEXT_DOMAIN); ?></h2>
<?php
}

?>
<div class="search_filter">
	
    <div class="vosl_fields_filter">
		<?php echo __("Search By: ", VOSL_TEXT_DOMAIN); ?>
        <select name="vosl_search_filter" id="vosl_search_filter">
            <option value="">--<?php echo __("Select Field", VOSL_TEXT_DOMAIN); ?>--</option>
            <option value="store_name"><?php echo __("Name", VOSL_TEXT_DOMAIN); ?></option>
            <option value="address"><?php echo __("Address", VOSL_TEXT_DOMAIN); ?></option>
            <option value="city"><?php echo __("City", VOSL_TEXT_DOMAIN); ?></option>
            <option value="state"><?php echo __("State", VOSL_TEXT_DOMAIN); ?></option>
            <option value="zip"><?php echo __("Zipcode/Postal Code", VOSL_TEXT_DOMAIN); ?></option>
            <?php do_action("vosl_add_custom_fields_to_search_dropdown_filter_admin"); ?>
        </select>
    </div>   
    
    <div class="tags_filter"><?php echo __("Filter By Tags: ", VOSL_TEXT_DOMAIN); ?>
        <select name="vosl_tags_filter" id="vosl_tags_filter">
            <option value="">--<?php echo __("Select Tag", VOSL_TEXT_DOMAIN); ?>--</option>
            <?php foreach($vosl_tags as $tag){ ?>
            <option value="<?=$tag['id']?>"><?=$tag['tag_name']?></option>
            <?php } ?>
        </select>
    </div>
    
</div>
<?php

require_once(VOSL_ACTIONS_PATH."/processLocationData.php");

print "<table style='width:100%'><tr><td>";
print "<div class='mng_loc_forms_links'>";

if (empty($_GET['q'])){ $_GET['q']=""; }
$search_value = ($_GET['q']==="")? "Search" : vosl_comma(stripslashes($_GET['q'])) ;
print "</div>";

print "</div>";
print "</td><td>";

//for search links
	$where = '';
	$num_per_page=$vosl_vars['admin_locations_per_page']; //edit this to determine how many locations to view per page of 'Manage Locations' page
	/*if ($numMembers2!=0) {include(VOSL_INCLUDES_PATH."/search-links.php");}*/
//end of for search links

print "</td></tr></table>";
?>
<div class="voslmodal"></div>
<?php
print "<form name='locationForm' id='locationForm' method='post'><input type='hidden' name='dele' id='dele' value='0' /><input type='hidden' name='loc_ids' id='loc_ids' /> ";

?>

<table id="vosl_manage_listings_dt" class="display" cellspacing="0" width="100%">
        <thead>
            <tr>
            	
                <th><?php echo __("ID", VOSL_TEXT_DOMAIN); ?></th>
                <?php do_action('vosl_show_selection_checkboxes'); ?>
                <?php do_action('vosl_add_custom_marker_icon_table_heading'); ?>
                <th><?php echo __("Name", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("Address", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("City", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("State", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("Zip", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("Latitude", VOSL_TEXT_DOMAIN); ?>, <?php echo __("Longitude", VOSL_TEXT_DOMAIN); ?></th>
                
                <?php /*?><th><?php echo __("Action", VOSL_TEXT_DOMAIN); ?></th><?php */?>
            </tr>
        </thead>
        <tfoot>
            <tr>
            	
                <th><?php echo __("ID", VOSL_TEXT_DOMAIN); ?></th>
                <?php do_action('vosl_show_selection_checkboxes'); ?>
                <?php do_action('vosl_add_custom_marker_icon_table_heading'); ?>
                <th><?php echo __("Name", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("Address", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("City", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("State", VOSL_TEXT_DOMAIN); ?></th>
                <th><?php echo __("Zip", VOSL_TEXT_DOMAIN); ?></th>
              
                <th><?php echo __("Latitude", VOSL_TEXT_DOMAIN); ?>, <?php echo __("Longitude", VOSL_TEXT_DOMAIN); ?></th>
                
                <?php /*?><th><?php echo __("Action", VOSL_TEXT_DOMAIN); ?></th><?php */?>
            </tr>
        </tfoot>
    </table>
<?php
	
	print "&nbsp;&nbsp;<input style='float:right; margin-top:10px;' type='button' value='".__("Delete All Listings", VOSL_TEXT_DOMAIN)."' class='button-red' onclick=\"deleteAllListings();\">";
	
	do_action('vosl_progressbar');
	print "<input name='act' type='hidden'><br>";
	wp_nonce_field("manage-locations_bulk");

print "</form>"; 
$ajax_url = admin_url( 'admin-ajax.php' );

$edit_link = 'admin.php?page='.VOSL_PAGES_DIR.'/edit-locations.php'; 
?>
</div>
<script type="text/javascript">
function deleteAllListings()
{
	if(confirm("<?php echo __("Do you want to delete all listings?", VOSL_TEXT_DOMAIN); ?>"))
	{
			jQuery("#dele").val(1);
			jQuery("#locationForm").submit();
	 }
}

jQuery(document).ready(function() {
	
	vosl_listing_admin_table = jQuery('#vosl_manage_listings_dt').DataTable( {
        "processing": true,
        "serverSide": true,
		bLengthChange: true,
		"sDom": 'Rlfrtlip',
		"ajax": {
            "url": "<?=$ajax_url?>?action=vosl_get_listings_admin",
            "type": "POST",
			"data": function ( d ) {
                d.filter_by = jQuery("#vosl_search_filter").val();
				d.tags_filter = jQuery("#vosl_tags_filter").val();
            }
        },
		"language": {
		  "emptyTable": "No data available for listings"
		},
		"columnDefs": [ 
		{ "visible": false, "targets": 0 },
		{ "width": "150px", "targets": 1, "orderable": false },
		{ "width": "200px", "targets": 2 },
		{ "width": "200px", "targets": 3 },
		{ "width": "100px", "targets": 4 },
		{ "width": "100px", "targets": 5 },
		{ "width": "100px", "targets": 6 },
		{ "width": "200px", "targets": 7, "orderable": true } ],
		"createdRow": function ( row, data, index ) {
			//console.log(data);
            if ( data[7] == '' ) {
				jQuery(row).addClass("vosl_row_highlight");
				jQuery('td', row).eq(6).html('<i class="fa text-yellow fa-exclamation-triangle" title="<?php echo addslashes(__("Unable to find lattitude,longitude for this listing. Please set correct address.", VOSL_TEXT_DOMAIN));?>" aria-hidden="true"></i>');
            }
			
        },
		"scrollX": true
    } );
	
	jQuery('#vosl_manage_listings_dt').on('click', 'tbody tr td', function() {
	  
	  var tr = jQuery(this).closest("tr");
	  var row_values = vosl_listing_admin_table.row(tr).data();
	  
	 if(jQuery(this).find("input[type=checkbox]").length == 0)
	  	document.location.href="<?=$edit_link?>&id="+row_values[0];
	  
	});
	
	jQuery('#btnSearch').click(function(){
      vosl_listing_admin_table.search(jQuery("#vosl_txtSearchText").val()).draw() ;
	});
});
</script>
<?php do_action('vosl_reinitialize_listing_data_table'); ?>
<style type="text/css">
#vosl_manage_listings_dt_info{ clear:none; margin-left:20px; margin-top:4px; }
.dataTables_processing{ z-index:9999; }
.listingssearchbox{ float:right; display:none; }
.wp-core-ui .notice.is-dismissible{ margin-top:20px; }
#vosl_manage_listings_dt{ 
	table-layout: fixed; // ***********add this
    word-wrap:break-word; // ***********and this
}
</style>