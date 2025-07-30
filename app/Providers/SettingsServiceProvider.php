<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use App\Helpers\SettingsHelper;

class SettingsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Only run in web context, not in console commands
        if ($this->app->runningInConsole()) {
            return;
        }

        // Check if settings table exists before trying to access it
        if (!Schema::hasTable('settings')) {
            return;
        }

        try {
            // Set application name from settings
            config(['app.name' => SettingsHelper::appName()]);
            
            // Set timezone from settings
            config(['app.timezone' => SettingsHelper::timezone()]);
            
            // Set locale from settings (default to Arabic)
            config(['app.locale' => SettingsHelper::language() ?? 'ar']);
            
            // Set debug mode from settings
            config(['app.debug' => SettingsHelper::isDebugMode()]);
            
            // Set log level from settings
            config(['logging.channels.stack.level' => SettingsHelper::logLevel()]);
            
            // Set mail configuration from settings
            config([
                'mail.from.name' => SettingsHelper::get('mail_from_name', config('mail.from.name')),
                'mail.from.address' => SettingsHelper::get('mail_from_address', config('mail.from.address')),
            ]);
            
        } catch (\Exception $e) {
            // If settings table doesn't exist yet (during migrations), use defaults
            // This prevents errors during the initial setup
        }
    }
} 