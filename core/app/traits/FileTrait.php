<?php

namespace Avife\traits;

trait FileTrait
{
    /**
     * @param string $basePath - Directory to target
     * @param array $exts - file with extension(s) to target 
     * @param int $hasAvif - 1: only unconverted files; 0: all files (both source files that have a converted copy and files without one); -1: only converted files
     * @return array - list of files 
     */
    private function findFiles(string $basePath, array $exts, int $hasAvif = 0)
    {
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
            $files = array_merge($files, $baseFiles);
        }
        /**
         * finding paths of all subdirectories within provided base directory
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
            $sub_files = $this->findFiles($sub_dir, $exts, $hasAvif);
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

    private function getThemeDirs()
    {
        $themes = array();
        if (is_child_theme()) {
            $themes[] = get_stylesheet_directory();
            $themes[] = get_template_directory();
        } else {
            $themes[] = get_template_directory();
        }

        return $themes;
    }

    private function isSupportedExtension($url, $format = ['jpg', 'jpeg', 'png'])
    {
        if (!isset($url)) return false;
        if (empty($format)) {
            return false;
        }
        $ext = pathinfo($url, PATHINFO_EXTENSION);
        return $ext && in_array($ext, $format);
    }

    private function isValidImageUrl($url){
        return strpos($url, get_bloginfo('url')) !== false && $this->isFileExists($url);
    }

    private function isFileExists($url){
        $path = $this->urlToPath($url);
        if($path == false) return false;
        if(!file_exists($path)) return false;
        return true;
    }

    private function urlToPath(string $url)
    {
        $parsed_url = parse_url($url);
        if (empty($parsed_url['path'])) {
            return false;
        }

        $url_path = ltrim($parsed_url['path'], '/');

        // Handle uploads
        $upload_dir = wp_upload_dir();
        if (strpos($url, $upload_dir['baseurl']) === 0) {
            $relative = str_replace($upload_dir['baseurl'], '', $url);
            $path = $upload_dir['basedir'] . $relative;
            $real_path = realpath($path);
            if ($real_path && strpos($real_path, realpath($upload_dir['basedir'])) === 0) {
                return $real_path;
            }
        }

        // Theme (child and parent)
        $theme_dirs = [
            get_stylesheet_directory(),
            get_template_directory()
        ];

        foreach ($theme_dirs as $theme_dir) {
            $theme_url = content_url(str_replace(ABSPATH, '', $theme_dir));
            if (strpos($url, $theme_url) === 0) {
                $relative = str_replace($theme_url, '', $url);
                $path = $theme_dir . $relative;
                $real_path = realpath($path);
                if ($real_path && strpos($real_path, realpath($theme_dir)) === 0) {
                    return $real_path;
                }
            }
        }

        // Plugins
        $plugin_url = plugins_url();
        if (strpos($url, $plugin_url) === 0) {
            $relative = str_replace($plugin_url, '', $url);
            $path = WP_PLUGIN_DIR . $relative;
            $real_path = realpath($path);
            if ($real_path && strpos($real_path, realpath(WP_PLUGIN_DIR)) === 0) {
                return $real_path;
            }
        }

        // Fallback: try matching with ABSPATH
        $site_url = site_url();
        if (strpos($url, $site_url) === 0) {
            $relative = str_replace($site_url, '', $url);
            $path = ABSPATH . ltrim($relative, '/');
            $real_path = realpath($path);
            if ($real_path && strpos($real_path, realpath(ABSPATH)) === 0) {
                return $real_path;
            }
        }

        return false;
    }
}
