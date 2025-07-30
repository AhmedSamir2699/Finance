<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="w-full flex justify-between items-center mb-2">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />


        @if (auth()->user()->can('timesheet.export'))
            <div class="relative inline-block text-left" x-data="{ ExportModal: false }">
                <div>
                    <button type="button"
                        class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-primary-500 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-primary-500"
                        id="options-menu" aria-haspopup="true" aria-expanded="true"
                        x-on:click="ExportModal = !ExportModal">

                        <i class="fa fa-download m-1 ml-2"></i>
                        {{ __('timesheets.show.export') }}
                    </button>
                </div>

                <x-modal name="ExportModal" class="hidden">
                    <form action="{{ route('timesheets.export') }}" method="post" class="grid grid-cols-2 gap-4 p-4">
                        @csrf
                        <div class="flex flex-col items-center">
                            <label for="date"
                                class="block text-sm font-medium text-slate-700">{{ __('timesheets.show.export_date') }}</label>
                            <select name="date" id="date"
                                class="bg-white border border-gray-300 p-2 rounded-lg px-8 text-center">
                                <option value="{{ Carbon\Carbon::parse($date)->format('Y-m-d') }}">
                                    {{ __('timesheets.show.export_today') }}
                                </option>
                                <option value="{{ Carbon\Carbon::parse($date)->format('Y-m') }}">
                                    {{ __('timesheets.show.export_month') }}
                                </option>
                            </select>
                        </div>
                        <div class="flex flex-col items-center">
                            <label for="date"
                                class="block text-sm font-medium text-slate-700">{{ __('timesheets.show.department') }}</label>
                            <select name="department_id" id="department"
                                class="bg-white border border-gray-300 p-2 rounded-lg px-8 text-center">
                                <option value="all">{{ __('timesheets.show.all') }}</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col items-center col-span-2">
                            <button
                                class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
                                type="submit">
                                {{ __('timesheets.show.export') }}
                            </button>
                        </div>
                    </form>
                </x-modal>
            </div>
        @endif

        @if (auth()->user()->can('timesheet.edit'))
            <a href="{{ route('timesheets.unended') }}" 
               class="inline-flex justify-center flex-0 rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-yellow-500 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-yellow-500">
                <i class="fa fa-exclamation-triangle m-1 ml-2"></i>
                {{ __('timesheets.unended.title') }}
            </a>
        @endif

        <select class="bg-white border border-gray-300 p-2 rounded-lg px-8 text-center"
            onchange="window.location.href = '?filter=' + this.value">
            <option value="">{{ __('timesheets.show.filter') }}</option>
            <option value="present" {{ request()->filter == 'present' ? 'selected' : '' }}>
                {{ __('timesheets.show.present') }}</option>
            <option value="absent" {{ request()->filter == 'absent' ? 'selected' : '' }}>
                {{ __('timesheets.show.absent') }}</option>
        </select>

        <input type="date" class="bg-white border border-gray-300 p-2 rounded-lg" wire:model="date" id="date"
            onchange="window.location.href = '{{ route('timesheets.show') }}/' + this.value"
            value="{{ Carbon\Carbon::parse($date)->format('Y-m-d') }}">

    </div>


    <div class="container mt-4 mx-auto">

        <table class="min-w-full leading-normal">
            <thead>
                <tr>
                    <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 w-12"></th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('timesheets.show.table.employee') }}</th>

                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('timesheets.show.table.start_at') }}</th>

                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('timesheets.show.table.end_at') }}</th>
                    <th
                        class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                        {{ __('timesheets.show.table.total_overall') }}</th>
                </tr>
            </thead>
            @foreach ($users as $user)
                <tbody x-data="{ expanded: false }">
                    <tr @click="expanded = !expanded" class="cursor-pointer hover:bg-gray-50">
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{'rotate-180': expanded}" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $user->name }}
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $user->first_start_at ?? __('timesheets.show.absent') }}
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                @if ($user->last_end_at)
                                    {{ $user->last_end_at }}
                                @elseif($user->is_last_shift_active)
                                    <a href="{{ route('timesheets.endshift', $user->last_timesheet_id) }}"
                                        class="text-blue-500 hover:text-blue-700">{{ __('timesheets.show.end') }}</a>
                                @endif
                            </p>
                        </td>
                        <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                            <p class="text-gray-900 whitespace-no-wrap">
                                {{ $user->formatted_total_time }}
                            </p>
                        </td>
                    </tr>
                    <tr x-show="expanded" x-cloak>
                        <td colspan="5" class="p-5 bg-gray-50 border-b border-gray-200">
                            <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4 text-center">
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">{{ __('timesheets.show.table.shift_start') }}</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->shift_start ? \Carbon\Carbon::parse($user->shift_start)->format('H:i') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">{{ __('timesheets.show.table.shift_end') }}</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->shift_end ? \Carbon\Carbon::parse($user->shift_end)->format('H:i') : '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">{{ __('timesheets.show.table.total_work') }}</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->formatted_present_time }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">{{ __('timesheets.show.table.total_break') }}</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->formatted_break_time }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">{{ __('timesheets.show.table.overtime') }}</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->formatted_overtime }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">{{ __('timesheets.show.table.late_arrival') }}</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->formatted_late_arrival }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 uppercase">{{ __('timesheets.show.table.early_leave') }}</p>
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->formatted_early_leave }}</p>
                                </div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            @endforeach
        </table>

    </div>
    <script>
        flatpickr("#date", {
            dateFormat: "Y-m-d" // Enforce YYYY-MM-DD format
        });
    </script>
</x-app-layout>
