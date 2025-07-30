<x-app-layout>

    <div class="mt-1">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        <div class="mt-5 w-3/4 mx-auto bg-white py-3 px-5 rounded-lg shadow">
            <h3 class="text-xl font-bold mb-4">{{ __('executive-plan.clone_page.title') }}
                [{{ __('executive-plan.clone_page.sub_title', ['month' => \Carbon\Carbon::create($year, $month)->translatedFormat('F'), 'year' => $year]) }}]
            </h3>
            <hr class="my-3 border border-b">
            <form action="{{ route('executive-plan.clone', ['year' => $year, 'month' => $month]) }}" method="POST">
                @csrf
                <p class="text-md font-semibold text-gray-900 mt-5 mb-8">{{ __('executive-plan.clone_page.description') }}</p>
                <div class="flex flex-col  justify-start items-right mb-4 gap-4">
                    <div class="flex flex-row gap-4">
                        <div class="flex flex-row items-center">
                            <label for="year"
                                class="text-lg font-semibold text-gray-900">{{ __('executive-plan.clone_page.year') }}</label>
                            <select name="year" id="year"
                                class="px-8 mx-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-center">
                                @foreach ($availableYears as $year)
                                    <option>
                                        <a
                                            href="{{ route('executive-plan.clone', ['year' => $year, 'month' => $month]) }}">{{ $year }}</a>
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-row items-center">
                            <label for="month"
                                class="text-lg font-semibold text-gray-900">{{ __('executive-plan.clone_page.month') }}</label>
                            <select name="month" id="month"
                                class="px-8 mx-2 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-center">
                                @for ($month = 1; $month <= 12; $month++)
                                    <option value="{{ $month }}">
                                        {{ \Carbon\Carbon::create($year, $month)->translatedFormat('F') }}
                                    </option>
                                @endfor
                            </select>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mt-4">{{ __('executive-plan.clone_page.select_columns') }}</h3>
                    <div x-data="{ selectAll: true, selectedColumns: [] }" class="flex flex-col gap-4">
                        {{-- check/uncheck all --}}
                        <div class="flex flex-row gap-3 items-center">
                            <input type="checkbox" id="all" x-model="selectAll"
                                x-on:change="selectedColumns = selectAll ? @json($columns->pluck('id')) : []"
                                class="rounded border border-gray-300 shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-center">
                            <label for="all" class="text-lg font-semibold text-gray-900">
                                {{ __('executive-plan.clone_page.select_all_columns') }}
                            </label>
                        </div>
                    
                        @foreach ($columns as $column)
                            <div class="flex flex-row gap-3 items-center">
                                <input type="checkbox" name="columns[]" id="column-{{ $column->id }}" value="{{ $column->id }}"
                                    x-model="selectedColumns"
                                    checked
                                    x-init="selectedColumns.push({{ $column->id }})"
                                    class="rounded border border-gray-300 shadow-sm focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm text-center">
                                <label for="column-{{ $column->id }}" class="text-lg font-semibold text-gray-900">
                                    {{ $column->name }} ({{ $column->cells->count() }})
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    <button type="submit"
                        class="bg-primary-600 inline text-md hover:text-secondary-300 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        <i class="fas fa-clone"></i>
                        {{ __('executive-plan.clone_page.clone') }}
                    </button>
                </div>
            </form>

        </div>


    </div>

</x-app-layout>
