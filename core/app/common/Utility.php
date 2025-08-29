<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

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
    public static function isLocalAvifConversionSupported()
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

        $url_path = ltrim($parsed_url['path'], '/');

        // Handle uploads
        $upload_dir = wp_upload_dir();
        if (strpos($url, $upload_dir['baseurl']) === 0) {
            $relative = str_replace($upload_dir['baseurl'], '', $url);
            $path = $upload_dir['basedir'] . $relative;
            if (file_exists($path)) return $path;
        }

        // Theme (child and parent)
        $theme_dirs = [
            get_stylesheet_directory(),
            get_template_directory()
        ];

        foreach ($theme_dirs as $theme_dir) {
            $theme_url = content_url(str_replace(ABSPATH, '', $theme_dir));
            if (strpos($url, $theme_url) === 0) {
                $relative = str_replace($theme_url, '', $url);
                $path = $theme_dir . $relative;
                if (file_exists($path)) return $path;
            }
        }

        // Plugins
        $plugin_url = plugins_url();
        if (strpos($url, $plugin_url) === 0) {
            $relative = str_replace($plugin_url, '', $url);
            $path = WP_PLUGIN_DIR . $relative;
            if (file_exists($path)) return $path;
        }

        // Fallback: try matching with ABSPATH
        $site_url = site_url();
        if (strpos($url, $site_url) === 0) {
            $relative = str_replace($site_url, '', $url);
            $path = ABSPATH . ltrim($relative, '/');
            if (file_exists($path)) return $path;
        }

        return false;
    }



    /**
     * Convert Absolute Path to Relative Url
     * @param string|array $url
     * @return string|array
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
    public static function deleteFiles(array $files): bool
    {
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
            //$dest = (string)rtrim($file, '.' . pathinfo($file, PATHINFO_EXTENSION)) . '.webp';

            /**
             * deleting the file
             */
            //if (file_exists($dest)) wp_delete_file($dest);
        }
        return true;
    }


    public static function isLocalDomain(): bool
    {
        $serverName = $_SERVER['SERVER_NAME'];
        $serverAddr = $_SERVER['SERVER_ADDR'];

        // Check if the server name is localhost or an IP in the private range
        if (
            $serverName === 'localhost' ||
            $serverAddr === '127.0.0.1' ||
            strpos($serverAddr, '192.168.') === 0 ||
            strpos($serverAddr, '10.') === 0 ||
            strpos($serverAddr, '172.') === 0 && (int)substr($serverAddr, 4, 2) >= 16 && (int)substr($serverAddr, 4, 2) <= 31
        ) {
            return true;
        }

        return false;
    }

    public static function prepareRequestHeader(
        string $apikey,
        string $contentType = 'application/json',
        string $accept = 'application/json'
    ): array {
        return [
            'X-RapidAPI-Key' => $apikey,
            'Content-Type' => $contentType,
            'Accept' => $accept
        ];
    }


    public static function prepareFormBody(string $filePath, $boundary, string $url = '')
    {
        // Prepare the file for upload
        $file = fopen($filePath, 'r');
        $delimiter = '-------------' . $boundary;

        // Multipart form data body
        $body = '--' . $delimiter . "\r\n" .
            'Content-Disposition: form-data; name="image"; filename="' . basename($filePath) . '"' . "\r\n" .
            'Content-Type: ' . mime_content_type($filePath) . "\r\n\r\n" .
            stream_get_contents($file) . "\r\n" .
            '--' . $delimiter . "\r\n" .
            'Content-Disposition: form-data; name="src"' . "\r\n\r\n" .
            $url . "\r\n" .
            '--' . $delimiter . '--';

        fclose($file);

        return $body;
    }
}
