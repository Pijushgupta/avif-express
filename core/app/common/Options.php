<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

class Options
{
    public static function ajaxGetAutoConvtStatus()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getAutoConvtStatus());
        wp_die();
    }

    public static function getAutoConvtStatus(): bool
    {
        return (bool)get_option('avifautoconvstatus', false);
    }

    public static function ajaxSetAutoConvtStatus()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setAutoConvtStatus());
        wp_die();
    }

    public static function setAutoConvtStatus(): bool
    {
        $autoConvStatus = self::getAutoConvtStatus();
        return update_option('avifautoconvstatus', !$autoConvStatus);
    }

    public static function ajaxGetOperationMode()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getOperationMode());
        wp_die();
    }

    public static function getOperationMode()
    {
        $opMode = get_option('avifoperationmode', false);
        if (!$opMode) {
            update_option('avifoperationmode', sanitize_text_field('inactive'));
        }
        return get_option('avifoperationmode');
    }


    public static function ajaxSetOperationMode()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setOperationMode(sanitize_text_field($_POST['mode'])));
        wp_die();
    }

    public static function setOperationMode(string $value = '')
    {
        if ($value == '') return;
        return update_option('avifoperationmode', $value);
    }

    public static function ajaxGetImgQuality()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getImageQuality());
        wp_die();
    }

    public static function getImageQuality(): int
    {
        return intval(get_option('avifimagequality', 70));
    }

    public static function ajaxSetImgQuality()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setImgQuality(intval($_POST['quality'])));
        wp_die();
    }

    public static function setImgQuality($value = '')
    {
        if ($value == '') return;
        return update_option('avifimagequality', $value);
    }

    public static function ajaxGetComSpeed()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getComSpeed());
        wp_die();
    }

    public static function getComSpeed(): int
    {
        return intval(get_option('avifcompressionspeed', 6));
    }

    public static function ajaxSetComSpeed()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setComSpeed(intval($_POST['speed'])));
        wp_die();
    }

    public static function setComSpeed($value = '')
    {
        if ($value == '') return;
        return update_option('avifcompressionspeed', $value);
    }

    public static function ajaxGetConversionEngine()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getConversionEngine());
        wp_die();
    }

    public static function getConversionEngine()
    {
        $engine = get_option('avifconversionengine', false);
        if (!$engine) {
            update_option('avifconversionengine', sanitize_text_field('cloud'));
        }
        return get_option('avifconversionengine');
    }

    public static function ajaxSetConversionEngine()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setConversionEngine(sanitize_text_field($_POST['engine'])));
        wp_die();
    }

    public static function setConversionEngine($value = '')
    {
        if ($value == '') return;
        return update_option('avifconversionengine', $value);
    }

    public static function ajaxGetOnTheFlyAvif()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getOnTheFlyAvif());
        wp_die();
    }

    public static function getOnTheFlyAvif(): bool
    {
        return (bool)get_option('avifontheflyavif', false);
    }

    public static function ajaxSetOnTheFlyAvif()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setOnTheFlyAvif());
        wp_die();
    }

    public static function setOnTheFlyAvif(): bool
    {
        $onTheFlyAvif = self::getOnTheFlyAvif();
        return update_option('avifontheflyavif', !$onTheFlyAvif);
    }

    public static function ajaxGetEnableLogging()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getEnableLogging());
        wp_die();
    }

    public static function getEnableLogging(): bool
    {
        return (bool)get_option('avifenablelogging', false);
    }

    public static function ajaxSetEnableLogging()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setEnableLogging());
        wp_die();
    }

    public static function setEnableLogging(): bool
    {
        $enableLogging = self::getEnableLogging();
        return update_option('avifenablelogging', !$enableLogging);
    }

    public static function ajaxGetApiKey()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getApiKey());
        wp_die();
    }

    public static function getApiKey()
    {
        return get_option('avifapikey', false);
    }

    public static function ajaxSetApiKey()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setApiKey(sanitize_text_field($_POST['apiKey'])));
        wp_die();
    }

    public static function setApiKey($value = '')
    {
        if ($value == '') return;
        //test api key before saving
        $status =  Api::check($value);

        switch ($status) {
            //case of connection issue
            case 3:
                return null;
                //case of invalid api key
            case 2:
                return false;
                //case of success
            case 1:
                update_option('avifapikey', $value);
                return true;
        }
    }

    public static function ajaxGetFallbackMode()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(esc_html(self::getFallbackMode()));
        wp_die();
    }

    public static function getFallbackMode(): string
    {
        if (!get_option('aviffallbackmode', false)) {
            self::setFallbackMode();
        }
        return esc_html(get_option('aviffallbackmode'));
    }

    public static function ajaxSetFallbackMode()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setFallbackMode(sanitize_text_field($_POST['fallbackMode'])));
        wp_die();
    }

    public static function setFallbackMode($value = 'original'): bool
    {
        return update_option('aviffallbackmode', $value);
    }

    public static function ajaxGetLazyLoad()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getLazyLoad());
        wp_die();
    }

    public static function getLazyLoad()
    {
        return (bool)get_option('aviflazyload', false);
    }

    public static function ajaxSetLazyLoad()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::setLazyLoad());
        wp_die();
    }

    public static function setLazyLoad()
    {
        return update_option('aviflazyload', !self::getLazyLoad());
    }
}
