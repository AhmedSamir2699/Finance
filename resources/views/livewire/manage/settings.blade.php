<div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-lg font-semibold text-gray-700">{{ __('settings.configure_system_preferences') }}</h3>
            <div class="flex space-x-3">
                <button wire:click="saveAll" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                    <i class="fas fa-save me-2"></i>{{ __('settings.save_all') }}
                </button>
            </div>
        </div>

        <!-- Settings Groups Tabs -->
        <div class="border-b border-gray-200 mb-6">
            <nav class="-mb-px flex gap-3">
                @foreach($groups as $group)
                    <button wire:click="setActiveGroup('{{ $group }}')" 
                            class="py-2 px-3 border-b-2 font-medium text-sm {{ $activeGroup === $group ? 'border-primary-500 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ __("settings.groups.{$group}") }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Settings Content -->
        <div class="space-y-6">
            @foreach($groupedSettings as $group => $settings)
                @if($group === $activeGroup)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($settings as $setting)
                            <div class="bg-gray-50 rounded-lg p-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __("settings.labels.{$setting['key']}") }}
                                </label>
                                
                                
                                @if(isset($setting['key']) && $this->hasHint($setting['key']))
                                    <div class="mb-3 p-2 bg-blue-50 border border-blue-200 rounded-md">
                                        <div class="flex items-start">
                                            <i class="fas fa-info-circle text-blue-500 mt-0.5 mx-2"></i>
                                            <p class="text-xs text-blue-700">{{ __('settings.hints.' . $setting['key']) }}</p>
                                        </div>
                                    </div>
                                @endif

                                @switch($setting['type'])
                                    @case('boolean')
                                        <div class="flex items-center">
                                            <input type="checkbox" 
                                                   wire:change="updateSetting('{{ $setting['key'] }}', $event.target.checked)"
                                                   @checked($setting['value'])
                                                   class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                            <label class="ms-2 text-sm text-gray-700">
                                                {{ $setting['value'] ? __('settings.enabled') : __('settings.disabled') }}
                                            </label>
                                        </div>
                                        @break

                                    @case('integer')
                                        <input type="number" 
                                               wire:change="updateSetting('{{ $setting['key'] }}', $event.target.value)"
                                               value="{{ $setting['value'] }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        @break
                                        @case('double')
                                        <input type="number" 
                                               wire:change="updateSetting('{{ $setting['key'] }}', $event.target.value)"
                                               value="{{ $setting['value'] }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                        @break

                                    @case('text')
                                        <textarea wire:change="updateSetting('{{ $setting['key'] }}', $event.target.value)"
                                                  rows="3"
                                                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">{{ $setting['value'] }}</textarea>
                                        @break

                                    @case('select')
                                        <select wire:change="updateSetting('{{ $setting['key'] }}', $event.target.value)"
                                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                            @if($setting['key'] === 'language')
                                                <option value="ar" {{ $setting['value'] === 'ar' ? 'selected' : '' }}>{{ __('common.arabic') }}</option>
                                                <option value="en" {{ $setting['value'] === 'en' ? 'selected' : '' }}>{{ __('common.english') }}</option>
                                            @elseif($setting['key'] === 'theme')
                                                <option value="light" {{ $setting['value'] === 'light' ? 'selected' : '' }}>{{ __('settings.theme_light') }}</option>
                                                <option value="dark" {{ $setting['value'] === 'dark' ? 'selected' : '' }}>{{ __('settings.theme_dark') }}</option>
                                            @elseif($setting['key'] === 'timezone')
                                                <option value="Asia/Riyadh" {{ $setting['value'] === 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh</option>
                                                <option value="UTC" {{ $setting['value'] === 'UTC' ? 'selected' : '' }}>UTC</option>
                                                <option value="Europe/London" {{ $setting['value'] === 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
                                            @elseif($setting['key'] === 'log_level')
                                                <option value="debug" {{ $setting['value'] === 'debug' ? 'selected' : '' }}>{{ __('settings.log_levels.debug') }}</option>
                                                <option value="info" {{ $setting['value'] === 'info' ? 'selected' : '' }}>{{ __('settings.log_levels.info') }}</option>
                                                <option value="warning" {{ $setting['value'] === 'warning' ? 'selected' : '' }}>{{ __('settings.log_levels.warning') }}</option>
                                                <option value="error" {{ $setting['value'] === 'error' ? 'selected' : '' }}>{{ __('settings.log_levels.error') }}</option>
                                            @endif
                                        </select>
                                        @break

                                    @case('image')
                                        <div class="space-y-3">
                                            <!-- Image Upload Area -->
                                            <div class="relative w-32 h-32 border-2 border-dashed border-gray-300 rounded-lg overflow-hidden hover:border-primary-400 transition-colors cursor-pointer">
                                                
                                                <!-- Hidden File Input -->
                                                <input type="file" 
                                                       wire:model="uploadedImages.{{ $setting['key'] }}"
                                                       accept="image/*"
                                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                                                
                                                @if($setting['value'] && $setting['value'] !== '')
                                                    <!-- Existing Image -->
                                                    <img src="{{ asset('storage/' . $setting['value']) }}" 
                                                         alt="{{ $setting['key'] }}" 
                                                         class="w-full h-full object-cover pointer-events-none">
                                                    
                                                    <!-- Overlay on Hover -->
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-50 transition-all duration-200 flex items-center justify-center pointer-events-none">
                                                        <div class="text-white opacity-0 hover:opacity-100 transition-opacity duration-200 text-center">
                                                            <i class="fas fa-camera text-2xl mb-2"></i>
                                                            <p class="text-xs">{{ __('settings.click_to_change') }}</p>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Placeholder -->
                                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 pointer-events-none">
                                                        <i class="fas fa-image text-3xl mb-2"></i>
                                                        <p class="text-xs text-center">{{ __('settings.click_to_select') }}</p>
                                                    </div>
                                                @endif
                                                
                                                <!-- Remove Button (if image exists) -->
                                                @if($setting['value'] && $setting['value'] !== '')
                                                    <button type="button" 
                                                            wire:click="removeImage('{{ $setting['key'] }}')"
                                                            class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs transition-colors z-10">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @endif
                                            </div>
                                            
                                            <!-- Upload Progress -->
                                            <div wire:loading wire:target="uploadedImages.{{ $setting['key'] }}" class="text-sm text-gray-500">
                                                <i class="fas fa-spinner fa-spin me-2"></i>{{ __('settings.uploading') }}
                                            </div>
                                            
                                            <!-- File Requirements -->
                                            <div class="text-xs text-gray-500">
                                                <p>{{ __('settings.image_requirements') }}</p>
                                            </div>
                                        </div>
                                        @break

                                    @default
                                        <input type="text" 
                                               wire:change="updateSetting('{{ $setting['key'] }}', $event.target.value)"
                                               value="{{ $setting['value'] }}"
                                               class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                @endswitch

                                @if($setting['is_public'])
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                                        {{ __('settings.public') }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div> 