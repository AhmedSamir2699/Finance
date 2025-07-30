<div class="mt-6">
    <div class="mt-3 flex flex-row">

        <div class="block relative mt-2 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                    <path
                        d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                    </path>
                </svg>
            </span>

            <input wire:model.live="search" placeholder="{{ __('users.search_placeholder') }}"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
        </div>
        <div class="block relative mt-2 ms-3 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <i class="fa fa-building h-4 w-4 fill-current text-gray-500"></i>
            </span>

            <select wire:model.live="selectedDepartment"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none mt-2 sm:mt-0">
                <option value="all">{{ __('users.all_departments') }}</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="block relative mt-2 ms-3 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <i class="fa fa-sort-amount-up h-4 w-4 fill-current text-gray-500"></i>
            </span>

            <select wire:model.live="perPage"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                <option>5</option>
                <option>10</option>
                <option>15</option>
                <option>20</option>
                <option>50</option>
            </select>
        </div>
    </div>

    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('users.fullname') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('users.department') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('users.reports') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex items-center">
                                    <div class="ml-3">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $user->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                @if ($user->department)
                                    <a href="{{ route('manage.departments.show', [$user->department]) }}"
                                        class="text-gray-900 whitespace-no-wrap">{{ $user->department->name }}</a>
                                @else
                                    <p class="text-gray-900 whitespace-no-wrap">-</p>
                                @endif
                            </td>

                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm flex flex-row gap-3 justify-center items-center"
                                x-data="{
                                    showMonthModal: false,
                                    selectedMonth: '',
                                    generateReportUrl() {
                                        if (!this.selectedMonth) return '#';
                                
                                        const [year, month] = this.selectedMonth.split('-');
                                        const startDate = `${year}-${month}-01`;
                                        const endDate = new Date(year, month, 0).toISOString().split('T')[0]; // last day of month
                                
                                        const baseUrl = '{{ route('users.reports.show', ['user' => $user->id]) }}';
                                        window.location = `${baseUrl}?start_date=${startDate}&end_date=${endDate}`;
                                    }
                                }">
                                <a href="{{ route('users.reports.show', [
                                    'user' => $user->id,
                                    'start_date' => \Carbon\Carbon::yesterday()->startOfDay()->toDateString(),
                                    'end_date' => \Carbon\Carbon::yesterday()->endOfDay()->toDateString(),
                                ]) }}
"
                                    class="text-white rounded px-2 py-1 bg-primary-300 hover:bg-primary-base hover:text-white focus:outline-none">
                                    {{ __('users.report.daily_report') }}
                                </a>
                                <!-- Monthly Report Button -->
                                <button type="button" x-on:click.stop="showMonthModal = true"
                                    class="text-white rounded px-2 py-1 bg-primary-300 hover:bg-primary-base hover:text-white focus:outline-none">
                                    {{ __('users.report.monthly_report') }}
                                </button>

                                <!-- Month Selection Modal -->
                                <div x-show="showMonthModal" style="display: none;"
                                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                    <div class="bg-white rounded-lg p-6 w-96" @click.away="showMonthModal = false">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ __('users.report.monthly_report') }}</h3>

                                        <p class="mt-2 text-sm text-gray-500">{{ __('users.report.select_month') }}
                                        </p>

                                        <form method="GET" :action="generateReportUrl()" class="mt-4 space-y-4">
                                            <div>
                                                <label for="month" class="block text-sm font-medium text-gray-700">
                                                    {{ __('users.report.month') }}
                                                </label>
                                                <input type="month" id="month" x-model="selectedMonth"
                                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-blue-200"
                                                    required>
                                            </div>
                                        </form>
                                    </div>
                                </div>





                                <a href="{{ route('users.reports.show', [
                                    'user' => $user->id,
                                    'start_date' => \Carbon\Carbon::now()->subYear()->startOfYear()->toDateString(),
                                    'end_date' => \Carbon\Carbon::now()->subYear()->endOfYear()->toDateString(),
                                ]) }}"
                                    class="text-white rounded px-2 py-1 bg-primary-300 hover:bg-primary-base hover:text-white focus:outline-none">
                                    {{ __('users.report.yearly_report') }}
                                </a>

                                <a href="{{ route('users.summary.show', [
                                    'user' => $user->id,
                                    'start_date' => \Carbon\Carbon::now()->subMonth()->startOfMonth()->toDateString(),
                                    'end_date' => \Carbon\Carbon::now()->subMonth()->endOfMonth()->toDateString(),
                                ]) }}"
                                    class="text-white rounded px-2 py-1 bg-green-500 hover:bg-green-600 hover:text-white focus:outline-none">
                                    {{ __('users.summary.title_short') }}
                                </a>



                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="px-5 py-5 border-t flex flex-col items-center xs:justify-between">
        {{ $users->links() }}
    </div>
</div>
