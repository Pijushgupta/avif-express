<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

use Avife\traits\ImageHelperTrait;

class Theme
{

    use ImageHelperTrait;
    
    public static function ajaxGetCurrentTheme()
    {
        if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
        echo json_encode(self::getCurrentTheme());
        wp_die();
    }

    /**
     * provides information about current theme
     * @return array[]
     */
    public static function getCurrentTheme()
    {
        $active_theme = wp_get_theme();

        $data = array(
            'theme_name' => $active_theme->get('Name'),
            'is_child' => is_child_theme(), //to remove
            'theme_root' => get_theme_root(), //to remove
            'theme_dir' => get_stylesheet_directory(), // to remove
            'files' => array(
                'converted' => intval(count(self::themeFilesConverted())),
                'total' => intval(count(self::themesFilesTotal()))
            ),
        );

        return array($data);
    }

    public static function ajaxThemeFilesConvert()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();

        $isCloudEngine = '0';
        if (Options::getConversionEngine() == 'cloud') $isCloudEngine = '1';

        if (!Utility::isLocalAvifConversionSupported() && $isCloudEngine == '0') wp_die();
        echo json_encode(self::themeFilesConvert());
        wp_die();
    }


    /**
     * themeFilesConvert
     * Converts all unconverted images inside theme directory
     * @return boolean
     */
    public static function themeFilesConvert()
    {
        $themeDirs = self::avif_get_theme_dirs();
        $filePaths = [];
        foreach ($themeDirs as $themeDir) {
            $filePaths = array_merge($filePaths, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), 1));
        }
        if (empty($filePaths) || gettype($filePaths) != 'array') return null;

        /**
         * Checking if 'set_time_limit' can be set or not
         * if not don't do anything
         */
        if (!Setting::avif_set_time_limit()) return false;
        $quality = Options::getImageQuality();
        $speed = Options::getComSpeed();

        $counter = 1;
        $keepAlive = 0;


        if (Options::getConversionEngine() == 'local') {
            foreach ($filePaths as $filePath) {

                $dest = (string)rtrim($filePath, '.' . pathinfo($filePath, PATHINFO_EXTENSION)) . '.avif';
                Image::convert($filePath, $dest, $quality, $speed);
                if ($counter == 5) {
                    return 'keep-alive';
                }
                $counter++;
            }
        }

        if (Options::getConversionEngine() == 'cloud') {
            //only allow 20 images per batch
            if (count($filePaths) > 20) {
                $filePaths = array_slice($filePaths, 0, 20);
                $keepAlive = 1;
            }
            $unConvertedAttachmentUrls = Utility::pathToAttachmentUrl($filePaths);

            $cs = Image::cloudConvert($unConvertedAttachmentUrls);
            if ($cs === false) return 'ccfail';
            if ($cs === 'ccover') return 'ccover';
            if ($keepAlive == 1) return 'keep-alive';

        }

        return true;
    }

    /**
     * ajaxThemeFilesDelete
     * ajax handle for themeFilesDelete
     * @return void
     */
    public static function ajaxThemeFilesDelete()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::themeFilesDelete());
        wp_die();
    }

    /**
     * themeFilesDelete
     * Deletes .avif files from theme folders.
     * In case of child theme, delete from parent and child.
     * @return boolean
     */
    public static function themeFilesDelete()
    {

        $filePaths = self::themeFilesConverted();
        /**
         * if no file found , terminating the process.
         */
        if (empty($filePaths)) return false;
        /**
         * iterating through file paths
         *
         */
        return Utility::deleteFiles($filePaths);
    }


    /**
     * themeFilesConverted
     * @return array returns an array containing the paths of jpg,webp and jpeg images in the theme dir(s) that are already converted
     */
    public static function themeFilesConverted()
    {
        $themeDirs = self::avif_get_theme_dirs();
        $convertedFiles = [];
        foreach ($themeDirs as $themeDir) {

            $convertedFiles = array_merge($convertedFiles, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), -1));
        }
        return $convertedFiles;
    }

    /**
     * themesFilesUnconverted
     * returns an array containing the paths of jpg,webp and jpeg images in the theme dir
     * that ate yet to get converted
     * @return array
     */
    public static function themesFilesUnconverted()
    {
        $themeDirs = self::avif_get_theme_dirs();
        $unconvertedFiles = [];
        foreach ($themeDirs as $themeDir) {
            $unconvertedFiles = array_merge($unconvertedFiles, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), 1));
        }
        return $unconvertedFiles;
    }

    /**
     * themesFilesTotal
     * returns an array containing the paths of jpg,webp and jpeg images in the theme dir
     * @return array
     */
    public static function themesFilesTotal()
    {
        $themeDirs = self::avif_get_theme_dirs();
        $totalFiles = [];
        foreach ($themeDirs as $themeDir) {
            $totalFiles = array_merge($totalFiles, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), 0));
        }
        return $totalFiles;
    }


    /**
     * avif_get_theme_dirs
     * @return array returns the theme path(s). In case of child theme, return parent and child theme path
     */
    public static function avif_get_theme_dirs()
    {
        $themes = array();
        if (is_child_theme()) {
            $themes[] = get_stylesheet_directory();
            $themes[] = get_template_directory();
        } else {
            $themes[] = get_template_directory();
        }

        return $themes;
    }
}
