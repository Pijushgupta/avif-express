<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../vendor/autoload.php';

use Avife\backend\Ui;
use Avife\backend\Enqueue;
use Avife\Routes;
use Avife\common\Image;

use Avife\frontend\Html;

if (is_admin()) {
	Ui::activate();
	Enqueue::do();
	Routes::enable();
	Image::activate();
}

if (!is_admin()) {
	Html::init();
}
