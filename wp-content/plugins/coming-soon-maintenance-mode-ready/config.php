<?php
    global $wpdb;
    if (WPLANG == '') {
        define('CSP_WPLANG', 'en_GB');
    } else {
        define('CSP_WPLANG', WPLANG);
    }
    if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

    define('CSP_PLUG_NAME', basename(dirname(__FILE__)));
    define('CSP_DIR', WP_PLUGIN_DIR. DS. CSP_PLUG_NAME. DS);
    define('CSP_TPL_DIR', CSP_DIR. 'tpl'. DS);
    define('CSP_CLASSES_DIR', CSP_DIR. 'classes'. DS);
    define('CSP_TABLES_DIR', CSP_CLASSES_DIR. 'tables'. DS);
	define('CSP_HELPERS_DIR', CSP_CLASSES_DIR. 'helpers'. DS);
    define('CSP_LANG_DIR', CSP_DIR. 'lang'. DS);
    define('CSP_IMG_DIR', CSP_DIR. 'img'. DS);
    define('CSP_TEMPLATES_DIR', CSP_DIR. 'templates'. DS);
    define('CSP_MODULES_DIR', CSP_DIR. 'modules'. DS);
    define('CSP_FILES_DIR', CSP_DIR. 'files'. DS);
    define('CSP_ADMIN_DIR', ABSPATH. 'wp-admin'. DS);

    define('CSP_SITE_URL', get_bloginfo('wpurl'). '/');
    define('CSP_JS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/js/');
    define('CSP_CSS_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/css/');
    define('CSP_IMG_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/img/');
    define('CSP_MODULES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/modules/');
    define('CSP_TEMPLATES_PATH', WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/templates/');
    define('S_IMG_POSTS_PATH', CSP_IMG_PATH. 'posts/');
    define('CSP_JS_DIR', CSP_DIR. 'js/');

    define('CSP_URL', CSP_SITE_URL);

    define('CSP_LOADER_IMG', CSP_IMG_PATH. 'loading-cube.gif');
    define('CSP_DATE_DL', '/');
    define('CSP_DATE_FORMAT', 'd/m/Y');
    define('CSP_DATE_FORMAT_HIS', 'd/m/Y (H:i:s)');
    define('CSP_DATE_FORMAT_JS', 'dd/mm/yy');
    define('CSP_DATE_FORMAT_CONVERT', '%d/%m/%Y');
    define('CSP_WPDB_PREF', $wpdb->prefix);
    define('CSP_DB_PREF', 'csp_');    /*TheOneEcommerce*/
    define('CSP_MAIN_FILE', 'csp.php');

    define('CSP_DEFAULT', 'default');
    define('CSP_CURRENT', 'current');
    
    
    define('CSP_PLUGIN_INSTALLED', true);
    define('CSP_VERSION', '0.0.2');
    define('CSP_USER', 'user');
    
    
    define('CSP_CLASS_PREFIX', 'cspc');        
    define('CSP_FREE_VERSION', false);
    
    define('CSP_API_UPDATE_URL', 'http://somereadyapiupdatedomain.com');
    
    define('CSP_SUCCESS', 'Success');
    define('CSP_FAILED', 'Failed');
	define('CSP_ERRORS', 'cspErrors');
	
	define('CSP_THEME_MODULES', 'theme_modules');
	
	
	define('CSP_ADMIN',	'admin');
	define('CSP_LOGGED','logged');
	define('CSP_GUEST',	'guest');
	
	define('CSP_ALL',		'all');
	
	define('CSP_METHODS',		'methods');
	define('CSP_USERLEVELS',	'userlevels');
	/**
	 * Framework instance code, unused for now
	 */
	define('CSP_CODE', 'csp');
