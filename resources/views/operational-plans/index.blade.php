<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-4 mx-auto">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
           <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800 leading-tight">
                    {{ __('operational-plan.index.title') }}
                </h2>
                <a href="{{ route('operational-plan.create') }}" class="bg-primary-300 text-white px-4 py-2 rounded-md hover:bg-primary-500">
                    {{ __('operational-plan.index.create') }}
                </a>
            </div>

            <div class="p-6 bg-white border-b border-gray-200">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 text-center uppercase tracking-wider">
                                {{ __('operational-plan.index.table.title') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 text-center uppercase tracking-wider">
                                {{ __('operational-plan.index.table.period') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 text-center uppercase tracking-wider">
                                {{ __('operational-plan.index.table.is_public') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 text-center uppercase tracking-wider">
                                {{ __('operational-plan.index.table.views') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-xs font-medium text-gray-500 text-center uppercase tracking-wider">
                                {{ __('operational-plan.index.table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($operationalPlans as $plan)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $plan->title }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $plan->period }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">
                                    <input type="checkbox" disabled {{ $plan->is_public ? 'checked' : '' }}>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $plan->views }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium flex justify-between items-center">
                                                                        <div x-data="{ importExcelModal: false }" class="">
                                        <button @click="importExcelModal = true"
                                            class="flex items-center gap-2 bg-primary-500 text-white hover:bg-primary-700 text-sm px-2 py-1 rounded">
                                            <i class="fa fa-file-excel"></i>
                                            <span>{{ __('operational-plan.show.import_excel') }}</span>
                                        </button>

                                        <x-modal name="importExcelModal" class="hidden">
                                            <div
                                                class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                                                {{ __('operational-plan.show.add_strategic_goal') }}
                                            </div>
                                            <div
                                                class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                                                <form method="post"
                                                    action="{{ route('operational-plan.import-excel', [$plan]) }}"
                                                    enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label for="excel_file"
                                                            class="block text-sm font-medium text-slate-700">{{ __('operational-plan.show.import_excel') }}</label>
                                                        <input type="file" name="excel_file" id="excel_file"
                                                            accept=".xlsx, .xls"
                                                            class="appearance-none rounded border border-gray-400 block pr-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                                                        <p class="text-xs text-gray-500 mt-1">
                                                            {{ __('operational-plan.show.excel_import_info') }}
                                                        </p>
                                                    </div>

                                                    <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
                                                        <button
                                                            class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
                                                            type="submit">
                                                            {{ __('operational-plan.show.save') }}
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </x-modal>
                                    </div>
                                    <a href="{{ route('operational-plan.show', $plan) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('operational-plan.index.table.view') }}</a>
                                    <a href="{{ route('operational-plan.edit', $plan) }}" class="text-indigo-600 hover:text-indigo-900">{{ __('operational-plan.index.table.edit') }}</a>
                                    <form action="{{ route('operational-plan.destroy', $plan) }}" method="POST" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            {{ __('operational-plan.index.table.delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    {{ __('operational-plan.index.table.no_data') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-app-layout>
