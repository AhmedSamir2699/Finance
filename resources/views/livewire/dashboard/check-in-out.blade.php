<div wire:poll.15000ms class="w-full px-6 mt-3 md:mt-0 block text-center">
    <div @class([
        'p-6 rounded-lg shadow-lg',
        'bg-green-100 border-green-200' => $checkIn && !$dayEnded,
        'bg-red-100 border-red-200' => !$checkIn && !$dayEnded,
        'bg-gray-100 border-gray-200' => $dayEnded,
    ])>
        <div class="flex justify-between items-center mb-4">
            <div class="text-sm text-gray-600">
                <span class="font-semibold">{{ __('dashboard.timesheet.shift') }}:</span> 
                {{ $user->shift_start ? \Carbon\Carbon::parse($user->shift_start)->format('H:i') : '-' }} - {{ $user->shift_end ? \Carbon\Carbon::parse($user->shift_end)->format('H:i') : '-' }}
            </div>
            <div @class([
                    'px-3 py-1 text-xs font-semibold rounded-full',
                    'bg-green-200 text-green-800' => $checkIn && !$dayEnded,
                    'bg-red-200 text-red-800' => !$checkIn && !$dayEnded,
                    'bg-gray-200 text-gray-800' => $dayEnded,
                ])
            >
                @if ($dayEnded)
                    {{ __('dashboard.timesheet.day_ended') }}
                @elseif($checkIn)
                    {{ __('dashboard.timesheet.checked_in') }}
                @else
                    {{ __('dashboard.timesheet.checked_out') }}
                @endif
            </div>
        </div>

        <div class="text-center my-6">
            <div class="text-4xl font-bold text-gray-800">{{ $timespan }}</div>
            @if($checkInTime)
                <div class="text-gray-500 mt-2">
                    {{ __('dashboard.timesheet.check_in_time') }}: {{ \Carbon\Carbon::parse($checkInTime)->format('H:i:s') }}
                </div>
            @endif
        </div>

        <div class="flex justify-center gap-3 space-x-4">
            <button wire:click="checkInOut"
                @class([
                    'w-full py-3 text-white font-semibold rounded-lg shadow-md transition',
                    'bg-red-500 hover:bg-red-600' => $checkIn,
                    'bg-green-500 hover:bg-green-600' => !$checkIn,
                ])
            >
                @if ($checkIn)
                    {{ __('dashboard.timesheet.check_out') }}
                @else
                    {{ __('dashboard.timesheet.check_in') }}
                @endif
            </button>

            @if (!$checkIn && !$dayEnded && $checkInTime)
                <button wire:click="endDay" class="w-full py-3 bg-yellow-500 text-white font-semibold rounded-lg shadow-md hover:bg-yellow-600 transition">
                    {{ __('dashboard.timesheet.end_day') }}
                </button>
            @endif
        </div>
        @if ($dayEnded)
            <p class="text-gray-500 text-sm mt-4">{{ __('dashboard.timesheet.day_ended_message') }}</p>
        @endif
    </div>
</div>
