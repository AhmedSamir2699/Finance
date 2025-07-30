<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <livewire:users.summary-filter :user="$user" />

        @if (isset($summaryData))
            @php
                $isCurrentUser = $user->id === auth()->id();
                $pdfRouteName = $isCurrentUser ? 'users.summary.my.pdf' : 'users.summary.pdf';
                $pdfRouteParams = $isCurrentUser ? ['start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d')] : ['user' => $user->id, 'start_date' => $start->format('Y-m-d'), 'end_date' => $end->format('Y-m-d')];
            @endphp
            <a href="{{ route($pdfRouteName, $pdfRouteParams) }}"
                class="bg-green-600 hover:bg-green-700 py-2 px-4 rounded text-white inline-flex items-center gap-2 text-sm font-medium transition-colors">
                <i class="fas fa-file-pdf"></i>
                {{ __('users.summary.export_pdf') }}
            </a>
        @endif
    </div>

    @if (isset($summaryData))
        <div class="w-[90%] mx-auto">
            <div class="flex flex-col my-5">
                <h3 class="text-2xl font-semibold text-gray-900 mb-6">
                    @php
                        $daysCount = $start->diffInDays($end) + 1; // Include both start and end dates
                        $daysCount = max(1, round($daysCount));
                    @endphp
                    {{ __('users.summary.title', ['user' => $user->name, 'days' => $daysCount]) }}
                </h3>

                <!-- User Info Section -->
                <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                    <h4 class="text-lg font-semibold text-gray-900 mb-4">{{ __('users.summary.user_info') }}</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('users.fullname') }}:</span>
                            <span class="text-sm text-gray-900">{{ $user->name }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('users.department') }}:</span>
                            <span
                                class="text-sm text-gray-900">{{ $user->department->name ?? __('users.no_department') }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('users.position') }}:</span>
                            <span class="text-sm text-gray-900">{{ $user->position ?? __('users.no_position') }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('users.shift_start') }}:</span>
                            <span
                                class="text-sm text-gray-900">{{ $user->shift_start ? \Carbon\Carbon::parse($user->shift_start)->format('H:i') : __('users.no_shift') }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">{{ __('users.shift_end') }}:</span>
                            <span
                                class="text-sm text-gray-900">{{ $user->shift_end ? \Carbon\Carbon::parse($user->shift_end)->format('H:i') : __('users.no_shift') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Summary Table -->
                <div class="bg-white rounded-t-lg shadow-sm p-6">
                    <table class="w-full border-collapse">
                        <thead>
                            <tr class="bg-gray-50">
                                <th
                                    class="border border-gray-300 px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.summary.evaluation_criteria.title') }}</th>
                                <th
                                    class="border border-gray-300 px-4 py-3 text-right text-sm font-medium text-gray-700">
                                    {{ __('users.summary.evaluation_criteria.percentage') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-3 text-sm text-gray-700">{{ __('users.summary.discipline.title') }}</td>
                                <td class="border border-gray-300 px-4 py-3 text-sm font-semibold text-primary-600">
                                    {{ isset($summaryData['discipline']['percentage']) ? number_format($summaryData['discipline']['percentage'], 2) : '0.00' }}%
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-3 text-sm text-gray-700">{{ __('users.summary.task_completion') }}</td>
                                <td class="border border-gray-300 px-4 py-3 text-sm font-semibold text-primary-600">
                                    {{ isset($summaryData['taskCompletion']) ? $summaryData['taskCompletion'] : '0.00' }}%
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-3 text-sm text-gray-700">{{ __('users.summary.task_quality') }}</td>
                                <td class="border border-gray-300 px-4 py-3 text-sm font-semibold text-primary-600">
                                    {{ isset($summaryData['taskQuality']) ? $summaryData['taskQuality'] : '0.00' }}%
                                </td>
                            </tr>
                            <tr class="border-b border-gray-200 hover:bg-gray-50">
                                <td class="border border-gray-300 px-4 py-3 text-sm text-gray-700">{{ __('users.summary.time_efficiency.title') }}
                                </td>
                                <td class="border border-gray-300 px-4 py-3 text-sm font-semibold text-primary-600">
                                    {{ isset($summaryData['timeQuality']) ? $summaryData['timeQuality'] : '0.00' }}%
                                </td>
                            </tr>
                            @if (isset($summaryData['evaluationCriteria']) && count($summaryData['evaluationCriteria']) > 0)
                                @foreach ($summaryData['evaluationCriteria'] as $criteria)
                                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                                        <td class="border border-gray-300 px-4 py-3 text-sm text-gray-700">
                                            {{ $criteria['name'] }}</td>
                                        <td
                                            class="border border-gray-300 px-4 py-3 text-sm font-semibold text-primary-600">
                                            {{ $criteria['percentage'] }}%</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- Final Total -->
                <div class="bg-primary-100 text-white rounded-b-lg shadow-sm p-2 text-center">
                    <h4 class="text-md font-semibold mb-2">{{ __('users.summary.final_total.title') }}</h4>
                    <span class="text-lg font-bold">{{ $summaryData['finalTotal'] }}%</span>
                </div>
            </div>
        </div>
    @endif


</x-app-layout>
