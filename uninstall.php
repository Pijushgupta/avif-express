<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

//delete option
$options = [
    'avifautoconvstatus',
    'avifoperationmode',
    'avifimagequality',
    'avifcompressionspeed',
    'avifconversionengine',
    'avifontheflyavif',
    'avifenablelogging',
    'avifapikey',
    'aviffallbackmode',
    'aviflazyload',
    'aviflazyloadrootmargin',
    'aviflazyloadjsthreshold',
    'aviflazyloadbackground',
    'avifbackgroundConv',
    'avifbackgroundevents'
];

foreach($options as $option){
    delete_option($option);
}