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
        // Parse the URL to get the path
        $parsed_url = parse_url($url);
        if (empty($parsed_url['path'])) return false;

        // Get the upload directory data
        $upload_dir = wp_upload_dir();
        $upload_baseurl = $upload_dir['baseurl'];
        $upload_basedir = $upload_dir['basedir'];

        // Remove the base URL part to get the relative path
        $relative_path = str_replace($upload_baseurl, '', $url);
        $file_path = $upload_basedir . $relative_path;

        // Check if the file exists
        if (file_exists($file_path)) {
            return $file_path;
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
            $dest = (string)rtrim($file, '.' . pathinfo($file, PATHINFO_EXTENSION)) . '.webp';

            /**
             * deleting the file
             */
            if (file_exists($dest)) wp_delete_file($dest);
        }
        return true;
    }


    public static function isLocalDomain(): bool
    {
        $serverName = $_SERVER['SERVER_NAME'];
        $serverAddr = $_SERVER['SERVER_ADDR'];

        // Check if the server name is localhost or an IP in the private range
        if ($serverName === 'localhost' ||
            $serverAddr === '127.0.0.1' ||
            strpos($serverAddr, '192.168.') === 0 ||
            strpos($serverAddr, '10.') === 0 ||
            strpos($serverAddr, '172.') === 0 && (int)substr($serverAddr, 4, 2) >= 16 && (int)substr($serverAddr, 4, 2) <= 31) {
            return true;
        }

        return false;
    }

    public static function prepareRequestHeader(
        string $apikey,
        string $contentType = 'application/json',
        string $accept = 'application/json'): array
    {
        return [
            'X-RapidAPI-Key' => $apikey,
            'Content-Type' => $contentType,
            'Accept' => $accept
        ];
    }


    public static function prepareFormBody(string $filePath, $boundary, string $url = ''){
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
