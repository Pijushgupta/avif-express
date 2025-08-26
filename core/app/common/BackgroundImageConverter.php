<?php 

namespace Avife\common;
use PijushGupta\ImageConverter\ImageConverter;
use WP_Background_Process;
use Avife\common\Options;
use Exception;

class BackgroundImageConverter extends WP_Background_Process{

    protected $prefix = 'avife';

    protected  $action = 'bgic';

    protected int $quality;

    protected int $speed;

    protected string $driver;

    private static $instance;

    public function __construct()
    {   
        parent::__construct();
        $this->quality = Options::getImageQuality();
        $this->speed = Options::getComSpeed();
        $this->driver = IS_IMAGICK_AVIF ? 'imagick' : 'gd';

        
    }
    /**
     * Gets the single instance of the class.
     *
     * @return self
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    //actual works  
    protected function task($item){
        
        try{
            $converter = new ImageConverter($item);
            $converter->setFormat('avif')->setDriver($this->driver)->setQuality($this->quality)->setSpeed($this->speed)->convert();
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