<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    
    <div class="container mt-4 mx-auto">
        <div class="bg-white rounded-lg shadow-md border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">
                    {{ __('tasks.executive_plan.create_from_plan') }}
                </h2>
                <p class="text-gray-600 mt-1">
                    {{ __('tasks.executive_plan.description') }}
                </p>
            </div>

            <div class="p-6">
                <livewire:tasks.executive-plan-create />
            </div>
        </div>
    </div>
</x-app-layout> 