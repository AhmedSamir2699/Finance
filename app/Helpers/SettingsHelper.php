<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SettingsHelper
{
    /**
     * Get a setting value with caching
     */
    public static function get($key, $default = null)
    {
        // Check if settings table exists
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return $default;
        }

        return Cache::remember('settings.' . $key, 3600, function () use ($key, $default) {
            try {
                return Setting::getValue($key, $default);
            } catch (\Exception $e) {
                return $default;
            }
        });
    }

    /**
     * Set a setting value and clear cache
     */
    public static function set($key, $value, $type = 'string', $description = null, $group = 'general')
    {
        Cache::forget('settings.' . $key);
        return Setting::setValue($key, $value, $type, $description, $group);
    }

    /**
     * Get all settings as array
     */
    public static function all()
    {
        // Check if settings table exists
        if (!Schema::hasTable('settings')) {
            return [];
        }

        return Cache::remember('settings.all', 3600, function () {
            try {
                return Setting::getAllAsArray();
            } catch (\Exception $e) {
                return [];
            }
        });
    }

    /**
     * Get settings by group
     */
    public static function getByGroup($group)
    {
        // Check if settings table exists
        if (!Schema::hasTable('settings')) {
            return collect();
        }

        return Cache::remember('settings.group.' . $group, 3600, function () use ($group) {
            try {
                return Setting::getByGroup($group);
            } catch (\Exception $e) {
                return collect();
            }
        });
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        // Check if settings table exists
        if (!Schema::hasTable('settings')) {
            return;
        }

        Cache::forget('settings.all');
        try {
            $settings = Setting::all();
            foreach ($settings as $setting) {
                Cache::forget('settings.' . $setting->key);
            }
            foreach (['general', 'email', 'security', 'notifications', 'ui', 'system'] as $group) {
                Cache::forget('settings.group.' . $group);
            }
        } catch (\Exception $e) {
            // If settings table doesn't exist, just clear the cache keys we know about
            foreach (['general', 'email', 'security', 'notifications', 'ui', 'system'] as $group) {
                Cache::forget('settings.group.' . $group);
            }
        }
    }

    /**
     * Get application name
     */
    public static function appName()
    {
        return self::get('app_name', 'نظام إدارة الموظفين');
    }

    /**
     * Get application description
     */
    public static function appDescription()
    {
        return self::get('app_description', 'نظام شامل لإدارة الموظفين وتتبع الحضور');
    }

    /**
     * Get application logo
     */
    public static function appLogo()
    {
        return self::get('app_logo', '/images/logo.png');
    }

    /**
     * Get application favicon
     */
    public static function appFavicon()
    {
        return self::get('app_favicon', '/favicon.ico');
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function isMaintenanceMode()
    {
        return self::get('maintenance_mode', false);
    }

    /**
     * Get maintenance message
     */
    public static function maintenanceMessage()
    {
        return self::get('maintenance_message', 'النظام تحت الصيانة. يرجى المحاولة مرة أخرى لاحقاً.');
    }



    /**
     * Get session timeout
     */
    public static function sessionTimeout()
    {
        return self::get('session_timeout', 120);
    }

    /**
     * Get minimum password length
     */
    public static function passwordMinLength()
    {
        return self::get('password_min_length', 8);
    }

    /**
     * Get login attempts limit
     */
    public static function loginAttemptsLimit()
    {
        return self::get('login_attempts_limit', 5);
    }

    /**
     * Get lockout duration
     */
    public static function lockoutDuration()
    {
        return self::get('lockout_duration', 15);
    }

    /**
     * Check if email notifications are enabled
     */
    public static function emailNotificationsEnabled()
    {
        return self::get('email_notifications', true);
    }

    /**
     * Check if push notifications are enabled
     */
    public static function pushNotificationsEnabled()
    {
        return self::get('push_notifications', true);
    }

    /**
     * Check if attendance reminders are enabled
     */
    public static function attendanceRemindersEnabled()
    {
        return self::get('attendance_reminders', true);
    }

    /**
     * Get application theme
     */
    public static function theme()
    {
        return self::get('theme', 'light');
    }

    /**
     * Get application language
     */
    public static function language()
    {
        return self::get('language', 'ar');
    }

    /**
     * Get application timezone
     */
    public static function timezone()
    {
        return self::get('timezone', 'Asia/Riyadh');
    }

    /**
     * Get date format
     */
    public static function dateFormat()
    {
        return self::get('date_format', 'Y-m-d');
    }

    /**
     * Get time format
     */
    public static function timeFormat()
    {
        return self::get('time_format', 'H:i');
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebugMode()
    {
        return self::get('debug_mode', false);
    }

    /**
     * Get log level
     */
    public static function logLevel()
    {
        return self::get('log_level', 'info');
    }

    /**
     * Get mail from address
     */
    public static function mailFromAddress()
    {
        return self::get('mail_from_address', config('mail.from.address'));
    }

    /**
     * Get mail from name
     */
    public static function mailFromName()
    {
        return self::get('mail_from_name', config('mail.from.name'));
    }

    /**
     * Get mail signature
     */
    public static function mailSignature()
    {
        return self::get('mail_signature', '');
    }
} 