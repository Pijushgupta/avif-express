<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Plugin Name: AVIF Express
 * Plugin URI: https://www.aavoya.co/avif-express
 * Author: Pijush Gupta
 * Author uri: https://www.linkedin.com/in/pijush-gupta-php/
 * Description: Convert Images to AVIF and serve them
 * Version: 2024.06.20
 * Tags: avif, images, performance, avif
 * text-domain: avif-express
 */

if (!file_exists(__DIR__ . '/core/app/app.php')) return;

function initiate_plugin() {


	/**
	 * text domain, can be used to translate string. But we are using vue js powered admin page.
	 * So it little to no use. Keeping it for future.
	 */
	if (!defined('AVIFE_TEXT_DOMAIN')) define('AVIFE_TEXT_DOMAIN', 'avif-express');

	/**
	 * plugin's admin page html title
	 */
	if (!defined('AVIFE_ADMIN_MENU_TITLE')) define('AVIFE_ADMIN_MENU_TITLE', 'Avif Express');

	/**
	 * Left hand side menu name 
	 */
	if (!defined('AVIFE_ADMIN_MENU_NAME')) define('AVIFE_ADMIN_MENU_NAME', 'Avif Express');

	/**
	 * This is the slug to used in url of admin page of the plugin 
	 */
	if (!defined('AVIFE_SPA_SLUG')) define('AVIFE_SPA_SLUG', 'avif-express');

	/**
	 * this to load css and js files
	 */
	if (!defined('AVIFE_REL')) define('AVIFE_REL', plugins_url('', __FILE__));

	/**
	 * this to find css, js and php files
	 */
	if (!defined('AVIFE_ABS')) define('AVIFE_ABS', plugin_dir_path(__FILE__));

	/**
	 * vue js root div name, which we are going to target via vue js code
	 * if you want change this to something else, please also change the same thing in app.js in assets/src directory. 
	 */
	if (!defined('AVIFE_VUE_ROOT_ID')) define('AVIFE_VUE_ROOT_ID', 'avife-root');

	/**
	 * storing imageMagick version
	 */
	if (!defined('AVIFE_IMAGICK_VER')) {
		if (class_exists('Imagick')) {
			$v = Imagick::getVersion();
			preg_match('/ImageMagick ([0-9]+\.[0-9]+\.[0-9]+)/', $v['versionString'], $v);
			if (version_compare($v[1], '7.0.25') >= 0) {
				define('AVIFE_IMAGICK_VER', $v[1]);
			} else {
				define('AVIFE_IMAGICK_VER', 0);
			}
		} else {
			define('AVIFE_IMAGICK_VER', 0);
		}
	}

	/**
	 * setting constant to determine if backup WEBP
	 * conversion possible or not. Its going to be used as
	 * fallback browsers that don't support Webp
	 */
	if(!defined('AVIF_WEBP_POSSIBLE')){
		if (class_exists('Imagick')) {
			$imagick = new Imagick();
			$formats = $imagick->queryFormats();
			if (!in_array('WEBP', $formats)) {
				define('AVIF_WEBP_POSSIBLE',false);
			
			}elseif(function_exists('imagewebp')){
				define('AVIF_WEBP_POSSIBLE',true);
			
			}else{
				define('AVIF_WEBP_POSSIBLE',false);
			}
		}else{
			define('AVIF_WEBP_POSSIBLE',false);
		}
		
	}

	/**
	 * Avif cloud engine address 
	 */
	if(!defined('AVIF_CLOUD_ADDRESS')){
		define('AVIF_CLOUD_ADDRESS','https://avif-express.p.rapidapi.com/convert');
	}

	/**
	 * Monolog log file path
	 */
	if(!defined('AVIF_LOG_FILE')){
		define('AVIF_LOG_FILE',__DIR__.'/logs/avif.log');
	}

	/**
	 * Monolog relative path
	 * /wp-content/plugins/avif-express/logs/avif.log
	 */
	if(!defined('AVIF_LOG_FILE_REL')){
		define('AVIF_LOG_FILE_REL',plugin_dir_url(__FILE__).'logs/avif.log');
	}
	
	/**
	 * loading the main app code 
	 */
	require_once __DIR__ . 	'/core/app/app.php';
}

/**
 * Loading everything on plugin_loaded action 
 */
add_action('plugins_loaded', 'initiate_plugin');
