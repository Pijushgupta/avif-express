<?php

namespace App\backend;

if (!defined('ABSPATH')) exit;

final class Enqueue {

	private static $globalScopeName = 'App\backend\Enqueue';

	public static function do() {
		add_action('admin_enqueue_scripts', array(self::$globalScopeName, 'add'));
	}

	public static function add($hook) {
		if ($hook != 'toplevel_page_' . AVIFE_SPA_SLUG) return;

		if (file_exists(AVIFE_ABS . 'core/app/backend/assets/dist/app.js')) {
			wp_enqueue_script('avife-vue-script', AVIFE_REL . '/core/app/backend/assets/dist/app.js', array(), '1.0.0', true);
		}

		if (file_exists(AVIFE_ABS . 'core/app/backend/assets/dist/app.css')) {
			wp_enqueue_style('avife-tailwind-style', AVIFE_REL . '/core/app/backend/assets/dist/app.css', array(), '1.0.0');
		}
	}
}
