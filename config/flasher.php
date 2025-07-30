<?php

declare(strict_types=1);

use Flasher\Prime\Configuration;


$basePath = env('APP_ENV') == 'local' ? '' : '/employee';
$path = $basePath . '/vendor/flasher';


/*
 * Default PHPFlasher configuration for Laravel.
 *
 * This configuration file defines the default settings for PHPFlasher when
 * used within a Laravel application. It uses the Configuration class from
 * the core PHPFlasher library to establish type-safe configuration.
 *
 * @return array PHPFlasher configuration
 */
return Configuration::from([
    // Default notification library (e.g., 'flasher', 'toastr', 'noty', 'notyf', 'sweetalert')
    'default' => 'flasher',

    // Path to the main PHPFlasher JavaScript file
    'main_script' => $path.'/flasher.min.js',

    // List of CSS files to style your notifications
    'styles' => [
        $path.'/flasher.min.css'
    ],

    // Set global options for all notifications (optional)
    'options' => [
        // 'timeout' => 5000, // Time in milliseconds before the notification disappears
        'position' => 'top-left', // Where the notification appears on the screen
    ],

    // Automatically inject JavaScript and CSS assets into your HTML pages
    'inject_assets' => true,

    // Enable message translation using Laravel's translation service
    'translate' => true,

    // URL patterns to exclude from asset injection and flash_bag conversion
    'excluded_paths' => [],

    // Map Laravel flash message keys to notification types
    'flash_bag' => [
        'success' => ['success'],
        'error' => ['error', 'danger'],
        'warning' => ['warning', 'alarm'],
        'info' => ['info', 'notice', 'alert'],
    ],

]);
