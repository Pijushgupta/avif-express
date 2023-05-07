<?php

namespace Avife\frontend;

if (!defined('ABSPATH')) exit;

use Avife\common\Options;
use Avife\common\Image;
use voku\helper\HtmlDomParser;

class Html {
	public static function init() {

		add_action('template_redirect', array('Avife\frontend\Html', 'checkConditions'), 9999);
	}
	public static function checkConditions() {
		if (is_admin() || is_feed() || wp_doing_ajax() || Options::getOperationMode() == 'inactive') return;
		ob_start('Avife\frontend\Html::getContent');
	}

	public static function getContent($content) {

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
		 * Checking if browser supporting Avif image support or not 
		 * Checking if fallback Webp enabled 
		 * if both are false returning the content as it is
		 */
		$httpAccept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT']:'';
		$isAvifSupported = strpos($httpAccept, 'image/avif');
		$isFallBackWebpEnabled = AVIF_WEBP_POSSIBLE; 
		if ( $isAvifSupported == false && $isFallBackWebpEnabled != true) {
			return $content;
		}


		

		$dom = HtmlDomParser::str_get_html($content);

		/**
		 *  iterating all the img element 
		 */
		foreach ($dom->getElementsByTagName('img') as &$image) {

			/**
			 * changing extension single image url to .avif
			 * if avif supported by browser
			 */
			if($isAvifSupported == true):
				$image->setAttribute('src', self::replaceImgSrc($image->getAttribute('src')));
				$image->setAttribute('srcset', self::replaceImgSrcSet($image->getAttribute('srcset')));
			endif;

			if($isAvifSupported == false && $isFallBackWebpEnabled == true):
			
				$image->setAttribute('src', self::webpReplaceImgSrc($image->getAttribute('src')));
				$image->setAttribute('srcset', self::webpReplaceImgSrcSet($image->getAttribute('srcset')));
			endif;

		}
		return $dom;
	}

	/**
	 * replace image url with .avif extension
	 */
	public static function replaceImgSrc($imageUrl) {
		/**
		 * Checking if the image source form same domain or not
		 * and the file really exists
		 */
		if (str_contains($imageUrl, get_bloginfo('url')) && self::isFileExists($imageUrl)) {
			return $imageUrl = rtrim($imageUrl, '.' . pathinfo($imageUrl, PATHINFO_EXTENSION)) . '.avif';
		}
		return $imageUrl;
	}
	/**
	 * replacing srcset urls
	 */
	public static function replaceImgSrcSet($srcset) {
		if (!$srcset) return;
		$srcset = explode(' ', $srcset);

		foreach ($srcset as $k => &$v) {
			/**
			 * checking if its a real url belongs to same domain 
			 * and the file really exists
			 */
			if (str_contains($v, get_bloginfo('url')) && self::isFileExists($v)) {
				/**
				 * getting the extension
				 */
				$ext = pathinfo($v, PATHINFO_EXTENSION);
				/**
				 * checking the extension against allowed ones
				 */
				if ($ext && in_array($ext, array('jpg', 'jpeg', 'png', 'webp'))) {
					/**
					 * finally creating the file url with .avif extension
					 */
					$v = rtrim($v, '.' . pathinfo($v, PATHINFO_EXTENSION)) . '.avif';
				}
				unset($ext);
			}
		}
		return implode(' ', $srcset);
	}

	

	public static function webpReplaceImgSrc($imageUrl){
		/**
		 * checking if the url belongs to this site or not
		 * checking if source file exists in server
		 * if not return the original image url
		 */
		if(!str_contains($imageUrl, get_bloginfo('url')) || !self::isFileExists($imageUrl)) return $imageUrl;

		
		/**
		 * checking if the webp file already existing in server or not
		 * if exist then serve it
		 */
		$newImageUrl = dirname($imageUrl).'/'.pathinfo($imageUrl, PATHINFO_FILENAME).'.webp';
		if(Image::attachmentUrlToPath($newImageUrl) != false) return $newImageUrl;

		/**
		 * starting conversion
		 * after the conversion webpConvert() will save the new file 
		 * in the same location
		 */
		$conversionStatus = Image::webpConvert(Image::attachmentUrlToPath($imageUrl));

		/**
		 * if conversion failed return the original
		 */
		if($conversionStatus !== true) return $imageUrl;
		
		/**
		 * finally retuning the converted image url
		 */
		return $newImageUrl;


	}

	public static function webpReplaceImgSrcSet($srcset){
		if (!$srcset) return;

		$srcset = explode(' ', $srcset);
		foreach($srcset as $k => &$v){
			
			if(!str_contains($v, get_bloginfo('url')) || self::isFileExists($v) != true) continue;
			$conversionStatus = Image::webpConvert(Image::attachmentUrlToPath($v));
			if($conversionStatus !== true) continue;
			$v = dirname($v).'/'.pathinfo($v, PATHINFO_FILENAME).'.webp';
		}
		unset($v);
		return implode(' ', $srcset);
	}

	

	/**
	 * isFileExists
	 * checks if provided url having real file 
	 * @param  string $url
	 * @return boolean true on success and false on fail
	 */
	public static function isFileExists($url) {
		$path = Image::attachmentUrlToPath($url);
		if ($path === false) return false;
		if (file_exists($path) === false) return false;
		return true;
	}
}
