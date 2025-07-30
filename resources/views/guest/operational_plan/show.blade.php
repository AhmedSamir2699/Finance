<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-5 w-3/4 mx-auto bg-white py-3 px-5 rounded-lg shadow">
        <div class="flex flex-row justify-between items-center">
            <h1 class="text-xl font-semibold text-gray-900 flex-1">
                {{ $plan->title }}
            </h1>
            <select id="department" name="department" class="flex-0 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500  p-2.5">
                <option value="summary">{{ __('operational-plan.guest.show.select_summary') }}</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}">{{ $department->title }}</option>
                @endforeach
            </select>
        </div>

        {{-- <table class="min-w-full text-center  border border-gray-300">
            <thead>
                <tr>
                    <th class="bg-primary-200 px-4 py-2 font-semibold text-white border-b border-r border-gray-400">
                        {{ __('operational-plan.guest.index.table.title') }}
                    </th>
                    <th class="bg-primary-200 px-4 py-2 font-semibold text-white border-b border-r border-gray-400">
                        {{ __('operational-plan.guest.index.table.period') }}
                    </th>
                    <th class="bg-primary-200 px-4 py-2 font-semibold text-white border-b border-r border-gray-400">
                        {{ __('executive-plan.actions') }}
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($plans as $plan)
                    <tr class="group text-gray-900 hover:bg-gray-100  transition-all ease-in-out">
                        <td class="py-2 border-r border-b border-gray-400">
                            {{ $plan->title }}
                        </td>
                        <td class="py-2 border-r border-b border-gray-400">
                            {{ $plan->period }}
                        </td>
                        <td class="py-2 border-r border-l border-b border-gray-400 ">
                          <div class="flex justify-center gap-3">
                            <a href="{{ route('guest.operational_plan.show', $plan->id) }}"
                                class="text-primary-base px-2 hover:bg-primary-600 hover:text-white font-bold rounded">
                                <i class="fa-solid fa-eye"></i>
                                {{ __('operational-plan.guest.index.table.show') }}
                            </a>
                            <a href="{{ route('guest.operational_plan.export', $plan->id) }}"
                                class="text-primary-base px-2 hover:bg-primary-600 hover:text-white font-bold rounded">
                                <i class="fa-solid fa-file-export"></i>
                                {{ __('operational-plan.guest.index.table.export') }}

                            </a>
                            <a href="{{ route('guest.operational_plan.table-view', $plan->id) }}"
                                class="text-primary-base px-2 hover:bg-primary-600 hover:text-white font-bold rounded">
                                <i class="fa-solid fa-table"></i>
                                {{ __('operational-plan.guest.index.table.table') }}
                            </a>  
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table> --}}
    </div>
</x-app-layout>
