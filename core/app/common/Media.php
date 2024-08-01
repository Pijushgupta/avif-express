<?php

namespace Avife\common;

if (!defined('ABSPATH')) exit;

class Media
{


    /**
     * ajaxCountMedia
     * @return void ajax handle for countMedia
     */
    public static function ajaxCountMedia()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();
        echo json_encode(self::countMedia());
        wp_die();
    }

    /**
     * countMedia
     *
     * @return array in array(number of converted images, number of total images, number of various image sizes)
     */
    public static function countMedia(): array
    {
        $uploadDirPath = wp_upload_dir()['basedir'];

        $allMedia = Theme::findFiles($uploadDirPath, array("png", "jpg", "webp", "jpeg"), 0);
        if (gettype($allMedia) == 'array') {
            $allMedia = count($allMedia);
        }


        $convertedMedia = Theme::findFiles($uploadDirPath, array("png", "jpg", "webp", "jpeg"), -1);

        if (gettype($convertedMedia) == 'array') {
            $convertedMedia = count($convertedMedia);
        }

        return array($convertedMedia, $allMedia, count(get_intermediate_image_sizes()));
    }

    /**
     * ajaxConvertRemaining
     *
     * @return void ajax handle for convertRemaining
     */
    public static function ajaxConvertRemaining()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();

        $isCloudEngine = '0';
        if (Options::getConversionEngine() == 'cloud') $isCloudEngine = '1';
        /**
         * checking if local conversion not possible and cloud conversion disabled
         * then terminate
         */
        if (!Utility::isLocalAvifConversionSupported() && $isCloudEngine == '0') wp_die();

        echo json_encode(self::convertRemaining());
        wp_die();
    }

    /**
     * convertRemaining
     *
     * @return boolean|null|string true on success. false on error, null on empty source and string 'keep-alive' to let client know to request again.
     */
    public static function convertRemaining()
    {


        $uploadDirPath = wp_upload_dir()['basedir'];

        $unConvertedAttachments = Theme::findFiles($uploadDirPath, array("png", "jpg", "jpeg"), 1);

        if (gettype($unConvertedAttachments) != 'array' || empty($unConvertedAttachments) || $unConvertedAttachments == 0) return null;

        /**
         * Checking if 'set_time_limit' can be set or not
         * if not don't do anything
         */
        if (!Setting::avif_set_time_limit()) return false;


        $keepAlive = 0;
        $counter = 1;
        $quality = Options::getImageQuality();
        $speed = Options::getComSpeed();


        if (Options::getConversionEngine() == 'local') {


            foreach ($unConvertedAttachments as $unConvertedAttachment) {

                /**
                 * creating path for file to delete. Just by removing original extension with .avif
                 */
                $dest = (string)rtrim($unConvertedAttachment, '.' . pathinfo($unConvertedAttachment, PATHINFO_EXTENSION)) . '.avif';

                Image::convert($unConvertedAttachment, $dest, $quality, $speed);

                if ($counter == 2) {
                    return 'keep-alive';
                }
                $counter++;
            }
        }

        if (Options::getConversionEngine() == 'cloud') {
            //only allow 20 images per batch
            if (count($unConvertedAttachments) > 20) {
                $unConvertedAttachments = array_slice($unConvertedAttachments, 0, 20);
                $keepAlive = 1;
            }
            $unConvertedAttachmentUrls = Utility::pathToAttachmentUrl($unConvertedAttachments);

            $cs = Image::cloudConvert($unConvertedAttachmentUrls);
            if ($cs === false) return 'ccfail';
            if ($cs === 'ccover') return 'ccover';
            if ($keepAlive == 1) return 'keep-alive';

        }
        return true;
    }

    /**
     * ajaxDeleteAll
     *
     * @return void ajax handle for deleteAll
     */
    public static function ajaxDeleteAll()
    {
        if (!wp_verify_nonce($_POST['avife_nonce'], 'avife_nonce')) wp_die();;
        echo json_encode(self::deleteAll());
        wp_die();
    }

    /**
     * deleteAll
     * delete All converted Images
     * @return bool true as signal
     */
    public static function deleteAll() : bool
    {

        $uploadDirPath = wp_upload_dir()['basedir'];
        $attachments = Theme::findFiles($uploadDirPath, array("png", "jpg", "webp", "jpeg"), -1);

       return Utility::deleteFiles($attachments);
    }


}
