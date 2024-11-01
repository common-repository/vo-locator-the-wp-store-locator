<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 */
class VoStoreLocator {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		global $vosl_plugin_admin;
		$this->load_dependencies();
		$this->plugin_admin = new VoStoreLocator_Admin();
		//FRONTEND HOOKS
		$this->plugin_public = new VoStoreLocator_Public();
		$this->vosl_define_db_tables();
		
		if (is_admin())
			$this->define_admin_hooks();
		else
			$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - VoslStoreLocator_Admin. Defines all hooks for the admin area.
	 * - VoslStoreLocator_Public. Defines all hooks for the public side of the site.
	 *
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		global $vosl_inc_classes_dir, $vosl_admin_classes_dir;
		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		 
		 

		require_once $vosl_admin_classes_dir . '/vosl-locator-admin.php';
		require_once $vosl_admin_classes_dir . '/vosl-locator-admin-static.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once $vosl_inc_classes_dir . '/vosl-locator-public.php';
	}
	
	private function vosl_define_db_tables()
	{
		global $wpdb; 
		$vosl_db_prefix = $wpdb->prefix; //better this way, in case prefix changes vs storing option - 1/29/15
		if (!defined('VOSL_DB_PREFIX')){ define('VOSL_DB_PREFIX', $vosl_db_prefix); }
		if (!empty($vosl_db_prefix)) {
			if (!defined('VOSL_TABLE')){ define('VOSL_TABLE', VOSL_DB_PREFIX."vostore_locator"); }
			if (!defined('VOSL_SETTING_TABLE')){ define('VOSL_SETTING_TABLE', VOSL_DB_PREFIX."vosl_setting"); }
			if (!defined('VOSL_TAGS_TABLE')){ define('VOSL_TAGS_TABLE', VOSL_DB_PREFIX."vosl_tags"); }
			if (!defined('VOSL_TAGS_ASSOC_TABLE')){ define('VOSL_TAGS_ASSOC_TABLE', VOSL_DB_PREFIX."vosl_tags_locations"); }
			if (!defined('VOSL_CUSTOM_FIELDS')){ define('VOSL_CUSTOM_FIELDS', VOSL_DB_PREFIX."vosl_custom_fields"); }
			if (!defined('VOSL_CUSTOM_FIELDS_ASSOC_TABLE')){ define('VOSL_CUSTOM_FIELDS_ASSOC_TABLE', VOSL_DB_PREFIX."vosl_store_custom_fields"); }
		}
	}
	
	/*All Admin Callbacks*/
	public function add_admin_menu() {
		
		global $vosl_capability;
		vosl_add_custom_cap();
		
		add_menu_page( "VO Locator", "VO Locator", $vosl_capability, VOSL_PAGES_DIR.'/locations.php', '', VOSL_BASE.'/images/logo.ico.png');
		
		add_submenu_page( VOSL_PAGES_DIR.'/locations.php', __("Locations", VOSL_TEXT_DOMAIN), __("Locations", VOSL_TEXT_DOMAIN), $vosl_capability, VOSL_PAGES_DIR.'/locations.php', '');
		add_submenu_page( VOSL_PAGES_DIR.'/locations.php', __("Settings", VOSL_TEXT_DOMAIN), __("Settings", VOSL_TEXT_DOMAIN), $vosl_capability, VOSL_PAGES_DIR.'/settings.php', '');
		
		add_submenu_page('vosl-plugin-edit-location', 'Edit Location', 'Edit Location', $vosl_capability, VOSL_PAGES_DIR.'/edit-locations.php', '');
		add_submenu_page('vosl-plugin-edit-tag', 'Edit Tag', 'Edit Tag', $vosl_capability, VOSL_PAGES_DIR.'/edit-tags.php', '');
		
		add_submenu_page('vosl-plugin-add-location', 'Add Location', 'Add Location', $vosl_capability, VOSL_PAGES_DIR.'/add-locations.php', '');
		add_submenu_page('vosl-plugin-add-tags', 'Add Tags', 'Add Tags', $vosl_capability, VOSL_PAGES_DIR.'/add-tags.php', '');
		add_submenu_page(VOSL_PAGES_DIR.'/locations.php', __("Tags", VOSL_TEXT_DOMAIN), __("Tags", VOSL_TEXT_DOMAIN), $vosl_capability, VOSL_PAGES_DIR.'/manage-tags.php', '');
		remove_submenu_page( "vosl-plugin", "vosl-plugin" );
		remove_submenu_page( VOSL_PAGES_DIR.'/locations.php', "vosl-plugin-edit-location" );
		remove_submenu_page( VOSL_PAGES_DIR.'/locations.php', "vosl-plugin-edit-tag" );
		remove_submenu_page( "vosl-plugin", "vosl-plugin-add-location" );
		remove_submenu_page( "vosl-plugin", "vosl-plugin-add-tags" );
		//remove_submenu_page( "vosl-plugin", "vosl-plugin-manage-tags" );
		
		// calling an action for pro addon menu
		do_action("voslpromenus");
	}
	
	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		global $vosl_main_file_path;
		
		add_action('admin_menu', array($this,'add_admin_menu'));
		add_action('admin_notices', array($this->plugin_admin, 'vosl_admin_notices'));
		VoStoreLocator_AdminStatic::initHooks();
		add_action('admin_print_scripts', array($this->plugin_admin, 'vo_wp_gear_manager_admin_scripts'));
		add_action('admin_print_styles', array($this->plugin_admin, 'vo_wp_gear_manager_admin_styles'));
		add_action('admin_init', array($this->plugin_admin, 'vosl_enqueue_admin_scripts'), 1);
		add_action( 'admin_enqueue_scripts', array($this->plugin_admin, 'vo_add_color_picker') );
		
		// Runs the admin notice ignore function incase a dismiss button has been clicked
        add_action( 'admin_init', array( $this->plugin_admin, 'vosl_admin_notice_ignore' ) );
		
		add_action('wp_ajax_vosl_get_listings_admin', array($this->plugin_admin, 'vosl_get_listings_admin'));
		add_action('wp_ajax_vosl_delete_listing', array($this->plugin_admin, 'vosl_delete_listing'));
		add_action('wp_ajax_vosl_delete_tag', array($this->plugin_admin, 'vosl_delete_tag'));
		
		add_action('wp_ajax_vosl_get_tags_listings_admin', array($this->plugin_admin, 'vosl_get_tags_listings_admin'));
		add_action('wp_ajax_vosl_delete_tags_listing', array($this->plugin_admin, 'vosl_delete_tags_listing'));
		
		add_action('wp_ajax_load_more_locations', array($this->plugin_admin, 'vo_find_more_locations_process_request'));
		add_action('wp_ajax_nopriv_load_more_locations', array($this->plugin_admin, 'vo_find_more_locations_process_request')); 
		
		add_action('wp_ajax_find_locations', array($this->plugin_admin, 'vo_find_locations_process_request'));
		add_action('wp_ajax_nopriv_find_locations', array($this->plugin_admin, 'vo_find_locations_process_request'));
		
		add_action('wp_ajax_vosl_update_settings', array($this->plugin_admin, 'vosl_update_settings'));
	}
	
	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {
	
		//echo "ss"; die;
		//add_action('the_content', array($this->plugin_public, 'render_template'));
		add_shortcode( 'VO-LOCATOR', array($this->plugin_public, 'render_standard_template') );
		add_action('vosl_add_bLinks', array($this->plugin_public, 'vosl_add_bLinks'), 50); 
		// load the locator js for front end
		// removed this line from here so that unnecessary js file is not loaded on entire site
		//add_action('wp_print_scripts', array($this->plugin_public, 'vo_load_locator_js'));
		add_action( 'wp_enqueue_scripts', array($this->plugin_public, 'register_volocator_script') );	
	}
}
