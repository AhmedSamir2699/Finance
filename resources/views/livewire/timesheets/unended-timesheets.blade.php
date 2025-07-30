<div>
    <div class="container mt-4 mx-auto">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">
                            {{ __('timesheets.unended.fix_title') }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ __('timesheets.unended.description') }}
                        </p>
                    </div>
                </div>



                    @if($isLoading)
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-4 flex items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            جاري المعالجة...
                        </div>
                    @endif

                    @if(!empty($timesheets))
                        <div class="mb-4">
                            <div class="text-lg font-semibold text-gray-700 mb-4">
                                {{ __('timesheets.unended.total_unended') }}: {{ $totalCount }}
                            </div>
                            <div class="flex space-x-2 gap-3">
                                <button type="button" 
                                        wire:click="selectAll" 
                                        class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-gray-500 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-gray-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        @if($selectedCount == $totalCount || $isLoading) disabled @endif>
                                    {{ __('timesheets.unended.select_all') }}
                                </button>
                                <button type="button" 
                                        wire:click="deselectAll" 
                                        class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-gray-400 text-base font-medium text-white hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-gray-400 disabled:opacity-50 disabled:cursor-not-allowed"
                                        @if($selectedCount == 0 || $isLoading) disabled @endif>
                                    {{ __('timesheets.unended.deselect_all') }}
                                </button>
                                <button type="button" 
                                        wire:click="saveSelectedTimesheets" 
                                        class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-green-500 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                        @if($selectedCount == 0 || $isLoading) disabled @endif>
                                    {{ __('timesheets.unended.fix_selected') }} ({{ $selectedCount }})
                                </button>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full leading-normal">
                                <thead>
                                    <tr>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            {{ __('timesheets.unended.select') }}
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            {{ __('timesheets.unended.employee') }}
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            {{ __('timesheets.unended.department') }}
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            {{ __('timesheets.unended.start_time') }}
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            {{ __('timesheets.unended.custom_end_time') }}
                                        </th>
                                        <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                            {{ __('timesheets.unended.actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                                                    <tbody>
                                        @foreach($timesheets as $timesheet)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <input type="checkbox" 
                                                           wire:model.live="selectedTimesheets" 
                                                           value="{{ $timesheet['id'] }}"
                                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 disabled:opacity-50 disabled:cursor-not-allowed"
                                                           @if($isLoading) disabled @endif>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        {{ $timesheet['user']['name'] }}
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        {{ $timesheet['user']['department']['name'] ?? __('timesheets.unended.no_department') }}
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <p class="text-gray-900 whitespace-no-wrap">
                                                        {{ \Carbon\Carbon::parse($timesheet['start_at'])->format('Y-m-d H:i:s') }}
                                                    </p>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <input type="datetime-local" 
                                                           wire:model="customEndTimes.{{ $timesheet['id'] }}"
                                                           value="{{ $suggestedEndTimes[$timesheet['id']] ?? '' }}"
                                                           class="border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                                           min="{{ \Carbon\Carbon::parse($timesheet['start_at'])->format('Y-m-d\TH:i') }}"
                                                           max="{{ now()->format('Y-m-d\TH:i') }}"
                                                           @if($isLoading) disabled @endif>
                                                </td>
                                                <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                                    <div class="flex space-x-1 gap-3">
                                                        <button type="button" 
                                                                wire:click="saveSingleTimesheet({{ $timesheet['id'] }})"
                                                                class="bg-green-500 hover:bg-green-700 text-white text-xs px-2 py-1 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                                                @if($isLoading || $processingTimesheetId == $timesheet['id']) disabled @endif>
                                                            @if($processingTimesheetId == $timesheet['id'])
                                                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            @else
                                                                {{ __('timesheets.unended.save_single') }}
                                                            @endif
                                                        </button>
                                                        <button type="button" 
                                                                wire:click="deleteSingleTimesheet({{ $timesheet['id'] }})"
                                                                class="bg-red-500 hover:bg-red-700 text-white text-xs px-2 py-1 rounded disabled:opacity-50 disabled:cursor-not-allowed"
                                                                @if($isLoading || $processingTimesheetId == $timesheet['id']) disabled @endif
                                                                onclick="return confirm('{{ __('timesheets.unended.confirm_delete') }}')">
                                                            @if($processingTimesheetId == $timesheet['id'])
                                                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                </svg>
                                                            @else
                                                                {{ __('timesheets.unended.delete_single') }}
                                                            @endif
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                            </table>
                        </div>


                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">{{ __('timesheets.unended.no_unended_timesheets') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 