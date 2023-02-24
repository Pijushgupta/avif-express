<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

use Avife\common\Image;
use Avife\common\Options;
use Avife\common\Setting;

class Theme {

	public static function ajaxGetCurrentTheme() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
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
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		if (function_exists('imageavif') == false && AVIFE_IMAGICK_VER <= 0) wp_die();
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
		if (empty($filePaths) || gettype($filePaths) != 'array') return null;

		$quality = Options::getImageQuality();
		$speed = Options::getComSpeed();
		/**
		 * Checking if 'set_time_limit' can be set or not 
		 * if not don't do anything
		 */
		if (Setting::avif_set_time_limit() == false) return false;
		$counter = 1;
		foreach ($filePaths as $filePath) {

			$dest = (string)rtrim($filePath, '.' . pathinfo($filePath, PATHINFO_EXTENSION)) . '.avif';
			Image::convert($filePath, $dest, $quality, $speed);
			if ($counter == 5) {
				return 'keep-alive';
			}
			$counter++;
		}

		return true;
	}

	/**
	 * ajaxThemeFilesDelete
	 * ajax handle for themeFilesDelete
	 * @return void
	 */
	public static function ajaxThemeFilesDelete() {
		if (wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce') == false) wp_die();
		echo json_encode(self::themeFilesDelete());
		wp_die();
	}

	/**
	 * themeFilesDelete
	 * Deletes .avif files from theme folders. 
	 * In case of child theme, delete from parent and child.
	 * @return boolean
	 */
	public static function themeFilesDelete() {

		$filePaths = self::themeFilesConverted();
		/**
		 * if no file found , terminating the process.
		 */
		if (empty($filePaths)) return false;
		/**
		 * iterating through file paths
		 * 
		 */
		foreach ($filePaths as $filePath) {
			/**
			 * creating path for file to delete. Just by removing original extension with .avif 
			 */
			$dest = (string)rtrim($filePath, '.' . pathinfo($filePath, PATHINFO_EXTENSION)) . '.avif';

			/**
			 * Finally deleting the file
			 */
			if (file_exists($dest)) wp_delete_file($dest);
		}
		/**
		 * returning true for positive signal
		 */
		return true;
	}



	/**
	 * themeFilesConverted
	 * @return array returns a array containing the paths of jpg,webp and jpeg images in the theme dir(s) that are already converted
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
	 * @param  string $basePath : root path to start the search
	 * @param  array $exts : extension of files to look for
	 * @param  int $hasAvif: 0  - All , 1 - Unconverted, -1 - Converted 
	 * @return array file paths
	 */
	public static function findFiles($basePath, $exts, $hasAvif = 0) {
		/**
		 * To store the paths 
		 */
		$files = [];
		/**
		 * iterating through provided extensions 
		 */
		foreach ($exts as $ext) {
			/**
			 * looking at provided directory path and storing all the file path with specific extension($ext) 
			 */
			$baseFiles = glob("$basePath/*.$ext");

			/**
			 * iterating through file paths
			 */
			foreach ($baseFiles as $key => $baseFile) {
				/**
				 * creating .avif file path from the source file path
				 */
				$avifFile = rtrim($baseFile, '.' . pathinfo($baseFile, PATHINFO_EXTENSION)) . '.avif';
				/**
				 * $hasAvif = 1 , unconverted files.
				 * $hasAvif = -1, converted files
				 * $hasAvif = 0, do nothing (keeping all)
				 */
				if (file_exists($avifFile) && $hasAvif == 1) {
					unset($baseFiles[$key]);
				}
				if (!file_exists($avifFile) && $hasAvif == -1) {
					unset($baseFiles[$key]);
				}
			}
			/**
			 * storing the source file paths
			 */
			$files  = array_merge($files, $baseFiles);
		}
		/**
		 * finding paths of all sub directories within provided base directory
		 * and storing them
		 * @type array
		 */
		$sub_dirs = glob("$basePath/*", GLOB_ONLYDIR);

		/**
		 * iterating through sub_directories
		 */
		foreach ($sub_dirs as $sub_dir) {
			/**
			 * calling itself with sub_directory with originally provided extension and return type.
			 * RECURSION 
			 */
			$sub_files = self::findFiles($sub_dir, $exts, $hasAvif);
			/**
			 * And storing that
			 */
			$files = array_merge($files, $sub_files);
		}
		/**
		 * Finally returning all
		 */
		return $files;
	}

	/**
	 * avif_get_theme_dirs
	 * @return array returns the theme path(s). In case of child theme, return parent and child theme path
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
