<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit();

class Image
{

    /**
     * activate
     * Adding our methods to WordPress hooks
     * @return void
     */
    public static function activate()
    {

        /**
         * Checking if auto conversion enabled
         */
        if (Options::getAutoConvtStatus()) {

            add_action('wp_generate_attachment_metadata', array('Avife\common\Image', 'beforeConvert'), 10, 2);
        }

        add_action('delete_attachment', array('Avife\common\Image', 'delete'), 10, 3);
    }

    /**
     * beforeConvert
     * Finding attachment and all its sizes and converting them, using self::convert method
     * @param mixed $metadata
     * @param mixed $attachment_id
     * @return void
     */
    public static function beforeConvert($metadata, $attachment_id)
    {
        /**
         * getting the attachment.
         * attachment is post type, so we can use get_post function
         * see: https://developer.wordpress.org/reference/functions/get_post/
         */
        $attachment = get_post($attachment_id);

        /**
         * getting the mime type for comparison
         */
        $mimeType = $attachment->post_mime_type;

        /**
         * if mime type not supported, return the metadata.
         * since we only supports jpeg,png,jpg
         */
        if (!in_array($mimeType, array('image/jpeg', 'image/png', 'image/jpg'))) return $metadata;

        /**
         * getting the quality and compression speed for local conversion for GD only
         */
        $quality = Options::getImageQuality();
        $speed = Options::getComSpeed();

        /**
         * converting original image(s)
         */
        $originalImages[0] = $attachment->guid;

        $uploadDirInfo = wp_upload_dir();
        if (!is_array($uploadDirInfo)) {
            Utility::logError('File System Permission issue.');
        }
        $originalImages[1] = $uploadDirInfo['baseurl'] . DIRECTORY_SEPARATOR . $metadata['file'];

        if ($originalImages[0] != $originalImages[1]) {

            foreach ($originalImages as $originalImage) {

                /**
                 * creating Path form image url
                 */
                $srcPath = Utility::attachmentUrlToPath($originalImage);

                if ($srcPath && $srcPath != '') {
                    /**
                     * creating destination file path
                     */
                    $desPath = rtrim($srcPath, '.' . pathinfo($srcPath, PATHINFO_EXTENSION)) . '.avif';

                    if (Options::getConversionEngine() == 'local') {
                        /**
                         * checking if local conversion possible or not
                         */
                        if (!Utility::isLocalAvifConversionSupported()) {
                            Utility::logError('Convert on Upload: Local avif support not found');
                            return $metadata;
                        }

                        self::convert($srcPath, $desPath, $quality, $speed);

                    }

                    if (Options::getConversionEngine() == 'cloud') {

                        $unConvertedAttachmentUrls[] = $originalImage;
                        self::cloudConvert($unConvertedAttachmentUrls);

                    }


                }
            }

        } else {

            $srcPath = Utility::attachmentUrlToPath($originalImages[0]);

            if ($srcPath && $srcPath != '') {

                $desPath = rtrim($srcPath, '.' . pathinfo($srcPath, PATHINFO_EXTENSION)) . '.avif';

                if (Options::getConversionEngine() == 'local') {

                    if (!Utility::isLocalAvifConversionSupported()) {
                        Utility::logError('Convert on Upload: Local avif support not found');

                        return $metadata;
                    }
                    self::convert($srcPath, $desPath, $quality, $speed);
                }

                if (Options::getConversionEngine() == 'cloud') {
                    $unConvertedAttachmentUrls[] = $originalImages[0];
                    self::cloudConvert($unConvertedAttachmentUrls);
                }
            }
        }
        /**
         * ends
         */

        /**
         * converting generated thumbnails
         */
        $fileDir = pathinfo(Utility::attachmentUrlToPath($originalImages[0]), PATHINFO_DIRNAME);
        $allSizes = $metadata['sizes'];
        foreach ($allSizes as $size) {

            $src = trailingslashit($fileDir) . $size['file'];

            if (file_exists($src)) {
                $des = rtrim($src, '.' . pathinfo($src, PATHINFO_EXTENSION)) . '.avif';

                if (Options::getConversionEngine() == 'local') {

                    if (!Utility::isLocalAvifConversionSupported()) {
                        Utility::logError('Convert on Upload: Local avif support not found');

                        return $metadata;
                    }
                    self::convert($src, $des, $quality, $speed);

                }

                if (Options::getConversionEngine() == 'cloud') {

                    $unConvertedAttachmentUrls[] = Utility::pathToAttachmentUrl($src);
                    self::cloudConvert($unConvertedAttachmentUrls);

                }

            }
        }
        /**
         * ends
         */


        return $metadata;
    }

    /**
     * delete
     * finding the converted image and its thumbs and deleting when user deletes an image from WordPress Media
     * @param mixed $post_id
     * @param mixed $post
     * @return void
     */
    public static function delete($post_id, $post)
    {

        $orginalImageUrl = $post->guid;
        $attachment_meta = wp_get_attachment_metadata($post_id);
        $orginalImageUrls[0] = $orginalImageUrl;
        $uploadDirInfo = wp_upload_dir();
        if (is_bool($uploadDirInfo)) {
            Utility::logError('Insufficient File Permissions');

            return;
        }
        $orginalImageUrls[1] = $uploadDirInfo['baseurl'] . '/' . $attachment_meta['file'];

        if ($orginalImageUrls[0] != $orginalImageUrls[1]) {
            foreach ($orginalImageUrls as $orginalImageUrl) {
                $orginalImagePath = Utility::attachmentUrlToPath($orginalImageUrl);
                $orginalImagePath = rtrim($orginalImagePath, '.' . pathinfo($orginalImagePath, PATHINFO_EXTENSION)) . '.avif';
                if (file_exists($orginalImagePath)) wp_delete_file($orginalImagePath);
            }
        } else {
            $orginalImagePath = Utility::attachmentUrlToPath($orginalImageUrls[0]);
            $orginalImagePath = rtrim($orginalImagePath, '.' . pathinfo($orginalImagePath, PATHINFO_EXTENSION)) . '.avif';
            if (file_exists($orginalImagePath)) wp_delete_file($orginalImagePath);
        }

        /**
         * Deleting Thumbs
         */
        $fileDir = pathinfo(Utility::attachmentUrlToPath($orginalImageUrls[0]), PATHINFO_DIRNAME);
        $sizes = $attachment_meta['sizes'];
        foreach ($sizes as $size) {
            $file = $fileDir . '/' . $size['file'];
            $file = rtrim($file, '.' . pathinfo($file, PATHINFO_EXTENSION)) . '.avif';
            if (file_exists($file)) wp_delete_file($file);
        }

        delete_post_meta($post_id, 'avifexpressconverted', false);
    }

    /**
     * convert
     * This Method actually convert image and save them
     * @param mixed $src Path of the source file
     * @param mixed $des Path to save converted file
     * @param mixed $quality Image Quality(0 - 100)
     * @param mixed $speed Conversion speed (0 - 10)
     * @return boolean
     */
    public static function convert($src, $des, $quality, $speed): bool
    {
        if (!$src && !$des && !$quality && !$speed) return false;

        if (!file_exists($src)) {
            Utility::logError('Source image file does not exist:' . $src);
            return false;
        }

        // Try Imagick First
        if (IS_IMAGICK_AVIF) {

            $imagick = new \Imagick();
            $formats = $imagick->queryFormats();
            if (in_array('AVIF', $formats)) {
                try {
                    $imagick->readImage($src);
                    $imagick->setImageFormat('avif');
                    if ($quality > 0) {
                        $imagick->setCompressionQuality($quality);
                        $imagick->setImageCompressionQuality($quality);
                    } else {
                        $imagick->setCompressionQuality(1);
                        $imagick->setImageCompressionQuality(1);
                    }
                    $imagick->writeImage($des);
                } catch (\ImagickException $e) {
                    Utility::logError('Imagick error: ' . $e->getMessage());
                }
                return true;
            }
        }

        // Try GD
        if (IS_GD_AVIF) {

            $fileType = getimagesize($src);
            if (!$fileType) {
                Utility::logError('Failed to get image size');
                return false;
            }
            $fileType = $fileType['mime'];

            switch ($fileType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $sourceGDImg = @imagecreatefromjpeg($src);
                    break;
                case 'image/png':
                    $sourceGDImg = @imagecreatefrompng($src);
                    break;
                default:
                    Utility::logError('Unsupported image type for GD conversion: ' . $fileType);
                    return false;
            }

            if (is_bool($sourceGDImg)) {
                Utility::logError('Failed to create GD image resource');
                return false;
            }

            //noinspection PhpUndefinedFunctionInspection
            @imageavif($sourceGDImg, $des, $quality, $speed);
            if (filesize($des) % 2 == 1) {
                file_put_contents($des, "\0", FILE_APPEND);
            }
            @imagedestroy($sourceGDImg);
            return true;
        }


        Utility::logError('Avif Express: Local avif support not found');

        return false;
    }

    /**
     * function to do cloud image conversion
     * @param array $urls array of urls
     */
    public static function cloudConvert(array $urls)
    {

        //get the api key from option table
        $apiKey = Options::getApiKey();

        //if api key is not set then return false
        if (!$apiKey) return false;

        //if the domain/installation is local
        $isLocal = Utility::isLocalDomain();

        //add origin and api key to the request header


        $avifServerImageData = [];
        if ($isLocal) {
            foreach ($urls as $url) {
                // Convert URL to local file path
                $filePath = Utility::attachmentUrlToPath($url);

                if (!$filePath || !file_exists($filePath)) {
                    Utility::logError("Error: File does not exist at path " . $filePath);
                    continue;
                }

                // Prepare the file for upload
                $file = fopen($filePath, 'r');
                $boundary = wp_generate_uuid4();
                $delimiter = '-------------' . $boundary;

                // Multipart form data body
                $body = '--' . $delimiter . "\r\n" .
                    'Content-Disposition: form-data; name="image"; filename="' . basename($filePath) . '"' . "\r\n" .
                    'Content-Type: ' . mime_content_type($filePath) . "\r\n\r\n" .
                    stream_get_contents($file) . "\r\n" .
                    '--' . $delimiter . "\r\n" .
                    'Content-Disposition: form-data; name="src"' . "\r\n\r\n" .
                    $url . "\r\n" .
                    '--' . $delimiter . '--';

                fclose($file);

                // 1. Sending image to the cloud server
                $cloudResponse = wp_remote_post(AVIF_CLOUD_ADDRESS, array(
                    'headers' => Utility::prepareRequestHeader($apiKey, 'multipart/form-data; boundary=' . $delimiter,),
                    'body' => $body
                ));

                // 2. Getting the response
                $body = wp_remote_retrieve_body($cloudResponse);
                if(intval(wp_remote_retrieve_header($cloudResponse,'x-ratelimit-requests-remaining')) == 0){
                    Utility::logError('Consumed all of the allocated API calls');
                    return 'ccover';
                }

                // Checking for any error and then logging it
                if (is_wp_error($cloudResponse)) {
                    Utility::logError("Error:" . $cloudResponse->get_error_message());
                }

                $imageUrls = json_decode($body, true);

                // Check server status code for any issue
                if (isset($imageUrls['status']) && $imageUrls['status'] !== 'success') {
                    Utility::logError("Error:" . print_r($imageUrls, true));
                }

                if (isset($imageUrls['status']) && $imageUrls['status'] == 'success') {
                    $avifServerImageData[] = $imageUrls['data'];
                }

            }

        } else {
            foreach ($urls as $url) {


                //actual payload with appended url, GET method
                $fullRequestUrl = AVIF_CLOUD_ADDRESS . '?url=' . urlencode($url);

                //conversion started
                //1. sending url to the cloud server
                $cloudResponse = wp_remote_get($fullRequestUrl,
                    [
                        'headers' => Utility::prepareRequestHeader($apiKey)
                    ]
                );

                //2. getting the response
                $body = wp_remote_retrieve_body($cloudResponse);

                //checking for any error and then logging it
                if (is_wp_error($cloudResponse)) {

                    Utility::logError("Error:" . $cloudResponse->get_error_message());

                }

                if(intval(wp_remote_retrieve_header($cloudResponse,'x-ratelimit-requests-remaining')) == 0){
                    Utility::logError('Consumed all of the allocated API calls');
                    return 'ccover';
                }

                $imageUrls = json_decode($body, true);

                //check server status code for any issue
                if (isset($imageUrls['status']) && $imageUrls['status'] !== 'success') {

                    Utility::logError("Error:" . print_r($imageUrls, true));

                }

                if (isset($imageUrls['status']) && $imageUrls['status'] == 'success') {

                    $avifServerImageData[] = $imageUrls['data'];
                }

            }
        }

        self::processResponse($avifServerImageData);

    }

    /**
     * Converting image(s) to webp
     * @param string $src source of the image
     * @return bool TRUE|FALSE true on success, false on fail
     */
    public static function webpConvert($src): bool
    {
        if (!$src) return false;

        $des = dirname($src) . DIRECTORY_SEPARATOR . pathinfo($src, PATHINFO_FILENAME) . '.webp';

        //check if the file already exists or not
        if (file_exists($des)) {
            return true;
        }

        if (IS_IMAGICK_WEBP) {
            try {
                $imagick = new \Imagick();
                $imagick->readImage($src);
                $imagick->setImageFormat('webp');
                return $imagick->writeImage($des);

            } catch (\ImagickException $e) {
                Utility::logError('Imagick error: ' . $e->getMessage());
                return false;
            }
        }

        if (IS_GD_WEBP) {
            $fileType = getimagesize($src);
            if (!$fileType) {
                Utility::logError('Failed to get image size');
                return false;
            }
            $fileType = $fileType['mime'];

            switch ($fileType) {
                case 'image/jpeg':
                case 'image/jpg':
                    $sourceGDImg = @imagecreatefromjpeg($src);
                    break;
                case 'image/png':
                    $sourceGDImg = @imagecreatefrompng($src);
                    break;
                default:
                    Utility::logError('Unsupported image type for GD conversion: ' . $fileType);
                    return false;
            }

            if (is_bool($sourceGDImg) && $sourceGDImg === false) {
                Utility::logError('Failed to create GD image resource');
                return false;
            }

            $result = @imagewebp($sourceGDImg, $des);
            if ($result === false) {
                Utility::logError('Failed to convert image to WebP using GD');
                return false;
            }

            @imagedestroy($sourceGDImg);
            return true;
        }

        return false;

    }

    /**
     * responsible for getting and writing converted images from cloud
     * @param $avifServerImageData
     * @return void
     */
    public static function processResponse($avifServerImageData)
    {
        //using WordPress file system class instead of php native file_get_contents()
        include_once ABSPATH . 'wp-admin/includes/file.php';

        if (!WP_Filesystem()) {
            Utility::logError('Unable to initialize the WP_filesystem');
            return;
        }

        global $wp_filesystem;

        //this if condition to prevent to null
        if (is_array($avifServerImageData) || is_object($avifServerImageData)) {
            foreach ($avifServerImageData as $imageUrl) {

                //creating destination file path form the source
                $srcImagePath = Utility::attachmentUrlToPath($imageUrl[0]);
                if (!$srcImagePath) {
                    Utility::logError('Unable to create absolute path from relative path of source image');
                    continue;
                }

                //creating destination path
                $pathInfo = pathinfo($srcImagePath);
                $avifFileName = $pathInfo["dirname"] . DIRECTORY_SEPARATOR . $pathInfo["filename"] . '.avif';

                //getting remote avif file
                $response = wp_remote_get($imageUrl[1]);
                if (is_wp_error($response)) {
                    Utility::logError("Avif Download Error:" . $response->get_error_message());
                    continue;
                }

                //retrieving avif file body content
                $body = wp_remote_retrieve_body($response);

                if (!$wp_filesystem->put_contents($avifFileName, $body, FS_CHMOD_FILE)) {
                    Utility::logError('Unable to write avif file');
                }

            }
        } else {
            Utility::logError('Invalid input: Expected array or object');
        }
    }
}
