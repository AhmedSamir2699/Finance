<?php

namespace App\Livewire\Manage;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Setting;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Cache;


class Settings extends Component
{
    use WithFileUploads;
    
    public $settings = [];
    public $groups = [];
    public $activeGroup = 'general';
    public $imagePreview = [];
    public $uploadedImages = [];

    protected $rules = [
        'settings.*.value' => 'required',
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        // Define the order we want settings to appear
        $settingOrder = [
            'general' => ['app_name', 'app_description', 'app_logo', 'app_favicon'],
            'ui' => ['language', 'theme', 'timezone', 'date_format', 'time_format'],
            'email' => ['mail_from_name', 'mail_from_address', 'mail_signature'],
            'security' => ['session_timeout', 'password_min_length', 'login_attempts_limit', 'lockout_duration'],
            'notifications' => ['email_notifications', 'push_notifications', 'attendance_reminders'],
            'system' => ['maintenance_mode', 'maintenance_message', 'debug_mode', 'log_level'],
        ];
        
        $allSettings = Setting::all()->keyBy('key');
        $orderedSettings = [];
        
        foreach ($settingOrder as $group => $keys) {
            foreach ($keys as $key) {
                if ($allSettings->has($key)) {
                    $orderedSettings[] = $allSettings[$key]->toArray();
                }
            }
        }
        
        $this->settings = $orderedSettings;
        $this->groups = Setting::distinct()->pluck('group')->toArray();
    }

    public function setActiveGroup($group)
    {
        $this->activeGroup = $group;
    }

    public function updateSetting($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting) {
            $setting->update(['value' => $value]);
            flash()->success(__('settings.setting_updated'));
            
            // Clear cache for this setting and related caches
            Cache::forget('settings.' . $key);
            Cache::forget('settings.all');
            Cache::forget('settings.group.' . $setting->group);
            
            // Reload settings to reflect changes
            $this->loadSettings();
        }
    }

    public function uploadImage($key)
    {
        try {
            $this->validate([
                'uploadedImages.' . $key => 'image|max:2048', // 2MB max
            ]);

            if (isset($this->uploadedImages[$key])) {
                $file = $this->uploadedImages[$key];
                
                // Store the file in storage/app/public/images
                $path = $file->store('images', 'public');
                
                if ($path) {
                    // Update the setting with the new path (just the relative path)
                    $this->updateSetting($key, $path);
                    
                    // Set preview (full URL for display)
                    $this->imagePreview[$key] = asset('storage/' . $path);
                    
                    // Clear the uploaded file from the property
                    $this->uploadedImages[$key] = null;
                    
                    flash()->success(__('settings.image_uploaded'));
                } else {
                    flash()->error(__('settings.upload_failed'));
                }
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            flash()->error(__('settings.invalid_file_type'));
        } catch (\Exception $e) {
            flash()->error(__('settings.upload_failed'));
        }
    }

    public function updated($propertyName, $value)
    {
        // Handle file uploads automatically
        if (str_starts_with($propertyName, 'uploadedImages.')) {
            $key = str_replace('uploadedImages.', '', $propertyName);
            if ($value) {
                $this->uploadImage($key);
            }
        }
    }

    public function removeImage($key)
    {
        $this->imagePreview[$key] = null;
        $this->uploadedImages[$key] = null;
        $this->updateSetting($key, '');
    }

    public function hasHint($key)
    {
        $hintKey = 'settings.hints.' . $key;
        $translation = __($hintKey);
        return $translation !== $hintKey;
    }

    public function saveAll()
    {
        $this->validate();

        foreach ($this->settings as $settingData) {
            $setting = Setting::find($settingData['id']);
            if ($setting) {
                $setting->update(['value' => $settingData['value']]);
            }
        }

        // Clear all settings cache
        SettingsHelper::clearCache();

        flash()->success(__('settings.all_settings_saved'));
    }

    public function resetToDefaults()
    {
        // Reset all settings to their default values (organized by logical groups)
        $defaults = [
            // Branding & Identity
            'app_name' => 'نظام إدارة الموظفين',
            'app_description' => 'نظام شامل لإدارة الموظفين وتتبع الحضور',
            'app_logo' => '/images/logo.png',
            'app_favicon' => '/favicon.ico',
            
            // User Interface
            'language' => 'ar',
            'theme' => 'light',
            'timezone' => 'Asia/Riyadh',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            
            // Email Configuration
            'mail_from_name' => 'نظام إدارة الموظفين',
            'mail_from_address' => 'noreply@example.com',
            'mail_signature' => "مع أطيب التحيات،\nفريق إدارة الموظفين",
            

            
            // Security & Access Control
            'session_timeout' => 120,
            'password_min_length' => 8,
            'login_attempts_limit' => 5,
            'lockout_duration' => 15,
            
            // Notifications & Alerts
            'email_notifications' => true,
            'push_notifications' => true,
            'attendance_reminders' => true,
            
            // System & Maintenance
            'maintenance_mode' => false,
            'maintenance_message' => 'النظام تحت الصيانة. يرجى المحاولة مرة أخرى لاحقاً.',
            'debug_mode' => false,
            'log_level' => 'info',
        ];

        foreach ($defaults as $key => $value) {
            Setting::where('key', $key)->update(['value' => $value]);
        }

        // Clear all settings cache
        SettingsHelper::clearCache();

        $this->loadSettings();
        flash()->success(__('settings.reset_to_defaults'));
    }

    public function render()
    {
        $groupedSettings = collect($this->settings)->groupBy('group');
        
        return view('livewire.manage.settings', [
            'groupedSettings' => $groupedSettings,
            'activeGroup' => $this->activeGroup,
        ]);
    }
} 