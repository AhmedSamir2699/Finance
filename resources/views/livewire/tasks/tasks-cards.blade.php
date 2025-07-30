<div class="flex flex-wrap -mx-6" wire:poll.15000ms>
    <div class="w-full px-6 mt-3 sm:w-1/2 md:w-1/3 block">
        <a href="#" class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm hover:shadow-md">
            <div class="flex-0 py-2 px-3 bg-orange-600 bg-opacity-75 rounded-full text-center">
                <i class="fas fa-tasks text-white fa-lg"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-semibold text-gray-700">{{ $todaysTasks }}</h4>
                <div class="text-gray-500">{{ __('tasks.todays_tasks') }}</div>
            </div>
        </a>
    </div>

    <div class="w-full px-6 mt-3 sm:w-1/2 md:w-1/3 block">
        <a href="#" class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm hover:shadow-md">
            <div class="flex-0 py-2 px-3 bg-red-600 bg-opacity-75 rounded-full text-center">
                <i class="fas fa-tasks text-white fa-lg"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-semibold text-gray-700">{{ $monthsTasks }}</h4>
                <div class="text-gray-500">{{ __('tasks.months_tasks') }}</div>
            </div>
        </a>
    </div>

    <div class="w-full px-6 mt-3 sm:w-1/2 md:w-1/3 block">
        <a href="#" class="flex items-center px-5 py-6 bg-white rounded-md shadow-sm hover:shadow-md">
            <div class="flex-0 py-2 px-3 bg-primary-600 bg-opacity-75 rounded-full text-center">
                <i class="fas fa-check text-white fa-lg"></i>
            </div>
            <div class="mx-5">
                <h4 class="text-2xl font-semibold text-gray-700">{{ $completedTasks }} من {{ $monthsTasks }}</h4>
                <div class="text-gray-500">{{ __('tasks.months_tasks_completed') }}</div>
            </div>
        </a>
    </div>
</div>
