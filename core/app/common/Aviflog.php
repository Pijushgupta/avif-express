<?php
namespace Avife\common;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class Aviflog{
    /**
     * Monolog logger channel
     */
    public $channel;

    /**
     * Monolog log type
     */
    public $type;

    /**
     * Monolog log message
     */
    public $message;
    
    /**
     * Monolog log context
     */
    public $context;
    

    public function __construct($channel = 'Avif Express',$type = null ,$message = 'No message provided', $context = []){
        $this->channel = $channel;
        
        if($type == null){
            $this->type = 'debug';
        }

        if(in_array($type,['error','warning','info','debug','notice','critical','alert'])){
            $this->type = $type;
        }

        if($context != []){
            $this->context = $context;
        }

        $this->message = $message;

        $this->log();
    }

    public function log(){
        $log = new Logger($this->channel);
        $log->pushHandler(new StreamHandler(AVIF_LOG_FILE, $this->typeToLevel()));
        $log->{$this->type}($this->message, $this->context);
    }

    public function typeToLevel(){
        switch($this->type){
            case 'error':
                return Level::Error;
            case 'warning':
                return Level::Warning;
            case 'info':
                return Level::Info;
            case 'debug':
                return Level::Debug;
            case 'notice':
                return Level::Notice;
            case 'critical':
                return Level::Critical;
            case 'alert':
                return Level::Alert;
            default:
                return Level::Debug;
        }
    
    }
    
}
