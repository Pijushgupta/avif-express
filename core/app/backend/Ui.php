<?php

namespace App\backend;

final class Ui {

	private static $globalScopeName = 'App\backend\Ui';

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
		$assetPath 	= AVIFE_REL . '/core/app/backend/assets/dist/';
		$gd = extension_loaded('gd');
		printf(
			'<script> 
			var avife_ajax_path = "%1$s"; 
			var avife_nonce = "%2$s"; 
			var assetPath = "%3$s";
			var gd = "%4$s"
			</script>
			<div id="%5$s"></div>',
			$url,
			$avife_nonce,
			$assetPath,
			$gd,
			AVIFE_VUE_ROOT_ID
		);
	}
}
