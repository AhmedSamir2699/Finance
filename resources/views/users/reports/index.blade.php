<x-app-layout>
    
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="container px-6 py-8 mx-auto">
        <div class="mb-6">
            <a href="{{ route('users.summary.general.index') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('users.summary.general.title') }}
            </a>
        </div>
        
        <livewire:users.reports.users-list />

    </div>

</x-app-layout>
