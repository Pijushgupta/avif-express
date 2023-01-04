<?php

namespace App\backend;

if (!defined('ABSPATH')) exit;

use App\backend\Image;
use App\common\Options;


class Theme {

	public static function ajaxGetCurrentTheme() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) return;
		echo json_encode(self::getCurrentTheme());
		wp_die();
	}

	public static function getCurrentTheme() {
		$active_theme = wp_get_theme();

		$data = array(
			'theme_name' => $active_theme->get('Name'),
			'is_child' => is_child_theme(), //to remove
			'theme_root' => get_theme_root(), //to remove
			'theme_dir' => get_stylesheet_directory(), // to remove 
			'files' => self::themeFilesCount()
		);

		return array($data);
	}

	public static function ajaxThemeFilesConvert() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) return;
		echo json_encode(self::themeFilesConvert());
		wp_die();
	}
	public static function themeFilesConvert() {
		$themeDirs = self::avif_get_theme_dirs();
		$filePaths = [];
		foreach ($themeDirs as $themeDir) {
			$filePaths = array_merge($filePaths, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), 1));
		}
		if (empty($filePaths)) return false;

		$quality = Options::getImageQuality();
		$speed = Options::getComSpeed();

		foreach ($filePaths as $filePath) {
			$mime = 'image/' . (string)pathinfo($filePath, PATHINFO_EXTENSION);
			$dest = (string)rtrim($filePath, '.' . pathinfo($filePath, PATHINFO_EXTENSION)) . '.avif';
			Image::convert($mime, $filePath, $dest, $quality, $speed);
		}
		return true;
	}

	public static function themeFilesCount() {
		return array(
			'converted' => intval(self::themeFilesConverted()),
			'total' => intval(self::themesFilesTotal())
		);
	}

	public static function themeFilesConverted() {
		return intval(self::themesFilesTotal() - self::themesFilesUnconverted());
	}

	public static function themesFilesUnconverted() {
		$themeDirs = self::avif_get_theme_dirs();
		$unconvertedFiles = 0;
		foreach ($themeDirs as $themeDir) {
			$unconvertedFiles += count(self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), 1));
		}
		return $unconvertedFiles;
	}

	public static function themesFilesTotal() {
		$themeDirs = self::avif_get_theme_dirs();
		$totalFiles = 0;
		foreach ($themeDirs as $themeDir) {
			$totalFiles += count(self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), 0));
		}
		return $totalFiles;
	}



	public static function findFiles($basePath, $exts, $hasAvif = 0) {
		$files = [];
		foreach ($exts as $ext) {

			$baseFiles = glob("$basePath/*.$ext");

			if ($hasAvif == 1) {
				foreach ($baseFiles as $key => $baseFile) {
					$avifFile = rtrim($baseFile, '.' . pathinfo($baseFile, PATHINFO_EXTENSION)) . '.avif';
					if (file_exists($avifFile)) {
						unset($baseFiles[$key]);
					}
				}
			}

			$files  = array_merge($files, $baseFiles);
		}

		foreach (glob("$basePath/*", GLOB_ONLYDIR) as $sub_dir) {

			$sub_files = self::findFiles($sub_dir, $exts, $hasAvif);
			$files = array_merge($files, $sub_files);
		}
		return $files;
	}
	public static function avif_get_theme_dirs() {
		$themes = array();
		if (is_child_theme()) {

			$themes[] = get_stylesheet_directory();
			$themes[] = get_template_directory();
		} else {
			$themes[] = get_template_directory();
		}

		return $themes;
	}
}
