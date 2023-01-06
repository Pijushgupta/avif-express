<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Plugin Name: AVIF Express
 * Plugin URI: https://www.aavoya.co/avif-express
 * Author: Pijush Gupta
 * Author uri: https://www.linkedin.com/in/pijush-gupta-php/
 * Description: Converts Images to AVIF
 * Version: 2023.01
 * Tags: avif, images, performance
 */

if (!file_exists(__DIR__ . '/core/app/app.php')) return;

function initiate_plugin() {
	/**
	 * TODO: Check php version
	 */
	if (!defined('AVIFE_TEXT_DOMAIN')) define('AVIFE_TEXT_DOMAIN', 'avif_text_domain');
	if (!defined('AVIFE_ADMIN_MENU_TITLE')) define('AVIFE_ADMIN_MENU_TITLE', 'Avif Express');
	if (!defined('AVIFE_ADMIN_MENU_NAME')) define('AVIFE_ADMIN_MENU_NAME', 'Avif Express');
	if (!defined('AVIFE_SPA_SLUG')) define('AVIFE_SPA_SLUG', 'avif-express');
	if (!defined('AVIFE_REL')) define('AVIFE_REL', plugins_url('', __FILE__));
	if (!defined('AVIFE_ABS')) define('AVIFE_ABS', plugin_dir_path(__FILE__));
	if (!defined('AVIFE_VUE_ROOT_ID')) define('AVIFE_VUE_ROOT_ID', 'avife-root');

	require_once __DIR__ . 	'/core/app/app.php';
}
add_action('plugins_loaded', 'initiate_plugin');
