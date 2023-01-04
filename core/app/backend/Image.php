<?php

namespace App\backend;

if (!defined('ABSPATH')) exit();

use App\common\Options;

class Image {

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
				$mime = 'image/' . pathinfo($srcPath, PATHINFO_EXTENSION);
				self::convert($mime, $srcPath, $desPath, $quality, $speed);
			}
		} else {
			$srcPath = self::attachmentUrlToPath($originalImages[0]);
			$desPath = rtrim($srcPath, '.' . pathinfo($srcPath, PATHINFO_EXTENSION)) . '.avif';
			$mime = 'image/' . pathinfo($srcPath, PATHINFO_EXTENSION);
			self::convert($mime, $srcPath, $desPath, $quality, $speed);
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
			$mime = 'image/' . pathinfo($src, PATHINFO_EXTENSION);
			self::convert($mime, $src, $des, $quality, $speed);
		}
		/**
		 * ends
		 */
		update_post_meta($attachment_id, 'avifexpressconverted', true);
		return $metadata;
	}

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



	public static function convert($mime, $src, $des, $quality, $speed) {
		if (!$mime && !$src && !$des && !$quality && !$speed) return false;

		if ($mime == 'image/jpeg' || $mime == 'image/jpg') {
			$sourceGDImg = @imagecreatefromjpeg($src);
		}
		if ($mime == 'image/png') {
			$sourceGDImg = @imagecreatefrompng($src);
		}
		if ($mime == 'image/webp') {
			$sourceGDImg = @imagecreatefromwebp($src);
		}
		if (gettype($sourceGDImg) == 'boolean') return;
		@imageavif($sourceGDImg, $des, $quality, $speed);
	}



	public static function attachmentUrlToPath($url) {
		$parsed_url = parse_url($url);
		if (empty($parsed_url['path'])) return false;
		$file = ABSPATH . ltrim($parsed_url['path'], '/');
		if (file_exists($file)) return $file;
		return false;
	}
}
