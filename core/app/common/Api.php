<?php

namespace Avife\common;

class Api
{
    public static function check(string $apiKey){
        $imagePath =  AVIFE_ABS  . 'sample.jpg';
        if (!file_exists($imagePath)) return false;

        $boundary = wp_generate_uuid4();
        $delimiter = '-------------' . $boundary;

        $body = Utility::prepareFormBody($imagePath, $boundary);

        $cloudResponse = wp_remote_post(AVIF_CLOUD_ADDRESS, array(
            'headers' => Utility::prepareRequestHeader($apiKey, 'multipart/form-data; boundary=' . $delimiter,),
            'body' => $body
        ));

        // Checking for any error and then logging it
        if (is_wp_error($cloudResponse)) {
            Utility::logError("Error:" . $cloudResponse->get_error_message());
            return 3;
        }

        if(wp_remote_retrieve_response_code($cloudResponse) == 403){
            Utility::logError("Error: Invalid API key");
            return 2;
        }

        //if no remaining call but api key is valid
        if(intval(wp_remote_retrieve_header($cloudResponse,'x-ratelimit-requests-remaining')) >= 0){
            Utility::logError('Consumed all of the allocated API calls');
            return 1;
        }

    }

}
