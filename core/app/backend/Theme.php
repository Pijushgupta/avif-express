<?php

namespace App\backend;

if (!defined('ABSPATH')) exit;

class Theme {

	public static function ajaxGetCurrentTheme() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) return;
		echo json_encode(self::getCurrentTheme());
		wp_die();
	}

	public static function getCurrentTheme() {
		return wp_get_theme();
	}
}
