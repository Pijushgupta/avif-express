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
		 * if rendering inactive return the original content
		 */
		
		if(Options::getOperationMode() != 'active') return $content;
		

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
		
	
		
		$dom  = HtmlDomParser::str_get_html($content);

		/**
		 * for img tags
		 */
		foreach ($dom->getElementsByTagName('img') as &$image) {
			
			if($isAvifSupported != false ){
				$image->setAttribute('src', self::replaceImgSrc($image->getAttribute('src')));
				$image->setAttribute('srcset', self::replaceImgSrcSet($image->getAttribute('srcset')));
			}
			if($isAvifSupported == false){
				$image->setAttribute('src', self::webpReplaceImgSrc($image->getAttribute('src')));
				$image->setAttribute('srcset', self::webpReplaceImgSrcSet($image->getAttribute('srcset')));
			}
		}
		
		/**
		 * for background image.
		 */
		foreach ($dom->find('[style*=background-image]') as &$element){

			$style = $element->getAttribute('style');
			
            preg_match('/url\((.*?)\)/', $style, $matches);
            $imageUrl = $matches[1];
			
			if($isAvifSupported != false){
				$updatedImageUrl = self::replaceImgSrc($imageUrl);
			}

			if($isAvifSupported == false){
				$updatedImageUrl = self::webpReplaceImgSrc($imageUrl);
			}
			
			$newStyle = str_replace($imageUrl, $updatedImageUrl, $style);
			$element->setAttribute('style',$newStyle);
		}
		
		
		return $dom;
	}

	/**
	 * replace image url with .avif extension
	 * if server support exist for avif images it will create that on the fly if file not existing
	 * else - It will try creating webp and serve it 
	 * if that is not possible then it will return original 
	 */
	public static function replaceImgSrc($imageUrl) {
		
		/**
		 * Checking if the images are already optimized images 
		 */
		$flieExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);
		if(strtolower($flieExtension) == 'svg' || strtolower($flieExtension) == 'webp' || strtolower($flieExtension) == 'avif'){
			return $imageUrl;
		}  

		/**
		 * Checking if the image source form same domain or not
		 */
		if(strpos($imageUrl, get_bloginfo('url')) === false) return  $imageUrl;

		/**
		 * creating the avif iamge url
		 */
		$avifImageUrl = rtrim($imageUrl, '.' . $flieExtension) . '.avif';

		/**
		 * checking if its already existing or not
		 * if yes then return it.
		 */
		if(self::isFileExists($avifImageUrl)) return $avifImageUrl;

		if(self::isFileExists($imageUrl) == false) return $imageUrl;
		
		
		/**
		 * creating on the fly if server support that 
		 * else try webp conversion
		 */
		if(AVIFE_IMAGICK_VER != 0 || function_exists('imageavif')){
			
			$imagePathSrc = Image::attachmentUrlToPath($imageUrl);
			$imagepathDest = Image::attachmentUrlToPath($avifImageUrl);
			Image::convert($imagePathSrc,$imagepathDest,Options::getImageQuality(),Options::getComSpeed());
			return $avifImageUrl;
		}else{
			return self::webpReplaceImgSrc($imageUrl);
		}
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
			if (strpos($v, get_bloginfo('url')) !== false && self::isFileExists($v)) {
				/**
				 * getting the extension
				 */
				$ext = pathinfo($v, PATHINFO_EXTENSION);
				/**
				 * checking the extension against allowed ones
				 * also this will eleminate non file strings
				 */
				if ($ext && in_array($ext, array('jpg', 'jpeg', 'png'))) {

					$avifImageUrl = rtrim($v, '.' . pathinfo($v, PATHINFO_EXTENSION)) . '.avif';
					
					/**
					 * checking if file exists or not 
					 */
					if(self::isFileExists($avifImageUrl)){
						/**
						 * creating the file url with .avif extension
						 */
						$v = $avifImageUrl;

					}else{
						/**
						 * creating on the fly if server support that 
						 * else try webp conversion
						 */
						if(AVIFE_IMAGICK_VER != 0 || function_exists('imageavif')){
							$imagePathSrc = Image::attachmentUrlToPath($v);
							$imagepathDest = Image::attachmentUrlToPath($avifImageUrl);
							Image::convert($imagePathSrc,$imagepathDest,Options::getImageQuality(),Options::getComSpeed());
							$v = $avifImageUrl;
						}else{
							$v = self::webpReplaceImgSrc($v);
						}

					}

					
					
				}
				unset($ext);
			}
		}
		return implode(' ', $srcset);
	}

	public static function webpReplaceImgSrc($imageUrl){
		
		/**
		 * Checking if the images are already optimized images 
		 */
		$flieExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);
		if(strtolower($flieExtension) == 'svg' || strtolower($flieExtension) == 'webp'|| strtolower($flieExtension) == 'avif'){
			return $imageUrl;
		} 

		/**
		 * checking if the url belongs to this site or not
		 * checking if source file exists in server
		 * if not return the original image url
		 */
		if(strpos($imageUrl, get_bloginfo('url')) === false || !self::isFileExists($imageUrl)) return $imageUrl;

		
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
		if($conversionStatus == false) return $imageUrl;
		
		/**
		 * finally retuning the converted image url
		 */
		return $newImageUrl;


	}

	public static function webpReplaceImgSrcSet($srcset){
		if (!$srcset) return;

		$srcset = explode(' ', $srcset);
		foreach($srcset as $k => &$v){
			
			/**
			 * if file do not exists or the url does not belong to our domain
			 * in any of that situation skip it.
			 */
			if(strpos($v, get_bloginfo('url')) === false || self::isFileExists($v) != true) continue;

			$ext = pathinfo($v, PATHINFO_EXTENSION);
			if (!in_array($ext, array('jpg', 'jpeg', 'png'))) {
				continue;
			}
			/**
			 * checking if the webp file exist or not
			 */
			$webpImageUrl = rtrim($v, '.' . pathinfo($v, PATHINFO_EXTENSION)) . '.webp';
			if(self::isFileExists($webpImageUrl)){
				$v = $webpImageUrl;

			}else{
				/**
				 * if file not existing then create one and change file extension
				 */
				$conversionStatus = Image::webpConvert(Image::attachmentUrlToPath($v));
				if($conversionStatus == false) continue;
				$v = $webpImageUrl;
				
				
			}

			
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
		if ($path == false) return false;
		if (file_exists($path) == false) return false;
		return true;
	}
}
