<?php

namespace Avife\frontend;

if (!defined('ABSPATH')) exit;

use Avife\common\Options;
use Avife\common\Image;
use Avife\common\Cookie;
use voku\helper\HtmlDomParser;


class Html {

	public static $isAvifSupported = false;

	public static function init() {
		
		add_action('template_redirect', array('Avife\frontend\Html', 'checkConditions'), 9999);
	}
	public static function checkConditions() {
		if (is_admin() || is_feed() || wp_doing_ajax() || Options::getOperationMode() == 'inactive') return;
		/**
		 * checking and storing, if the browser support avif format 
		 */
		$httpAccept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT']:'';
		self::$isAvifSupported = strpos($httpAccept, 'image/avif');

		/**
		 * creating cookie regarding avif support
		 * for: nginx, apache2, liteSpeed and etc
		 * based on this cookie value server can decide to cache or not to cache
		 */
		Cookie::setAvifCookie(self::$isAvifSupported);

		/**
		 * starting content replacement work
		 */
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
		
		
		
		
		
		$dom  = HtmlDomParser::str_get_html($content);
		
		/**
		 * this to compliment for very old browser that don't support avif 
		 * then either switch to 'original' or 'webp' defined by user
		 */
		$fallbackType = strtolower(Options::getFallbackMode());

		/**
		 * for img tags
		 */
		foreach ($dom->getElementsByTagName('img') as &$image) {
			
			if(self::$isAvifSupported != false ){

				

				if(WP_DEBUG == true){
					error_log('Avif express: Avif images supported on the browser');
				}

				$image->setAttribute('src', self::replaceImgSrc($image->getAttribute('src')));
				$image->setAttribute('srcset', self::replaceImgSrcSet($image->getAttribute('srcset')));
			}
			if(self::$isAvifSupported == false &&  $fallbackType == 'webp'){

				
				if(WP_DEBUG == true){
					error_log('Avif express:Avif images not supported on the browser');
				}

				$image->setAttribute('src', self::webpReplaceImgSrc($image->getAttribute('src')));
				$image->setAttribute('srcset', self::webpReplaceImgSrcSet($image->getAttribute('srcset')));
				
			}
		}
		
		/**
		 * for background images.
		 */
		foreach ($dom->find('[style*=background-image]') as &$element) {
            $style = $element->getAttribute('style');
            preg_match('/url\((\'|")?(.*?)\\1\)/', $style, $matches);
            if (isset($matches[2])) {
                $imageUrl = $matches[2];
                $updatedImageUrl = self::$isAvifSupported ? self::replaceImgSrc($imageUrl) : ($fallbackType == 'webp' ? self::webpReplaceImgSrc($imageUrl) : $imageUrl);
                $newStyle = str_replace($imageUrl, $updatedImageUrl, $style);
                $element->setAttribute('style', $newStyle);
            }
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
		 * ignore if image belongs to following formats, svg, webp or avif.
		 */
		$fileExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);
		if(strtolower($fileExtension) == 'svg' || strtolower($fileExtension) == 'webp' || strtolower($fileExtension) == 'avif'){
			return $imageUrl;
		}  

		/**
		 * Checking if the image source form same domain or not
		 * checking if the source file existing on the server or not
		 * if domain is different or file not existing return the original image url
		 */
		if(strpos($imageUrl, get_bloginfo('url')) === false || !self::isFileExists($imageUrl)) return  $imageUrl;

		/**
		 * creating the avif image url
		 */
		$avifImageUrl = dirname($imageUrl).'/'.pathinfo($imageUrl, PATHINFO_FILENAME).'.avif';

		/**
		 * checking if its already existing or not
		 * if yes then return it.
		 */
		if(self::isFileExists($avifImageUrl)) return $avifImageUrl;
		
		
		/**
		 * creating on the fly if server support that 
		 * else try webp conversion
		 */
		if(!Options::getOnTheFlyAvif()){
			if(AVIFE_IMAGICK_VER != 0 || function_exists('imageavif')){
				
				$imagePathSrc = Image::attachmentUrlToPath($imageUrl);
				$imagePathDest = rtrim($imagePathSrc, '.' . pathinfo($imagePathSrc, PATHINFO_EXTENSION)) . '.avif';

				Image::convert($imagePathSrc,$imagePathDest,Options::getImageQuality(),Options::getComSpeed());

				/**
				 * checking if the created file is valid or not
				 * due to GD bug sometimes it creates a file with 0 byte
				 */

				if(file_exists($imagePathDest) && filesize($imagePathDest) > 0){
					return $avifImageUrl;
				}
				
			}
			/**
			 * If on the fly failed then log it
			 */
		
			
			if(WP_DEBUG){
				error_log('Avif on the fly conversion failed');
			}
		}
		/**
		 * if server capable of generating webp then return that else return original
		 */
		return self::webpReplaceImgSrc($imageUrl);
		
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
				 * also this will eliminate non file strings
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
						if(!Options::getOnTheFlyAvif()){
							if(AVIFE_IMAGICK_VER != 0 || function_exists('imageavif')){
								$imagePathSrc = Image::attachmentUrlToPath($v);
								$imagePathDest = rtrim($imagePathSrc, '.' . pathinfo($imagePathSrc, PATHINFO_EXTENSION)) . '.avif';
								Image::convert($imagePathSrc,$imagePathDest,Options::getImageQuality(),Options::getComSpeed());
								/**
								 * checking if the created file is valid or not
								 * due GD bug sometimes it creates a file with 0 byte
								 */
								if(file_exists($imagePathDest) && filesize($imagePathDest) > 0){
									$v = $avifImageUrl;
								}else{
									
									if(WP_DEBUG){
										trigger_error('Avif express:Avif on the fly conversion failed');
									}
									$v = self::webpReplaceImgSrc($v);
								}
								
							}else{
								$v = self::webpReplaceImgSrc($v);
							}
						}
						else{
							$v = self::webpReplaceImgSrc($v);
						}
					}
				}
				unset($ext);
			}
		}
		return implode(' ', $srcset);
	}

	/**
	 * to replace src urls with .webp extension
	 */
	public static function webpReplaceImgSrc($imageUrl){
		
		/**
		 * Checking if the images are already optimized images 
		 */
		$fileExtension = pathinfo($imageUrl, PATHINFO_EXTENSION);
        if(in_array(strtolower($fileExtension), array('svg', 'webp', 'avif'))){
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
		$newImageUrl = dirname($imageUrl).DIRECTORY_SEPARATOR.pathinfo($imageUrl, PATHINFO_FILENAME).'.webp';

		/**
		 * checking if the file already existing or not
		 * if yes then return it.
		 */
		if(self::isFileExists($newImageUrl)) return $newImageUrl;

		/**
		 * starting conversion(on the fly)
		 * after the conversion webpConvert() will save the new file 
		 * in the same location
		 */
		$conversionStatus = Image::webpConvert(Image::attachmentUrlToPath($imageUrl));

		/**
		 * if conversion failed return the original
		 */
		if(!$conversionStatus) return $imageUrl;
		
		/**
		 * finally retuning the converted image url
		 */
		return $newImageUrl;


	}
	
	/**
	 * to replace srcset urls with .webp extension
	 */
	public static function webpReplaceImgSrcSet($srcset){
		if (!$srcset) return;

		$srcset = explode(' ', $srcset);
		foreach($srcset as $k => &$v){
			
			/**
			 * if file do not exists or the url does not belong to our domain
			 * in any of that situation skip it.
			 */
			if(strpos($v, get_bloginfo('url')) === false || !self::isFileExists($v)) continue;

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
			if(self::isFileExists($webpImageUrl)){
				$v = $webpImageUrl;

			}else{
				/**
				 * if file not existing then create one and change file extension
				 */
				$conversionStatus = Image::webpConvert(Image::attachmentUrlToPath($v));
				
				/**
				 * checking if webp conversion failed or not OR after conversion file not existing
				 * in above case skip the url replacement and continue with next one
				 */
				if(!$conversionStatus || !self::isFileExists($v)) continue;
				
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
