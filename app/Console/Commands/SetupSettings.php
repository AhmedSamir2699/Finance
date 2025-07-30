<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class SetupSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the settings system by running migration and seeder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up the settings system...');

        // Run the settings migration
        $this->info('Running settings migration...');
        Artisan::call('migrate', ['--path' => 'database/migrations/2024_01_01_000000_create_settings_table.php']);
        
        // Run the settings seeder
        $this->info('Running settings seeder...');
        Artisan::call('db:seed', ['--class' => 'SettingsSeeder']);
        
        // Clear settings cache
        $this->info('Clearing settings cache...');
        Artisan::call('settings:clear-cache');
        
        $this->info('Settings system setup completed successfully!');
        
        return Command::SUCCESS;
    }
} 