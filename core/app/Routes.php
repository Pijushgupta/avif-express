<?php

namespace App;

if (!defined('ABSPATH')) exit;

class Routes {

	public static function enable() {
		add_action('wp_ajax_ajaxCountMedia', array('App\common\Media', 'ajaxCountMedia'));
		add_action('wp_ajax_ajaxGetAutoConvtStatus', array('App\common\Options', 'ajaxGetAutoConvtStatus'));
		add_action('wp_ajax_ajaxSetAutoConvtStatus', array('App\common\Options', 'ajaxSetAutoConvtStatus'));
		add_action('wp_ajax_ajaxGetOperationMode', array('App\common\Options', 'ajaxGetOperationMode'));
		add_action('wp_ajax_ajaxSetOperationMode', array('App\common\Options', 'ajaxSetOperationMode'));
		add_action('wp_ajax_ajaxGetImgQuality', array('App\common\Options', 'ajaxGetImgQuality'));
		add_action('wp_ajax_ajaxSetImgQuality', array('App\common\Options', 'ajaxSetImgQuality'));
		add_action('wp_ajax_ajaxGetComSpeed', array('App\common\Options', 'ajaxGetComSpeed'));
		add_action('wp_ajax_ajaxSetComSpeed', array('App\common\Options', 'ajaxSetComSpeed'));
		add_action('wp_ajax_ajaxConvertRemaining', array('App\common\Media', 'ajaxConvertRemaining'));
		add_action('wp_ajax_ajaxDeleteAll', array('App\common\Media', 'ajaxDeleteAll'));
		add_action('wp_ajax_ajaxGetCurrentTheme', array('App\backend\Theme', 'ajaxGetCurrentTheme'));
		add_action('wp_ajax_ajaxThemeFilesConvert', array('App\backend\Theme', 'ajaxThemeFilesConvert'));
		add_action('wp_ajax_ajaxThemeFilesDelete', array('App\backend\Theme', 'ajaxThemeFilesDelete'));
	}
}
