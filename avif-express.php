<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Plugin Name: AVIF Express
 * Plugin URI: https://www.aavoya.co/avif-express
 * Author: Pijush Gupta
 * Author uri: https://www.linkedin.com/in/pijush-gupta-php/
 * Description: Converts Images to AVIF and serve them
 * Version: 2023.02.27
 * Tags: avif, images, performance
 */

if (!file_exists(__DIR__ . '/core/app/app.php')) return;

function initiate_plugin() {


	/**
	 * text domain, can be used to translate string. But we are using vue js powered admin page.
	 * So it little to no use. Keeping it for future.
	 */
	if (!defined('AVIFE_TEXT_DOMAIN')) define('AVIFE_TEXT_DOMAIN', 'avif_text_domain');

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
	 * this to load css and js 
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
			}else{
				define('AVIFE_IMAGICK_VER', 0);
			}
		} else {
			define('AVIFE_IMAGICK_VER', 0);
		}
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
