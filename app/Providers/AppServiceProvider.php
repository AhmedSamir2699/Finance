<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Helpers\SidebarHelper;
use App\Helpers\SettingsHelper;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('sidebar.helper', function ($app) {
            return new SidebarHelper();
        });
        
        $this->app->singleton('settings.helper', function ($app) {
            return new SettingsHelper();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Check if settings table exists before trying to access it
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return;
        }

        try {
            // Apply debug mode setting
            if (SettingsHelper::isDebugMode()) {
                config(['app.debug' => true]);
            }
            
            // Apply log level setting
            $logLevel = SettingsHelper::logLevel();
            if ($logLevel) {
                config(['logging.channels.stack.level' => $logLevel]);
            }
            
            // Apply email settings
            config([
                'mail.from.address' => SettingsHelper::mailFromAddress(),
                'mail.from.name' => SettingsHelper::mailFromName(),
            ]);
        } catch (\Exception $e) {
            // If settings table doesn't exist yet (during migrations), use defaults
        }
        
        Blade::directive('sidebarLinks', function () {
            return "<?php echo \App\Helpers\SidebarHelper::getSidebarLinksWithBadges(); ?>";
        });
        
        Blade::directive('activeElection', function () {
            return "<?php echo \App\Helpers\SidebarHelper::getActiveElection(); ?>";
        });
        
        Blade::directive('setting', function ($expression) {
            return "<?php echo \App\Helpers\SettingsHelper::get($expression); ?>";
        });
    }
}
