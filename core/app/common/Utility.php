<?php

namespace Avife\common;

class Utility
{

    /**
    * Logs an error message if WP_DEBUG is enabled.
    */
    public static function logError($message)
    {
        if (WP_DEBUG) {
            error_log($message);
        }
    }

    /**
     * Checks if local conversion is supported.
     */
    public static function isLocalAvifConversionSupported(): bool
    {
        return IS_IMAGICK_AVIF || IS_GD_AVIF;
    }
}
