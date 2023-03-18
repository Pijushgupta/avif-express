<?php

namespace Avife\backend;

final class Ui {

	private static $globalScopeName = 'Avife\backend\Ui';

	public static function activate() {
		if (current_user_can('manage_options')) {
			add_action('admin_menu', array(self::$globalScopeName, 'create'));
		}
	}


	public static function create() {

		add_menu_page(
			__(AVIFE_ADMIN_MENU_TITLE, AVIFE_TEXT_DOMAIN),
			__(AVIFE_ADMIN_MENU_NAME, AVIFE_TEXT_DOMAIN),
			'manage_options',
			AVIFE_SPA_SLUG,
			array(self::$globalScopeName, 'render'),
			'dashicons-images-alt2',
			20
		);
	}

	public static function render() {
		$url 		= admin_url('admin-ajax.php');
		$avife_nonce 		= wp_create_nonce('avife_nonce');
		$assetPath 	= AVIFE_REL . '/core/app/backend/assets/';
		$gd = extension_loaded('gd');
		$avifsupport = function_exists('imageavif') ? '1' : '0';
		$hasImagick = AVIFE_IMAGICK_VER <= 0 ? '0' : '1';
		$dashboardLang = explode('_', get_locale())[0];

		printf(
			'<script> 
			var avife_ajax_path = "%1$s"; 
			var avife_nonce = "%2$s"; 
			var assetPath = "%3$s";
			var gd = "%4$s";
			var avifsupport = "%5$s";
			var hasImagick = "%6$s";
			var adminLocale = "%7$s";
			</script>
			<div id="%8$s"></div>',
			$url,
			$avife_nonce,
			$assetPath,
			$gd,
			$avifsupport,
			$hasImagick,
			$dashboardLang,
			AVIFE_VUE_ROOT_ID
		);
	}
}
