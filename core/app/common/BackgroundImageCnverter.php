<?php 

namespace Avife\common;
use PijushGupta\ImageConverter\ImageConverter;
use WP_Background_Process;
use Avife\common\Options;
use Exception;

class BackgroundImageCnverter extends WP_Background_Process{

    protected string $action = 'avifeBGIC';

    protected int $quality;

    protected int $speed;

    public function __construct()
    {   
        parent::__construct();
        $this->quality = Options::getImageQuality();
        $this->speed = Options::getComSpeed();

        
    }
    //actual works  
    protected function task($item){
        try{
            $converter = new ImageConverter($item);
            $converter->setFormat('avif')->setQuality($this->quality)->setSpeed($this->speed)->convert();
        }catch(Exception $e){
            Utility::logError($e->getMessage());
        }
        
        return false;
    }

    //optional
    protected function complete()
    {
        parent::complete();
        //add any optional works in future after task 
    }
}