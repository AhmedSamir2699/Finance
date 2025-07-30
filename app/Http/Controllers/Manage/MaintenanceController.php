<?php

namespace App\Http\Controllers\Manage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class MaintenanceController extends Controller
{
    public function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('manage.index') => __('breadcrumbs.manage.settings'),
            route('manage.maintenance.index') => __('breadcrumbs.manage.maintenance'),
        ];

        return view('manage.maintenance.index', compact('breadcrumbs'));
    }

    public function debugEnvironment()
    {
        $envInfo = [
            'APP_ENV' => env('APP_ENV'),
            'APP_DEBUG' => env('APP_DEBUG'),
            'config_app_env' => config('app.env'),
            'config_app_debug' => config('app.debug'),
            'flasher_path' => env('APP_ENV') === 'production' ? '/employee/vendor/flasher/' : '/vendor/flasher/',
            'APP_URL' => env('APP_URL'),
            'APP_NAME' => env('APP_NAME'),
        ];

        return response()->json($envInfo);
    }

    public function clearCache()
    {
        try {
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');
            Artisan::call('settings:clear-cache');

            
            flash()->success(__('maintenance.cache_cleared_successfully'));
        } catch (\Exception $e) {
            flash()->error(__('maintenance.cache_clear_failed'));
        }

        return redirect()->route('manage.maintenance.index');
    }

    public function clearLog()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            
            if (File::exists($logPath)) {
                File::put($logPath, '');
                flash()->success(__('maintenance.log_cleared_successfully'));
            } else {
                flash()->info(__('maintenance.log_file_not_found'));
            }
        } catch (\Exception $e) {
            flash()->error(__('maintenance.log_clear_failed'));
        }

        return redirect()->route('manage.maintenance.index');
    }

    public function getLogContent()
    {
        try {
            $logPath = storage_path('logs/laravel.log');
            
            if (File::exists($logPath)) {
                $content = File::get($logPath);
                $lines = explode("\n", $content);
                $recentLines = array_slice($lines, -1000); // Get last 1000 lines
                return response()->json(['content' => implode("\n", $recentLines)]);
            } else {
                return response()->json(['content' => __('maintenance.log_file_not_found')]);
            }
        } catch (\Exception $e) {
            return response()->json(['content' => __('maintenance.log_read_failed')]);
        }
    }

    public function resetSettings()
    {
        try {
            Artisan::call('settings:reset');
            
            flash()->success(__('maintenance.settings_reset_successfully'));
        } catch (\Exception $e) {
            flash()->error(__('maintenance.settings_reset_failed'));
        }

        return redirect()->route('manage.maintenance.index');
    }
} 