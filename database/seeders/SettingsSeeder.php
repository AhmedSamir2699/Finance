<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // Branding & Identity (General)
            [
                'key' => 'app_name',
                'value' => 'نظام إدارة الموظفين',
                'type' => 'string',
                'description' => 'Application name displayed throughout the system',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'app_description',
                'value' => 'نظام شامل لإدارة الموظفين وتتبع الحضور',
                'type' => 'string',
                'description' => 'Application description',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'app_logo',
                'value' => '/images/logo.png',
                'type' => 'image',
                'description' => 'Path to application logo',
                'group' => 'general',
                'is_public' => true,
            ],
            [
                'key' => 'app_favicon',
                'value' => '/favicon.ico',
                'type' => 'image',
                'description' => 'Path to application favicon',
                'group' => 'general',
                'is_public' => true,
            ],

            // User Interface
            [
                'key' => 'language',
                'value' => 'ar',
                'type' => 'select',
                'description' => 'Default application language',
                'group' => 'ui',
                'is_public' => true,
            ],
            [
                'key' => 'theme',
                'value' => 'light',
                'type' => 'select',
                'description' => 'Application theme',
                'group' => 'ui',
                'is_public' => true,
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Riyadh',
                'type' => 'string',
                'description' => 'Default timezone',
                'group' => 'ui',
                'is_public' => true,
            ],
            [
                'key' => 'date_format',
                'value' => 'Y-m-d',
                'type' => 'string',
                'description' => 'Default date format',
                'group' => 'ui',
                'is_public' => true,
            ],
            [
                'key' => 'time_format',
                'value' => 'H:i',
                'type' => 'string',
                'description' => 'Default time format',
                'group' => 'ui',
                'is_public' => true,
            ],

            // Email Configuration
            [
                'key' => 'mail_from_name',
                'value' => 'نظام إدارة الموظفين',
                'type' => 'string',
                'description' => 'Default sender name for emails',
                'group' => 'email',
                'is_public' => false,
            ],
            [
                'key' => 'mail_from_address',
                'value' => 'noreply@example.com',
                'type' => 'string',
                'description' => 'Default sender email address',
                'group' => 'email',
                'is_public' => false,
            ],
            [
                'key' => 'mail_signature',
                'value' => 'مع أطيب التحيات،\nفريق إدارة الموظفين',
                'type' => 'text',
                'description' => 'Default email signature',
                'group' => 'email',
                'is_public' => false,
            ],



            // Security & Access Control
            [
                'key' => 'session_timeout',
                'value' => 120,
                'type' => 'integer',
                'description' => 'Session timeout in minutes',
                'group' => 'security',
                'is_public' => false,
            ],
            [
                'key' => 'password_min_length',
                'value' => 8,
                'type' => 'integer',
                'description' => 'Minimum password length',
                'group' => 'security',
                'is_public' => true,
            ],
            [
                'key' => 'login_attempts_limit',
                'value' => 5,
                'type' => 'integer',
                'description' => 'Maximum login attempts before lockout',
                'group' => 'security',
                'is_public' => false,
            ],
            [
                'key' => 'lockout_duration',
                'value' => 15,
                'type' => 'integer',
                'description' => 'Account lockout duration in minutes',
                'group' => 'security',
                'is_public' => false,
            ],

            // Notifications & Alerts
            [
                'key' => 'email_notifications',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Enable email notifications',
                'group' => 'notifications',
                'is_public' => false,
            ],
            [
                'key' => 'push_notifications',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Enable push notifications',
                'group' => 'notifications',
                'is_public' => false,
            ],
            [
                'key' => 'attendance_reminders',
                'value' => true,
                'type' => 'boolean',
                'description' => 'Enable attendance reminders',
                'group' => 'notifications',
                'is_public' => false,
            ],

            // System & Maintenance
            [
                'key' => 'maintenance_mode',
                'value' => false,
                'type' => 'boolean',
                'description' => 'Enable maintenance mode',
                'group' => 'system',
                'is_public' => true,
            ],
            [
                'key' => 'maintenance_message',
                'value' => 'النظام تحت الصيانة. يرجى المحاولة مرة أخرى لاحقاً.',
                'type' => 'text',
                'description' => 'Maintenance mode message',
                'group' => 'system',
                'is_public' => true,
            ],
            [
                'key' => 'debug_mode',
                'value' => false,
                'type' => 'boolean',
                'description' => 'Enable debug mode',
                'group' => 'system',
                'is_public' => false,
            ],
            [
                'key' => 'log_level',
                'value' => 'info',
                'type' => 'select',
                'description' => 'Application log level',
                'group' => 'system',
                'is_public' => false,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
} 