

<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-semibold text-gray-700 leading-tight mb-6">{{ __('manage.attendance_settings.headline') }}</h2>
            @livewire('manage.attendance-settings')
        </div>
    </div>
</x-app-layout>
