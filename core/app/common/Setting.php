<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

class Setting
{
    public static function avif_set_time_limit(): bool
    {
        if (function_exists('wp_is_ini_value_changeable') && wp_is_ini_value_changeable('max_execution_time')) {
            set_time_limit(0);
            return true;
        }
        return false;
    }

    /**
     * ajax handle deleteLogFile
     */
    public static function ajaxDeleteLogFile()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        if (self::deleteLogFile()) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo json_encode(array('status' => 'error'));
        }
        wp_die();
    }

    /**
     * deleteLogFile
     */
    public static function deleteLogFile(): bool
    {
        if (file_exists(AVIF_LOG_FILE)) {
            @unlink(AVIF_LOG_FILE);
            return true;
        }
        return false;
    }


    /**
     * ajax handle isLogFileExists
     */
    public static function ajaxIsLogFileExists()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        if (self::isLogFileExists()) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo json_encode(array('status' => 'error'));
        }
        wp_die();
    }

    /**
     * isLogFileExists
     */
    public static function isLogFileExists(): bool
    {
        if (file_exists(AVIF_LOG_FILE)) {
            return true;
        }
        return false;
    }

}
