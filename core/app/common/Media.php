<?php

namespace App\common;

if (!defined('ABSPATH')) exit;

class Media {
	public static function ajaxCountMedia() {

		$attachments =  get_posts(array('post_type' => 'attachment', 'posts_per_page' => -1));
		echo json_encode($attachments);
		wp_die();
	}
	public static function convertUpload($all = false) {
		$attachments = get_posts(array(
			'post_type' => 'attachment',
			'posts_per_page' => -1,

		));
		$newAttachment = array();
		foreach ($attachments as $attachment) {
			if ($all == false || get_post_meta($attachment->ID, 'avifexpressconverted', true) != true) {
				$newAttachment[] = $attachment;
			}
		}
		return $newAttachment;
	}
}
