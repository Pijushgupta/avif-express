<?php

namespace App\common;

if (!defined('ABSPATH')) exit;

use App\backend\Image;

class Media {
	public static $status = null;
	public static function ajaxCountMedia() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) return;

		$allMedia = self::getAttachments(-1);
		if (gettype($allMedia) == 'array') {
			$allMedia = count($allMedia);
		}

		$convertedMedia = self::getAttachments(1);
		if (gettype($convertedMedia) == 'array') {
			$convertedMedia = count($convertedMedia);
		}
		echo json_encode(array($convertedMedia, $allMedia, count(get_intermediate_image_sizes())));
		wp_die();
	}
	public static function ajaxConvertRemaining() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) return;

		self::convertRemaining();
		echo json_encode('done');
		wp_die();
	}
	public static function convertRemaining() {
		$unConvertedAttachments = self::getAttachments(0);
		if (gettype($unConvertedAttachments) != 'array' || empty($unConvertedAttachments) || $unConvertedAttachments == 0) {
			return false;
		}


		foreach ($unConvertedAttachments as $unConvertedAttachment) {
			Image::beforeConvert(wp_get_attachment_metadata($unConvertedAttachment->ID), $unConvertedAttachment->ID);
		}
	}
	public static function ajaxDeleteAll() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) return;
		echo json_encode(self::deleteAll());
		wp_die();
	}
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
