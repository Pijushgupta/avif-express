<?php

namespace Avife;

if (!defined('ABSPATH')) exit;

class Routes
{

    public static function enable()
    {
        //keep it simple for IDE, no factorization needed!!!
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
        add_action('wp_ajax_ajaxGetOnTheFlyAvif', array('Avife\common\Options', 'ajaxGetOnTheFlyAvif'));
        add_action('wp_ajax_ajaxSetOnTheFlyAvif', array('Avife\common\Options', 'ajaxSetOnTheFlyAvif'));
        add_action('wp_ajax_ajaxGetEnableLogging', array('Avife\common\Options', 'ajaxGetEnableLogging'));
        add_action('wp_ajax_ajaxSetEnableLogging', array('Avife\common\Options', 'ajaxSetEnableLogging'));
        add_action('wp_ajax_ajaxGetApiKey', array('Avife\common\Options', 'ajaxGetApiKey'));
        add_action('wp_ajax_ajaxSetApiKey', array('Avife\common\Options', 'ajaxSetApiKey'));
        add_action('wp_ajax_ajaxGetFallbackMode', array('Avife\common\Options', 'ajaxGetFallbackMode'));
        add_action('wp_ajax_ajaxSetFallbackMode', array('Avife\common\Options', 'ajaxSetFallbackMode'));

        add_action('wp_ajax_ajaxGetLazyLoad', array('Avife\common\Options', 'ajaxGetLazyLoad'));
        add_action('wp_ajax_ajaxSetLazyLoad', array('Avife\common\Options', 'ajaxSetLazyLoad'));

        add_action('wp_ajax_ajaxCountMedia', array('Avife\common\Media', 'ajaxCountMedia'));
        add_action('wp_ajax_ajaxConvertRemaining', array('Avife\common\Media', 'ajaxConvertRemaining'));
        add_action('wp_ajax_ajaxDeleteAll', array('Avife\common\Media', 'ajaxDeleteAll'));

        add_action('wp_ajax_ajaxGetCurrentTheme', array('Avife\common\Theme', 'ajaxGetCurrentTheme'));
        add_action('wp_ajax_ajaxThemeFilesConvert', array('Avife\common\Theme', 'ajaxThemeFilesConvert'));
        add_action('wp_ajax_ajaxThemeFilesDelete', array('Avife\common\Theme', 'ajaxThemeFilesDelete'));

        add_action('wp_ajax_ajaxGetGdInfo', array('Avife\common\PhpInfo', 'ajaxGetGdInfo'));
        add_action('wp_ajax_ajaxGetImagickInfo', array('Avife\common\PhpInfo', 'ajaxGetImagickInfo'));
        add_action('wp_ajax_ajaxGetPhpInfo', array('Avife\common\PhpInfo', 'ajaxGetPhpInfo'));

        add_action('wp_ajax_ajaxDeleteLogFile', array('Avife\common\Setting', 'ajaxDeleteLogFile'));
        add_action('wp_ajax_ajaxIsLogFileExists', array('Avife\common\Setting', 'ajaxIsLogFileExists'));


    }
}
