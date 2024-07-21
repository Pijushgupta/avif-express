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

    /**
     * attachmentUrlToPath
     * This function converts the url of an Image to actual path of that image
     * @param string $url url of an Image
     * @return string|boolean  path of an Image, false on fail
     */
    public static function attachmentUrlToPath(string $url)
    {
        $parsed_url = parse_url($url);
        if (empty($parsed_url['path'])) return false;
        $file = ABSPATH . ltrim($parsed_url['path'], '/');
        if (file_exists($file)) return $file;
        return false;
    }

    /**
     * Convert Absolute Path to Relative Url
     * @param String|array $url
     * @return String|array
     */
    public static function pathToAttachmentUrl($url = '')
    {
        if ($url == '') return '';
        if (is_array($url)) {
            $relativeUrl = [];
            foreach ($url as $link) {
                $relativeUrl[] = self::pathToAttachmentUrl($link);
            }
            return $relativeUrl;
        }
        $wpContentPosition = strpos($url, 'wp-content');
        if ($wpContentPosition !== false) {
            return get_site_url() . '/wp-content' . explode('wp-content', $url)[1];
        }

        return '';
    }


    /**
     * deletes converted webp and avif files if present
     * @param array $files
     * @return bool
     */
    public static function deleteFiles(array $files) : bool{
        foreach ($files as $file) {
            /**
             * creating path for file to delete. Just by removing original extension with .avif
             */
            $dest = (string)rtrim($file, '.' . pathinfo($file, PATHINFO_EXTENSION)) . '.avif';

            /**
             * Finally deleting the file
             */
            if (file_exists($dest)) wp_delete_file($dest);

            /**
             * delete fallback webp
             */
            $dest = (string)rtrim($file, '.' . pathinfo($file, PATHINFO_EXTENSION)) . '.webp';

            /**
             * deleting the file
             */
            if (file_exists($dest)) wp_delete_file($dest);
        }
        return true;
    }
}
