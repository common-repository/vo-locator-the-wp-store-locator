<?php
/*
Plugin Name: VO Store Locator
Plugin URI: http://www.vitalorganizer.com/vo-locator-wordpress-store-locator-plugin/
Description: Simple wordpress store locator plugin to manage multiple business locations and other any places using Google Maps. Manage a few or thousands of locations effortlessly with setup in minutes.
Version: 3.3.0
Author: Jurski
Author URI: http://www.vitalorganizer.com
*/
$vosl_version = 3.3;
define('VOSL_VERSION', $vosl_version);
$vosl_db_version = 1.9;
$vosl_main_file_path = plugin_basename(__FILE__);

if(!session_id()) {
	session_start();
}

function volocator() {
    global $volocator;

    if ( ! isset( $volocator ) ) {
        // Include Freemius SDK.
        require_once dirname(__FILE__) . '/freemius/start.php';

        $volocator = fs_dynamic_init( array(
            'id'                  => '1671',
            'slug'                => 'vo-locator-the-wp-store-locator',
            'type'                => 'plugin',
            'public_key'          => 'pk_d12273c0b7bec70b8e3fa3ee85744',
            'is_premium'          => false,
            'has_addons'          => false,
            'has_paid_plans'      => false,
            'menu'                => array(
                'slug'           => 'vo-locator-the-wp-store-locator/vosl-admin/pages/locations.php',
                'first-path'     => 'admin.php?page=vo-locator-the-wp-store-locator%2Fvosl-admin%2Fpages%2Fsettings.php',
                'account'        => false,
                'support'        => false,
            ),
        ) );
    }

    return $volocator;
}

// Init Freemius.
volocator();
// Signal that SDK was initiated.
do_action( 'volocator_loaded' );

include_once("vosl-define.php");
include_once("vosl-functions.php");
//echo $vosl_admin_classes_dir; die;
require $vosl_inc_classes_dir.'/vosl-locator.php';
$vosl_output = new VoStoreLocator(); 

register_activation_hook( __FILE__, 'vosl_install_tables');
register_deactivation_hook( __FILE__, 'vosl_deactivation');

load_plugin_textdomain(VOSL_TEXT_DOMAIN, FALSE, dirname(plugin_basename(__FILE__)).'/languages/');

function vosl_update_db_check() {
    global $vosl_db_version;
	
    if (vosl_data('vosl_db_version') != $vosl_db_version) {
        vosl_install_tables();
    }
}
add_action('plugins_loaded', 'vosl_update_db_check');
?>