<?php
global $vosl_dir, $vosl_base, $vosl_path,$text_domain, $vosl_admin_classes_dir, $vosl_inc_classes_dir, $vosl_capability;
$vosl_siteurl=get_option('siteurl'); $vosl_blog_charset=get_option('blog_charset'); $vosl_admin_email=get_option('admin_email');
$vosl_site_name=get_option('blogname');
$vosl_dir=dirname(plugin_basename(__FILE__)); //plugin absolute server directory name
//$vosl_pub_dir=$vosl_dir."/vosl-pub";
$vosl_inc_dir=$vosl_dir."/vosl-inc";
$vosl_admin_dir=$vosl_dir."/vosl-admin";
$vosl_base=plugins_url('', __FILE__); //URL to plugin directory
$vosl_path=substr(plugin_dir_path(__FILE__), 0, -1); //absolute server path to plugin directory; substr() to remove trailing slash
$vosl_inc_classes_dir=$vosl_path."/vosl-inc/classes";
$vosl_admin_classes_dir=$vosl_path."/vosl-admin/classes";
$vosl_capability = 'manage_vosl';

$vosl_uploads=wp_upload_dir();
$top_nav_base="/".substr($_SERVER["PHP_SELF"],1)."?page=";
$admin_nav_base=$vosl_siteurl."/wp-admin/admin.php?page="; //die($admin_nav_base); 
$text_domain="volocator";
/*$view_link="| <a href='".$admin_nav_base.$vosl_admin_dir."/pages/locations.php'>".__("Manage Locations", $text_domain)."</a> <script>setTimeout(function(){jQuery('.sl_admin_success').fadeOut('slow');}, 6000);</script>";*/
$manage_vosl_locations_link="| <a href='".$admin_nav_base.$vosl_admin_dir."/pages/locations.php.'>".__("Manage Locations", $text_domain)."</a> <script>setTimeout(function(){jQuery('.sl_admin_success').fadeOut('slow');}, 6000);</script>";
$view_tag_link="| <a href='admin.php?page=manage-vosl-tags'>".__("Manage Tags", $text_domain)."</a> <script>setTimeout(function(){jQuery('.sl_admin_success').fadeOut('slow');}, 6000);</script>";
$web_domain=str_replace("www.","",$_SERVER['HTTP_HOST']);

//define("VOSL_LOCATIONS_PAGESIZE",100);
define("VOSL_MAINWEBSITE_URL",'https://www.vitalorganizer.com');
define('VOSL_SITEURL', $vosl_siteurl); define('VOSL_BLOG_CHARSET', $vosl_blog_charset); define('VOSL_ADMIN_EMAIL', $vosl_admin_email); define('VOSL_SITE_NAME', $vosl_site_name);
define('VOSL_DIR', $vosl_dir);
define('VOSL_PUB_DIR', $vosl_dir);
define('VOSL_CSS_DIR', VOSL_PUB_DIR."/css");
define('VOSL_ICONS_DIR', VOSL_PUB_DIR."/icons");
define('VOSL_JS_DIR', VOSL_PUB_DIR."/js");
define('VOSL_IMAGES_DIR_ORIGINAL', VOSL_PUB_DIR."/images");
define('VOSL_INC_DIR', $vosl_inc_dir);
define('VOSL_ACTIONS_DIR', VOSL_INC_DIR."/actions");
define('VOSL_INCLUDES_DIR', VOSL_INC_DIR."/includes");
define('VOSL_ADMIN_DIR', $vosl_admin_dir);
define('VOSL_PAGES_DIR', VOSL_ADMIN_DIR."/pages");
define('VOSL_BASE', $vosl_base);
define('VOSL_PUB_BASE', VOSL_BASE);
define('VOSL_CSS_BASE', VOSL_PUB_BASE."/css");
define('VOSL_ICONS_BASE', VOSL_PUB_BASE."/icons");
define('VOSL_JS_BASE', VOSL_PUB_BASE."/js");
define('VOSL_IMAGES_BASE_ORIGINAL', VOSL_PUB_BASE."/images");
define('VOSL_INC_BASE', VOSL_BASE."/vosl-inc");
define('VOSL_ACTIONS_BASE', VOSL_INC_BASE."/actions");
define('VOSL_INCLUDES_BASE', VOSL_INC_BASE."/includes");
define('VOSL_ADMIN_BASE', VOSL_BASE."/vosl-admin");
define('VOSL_PAGES_BASE', VOSL_ADMIN_BASE."/pages");
define('VOSL_PATH', $vosl_path);
define('VOSL_PUB_PATH', VOSL_PATH);
define('VOSL_CSS_PATH', VOSL_PUB_PATH."/css");
define('VOSL_ICONS_PATH', VOSL_PUB_PATH."/icons");
define('VOSL_JS_PATH', VOSL_PUB_PATH."/js");
define('VOSL_IMAGES_PATH_ORIGINAL', VOSL_PUB_PATH."/images");
define('VOSL_INC_PATH', VOSL_PATH."/vosl-inc");
define('VOSL_ACTIONS_PATH', VOSL_INC_PATH."/actions");
define('VOSL_INCLUDES_PATH', VOSL_INC_PATH."/includes");
define('VOSL_ADMIN_PATH', VOSL_PATH."/vosl-admin");
define('VOSL_PAGES_PATH', VOSL_ADMIN_PATH."/pages");
define('VOSL_TOP_NAV_BASE', $top_nav_base);
define('VOSL_ADMIN_NAV_BASE', $admin_nav_base);
define('VOSL_TEXT_DOMAIN', $text_domain);
define('VOSL_VIEW_LINK', $manage_vosl_locations_link);
define('VOSL_WEB_DOMAIN', $web_domain);
define('VOSL_INFORMATION_PAGE', VOSL_TOP_NAV_BASE.VOSL_PAGES_DIR."/information.php");
define('VOSL_MANAGE_LOCATIONS_PAGE', VOSL_TOP_NAV_BASE.VOSL_PAGES_DIR."/locations.php");
define('VOSL_ADD_LOCATIONS_PAGE', "admin.php?page=".VOSL_PAGES_DIR."/add-locations.php");
define('VOSL_ADD_TAGS_PAGE', "admin.php?page=".VOSL_PAGES_DIR."/add-tags.php");
define('VOSL_PARENT_PAGE', VOSL_INFORMATION_PAGE); //Initial nav page
define('VOSL_PARENT_URL', preg_replace("@".preg_quote(VOSL_TOP_NAV_BASE)."@", "",VOSL_PARENT_PAGE)); //Initial nav page (w/o top-nav base)
?>