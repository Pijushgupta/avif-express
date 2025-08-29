<?php

namespace Avife\common;

if (!defined('ABSPATH')) {
    exit;
}

class CronManager
{
    private $hook = '';
    private $callback;

    public function __construct(string $hook, callable $callback)
    {
        $this->hook = $hook;
        $this->callback = $callback;
        // Register the callback
        add_action($this->hook, $this->callback);
    }

    /**
     * Schedule a cron job with given interval (hourly, twicedaily, daily, weekly).
     */
    public function schedule(string $interval)
    {
        // Check if an event is already scheduled for this hook.
        if (!wp_next_scheduled($this->hook)) {
            if (in_array($interval, ['hourly', 'twicedaily', 'daily', 'weekly'], true)) {
                wp_schedule_event(time(), $interval, $this->hook);
            }
        }
    }

    /**
     * Clear any scheduled event.
     */
    public function clear()
    {
        wp_clear_scheduled_hook($this->hook);
    }

    /**
     * Get current schedule name (if any).
     */
    public function getCurrentSchedule()
    {
        $event = wp_get_scheduled_event($this->hook);

        if ($event && !empty($event->schedule)) {
            return $event->schedule; // e.g. 'hourly', 'daily', or custom slug
        }

        return null;
    }
}
