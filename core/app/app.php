<?php
if (!defined('ABSPATH')) exit;

/**
 * Loading auto-loader(composer) 
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Avife\backend\Ui;
use Avife\backend\Enqueue;
use Avife\Routes;
use Avife\common\Image;
use Avife\frontend\Html;

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
