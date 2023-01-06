<?php
if (!defined('ABSPATH')) exit;
require_once __DIR__ . '/../vendor/autoload.php';

use App\backend\Ui;
use App\backend\Enqueue;
use App\Routes;
use App\common\Image;

use App\frontend\Html;

if (is_admin()) {
	Ui::activate();
	Enqueue::do();
	Routes::enable();
	Image::activate();
}

if (!is_admin()) {
	Html::init();
}
