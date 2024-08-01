<?php
if (!defined('ABSPATH')) exit; // Exit if accessed directly
/**
 * Plugin Name: AVIF Express
 * Plugin URI: https://www.aavoya.co/avif-express
 * Author: Pijush Gupta
 * Author uri: https://www.linkedin.com/in/pijush-gupta-php/
 * Description: Convert Images to AVIF and serve them
 * Version: 2024.08.02
 * Tags: avif, images, performance, avif
 * text-domain: avif-express
 */

if (!file_exists(__DIR__ . '/core/app/app.php')) return;

function initiate_plugin()
{

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
     * setting imagick constant
     */
    if (!defined('IS_IMAGICK_AVIF') || !defined('IS_IMAGICK_WEBP')) {
        $isImagickAvifSupported = false;
        $isImagickWebpSupported = false;

        if (class_exists('Imagick')) {
            $imagick = new \Imagick();
            $formats = array_map('strtolower', $imagick->queryFormats());

            $isImagickAvifSupported = in_array('avif', $formats) && function_exists('imageavif');
            $isImagickWebpSupported = in_array('webp', $formats);
        }

        if (!defined('IS_IMAGICK_AVIF')) {
            define('IS_IMAGICK_AVIF', $isImagickAvifSupported);
        }

        if (!defined('IS_IMAGICK_WEBP')) {
            define('IS_IMAGICK_WEBP', $isImagickWebpSupported);
        }
    }

    /**
     * setting GD constants
     */
    if(!defined('IS_GD_AVIF') || !defined('IS_GD_WEBP')){

        $isGdWebpSupported = false;
        $isGdAvifSupported = false;
        if(extension_loaded('gd') && function_exists('gd_info')){
            $gdInfo = gd_info();
            $isGdWebpSupported = !empty($gdInfo['WebP Support']);
            $isGdAvifSupported = !empty($gdInfo['AVIF Support']);
        }

        if (!defined('IS_GD_WEBP')) {
            define('IS_GD_WEBP', $isGdWebpSupported);
        }

        if (!defined('IS_GD_AVIF')) {
            define('IS_GD_AVIF', $isGdAvifSupported);
        }
    }

    /**
     * Avif cloud engine address
     */
    if (!defined('AVIF_CLOUD_ADDRESS')) {
        define('AVIF_CLOUD_ADDRESS', 'https://avif-express.p.rapidapi.com/convert');
    }

    /**
     * Monolog log file path
     */
    if (!defined('AVIF_LOG_FILE')) {
        define('AVIF_LOG_FILE', __DIR__ . '/logs/avif.log');
    }

    /**
     * Monolog relative path
     * /wp-content/plugins/avif-express/logs/avif.log
     */
    if (!defined('AVIF_LOG_FILE_REL')) {
        define('AVIF_LOG_FILE_REL', plugin_dir_url(__FILE__) . 'logs/avif.log');
    }

    /**
     * loading the main app code
     */
    require_once __DIR__ . '/core/app/app.php';
}

/**
 * Loading everything on plugin_loaded action
 */
add_action('plugins_loaded', 'initiate_plugin');
