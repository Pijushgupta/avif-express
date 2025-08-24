<?php 

namespace Avife\trait;


trait ImageHelperTrait {

    /**
     * findFiles
     * @param string $basePath : root path to start the search
     * @param array $exts : extension of files to look for
     * @param int $hasAvif : 0  - All , 1 - Unconverted, -1 - Converted
     * @return array file paths
     */
    private static function findFiles(string $basePath, array $exts, int $hasAvif = 0) : array
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
}