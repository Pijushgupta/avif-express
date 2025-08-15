<?php 

namespace Avife\factory;

if (!defined('ABSPATH')) exit;

use Avife\common\Lazyloadhtml as lazyhtml;
use Avife\common\Lazyloadjs as lazyjs;
use Avife\common\Options;


class Lazyloadfactory {
    private array $methods = [];
    public function __construct()
    {
        $this->methods = [
            'html'=> new lazyhtml(),
            'js' => new lazyjs('0px 0px '.Options::getLazyLoadJsRootMargin().'px 0px',Options::getLazyLoadJsThreshold())
           
        ];
    }

    public function Lazyload(string $methodName){
        return $this->methods[$methodName] ?? throw new \InvalidArgumentException("Invalid Argument");
    }
}