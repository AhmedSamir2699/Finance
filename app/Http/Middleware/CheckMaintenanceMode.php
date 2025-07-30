<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip maintenance check for auth routes, asset routes, API routes, and health checks
        if ($request->is('auth/*') || $request->is('login') || $request->is('logout') || 
            $request->is('vendor/*') || $request->is('css/*') || $request->is('js/*') || 
            $request->is('images/*') || $request->is('favicon.ico') || $request->is('*.css') || 
            $request->is('*.js') || $request->is('*.png') || $request->is('*.jpg') || 
            $request->is('*.jpeg') || $request->is('*.gif') || $request->is('*.svg') || 
            $request->is('*.ico') || $request->is('*.woff') || $request->is('*.woff2') || 
            $request->is('*.ttf') || $request->is('*.eot') || $request->is('api/*') || 
            $request->is('up') || $request->is('health') || $request->is('livewire/*')) {
            return $next($request);
        }

        // Check if settings table exists before trying to access it
        if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            return $next($request);
        }

        try {
            // Check if maintenance mode is enabled
            if (SettingsHelper::isMaintenanceMode()) {
                // Allow access for users with admin/manager roles
                if (Auth::check() && Auth::user()->hasRole(['super-admin'])) {
                    return $next($request);
                }
                
                // Show maintenance page for all other users
                return response()->view('errors.maintenance', [
                    'message' => SettingsHelper::maintenanceMessage()
                ], 503);
            }
        } catch (\Exception $e) {
            // If settings table doesn't exist yet, continue normally
        }

        return $next($request);
    }
} 