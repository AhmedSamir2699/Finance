<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Helpers\SidebarHelper;

class ClearSidebarCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sidebar:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the sidebar badge cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        SidebarHelper::clearCache();
        $this->info('Sidebar cache cleared successfully!');
        
        return 0;
    }
} 