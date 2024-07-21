<?php

namespace Avife\common;
class PhpInfo
{

    public static function ajaxGetGdInfo()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getGdInfo());
        wp_die();
    }

    public static function getGdInfo()
    {
        if (!function_exists('gd_info')) return false;
        return gd_info();
    }

    public static function ajaxGetImagickInfo()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getImagickInfo());
        wp_die();
    }

    public static function getImagickInfo()
    {
        if (!extension_loaded('imagick') || !class_exists('Imagick')) return false;
        return array(
            'version' => \Imagick::getVersion(),
            'formats' => \Imagick::queryFormats(),
        );
    }

    public static function ajaxGetPhpInfo()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::getPhpInfo());
        wp_die();
    }

    public static function getPhpInfo()
    {
        return array(
            'version' => phpversion(),
            'curl' => function_exists('curl_version') ? curl_version() : false
        );
    }
}
