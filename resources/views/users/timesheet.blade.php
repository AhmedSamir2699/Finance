<x-app-layout>
    <div class="container mx-auto">
        
        @include('users.info-component')

        <div
            class="grid grid-cols-1 bg-white gap-3 py-8 px-3 rounded-xl shadow-md justify-center items-center text-start mt-8">

            <div class="flex flex-row">
                <span class="font-bold uppercase whitespace-no-wrap">{{ __('users.timesheet.today') }}:</span>
                <span class="cursor-default focus:outline-none px-2">{{ now()->format('Y-m-d') }}</span>
            </div>
            <div class="flex flex-row">
                <span
                    class="font-bold uppercase whitespace-no-wrap">{{ __('users.timesheet.today_total_hours') }}:</span>
                <span class="cursor-default focus:outline-none px-2">

                    {{ $formattedTime }}

                </span>
            </div>

            <table class="table-auto w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2">{{ __('users.timesheet.check_in') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('users.timesheet.check_out') }}</th>
                        <th class="border border-gray-300 px-4 py-2">{{ __('users.timesheet.timespan') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($user->timesheets()->whereDate('start_at',now())->orderBy('start_at','DESC')->get() as $timesheet)
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $timesheet->start_at }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">{{ $timesheet->end_at }}</td>
                            <td class="border border-gray-300 px-4 py-2 text-center">
                                {{ $timesheet->end_at ? Carbon\Carbon::parse($timesheet->start_at)->diff($timesheet->end_at) : Carbon\Carbon::parse($timesheet->start_at)->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="border border-gray-300 px-4 py-2 text-center" colspan="4">
                                {{ __('users.timesheet.empty') }}</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

        </div>
    </div>
</x-app-layout>
