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
			'files' => array(
				'converted' => intval(count(self::themeFilesConverted())),
				'total' => intval(count(self::themesFilesTotal()))
			),
		);

		return array($data);
	}

	public static function ajaxThemeFilesConvert() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) return;
		echo json_encode(self::themeFilesConvert());
		wp_die();
	}


	/**
	 * themeFilesConvert
	 * Converts all unconverted images inside theme directory
	 * @return boolean
	 */
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
			$dest = (string)rtrim($filePath, '.' . pathinfo($filePath, PATHINFO_EXTENSION)) . '.avif';
			Image::convert($filePath, $dest, $quality, $speed);
		}
		return true;
	}

	/**
	 * ajaxThemeFilesDelete
	 *
	 * @return void
	 */
	public static function ajaxThemeFilesDelete() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) return;
		echo json_encode(self::themeFilesDelete());
		wp_die();
	}

	public static function themeFilesDelete() {
		$themeDirs = self::avif_get_theme_dirs();
		$filePaths = [];
		foreach ($themeDirs as $themeDir) {
			$filePaths = array_merge($filePaths, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), -1));
		}
		if (empty($filePaths)) return false;
		foreach ($filePaths as $filePath) {
			$dest = (string)rtrim($filePath, '.' . pathinfo($filePath, PATHINFO_EXTENSION)) . '.avif';
			if (file_exists($dest)) wp_delete_file($dest);
		}
		return true;
	}



	/**
	 * themeFilesConverted
	 * returns a array containing the paths of jpg,webp and jpeg images in the theme dir
	 * that are already converted
	 * @return array
	 */
	public static function themeFilesConverted() {
		$themeDirs = self::avif_get_theme_dirs();
		$convertedFiles = [];
		foreach ($themeDirs as $themeDir) {
			$convertedFiles = array_merge($convertedFiles, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), -1));
		}
		return $convertedFiles;
	}

	/**
	 * themesFilesUnconverted
	 * returns a array containing the paths of jpg,webp and jpeg images in the theme dir
	 * that ate yet to get converted
	 * @return array
	 */
	public static function themesFilesUnconverted() {
		$themeDirs = self::avif_get_theme_dirs();
		$unconvertedFiles = [];
		foreach ($themeDirs as $themeDir) {
			$unconvertedFiles = array_merge($unconvertedFiles, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), 1));
		}
		return $unconvertedFiles;
	}

	/**
	 * themesFilesTotal
	 * returns a array containing the paths of jpg,webp and jpeg images in the theme dir
	 * @return array
	 */
	public static function themesFilesTotal() {
		$themeDirs = self::avif_get_theme_dirs();
		$totalFiles = [];
		foreach ($themeDirs as $themeDir) {
			$totalFiles = array_merge($totalFiles, self::findFiles($themeDir, array("png", "jpg", "webp", "jpeg"), 0));
		}
		return $totalFiles;
	}



	/**
	 * findFiles
	 * @param  string $basePath : root path
	 * @param  array $exts : extension of files to look for
	 * @param  int $hasAvif: 0  - All , 1 - Unconverted, -1 - Converted 
	 * @return array
	 */
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

			if ($hasAvif == -1) {
				foreach ($baseFiles as $key => $baseFile) {
					$avifFile = rtrim($baseFile, '.' . pathinfo($baseFile, PATHINFO_EXTENSION)) . '.avif';
					if (!file_exists($avifFile)) {
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

	/**
	 * avif_get_theme_dirs
	 * returns the theme path(s)
	 * In case child theme, return parent and child theme path
	 * @return array
	 */
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
