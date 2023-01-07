<?php

namespace App\frontend;

if (!defined('ABSPATH')) exit;

use App\common\Options;
use voku\helper\HtmlDomParser;

class Html {
	public static function init() {

		add_action('template_redirect', array('App\frontend\Html', 'checkConditions'));
	}
	public static function checkConditions() {
		if (is_admin() || is_feed() || wp_doing_ajax() || Options::getOperationMode() == 'inactive') return;
		ob_start('App\frontend\Html::getContent');
	}

	public static function getContent($content) {

		$userAgent = $_SERVER['HTTP_USER_AGENT'];
		/**
		 * TODO: Remove after edge start supporting avif
		 */
		if (strpos($userAgent, 'Edg')) {
			return $content;
		}
		/**
		 * if it's not html return the content
		 */
		if (!preg_match("#^\\s*<#", $content)) {
			return $content;
		}

		$dom = HtmlDomParser::str_get_html($content);

		/**
		 *  iterating all the img element 
		 */
		foreach ($dom->getElementsByTagName('img') as &$image) {

			/**
			 * changing extension single image url to .avif
			 */
			$image->setAttribute('src', self::replaceImgSrc($image->getAttribute('src')));

			$image->setAttribute('srcset', self::replaceImgSrcSet($image->getAttribute('srcset')));
		}
		return $dom;
	}

	/**
	 * replace image url with .avif extension
	 */
	public static function replaceImgSrc($imageUrl) {
		/**
		 * Checking if the image source form same domain or not
		 */
		if (str_contains($imageUrl, get_bloginfo('url'))) {
			return $imageUrl = rtrim($imageUrl, '.' . pathinfo($imageUrl, PATHINFO_EXTENSION)) . '.avif';
		}
	}
	/**
	 * replacing srcset urls
	 */
	public static function replaceImgSrcSet($srcset) {
		if (!$srcset) return;
		$srcset = explode(' ', $srcset);

		foreach ($srcset as $k => &$v) {
			if (str_contains($v, get_bloginfo('url'))) {
				$ext = pathinfo($v, PATHINFO_EXTENSION);
				if ($ext && in_array($ext, array('jpg', 'jpeg', 'png', 'webp'))) {
					$v = rtrim($v, '.' . pathinfo($v, PATHINFO_EXTENSION)) . '.avif';
				}
				unset($ext);
			}
		}
		return implode(' ', $srcset);
	}
}
