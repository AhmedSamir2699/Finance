<div>
    <!-- Filters -->
    <div class="mb-6 bg-gray-50 p-4 rounded-lg">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('evaluate.history.search') }}
                </label>
                <input type="text" 
                       id="search" 
                       wire:model.live="search" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500"
                       placeholder="{{ __('evaluate.history.search_placeholder') }}">
            </div>

            <!-- Date Filter -->
            <div>
                <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('evaluate.history.date_filter') }}
                </label>
                <input type="date" 
                       id="dateFilter" 
                       wire:model.live="dateFilter" 
                       class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
            </div>

            <!-- Criteria Filter -->
            <div>
                <label for="criteriaFilter" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('evaluate.history.criteria_filter') }}
                </label>
                <select id="criteriaFilter" 
                        wire:model.live="criteriaFilter" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">{{ __('evaluate.history.all_criteria') }}</option>
                    @foreach($criteria as $criterion)
                        <option value="{{ $criterion->id }}">{{ $criterion->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- User Filter -->
            <div>
                <label for="userFilter" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ __('evaluate.history.user_filter') }}
                </label>
                <select id="userFilter" 
                        wire:model.live="userFilter" 
                        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500">
                    <option value="">{{ __('evaluate.history.all_users') }}</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Clear Filters Button -->
        <div class="mt-4">
            <button wire:click="clearFilters" 
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                {{ __('evaluate.history.clear_filters') }}
            </button>
        </div>
    </div>

    <!-- Results -->
    @if($evaluations->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('evaluate.history.table.user') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('evaluate.history.table.criteria') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('evaluate.history.table.score') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('evaluate.history.table.evaluated_at') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('evaluate.history.table.created_at') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($evaluations as $evaluation)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $evaluation->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $evaluation->criteria->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $evaluation->score }}/{{ $evaluation->criteria->max_value }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $evaluation->evaluated_at->format('Y-m-d') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $evaluation->created_at->format('Y-m-d H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $evaluations->links() }}
        </div>
    @else
        <div class="text-center py-8">
            <p class="text-gray-500">{{ __('evaluate.history.no_evaluations_found') }}</p>
        </div>
    @endif
</div> 