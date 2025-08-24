<?php
if (!defined('ABSPATH')) exit;

/**
 * Loading auto-loader(composer)
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Avife\Routes;
use Avife\backend\Ui;
use Avife\common\Cron;
use Avife\common\Image;
use Avife\frontend\Html;
use Avife\backend\Enqueue;

/**
 * backend code
 */
if (is_admin()) {
    /**
     * creating backend UI
     */
    Ui::activate();

    /**
     * Adding css and JS to Backend UI
     */
    Enqueue::do();

    /**
     * adding/enabling ajax(non REST) routes
     */
    Routes::enable();

    /**
     * Loading Image conversion class
     * reason to loading initially to hook it with 'wp_generate_attachment_metadata' and 'delete_attachment'
     */
    Image::activate();
}

/**
 * frontend code
 */
if (!is_admin()) {
    /**
     * initializing frontend for url alteration
     */
    Html::init();
}

/**
 * Initiate the cron job directly.
 * The entire file is already loaded on the 'plugins_loaded' hook.
 */
$cron = new Cron();
$cron->initiateCron();

