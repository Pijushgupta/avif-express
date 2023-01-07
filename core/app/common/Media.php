<?php

namespace App\common;

if (!defined('ABSPATH')) exit;

use App\common\Setting;
use App\common\Image;

class Media {

	/**
	 * ajaxCountMedia
	 * @return void ajax handle for countMedia
	 */
	public static function ajaxCountMedia() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::countMedia());
		wp_die();
	}

	/**
	 * countMedia
	 *
	 * @return array in array(number of converted iamges, number of total images, number of various image sizes)
	 */
	public static function countMedia() {
		$allMedia = self::getAttachments(-1);
		if (gettype($allMedia) == 'array') {
			$allMedia = count($allMedia);
		}

		$convertedMedia = self::getAttachments(1);
		if (gettype($convertedMedia) == 'array') {
			$convertedMedia = count($convertedMedia);
		}

		return array($convertedMedia, $allMedia, count(get_intermediate_image_sizes()));
	}

	/**
	 * ajaxConvertRemaining
	 *
	 * @return void ajax handle for convertRemaining 
	 */
	public static function ajaxConvertRemaining() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false || extension_loaded('GD') != 1) wp_die();
		echo json_encode(self::convertRemaining());
		wp_die();
	}

	/**
	 * convertRemaining
	 *
	 * @return boolean|null|string true on success. false on error, null on empty source and string 'keep-alive' to let client know to request again. 
	 */
	public static function convertRemaining() {
		$unConvertedAttachments = self::getAttachments(0);
		if (gettype($unConvertedAttachments) != 'array' || empty($unConvertedAttachments) || $unConvertedAttachments == 0) return null;

		/**
		 * Checking if 'set_time_limit' can be set or not 
		 * if not don't do anything
		 */
		if (Setting::avif_set_time_limit() == false) return false;
		$counter = 1;
		foreach ($unConvertedAttachments as $unConvertedAttachment) {

			Image::beforeConvert(wp_get_attachment_metadata($unConvertedAttachment->ID), $unConvertedAttachment->ID);
			if ($counter == 2) {
				return 'keep-alive';
			}
			$counter++;
		}
		return true;
	}

	/**
	 * ajaxDeleteAll
	 *
	 * @return void ajax handle for deleteAll
	 */
	public static function ajaxDeleteAll() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();;
		echo json_encode(self::deleteAll());
		wp_die();
	}

	/**
	 * deleteAll
	 * delete All converted Images
	 * @return true as signal
	 */
	public static function deleteAll() {
		$attachments = self::getAttachments(1);
		foreach ($attachments as $attachment) {
			Image::delete($attachment->ID, $attachment);
		}
		return true;
	}

	/**
	 * getAttachments
	 *
	 * @param  mixed $all : -1 for all, 0 for un converted, 1 for converted
	 * @return Array
	 */
	public static function getAttachments($all = -1) {
		$attachments = get_posts(array(
			'post_type' => 'attachment',
			'posts_per_page' => -1,

		));
		if ($all === -1) {
			if (empty($attachments) || $attachments == null) return 0;
			return $attachments;
		}
		if ($all === 0) {
			$newAttachment = array();
			foreach ($attachments as $attachment) {
				if (get_post_meta($attachment->ID, 'avifexpressconverted', null) != true)
					$newAttachment[] = $attachment;
			}
			if (empty($newAttachment)) return 0;
			return $newAttachment;
		}
		if ($all === 1) {
			$newAttachment = array();
			foreach ($attachments as $attachment) {
				if (get_post_meta($attachment->ID, 'avifexpressconverted', null) == true)
					$newAttachment[] = $attachment;
			}
			if (empty($newAttachment)) return 0;
			return $newAttachment;
		}
	}
}
