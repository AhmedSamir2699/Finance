<x-app-layout>
    <div class="w-full flex justify-between items-center mb-2">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
        
        <a href="{{ route('timesheets.show') }}" 
           class="inline-flex justify-center flex-0 rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-gray-500 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-gray-500">
            <i class="fa fa-arrow-left m-1 ml-2"></i>
            {{ __('timesheets.unended.back_to_timesheets') }}
        </a>
    </div>

    <livewire:timesheets.unended-timesheets />
</x-app-layout> 