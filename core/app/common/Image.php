<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit();


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
		/**
		 * getting the attachment
		 * attachment is post type, so we can use get_post function
		 * see: https://developer.wordpress.org/reference/functions/get_post/
		 */
		$attachment = get_post($attachment_id);
		
		/**
		 * getting the mime type for comparison 
		 */
		$mimeType = $attachment->post_mime_type;

		/**
		 * if mime type not supported, return the metadata.
		 * since we only supports jpeg,png,jpg and webp(yet to be supported on bulk conversion)
		 */
		if (!in_array($mimeType, array('image/jpeg', 'image/png', 'image/jpg', 'image/webp',))) return $metadata;

		/**
		 * getting the quality and compression speed for local conversion for GD only 
		 */
		$quality = Options::getImageQuality();
		$speed = Options::getComSpeed();

		/**
		 * converting original image(s)
		 */
		$originalImages[0] =  $attachment->guid;

		$uploadDirInfo = wp_upload_dir();
		$originalImages[1] = $uploadDirInfo['baseurl'] . DIRECTORY_SEPARATOR . $metadata['file'];

		/**
		 * checking if GD can create avif image or not,
		 * based on that setting flag avifsupport,
		 * 0 = not capable , 1 = capable 
		 */
		$avifsupport = '0';
		if(function_exists('imageavif') && function_exists('gd_info') && gd_info()['AVIF Support'] != ''){
			$avifsupport = '1';
		} 

		/**
		 * checking if Imagick can create avif image or not,
		 * based on that setting flag hasImagick,
		 * 0 = not capable, 1 = capable 
		 */
		$hasImagick = '0';
		if(extension_loaded('imagick') && class_exists('Imagick') && AVIFE_IMAGICK_VER > 0){
			$imagick = new \Imagick();
			$formats = $imagick->queryFormats();
			if (in_array('AVIF', $formats)) {
				$hasImagick = '1';
			}
		}


		if ($originalImages[0] != $originalImages[1]) {

			foreach ($originalImages as $originalImage) {
				
				/**
				 * creating Path form image url
				 */
				$srcPath = self::attachmentUrlToPath($originalImage);

				if ($srcPath != false && $srcPath != '') {
					/**
					 * creating destination file path
					 */
					$desPath = rtrim($srcPath, '.' . pathinfo($srcPath, PATHINFO_EXTENSION)) . '.avif';

					if(Options::getConversionEngine() == 'local'){
						
						/**
						 * in case of both local conversion failed,
						 * logging it and returning original meta,
						 * with return it will exit the flow
						 */
						if($avifsupport == '0' && $hasImagick == '0'){
							if(WP_DEBUG == true) error_log('Convert on Upload: Local avif support not found');
							return $metadata;
						} 

						/**
						 * if not existed from the flow from the previous if 
						 * condition, continue with local conversion. 
						 */
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
					
					if($avifsupport == '0' && $hasImagick == '0'){
						if(WP_DEBUG == true) error_log('Convert on Upload: Local avif support not found');
						
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

					if($avifsupport == '0' && $hasImagick == '0'){
						if(WP_DEBUG == true) error_log('Convert on Upload: Local avif support not found');
					
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

		
		if(WP_DEBUG == true){
			trigger_error('Avif Express: Local avif support not found');
		}
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

		//add origin and api key to the request header
		$requestHeader = array(
			
			'X-RapidAPI-Key' => $apiKey,
			'Content-Type' => 'application/json',
			'Accept' => 'application/json'
		);	

		$avifServerImageData = [];

		foreach($urls as $url){
			

			//actual payload with appended url, GET method
			$fullRequestUrl = AVIF_CLOUD_ADDRESS . '?url=' . urlencode($url);

			//add origin to the request header
			$origin = str_replace(['https://','http://'],'',strtolower(get_site_url()));

			//conversion started
			//1. sending all urls(array of url) to the cloud server
			$cloudResponse = wp_remote_get($fullRequestUrl , array('headers' => $requestHeader));

			//2. getting the response contain all urls(array of url where url['src] = source url and url['dest'] is avif cloud server converted image url) 
			$body = wp_remote_retrieve_body($cloudResponse);

			//checking for any error and then logging it, if WP_DEBUG is true and then exit
			if(is_wp_error($cloudResponse)) {
				if(WP_DEBUG == true) error_log("Error:" . $cloudResponse->get_error_message());
				
				
			}

			$imageUrls = json_decode($body, true);

			//check server status code for any issue
			if(isset($imageUrls['status']) && $imageUrls['status'] !== 'success'){
				if(WP_DEBUG == true) error_log("Error:" . print_r($imageUrls));
			
				
			}

			if(isset($imageUrls['status']) && $imageUrls['status'] == 'success'){
				
				
				$avifServerImageData[] = $imageUrls['data'];
			}
			
		}
		
		
		
		//using wordpress file system class instead of php native file_get_contents()
		include_once ABSPATH . 'wp-admin/includes/file.php';
		WP_Filesystem();

		//this if condition to prevent to null
		if(is_array($avifServerImageData) || is_object($avifServerImageData)){
			foreach($avifServerImageData as $imageUrl){
			
				//creating destination file path form the source
				$srcImagePath = self::attachmentUrlToPath($imageUrl[0]);
				if($srcImagePath == false ) {
					if(WP_DEBUG == true) error_log('Unable to create absolute path from relative path of source image');
				
					continue;
				}

				//creating destination path
				$pathInfo = pathinfo($srcImagePath);
				$avifFileName = $pathInfo["dirname"] . DIRECTORY_SEPARATOR . $pathInfo["filename"] . '.avif';
				
	
				//getting remote avif file 
				$response = wp_remote_get($imageUrl[1]);
				if(is_wp_error($response)){
					if(WP_DEBUG == true) error_log("Avif Download Error:".$response->get_error_message());
			
					continue;
				} 
				//retrieving avif file body content
				$body = wp_remote_retrieve_body($response);
				if (WP_Filesystem()) {
					global $wp_filesystem;
					if(!$wp_filesystem->put_contents($avifFileName, $body, FS_CHMOD_FILE)){
						if(WP_DEBUG == true) error_log('Unable to write avif file');
					
						continue;
					}
					
				}else{
					if(WP_DEBUG == true) error_log('Unable to initialize the WP_filesystem');
				
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
