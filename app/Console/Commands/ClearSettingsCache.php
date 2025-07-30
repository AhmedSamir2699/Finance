<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SettingsHelper;

class ClearSettingsCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'settings:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all settings cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SettingsHelper::clearCache();
        
        $this->info('Settings cache cleared successfully.');
        
        return Command::SUCCESS;
    }
} 