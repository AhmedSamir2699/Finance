<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    
    <div class="container px-6 py-8 mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-700 leading-tight">{{ __('settings.edit_settings') }}</h2>
            <p class="text-gray-600 mt-2">{{ __('settings.edit_description') }}</p>
        </div>

        @livewire('manage.settings')
    </div>
</x-app-layout> 