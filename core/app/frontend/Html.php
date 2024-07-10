<?php

namespace Avife\frontend;

if (!defined('ABSPATH')) exit;

use Avife\common\Options;
use Avife\common\Image;
use Avife\common\Cookie;
use voku\helper\HtmlDomParser;


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
        $httpAccept = $_SERVER['HTTP_ACCEPT'] ?? '';
        self::$isAvifSupported = strpos($httpAccept, 'image/avif');

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
        var_dump(self::$enableOnTheFlyConversion);
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

        foreach (self::$dom->find('[style*=background-image]') as &$element) {
            $style = $element->getAttribute('style');
            /**
             * /..../ delimiter
             * url to match "url" string in style element
             * \( to match "(" after "url" string, "(" is special char, so we need to escape it
             * ( start of a capturing group
             * \'|" , "'" is a special chan so escaping it with \', "|" is logical-or or alteration operator, " is just
             * double quote . So its target '.....' or ".....". ex: background-image:url(".....")
             * ) ending of a capturing group
             * ? matches the shortest possible match
             * () creates a new capturing group
             * .* targets all the char inside '.....' or "....." or just ..... , excepts new line char
             * \1 referencing first capture group for ' or " for string closing char. \\1 = \1 , \ is escaping "\"
             * \) closing the of url(.....")" . \ is escaping ).
             * /..../ delimiter ends
             *
             * $style source
             * $matches destination
             * $matches[1] - Contains the " or '
             * $matches[2] - contains the url without any quote
             */
            preg_match('/url\((\'|")?(.*?)\\1\)/', $style, $matches);

            if (isset($matches[2])) {

                $updatedImageUrl = $imageUrl = $matches[2];

                if (self::$isAvifSupported) {
                    $updatedImageUrl = self::replaceImgSrc($imageUrl);
                } elseif (self::$isAvifSupported && self::$fallbackType == 'webp') {
                    $updatedImageUrl = self::webpReplaceImgSrc($imageUrl);
                }

                $newStyle = str_replace($imageUrl, $updatedImageUrl, $style);
                $element->setAttribute('style', $newStyle);
            }
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

        /**
         * Checking if the images are already optimized images
         * ignore if image belongs to following formats, svg, webp or avif.
         */
        $fileExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);
        if (strtolower($fileExtension) == 'svg' || strtolower($fileExtension) == 'webp' || strtolower($fileExtension) == 'avif') {
            return $imageUrl;
        }

        /**
         * Checking if the image source form same domain or not
         * checking if the source file existing on the server or not
         * if domain is different or file not existing return the original image url
         */
        if (strpos($imageUrl, get_bloginfo('url')) === false || !self::isFileExists($imageUrl)) return $imageUrl;

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
        if (self::$enableOnTheFlyConversion && (AVIFE_IMAGICK_VER != 0 || function_exists('imageavif'))) {


            $imagePathSrc = Image::attachmentUrlToPath($imageUrl);
            $imagePathDest = rtrim($imagePathSrc, '.' . pathinfo($imagePathSrc, PATHINFO_EXTENSION)) . '.avif';

            Image::convert($imagePathSrc, $imagePathDest, Options::getImageQuality(), Options::getComSpeed());

            /**
             * checking if the created file is valid or not
             * due to GD bug sometimes it creates a file with 0 byte
             */
            if (file_exists($imagePathDest) && filesize($imagePathDest) > 0) {
                return $avifImageUrl;
            }

        }elseif(self::$fallbackType == 'webp') {
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
            /**
             * checking if it's a real url belongs to same domain
             * and the file really exists
             * if it fails skip and jump on next iterate
             */
            if(!self::isValidImageUrl($v)) continue;

            /**
             * checking the extension against allowed ones
             */
            if(!self::isSupportedExtension($v)) continue;

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
            if (self::$enableOnTheFlyConversion && (AVIFE_IMAGICK_VER != 0 || function_exists('imageavif'))) {


                $imagePathSrc = Image::attachmentUrlToPath($v);
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

            }elseif(self::$fallbackType == 'webp'){
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

        /**
         * Checking if the images are already optimized images
         */
        $fileExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);
        if (in_array(strtolower($fileExtension), array('svg', 'webp', 'avif'))) {
            return $imageUrl;
        }

        /**
         * checking if the url belongs to this site or not
         * checking if source file exists in server
         * if not return the original image url
         */
        if (strpos($imageUrl, get_bloginfo('url')) === false || !self::isFileExists($imageUrl)) return $imageUrl;

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
        $conversionStatus = Image::webpConvert(Image::attachmentUrlToPath($imageUrl));

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

            /**
             * if file do not exists or the url does not belong to our domain
             * in any of that situation skip it.
             */
            if (strpos($v, get_bloginfo('url')) === false || !self::isFileExists($v)) continue;

            $ext = pathinfo($v, PATHINFO_EXTENSION);
            if (!in_array($ext, array('jpg', 'jpeg', 'png'))) {
                continue;
            }

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
                $conversionStatus = Image::webpConvert(Image::attachmentUrlToPath($v));

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
     * @param string &$imageUrl
     * @return void
     */
    public static function createFallBackWebP(&$imageUrl){
            if(self::$fallbackType == 'webp'){
                $imageUrl = self::webpReplaceImgSrc($imageUrl);
            }
    }

    /**
     * checks if an image having a supported extension or not
     * @param string $url
     * @return bool true|false
     */
    public static function isSupportedExtension(string $url){
        if(!isset($url)) return false;
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        return $ext && in_array($ext, array('jpg', 'jpeg', 'png'));
    }

    /**
     * checks if it is  a valid image url and real file present in server
     * @param $url
     * @return bool
     */
    public static function isValidImageUrl($url){
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
        $path = Image::attachmentUrlToPath($url);
        if (!$path) return false;
        if (!file_exists($path)) return false;
        return true;
    }
}
