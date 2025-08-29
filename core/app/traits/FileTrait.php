<?php

namespace Avife\traits;

trait FileTrait{
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
    
    private function getThemeDirs(): array
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
}