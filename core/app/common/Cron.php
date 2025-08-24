<?php

namespace Avife\common;

if (!defined('ABSPATH')) {
    exit;
}

use Avife\common\CronManager;
use Avife\common\Options;
use Avife\common\BackgroundImageCnverter;
use Avife\trait\FileTrait;

class Cron
{
    use FileTrait;

    public function initiateCron()
    {
        $cron = new CronManager(
            'avife_auto_convert',
            [$this, 'initiateConversion']
        );

        $backgroundConv = Options::getBackgroundConv();
        $currentSchedule = wp_next_scheduled('avife_auto_convert');

        // If background conversion is off, clear any existing cron job.
        if ($backgroundConv == 'off') {
            if ($currentSchedule) {
                $cron->clear();
            }
            return;
        }

        $eventInterval = Options::getBackgroundConvEvent();

        // If the cron job is NOT scheduled, schedule it.
        if (!$currentSchedule) {
            $cron->schedule($eventInterval);
        }
        // If the cron job is scheduled but the interval has changed, clear and reschedule.
        else {
            $scheduledInterval = $cron->getCurrentSchedule();
            if ($scheduledInterval !== $eventInterval) {
                $cron->clear();
                $cron->schedule($eventInterval);
            }
        }
    }

    //the actual work getting done here - not related to schedule or action hook
    public function initiateConversion(): bool
    {
        // check if background image conversion is enabled or not 
        $directoryToTarget = Options::getBackgroundConv();
        if ($directoryToTarget == 'off') return false;

        $backgroundImageConverterObj = new BackgroundImageCnverter();
        if($backgroundImageConverterObj->is_processing()) return false;
        
        $directoryPaths = [];
        
        if ($directoryToTarget == 'upload' || $directoryToTarget == 'themeandupload') {
            $directoryPaths[] = wp_upload_dir()['basedir'];
        }

        if ($directoryToTarget == 'theme' || $directoryToTarget == 'themeandupload') {
            //for only active theme directory
            if (is_child_theme()) {
                $directoryPaths[] = get_stylesheet_directory();
                $directoryPaths[] = get_template_directory();
            } else {
               $directoryPaths[] = get_template_directory();
            }
            //for whole theme directory just use get_theme_root() - not ideal for saving server space 
        }

        $filesToConvert = [];
        foreach($directoryPaths as $directoryPath){
            $filesToConvert = array_merge($filesToConvert,$this->findFiles($directoryPath,array('jpg','png','jpeg'),1));
        }

        if(empty($filesToConvert)) return false;

        foreach($filesToConvert as $filetoConvert){
            $backgroundImageConverterObj->push_to_queue($filetoConvert);
        }

        $backgroundImageConverterObj->save()->dispatch();

        return true;
    }
}
