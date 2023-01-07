<?php

namespace App\common;

if (!defined('ABSPATH')) exit();

use App\common\Options;

class Image {

	/**
	 * activate
	 * Adding our methods to wordpress hooks
	 * @return void
	 */
	public static function activate() {
		/**
		 * checking if GD extension is enabled 
		 */
		if (extension_loaded('gd') != 1) return;
		/**
		 * Checking if auto conversion enabled 
		 */
		if (Options::getAutoConvtStatus()) {
			add_action('wp_generate_attachment_metadata', array('App\backend\Image', 'beforeConvert'), 10, 2);
		}

		add_action('delete_attachment',  array('App\backend\Image', 'delete'), 10, 3);
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
		if (!in_array($mimeType, array('image/jpeg', 'image/png', 'image/jpg', 'image/webp',))) return;

		$quality = Options::getImageQuality();
		$speed = Options::getComSpeed();

		/**
		 * converting original image(s)
		 */
		$originalImages[0] =  $attachment->guid;
		$uploadDirInfo = wp_upload_dir();
		$originalImages[1] = $uploadDirInfo['baseurl'] . '/' . $metadata['file'];

		if ($originalImages[0] != $originalImages[1]) {
			foreach ($originalImages as $originalImage) {

				$srcPath = self::attachmentUrlToPath($originalImage);
				$desPath = rtrim($srcPath, '.' . pathinfo($srcPath, PATHINFO_EXTENSION)) . '.avif';

				self::convert($srcPath, $desPath, $quality, $speed);
			}
		} else {
			$srcPath = self::attachmentUrlToPath($originalImages[0]);
			$desPath = rtrim($srcPath, '.' . pathinfo($srcPath, PATHINFO_EXTENSION)) . '.avif';

			self::convert($srcPath, $desPath, $quality, $speed);
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
			$src = $fileDir . '/' . $size['file'];
			$des = rtrim($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) . '.avif';

			self::convert($src, $des, $quality, $speed);
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
		@imagedestroy($sourceGDImg);
	}

	/**
	 * attachmentUrlToPath
	 * This function converts the url of an Image to actual path of that image 
	 * @param  string $url url of an Image
	 * @return string  path of an Image
	 */
	public static function attachmentUrlToPath($url) {
		$parsed_url = parse_url($url);
		if (empty($parsed_url['path'])) return false;
		$file = ABSPATH . ltrim($parsed_url['path'], '/');
		if (file_exists($file)) return $file;
		return false;
	}
}
