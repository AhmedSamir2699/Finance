<div>
    <!-- Filters -->
    <div class="bg-gray-50 rounded-lg p-4 mb-6">
        <div class="flex flex-row gap-3">
            <!-- Date Range Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('tasks.executive_plan.date_range') }}
                </label>
                <div class="flex gap-2">
                    <input type="date" wire:model.live="fromDate" 
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <input type="date" wire:model.live="toDate" 
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                </div>
            </div>

            <!-- Department Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('tasks.executive_plan.filter_department') }}
                </label>
                <select wire:model.live="selectedDepartment" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">{{ __('tasks.executive_plan.all_departments') }}</option>
                    @foreach($departments as $department)
                        <option value="{{ $department->id }}">{{ $department->name }}</option>
                    @endforeach
                </select>
                @if($departments->count() === 0)
                    <p class="text-xs text-gray-500 mt-1">
                        {{ __('tasks.executive_plan.no_departments_available') }}
                    </p>
                @endif
            </div>

            <!-- User Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('tasks.executive_plan.filter_user') }}
                </label>
                <select wire:model.live="selectedUser" 
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                    <option value="">{{ __('tasks.executive_plan.all_users') }}</option>
                    @foreach($this->getAssignableUsers() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('tasks.search.label') }}
                </label>
                <input type="text" wire:model.live="search" 
                       placeholder="{{ __('tasks.search.placeholder') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
            </div>
        </div>
    </div>

    <!-- Results -->
    @if($usersWithCells->count() > 0)
        <div class="bg-white rounded-lg shadow-md border overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">
                    {{ __('tasks.executive_plan.available_cells') }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">
                    {{ __('tasks.executive_plan.select_cell_to_create_task') }}
                </p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('tasks.executive_plan.user') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('tasks.executive_plan.cell_title') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('tasks.executive_plan.date') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('tasks.executive_plan.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($usersWithCells as $userData)
                            @foreach($userData['cells'] as $cell)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $userData['user']->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $userData['user']->department->name ?? __('tasks.executive_plan.no_department') }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ $cell->value }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            {{ \Carbon\Carbon::parse($cell->date)->format('Y-m-d') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button wire:click="createTaskFromCell({{ $cell->id }})"
                                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            {{ __('tasks.executive_plan.create_task') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($usersWithCells->hasPages())
                <div class="px-6 py-3 border-t border-gray-200">
                    {{ $usersWithCells->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="bg-white rounded-lg shadow-md border p-6 text-center">
            <div class="text-gray-500">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    {{ __('tasks.executive_plan.no_cells_found') }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ __('tasks.executive_plan.no_cells_description') }}
                </p>
            </div>
        </div>
    @endif
</div> 