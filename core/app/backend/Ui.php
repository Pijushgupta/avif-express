<?php

namespace Avife\backend;

use Avife\common\Options;

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
		
		$avifsupport = '0';
		if(function_exists('imageavif') && function_exists('gd_info') && gd_info()['AVIF Support'] != '') $avifsupport = '1';
		
		$hasImagick = '0';
		if(extension_loaded('imagick') && class_exists('Imagick') && AVIFE_IMAGICK_VER > 0){
			$imagick = new \Imagick();
			$formats = $imagick->queryFormats();
			if (in_array('AVIF', $formats)) {
				$hasImagick = '1';
			}
		}

		$isCloudEngine = '0';
		if(Options::getConversionEngine() == 'cloud') $isCloudEngine = '1';
		
		
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
			var isCloudEngine = "%9$s";
			</script>
			<div id="%8$s"></div>',
			$url,
			$avife_nonce,
			$assetPath,
			$gd,
			$avifsupport,
			$hasImagick,
			$dashboardLang,
			AVIFE_VUE_ROOT_ID,
			$isCloudEngine
		);
	}
}
