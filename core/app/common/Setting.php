<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

class Setting {
	public static function avif_set_time_limit() {
		if (function_exists('wp_is_ini_value_changeable') && wp_is_ini_value_changeable('max_execution_time')) {
			set_time_limit(0);
			return true;
		}
		return false;
	}
}
