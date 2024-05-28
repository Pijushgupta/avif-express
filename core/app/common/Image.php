<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit();

use WP_Error;
use Avife\common\Options;

class Image {

	/**
	 * activate
	 * Adding our methods to wordpress hooks
	 * @return void
	 */
	public static function activate() {

		
		/**
		 * Checking if auto conversion enabled 
		 */
		if (Options::getAutoConvtStatus()) {

			add_action('wp_generate_attachment_metadata', array('Avife\common\Image', 'beforeConvert'), 10, 2);
		}

		add_action('delete_attachment',  array('Avife\common\Image', 'delete'), 10, 3);
	}

	/**
	 * beforeConvert
	 * Finding attachment and all its sizes and converting them, using self::convert method
	 * @param  mixed $metadata
	 * @param  mixed $attachment_id
	 * @return void
	 */
	public static function beforeConvert($metadata, $attachment_id) {

		$attachment = get_post($attachment_id);
		$mimeType = $attachment->post_mime_type;
		if (!in_array($mimeType, array('image/jpeg', 'image/png', 'image/jpg', 'image/webp',))) return $metadata;

		$quality = Options::getImageQuality();
		$speed = Options::getComSpeed();

		/**
		 * converting original image(s)
		 */
		$originalImages[0] =  $attachment->guid;

		$uploadDirInfo = wp_upload_dir();
		$originalImages[1] = $uploadDirInfo['baseurl'] . DIRECTORY_SEPARATOR . $metadata['file'];

		if ($originalImages[0] != $originalImages[1]) {
			foreach ($originalImages as $originalImage) {

				$srcPath = self::attachmentUrlToPath($originalImage);

				if ($srcPath != false && $srcPath != '') {
					$desPath = rtrim($srcPath, '.' . pathinfo($srcPath, PATHINFO_EXTENSION)) . '.avif';

					if(Options::getConversionEngine() == 'local'){
						$avifsupport = '0';
						if(function_exists('imageavif') && function_exists('gd_info') && gd_info()['AVIF Support'] != '') $avifsupport = '1';
		
						$hasImagick = '0';
						if(extension_loaded('imagick') && class_exists('Imagick') && AVIFE_IMAGICK_VER > 0){
							$imagick = new \Imagick();
							$formats = $imagick->queryFormats();
							if (in_array('AVIF', $formats)) {
								$hasImagick = '1';
							}
						}
						if($avifsupport == '0' && $hasImagick == '0'){
							if(WP_DEBUG == true) error_log('Convert on Upload: Local avif support not found');
							if(Options::getEnableLogging()) new Aviflog('Avif express','warning', 'Convert on Upload: Local avif support not found',['file'=>__FILE__,'Line'=>__LINE__]);
							return $metadata;
						} 
						self::convert($srcPath, $desPath, $quality, $speed);
					}

					if(Options::getConversionEngine() == 'cloud'){
						$unConvertedAttachmentUrls[] = $originalImage;
						self::cloudConvert($unConvertedAttachmentUrls);
					}
					

				}
			}

		} else {

			$srcPath = self::attachmentUrlToPath($originalImages[0]);
			if ($srcPath != false && $srcPath != '') {
				$desPath = rtrim($srcPath, '.' . pathinfo($srcPath, PATHINFO_EXTENSION)) . '.avif';

				if(Options::getConversionEngine() == 'local'){
					$avifsupport = '0';
					if(function_exists('imageavif') && function_exists('gd_info') && gd_info()['AVIF Support'] != '') $avifsupport = '1';
		
					$hasImagick = '0';
					if(extension_loaded('imagick') && class_exists('Imagick') && AVIFE_IMAGICK_VER > 0){
						$imagick = new \Imagick();
						$formats = $imagick->queryFormats();
						if (in_array('AVIF', $formats)) {
							$hasImagick = '1';
						}
					}
					if($avifsupport == '0' && $hasImagick == '0'){
						if(WP_DEBUG == true) error_log('Convert on Upload: Local avif support not found');
						if(Options::getEnableLogging()) new Aviflog('Avif express','warning', 'Convert on Upload: Local avif support not found',['file'=>__FILE__,'Line'=>__LINE__]);
						return $metadata;
					} 
					self::convert($srcPath, $desPath, $quality, $speed);
				}

				if(Options::getConversionEngine() == 'cloud'){
					$unConvertedAttachmentUrls[] = $originalImages[0];
					self::cloudConvert($unConvertedAttachmentUrls);
				}
			}
		}
		/**
		 * ends
		 */

		/**
		 * converting generated thumbnails
		 */
		$fileDir = pathinfo(self::attachmentUrlToPath($originalImages[0]), PATHINFO_DIRNAME);
		$allSizes = $metadata['sizes'];
		foreach ($allSizes as $size) {
			$src = trailingslashit($fileDir) . $size['file'];
			if (file_exists($src)) {
				$des = rtrim($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) . '.avif';

				if(Options::getConversionEngine() == 'local'){
					$avifsupport = '0';
					if(function_exists('imageavif') && function_exists('gd_info') && gd_info()['AVIF Support'] != '') $avifsupport = '1';
	
					$hasImagick = '0';
					if(extension_loaded('imagick') && class_exists('Imagick') && AVIFE_IMAGICK_VER > 0){
						$imagick = new \Imagick();
						$formats = $imagick->queryFormats();
						if (in_array('AVIF', $formats)) {
							$hasImagick = '1';
						}
					}
					if($avifsupport == '0' && $hasImagick == '0'){
						if(WP_DEBUG == true) error_log('Convert on Upload: Local avif support not found');
						if(Options::getEnableLogging()) new Aviflog('Avif express','warning', 'Convert on Upload: Local avif support not found',['file'=>__FILE__,'Line'=>__LINE__]);
						return $metadata;
					} 
					self::convert($src, $des, $quality, $speed);
				}

				if(Options::getConversionEngine() == 'cloud'){
					$unConvertedAttachmentUrls[] = self::pathToAttachmentUrl($src);
					self::cloudConvert($unConvertedAttachmentUrls);
				}

			}
		}
		/**
		 * ends
		 */
		update_post_meta($attachment_id, 'avifexpressconverted', true);
		return $metadata;
	}

	/**
	 * delete
	 * finding the converted image and its thumbs and deleting
	 * @param  mixed $post_id
	 * @param  mixed $post
	 * @return void
	 */
	public static function delete($post_id, $post) {

		$orginalImageUrl = $post->guid;
		$attachment_meta =  wp_get_attachment_metadata($post_id);
		$orginalImageUrls[0] =  $orginalImageUrl;
		$uploadDirInfo = wp_upload_dir();
		$orginalImageUrls[1] = $uploadDirInfo['baseurl'] . '/' . $attachment_meta['file'];

		if ($orginalImageUrls[0] != $orginalImageUrls[1]) {
			foreach ($orginalImageUrls as $orginalImageUrl) {
				$orginalImagePath = self::attachmentUrlToPath($orginalImageUrl);
				$orginalImagePath = rtrim($orginalImagePath, '.' . pathinfo($orginalImagePath, PATHINFO_EXTENSION)) . '.avif';
				if (file_exists($orginalImagePath)) wp_delete_file($orginalImagePath);
			}
		} else {
			$orginalImagePath = self::attachmentUrlToPath($orginalImageUrls[0]);
			$orginalImagePath = rtrim($orginalImagePath, '.' . pathinfo($orginalImagePath, PATHINFO_EXTENSION)) . '.avif';
			if (file_exists($orginalImagePath)) wp_delete_file($orginalImagePath);
		}

		/**
		 * Deleting Thumbs
		 */
		$fileDir = pathinfo(self::attachmentUrlToPath($orginalImageUrls[0]), PATHINFO_DIRNAME);
		$sizes = $attachment_meta['sizes'];
		foreach ($sizes as $size) {
			$file = $fileDir . '/' . $size['file'];
			$file = rtrim($file, '.' . pathinfo($file, PATHINFO_EXTENSION)) . '.avif';
			if (file_exists($file)) wp_delete_file($file);
		}

		delete_post_meta($post_id, 'avifexpressconverted', false);
	}

	/**
	 * convert
	 * This Method actually convert image and save them  
	 * @param  mixed $src Path of the source file
	 * @param  mixed $des Path to save converted file 
	 * @param  mixed $quality Image Quality(0 - 100)
	 * @param  mixed $speed Conversion speed (0 - 10)
	 * @return void
	 */
	public static function convert($src, $des, $quality, $speed) {
		if (!$src && !$des && !$quality && !$speed) return false;

		$fileType = getimagesize($src)['mime'];
		// Try Imagick First
		if (extension_loaded('imagick') && class_exists('Imagick') && AVIFE_IMAGICK_VER > 0) {
			$imagick = new \Imagick();
			$formats = $imagick->queryFormats();
			if (in_array('AVIF', $formats)) {
				$imagick->readImage($src);
				$imagick->setImageFormat('avif');
				if ($quality > 0) {
					$imagick->setCompressionQuality($quality);
					$imagick->setImageCompressionQuality($quality);
				} else {
					$imagick->setCompressionQuality(1);
					$imagick->setImageCompressionQuality(1);
				}

				$imagick->writeImage($des);
				return;
			}
		}

		//Try GD -- going to be deprecated
		if(function_exists('imageavif') &&  function_exists('gd_info') && gd_info()['AVIF Support'] != '') {
			if ($fileType == 'image/jpeg' ||  $fileType == 'image/jpg') {
				$sourceGDImg = @imagecreatefromjpeg($src);
			}
			if ($fileType == 'image/png') {
				$sourceGDImg = @imagecreatefrompng($src);
			}
			if ($fileType == 'image/webp') {
				$sourceGDImg = @imagecreatefromwebp($src);
			}
			if (gettype($sourceGDImg) == 'boolean') return;
			@imageavif($sourceGDImg, $des, $quality, $speed);
			if (filesize($des) % 2 == 1) {
				file_put_contents($des, "\0", FILE_APPEND);
			}
			@imagedestroy($sourceGDImg);
			return;
		}

		if(Options::getEnableLogging()) new Aviflog('Avif express','warning', 'Local avif support not found',['file'=>__FILE__,'Line'=>__LINE__]);
		return;
		
		
	}
	
	/**
	 * function to do cloud image conversion 
	 * @param array $urls array of urls
	 */
	public static function cloudConvert(array $urls){
		
		//get the api key from option table 
		$apiKey = Options::getApiKey();

		//if api key is not set then return false
		if($apiKey == false) return false;

		//array to string conversion
		$jsonEncodedUrls = json_encode($urls);

		//actual payload with appended url, GET method
		$fullRequestUrl = AVIF_CLOUD_ADDRESS . '?urls=' . urlencode($jsonEncodedUrls);
		
		//add origin to the request header
		$origin = str_replace(['https://','http://'],'',strtolower(get_site_url()));

		
		//add origin and api key to the request header
		$requestHeader = array(
			'Origin' => $origin,
			'Api-key' => $apiKey,
			'Content-Type' => 'application/json',
			'Accept' => 'application/json'
		);		


		//conversion started
		//1. sending all urls(array of url) to the cloud server
		$cloudResponse = wp_remote_get($fullRequestUrl , array('headers' => $requestHeader));
		
		//2. getting the response contain all urls(array of url where url['src] = source url and url['dest'] is avif cloud server converted image url) 
		$body = wp_remote_retrieve_body($cloudResponse);
		
		//checking for any error and then logging it, if WP_DEBUG is true and then exit
		if(is_wp_error($cloudResponse)) {
			if(WP_DEBUG == true) error_log("Error:" . $cloudResponse->get_error_message());
			if(Options::getEnableLogging()) new Aviflog('Avif express','error', $cloudResponse->get_error_message() ,['file'=>__FILE__,'Line'=>__LINE__]);
			return false;
		}

		
		//converting json response to array of arrays
		$imageUrls = json_decode($body, true);
		
		//check server status code for any issue
		if($imageUrls['status'] !== 200){
			if(WP_DEBUG == true) error_log("Error:" . $imageUrls['msg']? $imageUrls['msg'] : $imageUrls['status']);
			if(Options::getEnableLogging()) new Aviflog('Avif express','error', $imageUrls['msg']? $imageUrls['msg'] : $imageUrls['status'] ,['file'=>__FILE__,'Line'=>__LINE__]);
			return false;
		}
		
		$imageUrls = $imageUrls['data'];

		//using wordpress file system class instead of php native file_get_contents()
		include_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		//this if condition to prevent to null
		if(is_array($imageUrls) || is_object($imageUrls)){
			foreach($imageUrls as $imageUrl){
			
				//creating destination file path form the source
				$srcImagePath = self::attachmentUrlToPath($imageUrl['src']);
				if($srcImagePath == false ) {
					if(WP_DEBUG == true) error_log('Unable to create absolute path from relative path of source image');
					if(Options::getEnableLogging()) new Aviflog('Avif express','error', 'Unable to create absolute path from relative path of source image' ,['file'=>__FILE__,'Line'=>__LINE__]);
					continue;
				}

				//creating destination path
				$pathInfo = pathinfo($srcImagePath);
				$avifFileName = $pathInfo["dirname"] . DIRECTORY_SEPARATOR . $pathInfo["filename"] . '.avif';
				
	
				//getting remote avif file 
				$response = wp_remote_get($imageUrl['dist']);
				if(is_wp_error($response)){
					if(WP_DEBUG == true) error_log("Avif Download Error:".$response->get_error_message());
					if(Options::getEnableLogging()) new Aviflog('Avif express','error', $response->get_error_message() ,['file'=>__FILE__,'Line'=>__LINE__]);
					continue;
				} 
				//retrieving avif file body content
				$body = wp_remote_retrieve_body($response);
				if (WP_Filesystem()) {
					global $wp_filesystem;
					if(!$wp_filesystem->put_contents($avifFileName, $body, FS_CHMOD_FILE)){
						if(WP_DEBUG == true) error_log('Unable to write avif file');
						if(Options::getEnableLogging()) new Aviflog('Avif express','error', 'Unable to write avif file' ,['file'=>__FILE__,'Line'=>__LINE__]);
						continue;
					}
					
				}else{
					if(WP_DEBUG == true) error_log('Unable to initialize the WP_filesystem');
					if(Options::getEnableLogging()) new Aviflog('Avif express','error', 'Unable to initialize the WP_filesystem' ,['file'=>__FILE__,'Line'=>__LINE__]);
				}
			}
		}
		
	}

	/**
	 * Converting image(s) to webp
	 * @param string $src source of the image
	 * @return bool TRUE|FALSE true on success, false on fail
	 */
	public static function webpConvert($src){
		if (!$src) return false;

		if(!extension_loaded('imagick')){
			if(WP_DEBUG == true) error_log('Imagick extension not loaded');
			if(Options::getEnableLogging()) new Aviflog('Avif express','error', 'Imagick extension not loaded' ,['file'=>__FILE__,'Line'=>__LINE__]);
			return $src;
		};

		$des = dirname($src).DIRECTORY_SEPARATOR.pathinfo($src, PATHINFO_FILENAME).'.webp';
		
		//check if the file already exists or not 
		if(file_exists($des)){
			return true;
		}

		if (class_exists('Imagick')) {
			$imagick = new \Imagick();
			$formats = $imagick->queryFormats();
			if (in_array('WEBP', $formats)) {
				$imagick->readImage($src);
				$imagick->setImageFormat('webp');
				return $imagick->writeImage($des);
			}
		}

		return false;

	}

	/**
	 * attachmentUrlToPath
	 * This function converts the url of an Image to actual path of that image 
	 * @param  string $url url of an Image
	 * @return string|boolean  path of an Image, false on fail
	 */
	public static function attachmentUrlToPath($url) {
		$parsed_url = parse_url($url);
		if (empty($parsed_url['path'])) return false;
		$file = ABSPATH . ltrim($parsed_url['path'], '/');
		if (file_exists($file)) return $file;
		return false;
	}

	/**
	 * Convert Absolute Path to Relative Url
	 * @param String|Array $url
	 * @return String|Array 
	 */
	public static function pathToAttachmentUrl($url = ''){
		if($url == '') return '';
		if(is_array($url)){
			$relativeUrl = [];
			foreach($url as $link){
				$relativeUrl[] = self::pathToAttachmentUrl($link);
			}
			return $relativeUrl;
		}
		$wpContentPosition = strpos($url, 'wp-content');
		if ($wpContentPosition !== false) {
			return get_site_url() . '/wp-content'. explode('wp-content',$url)[1];
		}
		
		return '';
	}
}
