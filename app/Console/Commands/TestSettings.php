<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SettingsHelper;

class TestSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if settings are properly implemented and working';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Settings Implementation...');
        $this->newLine();

        // Test maintenance mode
        $maintenanceMode = SettingsHelper::isMaintenanceMode();
        $this->info("Maintenance Mode: " . ($maintenanceMode ? 'Enabled' : 'Disabled'));
        
        if ($maintenanceMode) {
            $message = SettingsHelper::maintenanceMessage();
            $this->info("Maintenance Message: " . $message);
        }

        // Test debug mode
        $debugMode = SettingsHelper::isDebugMode();
        $this->info("Debug Mode: " . ($debugMode ? 'Enabled' : 'Disabled'));

        // Test log level
        $logLevel = SettingsHelper::logLevel();
        $this->info("Log Level: " . $logLevel);

        // Test app name
        $appName = SettingsHelper::appName();
        $this->info("App Name: " . $appName);

        // Test language
        $language = SettingsHelper::language();
        $this->info("Language: " . $language);

        $this->newLine();
        $this->info('Settings test completed!');
        
        if ($maintenanceMode) {
            $this->warn('⚠️  Maintenance mode is enabled! Only admins/managers can access the system.');
        }
        
        if ($debugMode) {
            $this->warn('⚠️  Debug mode is enabled! This should be disabled in production.');
        }
    }
} 