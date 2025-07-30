<div>
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">{{ __('users.timesheet.timesheet') }} - {{ $date }}</h3>
        <div>
            <button wire:click="previousDay" class="px-4 py-2 bg-gray-200 rounded-lg">&lt;</button>
            <button wire:click="nextDay" class="px-4 py-2 bg-gray-200 rounded-lg">&gt;</button>
        </div>
    </div>

    <table class="min-w-full leading-normal">
        <thead>
            <tr>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    {{ __('timesheets.show.table.start_at') }}
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    {{ __('timesheets.show.table.end_at') }}
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    {{ __('timesheets.show.table.total_work') }}
                </th>
                <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                    {{ __('timesheets.show.table.total_break') }}
                </th>
            </tr>
        </thead>
        <tbody>
            @if ($timesheets->isEmpty())
                <tr>
                    <td colspan="4" class="px-5 py-5 border-b border-gray-200 bg-white text-sm text-center">
                        {{ __('timesheets.show.no_records') }}
                    </td>
                </tr>
            @else
                <tr>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">
                            {{ $first_start_at ?? __('timesheets.show.absent') }}
                        </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">
                            @if ($last_end_at)
                                {{ $last_end_at }}
                            @elseif($is_last_shift_active)
                                <a href="{{ route('timesheets.endshift', $last_timesheet_id) }}"
                                    class="text-blue-500 hover:text-blue-700">{{ __('timesheets.show.end') }}</a>
                            @endif
                        </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">
                            {{ $formatted_present_time }}
                        </p>
                    </td>
                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                        <p class="text-gray-900 whitespace-no-wrap">
                            {{ $formatted_break_time }}
                        </p>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
