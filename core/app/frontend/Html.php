<?php

namespace Avife\frontend;

if (!defined('ABSPATH')) exit;

use Avife\common\Options;
use Avife\common\Image;
use Avife\common\Cookie;
use Avife\common\Utility;

use voku\helper\HtmlDomParser;
//use Masterminds\HTML5;

class Html
{

    /**
     * if the browser supports avif image or not
     * @var bool
     */
    private static $isAvifSupported = false;

    /**
     * fallback image type set by user
     * @var string
     */
    private static $fallbackType = 'original';

    /**
     * @var (voku\helper\HtmlDomParser)
     */
    private static $dom;

    /**
     * @var bool
     */
    private static $enableOnTheFlyConversion = false;

    /**
     * @var bool
     */
    private static $enableLazyLoading = false;

    public static function init()
    {
        add_action('template_redirect', array('Avife\frontend\Html', 'checkConditions'), 9999);
    }

    public static function checkConditions()
    {

        /**
         * if an administrative(also normal page while user logged in on another tab) interface page or
         * if the current request is for an RSS or Atom feed or
         * if the current request is an AJAX request or
         * if rendering "inactive" then terminate
         */
        if (is_admin() || is_feed() || wp_doing_ajax() || Options::getOperationMode() == 'inactive') return;

        /**
         * checking, if the browser support avif format
         */
        self::$isAvifSupported = strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'image/avif');

        /**
         * this to compliment for very old browser that don't support avif
         * then either switch to 'original' or 'webp' defined by user
         */
        self::$fallbackType = strtolower(Options::getFallbackMode());

        /**
         * creating cookie regarding avif support
         * for: nginx, apache2, liteSpeed etc.
         * based on this cookie value server can decide to cache or not to cache
         */
        Cookie::setAvifCookie(self::$isAvifSupported);

        /**
         * checking if browser support avif and fallback mode normal
         * return original content.
         */
        if (self::$isAvifSupported === false && self::$fallbackType !== 'original') return;

        /**
         * storing option data if "Disable on the fly avif" conversion true or false
         */
        self::$enableOnTheFlyConversion = !Options::getOnTheFlyAvif();

        /**
         * storing option data for `Lazy Loading images`
         */
        self::$enableLazyLoading = Options::getLazyLoad();

        /**
         * starting content replacement work
         */
        ob_start('Avife\frontend\Html::getContent');
    }

    public static function getContent($content)
    {

        /**
         * if it's not html return the content
         * simple preg_match
         * # is the delimiter for the regular expression. It can be any character, but "#" is commonly used.
         * ^ is an anchor that matches the beginning of the string.
         * \s* matches zero or more whitespace characters (spaces, tabs, line breaks).
         * < matches the "<" character.
         */
        if (!preg_match("#^\\s*<#", $content)) {
            return $content;
        }

        /**
         * loading the dom using voku\helper\HtmlDomParser
         * see more: https://github.com/voku/simple_html_dom
         */
        self::$dom = HtmlDomParser::str_get_html($content);

        /**
         * handing img tags
         */
        self::handleImg();

        /**
         * for inline background images.
         */
        self::handleImgBG();

        return self::$dom;
    }

    /**
     * replaces images(img tag) with proper avif file
     * @return void
     */
    public static function handleImg()
    {

        foreach (self::$dom->getElementsByTagName('img') as &$image) {

            $image->setAttribute('converter', 'avif-express');
            if (self::$enableLazyLoading) $image->setAttribute('loading', 'lazy');

            if (self::$isAvifSupported) {

                $image->setAttribute('src', self::replaceImgSrc($image->getAttribute('src')));
                $image->setAttribute('srcset', self::replaceImgSrcSet($image->getAttribute('srcset')));
            }
            if (!self::$isAvifSupported && self::$fallbackType == 'webp') {

                $image->setAttribute('src', self::webpReplaceImgSrc($image->getAttribute('src')));
                $image->setAttribute('srcset', self::webpReplaceImgSrcSet($image->getAttribute('srcset')));
            }
        }
    }

    /**
     * replaces inline css with images with avif images
     * @return void
     */
    public static function handleImgBG()
    {
        $replaceCallback = function ($matches) {
            $imageUrl = trim($matches[2]);
            $updatedImageUrl = $imageUrl;

            if (self::$isAvifSupported) {
                $updatedImageUrl = self::replaceImgSrc($imageUrl);
            } elseif (self::$fallbackType === 'webp') {
                $updatedImageUrl = self::webpReplaceImgSrc($imageUrl);
            }

            return str_replace($imageUrl, $updatedImageUrl, $matches[0]);
        };

        // 1. Inline style attributes
        foreach (self::$dom->find('[style]') as &$element) {
            $style = $element->getAttribute('style');
            $style = preg_replace_callback(
                '/\bbackground(?:-image)?\s*:\s*[^;{]*?url\((["\']?)([^"\')]+)\1\)/i',
                $replaceCallback,
                $style
            );
            $element->setAttribute('style', $style);
        }

        // 2. <style> tag contents
        foreach (self::$dom->find('style') as &$styleTag) {
            $css = $styleTag->innertext;
            $css = preg_replace_callback(
                '/url\((["\']?)([^"\')]+)\1\)/i',
                $replaceCallback,
                $css
            );
            $styleTag->innertext = $css;
        }
    }





    /**
     * replace image url with .avif extension
     * if server support exist for avif images it will create that on the fly if file not existing
     * else - It will try creating webp and serve it
     * if that is not possible then it will return original
     */
    public static function replaceImgSrc($imageUrl)
    {
        $imageUrl = trim($imageUrl);

        /**
         * checking if an image having a supported extension or not
         */
        if (!self::isSupportedExtension($imageUrl)) return $imageUrl;

        /**
         * Checking if the image source form same domain or not
         * checking if the source file existing on the server or not
         * if domain is different or file not present return the original image url
         */
        if (!self::isValidImageUrl($imageUrl)) return $imageUrl;

        /**
         * creating the avif image url
         */
        $avifImageUrl = dirname($imageUrl) . '/' . pathinfo($imageUrl, PATHINFO_FILENAME) . '.avif';

        /**
         * checking if its already existing or not
         * if yes then return it.
         */
        if (self::isFileExists($avifImageUrl)) return $avifImageUrl;


        /**
         * creating on the fly if server support that
         * else try webp conversion
         */
        if (self::$enableOnTheFlyConversion && Utility::isLocalAvifConversionSupported()) {


            $imagePathSrc = Utility::attachmentUrlToPath($imageUrl);
            $imagePathDest = rtrim($imagePathSrc, '.' . pathinfo($imagePathSrc, PATHINFO_EXTENSION)) . '.avif';

            Image::convert($imagePathSrc, $imagePathDest, Options::getImageQuality(), Options::getComSpeed());

            /**
             * checking if the created file is valid or not
             * due to GD bug(inherited from libavif) sometimes it creates a file with 0 byte
             */
            if (file_exists($imagePathDest) && filesize($imagePathDest) > 0) {
                return $avifImageUrl;
            }
        } elseif (self::$fallbackType == 'webp') {
            return self::webpReplaceImgSrc($imageUrl);
        }
        return $imageUrl;
    }

    /**
     * replacing srcset urls
     */
    public static function replaceImgSrcSet($srcset)
    {
        if (!$srcset) return;
        $srcset = explode(' ', $srcset);

        foreach ($srcset as $k => &$v) {
            $v = trim($v);
            /**
             * checking if it's a real url belongs to same domain
             * and the file really exists
             * if it fails skip and jump on next iterate
             */
            if (!self::isValidImageUrl($v)) continue;

            /**
             * checking the extension against allowed ones
             */
            if (!self::isSupportedExtension($v)) continue;

            /**
             * creating new image file url with altered extension
             */
            $avifImageUrl = rtrim($v, '.' . pathinfo($v, PATHINFO_EXTENSION)) . '.avif';

            /**
             * checking if new file exists or not
             * if exists then replace the original url with altered url
             * and jump on next iteration
             */
            if (self::isFileExists($avifImageUrl)) {
                $v = $avifImageUrl;
                continue;
            }

            /**
             * creating on the fly avif image file if server support that
             * else try webp conversion if user has set fallback image to webp
             * OR else jump to next iteration
             */
            if (self::$enableOnTheFlyConversion && Utility::isLocalAvifConversionSupported()) {


                $imagePathSrc = Utility::attachmentUrlToPath($v);
                $imagePathDest = rtrim($imagePathSrc, '.' . pathinfo($imagePathSrc, PATHINFO_EXTENSION)) . '.avif';

                Image::convert($imagePathSrc, $imagePathDest, Options::getImageQuality(), Options::getComSpeed());

                /**
                 * checking if the created file is valid or not
                 * due GD bug sometimes it creates a file with 0 byte
                 */
                if (file_exists($imagePathDest) && filesize($imagePathDest) > 0) {
                    $v = $avifImageUrl;
                    continue;
                }
            } elseif (self::$fallbackType == 'webp') {
                $v = self::webpReplaceImgSrc($v);
            }
        }
        return implode(' ', $srcset);
    }

    /**
     * to replace src urls with .webp extension
     */
    public static function webpReplaceImgSrc($imageUrl)
    {
        $imageUrl = trim($imageUrl);

        /**
         * Checking if the images are already optimized images
         */
        if (!self::isSupportedExtension($imageUrl)) return $imageUrl;

        /**
         * checking if the url belongs to this site or not
         * checking if source file exists in server
         * if not return the original image url
         */
        if (!self::isValidImageUrl($imageUrl)) return $imageUrl;

        /**
         * checking if the webp file already existing in server or not
         * if exist then serve it
         */
        $newImageUrl = dirname($imageUrl) . DIRECTORY_SEPARATOR . pathinfo($imageUrl, PATHINFO_FILENAME) . '.webp';

        /**
         * checking if the file already existing or not
         * if yes then return it.
         */
        if (self::isFileExists($newImageUrl)) return $newImageUrl;

        /**
         * starting conversion(on the fly)
         * after the conversion webpConvert() will save the new file
         * in the same location
         */
        $conversionStatus = Image::webpConvert(Utility::attachmentUrlToPath($imageUrl));

        /**
         * if conversion failed return the original
         */
        if (!$conversionStatus) return $imageUrl;

        /**
         * finally retuning the converted image url
         */
        return $newImageUrl;
    }

    /**
     * to replace srcset urls with .webp extension
     */
    public static function webpReplaceImgSrcSet($srcset)
    {
        if (!$srcset) return;

        $srcset = explode(' ', $srcset);
        foreach ($srcset as $k => &$v) {

            $v = trim($v);

            /**
             * if present or the url does not belong to our domain
             * in any of that situation skip it.
             */
            if (!self::isValidImageUrl($v)) continue;

            /**
             * checking if image is supported
             */
            if (!self::isSupportedExtension($v)) continue;

            /**
             * creating the webp file url
             */
            $webpImageUrl = rtrim($v, '.' . pathinfo($v, PATHINFO_EXTENSION)) . '.webp';

            /**
             * checking if the webp file exist or not
             */
            if (self::isFileExists($webpImageUrl)) {
                $v = $webpImageUrl;
            } else {
                /**
                 * if file not existing then create one and change file extension
                 */
                $conversionStatus = Image::webpConvert(Utility::attachmentUrlToPath($v));

                /**
                 * checking if webp conversion failed or not OR after conversion file not existing
                 * in above case skip the url replacement and continue with next one
                 */
                if (!$conversionStatus || !self::isFileExists($v)) continue;

                /**
                 * finally replacing the url with webp url
                 */
                $v = $webpImageUrl;
            }
        }
        unset($v);
        return implode(' ', $srcset);
    }


    /**
     * checks if an image having a supported extension or not
     * @param string $url
     * @return bool true|false
     */
    public static function isSupportedExtension(string $url)
    {
        if (!isset($url)) return false;
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        return $ext && in_array($ext, array('jpg', 'jpeg', 'png'));
    }

    /**
     * checks if it is a valid image url and real file present in server
     * @param $url
     * @return bool
     */
    public static function isValidImageUrl($url)
    {
        return strpos($url, get_bloginfo('url')) !== false && self::isFileExists($url);
    }

    /**
     * isFileExists
     * checks if provided url having real file
     * @param string $url
     * @return boolean true on success and false on fail
     */
    public static function isFileExists(string $url): bool
    {
        $path = Utility::attachmentUrlToPath($url);
        if (!$path) return false;
        if (!file_exists($path)) return false;
        return true;
    }
}
