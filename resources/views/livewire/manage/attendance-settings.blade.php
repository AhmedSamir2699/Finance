<div>
    <!-- Tab Navigation -->
    <div class="flex space-x-4 border-b border-gray-200 mb-6">
        <button wire:click="setScope('global')" 
                class="py-3 px-4 font-medium text-sm {{ $scope_type === 'global' ? 'border-b-2 border-primary-500 text-primary-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ __('manage.attendance_settings.global') }}
        </button>
        <button wire:click="setScope('department')" 
                class="py-3 px-4 font-medium text-sm {{ $scope_type === 'department' ? 'border-b-2 border-primary-500 text-primary-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ __('manage.attendance_settings.department') }}
        </button>
        <button wire:click="setScope('user')" 
                class="py-3 px-4 font-medium text-sm {{ $scope_type === 'user' ? 'border-b-2 border-primary-500 text-primary-600' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
            {{ __('manage.attendance_settings.user') }}
        </button>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <!-- Settings Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('manage.attendance_settings.configure_settings') }}</h3>
        
        <form wire:submit.prevent="save" class="space-y-6">
            @if($scope_type === 'department')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('manage.attendance_settings.department') }}</label>
                    <select wire:model="scope_id" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        <option value="">{{ __('manage.attendance_settings.select_department') }}</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}">{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif($scope_type === 'user')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('manage.attendance_settings.user') }}</label>
                    <select wire:model="scope_id" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        <option value="">{{ __('manage.attendance_settings.select_user') }}</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('manage.attendance_settings.late_arrival_tolerance') }}</label>
                    <input type="number" wire:model="late_arrival_tolerance" min="0" 
                           class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                    <p class="text-xs text-gray-500 mt-1">{{ __('manage.attendance_settings.late_arrival_hint') }}</p>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('manage.attendance_settings.early_leave_tolerance') }}</label>
                    <input type="number" wire:model="early_leave_tolerance" min="0" 
                           class="form-input w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                    <p class="text-xs text-gray-500 mt-1">{{ __('manage.attendance_settings.early_leave_hint') }}</p>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2">
                    {{ __('manage.attendance_settings.save') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Current Settings -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-700 mb-4">{{ __('manage.attendance_settings.current_settings') }}</h3>
        
        @if(count($settings) > 0)
            <div class="space-y-3">
                @foreach($settings as $setting)
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $setting->scope_type === 'global' ? 'bg-blue-100 text-blue-800' : 
                                       ($setting->scope_type === 'department' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                    {{ __("manage.attendance_settings.{$setting->scope_type}") }}
                                </span>
                                @if($setting->scope_id)
                                    <span class="text-sm text-gray-600">
                                        ({{ $setting->scope_type === 'department' ? \App\Models\Department::find($setting->scope_id)->name : \App\Models\User::find($setting->scope_id)->name }})
                                    </span>
                                @endif
                            </div>
                            <div class="mt-2 text-sm text-gray-600">
                                <span class="font-medium">{{ __('manage.attendance_settings.late') }}:</span> {{ $setting->late_arrival_tolerance }} {{ __('manage.attendance_settings.min') }} | 
                                <span class="font-medium">{{ __('manage.attendance_settings.early') }}:</span> {{ $setting->early_leave_tolerance }} {{ __('manage.attendance_settings.min') }}
                            </div>
                        </div>
                        @if($setting->scope_type !== 'global')
                            <button wire:click="delete({{ $setting->id }})" 
                                    wire:confirm="{{ __('manage.attendance_settings.delete_confirm') }}"
                                    class="text-red-600 hover:text-red-800 p-2 rounded-md hover:bg-red-50">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <i class="fas fa-cog text-4xl mb-4"></i>
                <p>{{ __('manage.attendance_settings.no_settings') }}</p>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('toastr', event => {
            toastr[event.type](event.message);
        });
    });
</script>
@endpush
