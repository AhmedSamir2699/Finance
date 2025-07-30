<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <div class="flex gap-2">
        <form method="GET" action="{{ route('users.reports.show', ['user' => $user->id]) }}"
            class="d-flex align-items-center gap-2 my-6">
            <label for="start_date" class="mb-0">من:</label>
            <input type="date" id="start_date" name="start_date"
                value="{{ request()->get('start_date', $start->format('Y-m-d')) }}"
                class="rounded-md border-gray-300 focus:border-primary-base focus:ring-primary-base" required>

            <label for="end_date" class="mb-0">إلى:</label>
            <input type="date" id="end_date" name="end_date"
                value="{{ request()->get('end_date', $end->format('Y-m-d')) }}"
                class="rounded-md border-gray-300 focus:border-primary-base focus:ring-primary-base" required>

            <button type="submit"
                class="bg-primary-base py-1 px-3 rounded text-white">تصفية</button>
        </form>


        @php
            $startDate = request()->get('start_date');
            $endDate = request()->get('end_date');
            $hasDateRange = $startDate && $endDate;
        @endphp
        <div class="flex items-center gap-2 ">
        @if($hasDateRange)
            <a href="{{ route('users.reports.export', [$user, 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                class="bg-primary-base py-1 px-3 rounded text-white">
                تصدير
            </a>
        @else
            <button disabled
                class="py-1 px-3 bg-gray-400 text-white rounded-md cursor-not-allowed opacity-50">
                تصدير
            </button>
        @endif
        </div>
    </div>
    @if (request()->has('start_date') && request()->has('end_date'))

        <div class="w-[90%] mx-auto">
            <div class="flex flex-col my-5">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">
                    @php
                        $startDate = \Carbon\Carbon::parse(request()->get('start_date'));
                        $endDate = \Carbon\Carbon::parse(request()->get('end_date'));
                        $daysCount = $startDate->diffInDays($endDate) + 1; // Include both start and end dates
                        $daysCount = $daysCount < 1 ? 1 : $daysCount;
                    @endphp
                    {{ __('users.report.title', ['user' => $user->name, 'days' => $daysCount]) }}

                </h3>

 
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-{{ count($data['evaluationCriteria'])+2 }} mb-6">

                    <div class="bg-white shadow-md rounded-lg p-4 col-span-{{ count($data['evaluationCriteria'])-1 }}">
                        <h5 class="text-lg font-medium text-gray-700">{{ __('users.report.final_score') }}
                        </h5>
                        <p class="text-center text-2xl font-bold text-gray-900 mt-2">
                            <span>{{ $data['data']['finalScore'] }}%</span>
                        </p>
                    </div>
                    <!-- Attendance Card -->
                    <div class="bg-white shadow-md rounded-lg p-4">
                        <h5 class="text-lg font-medium text-gray-700">{{ __('users.report.attendance.present') }}
                        </h5>
                        <p class="text-center text-2xl font-bold text-gray-900 mt-2">
                            <span>{{ $data['data']['attendance']['present'] }}</span>
                            <span
                                title="{{ number_format(intval($data['data']['attendance']['expected_minutes']) / 60, 2) }} / {{ number_format(intval($data['data']['attendance']['present_minutes']) / 60, 2) }} "
                                class="text-sm font-normal text-gray-600">[{{ $data['data']['attendance']['present_minutes'] . '/' . $data['data']['attendance']['expected_minutes'] }}]</span>
                        </p>
                    </div>
                <!-- Evaluation Section -->
                @if(count($data['evaluationCriteria']) > 0)
                    <!-- Evaluation Summary Cards -->
                        @foreach($data['evaluationCriteria'] as $criteria)
                            <div class="bg-white shadow-md rounded-lg p-4">
                                <h5 class="text-lg font-medium text-gray-700 mb-2">{{ $criteria['name'] }}</h5>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">{{ __('users.report.evaluation.average_score') }}:</span>
                                        <span class="text-sm font-semibold text-primary-600">{{ $criteria['average_score'] }}/{{ $criteria['max_value'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">{{ __('users.report.evaluation.percentage') }}:</span>
                                        <span class="text-sm font-semibold text-primary-600">{{ $criteria['percentage'] }}%</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-500">{{ __('users.report.evaluation.records') }}:</span>
                                        <span class="text-sm text-gray-600">{{ $criteria['records_count'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                @endif

                </div>

                <!-- Productivity Section -->
                <h4 class="text-xl font-semibold text-gray-900 mt-8 mb-4">{{ __('users.report.productivity') }}
                </h4>
                <div class="bg-white shadow-md rounded-lg p-4">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.report.tasks.required_time') }}</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.report.tasks.actual_time') }}</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.report.tasks.time_quality') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">
                                    {{ $data['data']['tasks']['requiredTime'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $data['data']['tasks']['actualTime'] }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ $data['data']['tasks']['timeQuality'] }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <hr class="mt-10 mb-5 border-gray-300">

                <!-- Tasks Sections -->
                @foreach (['scheduled', 'unscheduled', 'training', 'continous'] as $taskType)
                    <h4 class="text-xl font-semibold text-gray-900 mt-8 mb-4">
                        {{ __('users.report.tasks.' . $taskType) }}</h4>
                    <div class="bg-white shadow-md rounded-lg p-4">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-100">
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                        {{ __('users.report.tasks.name') }}</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                        {{ __('users.report.tasks.estimated_time') }}</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                        {{ __('users.report.tasks.actual_time') }}</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                        {{ __('users.report.tasks.time_quality') }}</th>
                                    <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                        {{ __('users.report.tasks.quality_percentage') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($data['taskCategories'][$taskType] as $task)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $task->title }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $task->estimated_time }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $task->actual_time }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">
                                            {{ $task->actual_time > 0 ? number_format(($task->estimated_time / $task->actual_time) * 100, 2) : 0 }}%
                                        </td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $task->quality_percentage }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td class="px-4 py-3 text-sm text-gray-700 text-center" colspan="5">
                                            {{ __('users.report.tasks.no_tasks') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach

                <hr class="mt-10 mb-5 border-gray-300">

                <!-- Attendance Section -->
                @php
                    // Calculate totals from detailed attendance data
                    $totalMinutes = 0;
                    $regularMinutes = 0;
                    $overtimeMinutes = 0;
                    $breakMinutes = 0;
                    
                    foreach ($data['detailedAttendance'] as $attendanceData) {
                        $totalMinutes += $attendanceData['total_minutes'];
                        $regularMinutes += $attendanceData['regular_minutes'];
                        $overtimeMinutes += $attendanceData['overtime_minutes'];
                        $breakMinutes += $attendanceData['break_minutes'];
                    }

                    $hours = floor($totalMinutes / 60);
                    $minutes = $totalMinutes % 60;

                    $hoursText = $hours === 1 ? 'ساعة' : ($hours <= 10 ? 'ساعات' : 'ساعة');
                    $minutesText = $minutes === 1 ? 'دقيقة' : ($minutes <= 10 ? 'دقائق' : 'دقيقة');
                @endphp
                <h4 class="text-xl font-semibold text-gray-900 mt-8 mb-4">{{ __('users.report.attendance.title') }}
                    -
                    {{ $hours . ' ' . $hoursText . ' و ' . $minutes . ' ' . $minutesText }}</h4>

                <!-- Detailed Attendance Breakdown -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h5 class="text-lg font-medium text-gray-900 mb-3">{{ __('users.report.attendance.breakdown') }}</h5>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="text-sm text-gray-600">{{ __('users.report.attendance.regular_work') }}</div>
                            <div class="text-lg font-semibold text-blue-600">
                                @php
                                    $regularHours = floor($regularMinutes / 60);
                                    $regularMins = $regularMinutes % 60;
                                @endphp
                                {{ $regularHours }}س {{ $regularMins }}د
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="text-sm text-gray-600">{{ __('users.report.attendance.overtime') }}</div>
                            <div class="text-lg font-semibold text-orange-600">
                                @php
                                    $overtimeHours = floor($overtimeMinutes / 60);
                                    $overtimeMins = $overtimeMinutes % 60;
                                @endphp
                                {{ $overtimeHours }}س {{ $overtimeMins }}د
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-3 shadow-sm">
                            <div class="text-sm text-gray-600">{{ __('users.report.attendance.break_time') }}</div>
                            <div class="text-lg font-semibold text-green-600">
                                @php
                                    $breakHours = floor($breakMinutes / 60);
                                    $breakMins = $breakMinutes % 60;
                                @endphp
                                {{ $breakHours }}س {{ $breakMins }}د
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow-md rounded-lg p-4">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.report.attendance.date') }}</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.report.attendance.regular_work') }}</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.report.attendance.overtime') }}</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.report.attendance.break_time') }}</th>
                                <th class="px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.report.attendance.total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data['detailedAttendance'] as $date => $attendanceData)
                                <tr class="border-b border-gray-200 hover:bg-gray-50 cursor-pointer" onclick="toggleDetails('{{ $date }}')">
                                    <td class="px-4 py-3 text-sm text-gray-700 font-medium">
                                        <div class="flex items-center">
                                            <span class="mr-2">{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</span>
                                            <svg class="w-4 h-4 transform transition-transform duration-200" id="icon-{{ $date }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $attendanceData['regular_hours'] }}س {{ $attendanceData['regular_mins'] }}د
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $attendanceData['overtime_hours'] }}س {{ $attendanceData['overtime_mins'] }}د
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        {{ $attendanceData['break_hours'] }}س {{ $attendanceData['break_mins'] }}د
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 font-semibold">
                                        {{ $attendanceData['total_sum_hours'] }}س {{ $attendanceData['total_sum_mins'] }}د
                                    </td>
                                </tr>
                                <!-- Expandable Details Row -->
                                <tr id="details-{{ $date }}" class="hidden bg-gray-50">
                                    <td colspan="5" class="px-4 py-3">
                                        <div class="bg-white rounded-lg p-4 shadow-sm">
                                            <h6 class="text-sm font-medium text-gray-900 mb-3">{{ __('users.report.attendance.details') }}</h6>
                                            @if($attendanceData['has_timesheets'])
                                                <table class="w-full text-sm">
                                                    <thead>
                                                        <tr class="border-b border-gray-200">
                                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600">{{ __('users.timesheet.check_in') }}</th>
                                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600">{{ __('users.timesheet.check_out') }}</th>
                                                            <th class="px-3 py-2 text-right text-xs font-medium text-gray-600">{{ __('users.timesheet.timespan') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($attendanceData['timesheets'] as $timesheet)
                                                            <tr class="border-b border-gray-100">
                                                                <td class="px-3 py-2 text-xs text-gray-700 text-center">
                                                                    {{ $timesheet['start_time'] }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs text-gray-700 text-center">
                                                                    {{ $timesheet['end_time'] ?? '-' }}
                                                                </td>
                                                                <td class="px-3 py-2 text-xs text-gray-700 text-center">
                                                                    {{ $timesheet['duration'] }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else
                                                <p class="text-xs text-gray-500 text-center">{{ __('users.report.attendance.no_records') }}</p>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-4 py-3 text-sm text-gray-700 text-center" colspan="5">
                                        {{ __('users.report.attendance.no_records') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <script>
        flatpickr("#date", {
            dateFormat: "Y-m-d" // Enforce YYYY-MM-DD format
        });
        
        function toggleDetails(date) {
            const detailsRow = document.getElementById(`details-${date}`);
            const icon = document.getElementById(`icon-${date}`);
            
            if (detailsRow.classList.contains('hidden')) {
                detailsRow.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                detailsRow.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }
    </script>
</x-app-layout>
