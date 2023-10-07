<?php

namespace Avife;

if (!defined('ABSPATH')) exit;

class Routes {

	public static function enable() {
		add_action('wp_ajax_ajaxCountMedia', array('Avife\common\Media', 'ajaxCountMedia'));
		add_action('wp_ajax_ajaxGetAutoConvtStatus', array('Avife\common\Options', 'ajaxGetAutoConvtStatus'));
		add_action('wp_ajax_ajaxSetAutoConvtStatus', array('Avife\common\Options', 'ajaxSetAutoConvtStatus'));
		add_action('wp_ajax_ajaxGetOperationMode', array('Avife\common\Options', 'ajaxGetOperationMode'));
		add_action('wp_ajax_ajaxSetOperationMode', array('Avife\common\Options', 'ajaxSetOperationMode'));
		add_action('wp_ajax_ajaxGetImgQuality', array('Avife\common\Options', 'ajaxGetImgQuality'));
		add_action('wp_ajax_ajaxSetImgQuality', array('Avife\common\Options', 'ajaxSetImgQuality'));
		add_action('wp_ajax_ajaxGetComSpeed', array('Avife\common\Options', 'ajaxGetComSpeed'));
		add_action('wp_ajax_ajaxSetComSpeed', array('Avife\common\Options', 'ajaxSetComSpeed'));
		add_action('wp_ajax_ajaxGetConversionEngine', array('Avife\common\Options', 'ajaxGetConversionEngine'));
		add_action('wp_ajax_ajaxSetConversionEngine', array('Avife\common\Options', 'ajaxSetConversionEngine'));

		add_action('wp_ajax_ajaxConvertRemaining', array('Avife\common\Media', 'ajaxConvertRemaining'));
		add_action('wp_ajax_ajaxDeleteAll', array('Avife\common\Media', 'ajaxDeleteAll'));
		add_action('wp_ajax_ajaxGetCurrentTheme', array('Avife\common\Theme', 'ajaxGetCurrentTheme'));
		add_action('wp_ajax_ajaxThemeFilesConvert', array('Avife\common\Theme', 'ajaxThemeFilesConvert'));
		add_action('wp_ajax_ajaxThemeFilesDelete', array('Avife\common\Theme', 'ajaxThemeFilesDelete'));
		
	}
}
