<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-4 mx-auto">
        <div class="overflow-hidden">
    
            <div class="p-6 bg-white border rounded-lg shadow my-3" x-data="{ expandProgramItems: true }">

                <div class="flex items-center gap-x-2" @click="expandProgramItems = !expandProgramItems">
                    <span class="text-gray-900 font-medium text-lg whitespace-nowrap">
                        {{ __('operational-plan.show.strategic_goals.program.items') }} ({{ $subProgram->items->count() }})
                    </span>
                    <i class="fa fa-chevron-down text-gray-500" x-show="expandProgramItems"
                        x-cloak></i>
                    <i class="fa fa-chevron-left text-gray-500" x-show="!expandProgramItems"
                        x-cloak></i>
                </div>
                <table class="w-full text-sm text-start text-gray-500 rounded-b-md mt-3"
                    x-show="expandProgramItems" x-cloak>
                    <thead class="text-xs text-white uppercase bg-primary-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-start">
                                {{ __('operational-plan.show.strategic_goals.program.item.name') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                {{ __('operational-plan.show.strategic_goals.program.item.quantity') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                {{ __('operational-plan.show.strategic_goals.program.item.unit_cost') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                {{ __('operational-plan.show.strategic_goals.program.item.total_cost') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                {{ __('operational-plan.show.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td colspan="3" class="px-6 py-4 text-gray-500">
                                <div x-data="{ showProgramModal: false }">

                                    <button @click="showProgramModal = true"
                                        class="flex items-center gap-2 bg-primary-500 text-white hover:bg-primary-700 text-sm px-2 py-1 rounded">
                                        <i class="fa fa-plus"></i>
                                        <span>{{ __('operational-plan.show.strategic_goals.program.add_item') }}</span>
                                    </button>

                                    <x-modal name="showProgramModal" class="hidden">
                                        <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                                            {{ __('operational-plan.show.strategic_goals.program.add_item') }}
                                        </div>
                                        <div
                                            class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                                            <form method="post"
                                                action="{{ route('operational-plan.departments.items.store', [$operationalPlan, $department, $program, $subProgram]) }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="title"
                                                        class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.show.strategic_goals.program.item.name') }}</label>
                                                    <input type="text" name="title" id="title"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                        placeholder="{{ __('operational-plan.show.strategic_goals.program.item.name') }}"
                                                        required>
                                                </div>

                                                <div class="mb-4">
                                                    <label for="quantity"
                                                        class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.show.strategic_goals.program.item.quantity') }}</label>
                                                    <input type="number" name="quantity" id="quantity"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                        placeholder="{{ __('operational-plan.show.strategic_goals.program.item.quantity') }}"
                                                        min="1" step="1"
                                                        value="1"
                                                        required>

                                                </div>

                                                <div class="mb-4">
                                                    <label for="unit_cost"
                                                        class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.show.strategic_goals.program.item.unit_cost') }}</label>
                                                    <input type="number" name="unit_cost" id="unit_cost"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                        placeholder="{{ __('operational-plan.show.strategic_goals.program.item.unit_cost') }}"
                                                        min="0" step="0.01"
                                                        value="0"
                                                        required>

                                                </div>
                                                <button type="submit"
                                                    class="bg-primary-500 text-white hover:bg-primary-700 text-sm px-4 py-2 rounded">
                                                    <i class="fa fa-plus"></i>
                                                    {{ __('operational-plan.show.strategic_goals.program.add_item') }}
                                                </button>
                                            </form>
                                        </div>
                                    </x-modal>
                                </div>
                            </td>
                        </tr>
                        @foreach ($subProgram->items as $item)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $item->title }}
                                </td>
                                <td>
                                    <div class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-center">
                                        {{ !is_numeric($item->quantity) ? '0' : number_format($item->quantity) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-center">
                                        {{ number_format($item->unit_cost) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-center">
                                        {{ number_format($item->unit_cost * $item->quantity) }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 flex gap-x-2 justify-center items-center">
                                    <form method="post"
                                        action="{{ route('operational-plan.departments.items.destroy', [$operationalPlan, $department, $program, $subProgram, $item]) }}">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-500 text-white hover:bg-red-700 text-sm px-2 py-1 rounded">
                                            <i class="fa fa-trash"></i>
                                            {{ __('operational-plan.show.delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>


            <div class="p-6 bg-white border rounded-lg shadow my-3"  x-data="{ expandProgramActivities: true }">

                <div class="flex items-center gap-x-2" @click="expandProgramActivities = !expandProgramActivities">
                   
                    <span class="text-gray-900 font-medium text-lg whitespace-nowrap">
                        {{ __('operational-plan.show.strategic_goals.program.activities') }} ({{ $subProgram->activities->count() }})
                    </span>
                    <i class="fa fa-chevron-down text-gray-500" x-show="expandProgramActivities"
                        x-cloak></i>
                    <i class="fa fa-chevron-left text-gray-500" x-show="!expandProgramActivities"
                        x-cloak></i>
                </div>
                <table class="w-full text-sm text-start text-gray-500 rounded-b-md mt-3"
                    x-show="expandProgramActivities" x-cloak>
                    <thead class="text-xs text-white uppercase bg-primary-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-start">
                                {{ __('operational-plan.show.strategic_goals.program.activities_table.name') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                {{ __('operational-plan.show.strategic_goals.program.activities_table.yearly_target') }}
                            </th>
                            <th scope="col" class="px-6 py-3 text-center">
                                {{ __('operational-plan.show.strategic_goals.program.activities_table.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td colspan="3" class="px-6 py-4 text-gray-500">
                                <div x-data="{ showProgramModal: false }">

                                    <button @click="showProgramModal = true"
                                        class="flex items-center gap-2 bg-primary-500 text-white hover:bg-primary-700 text-sm px-2 py-1 rounded">
                                        <i class="fa fa-plus"></i>
                                        <span>{{ __('operational-plan.show.strategic_goals.program.activities_table.add_activity') }}</span>
                                    </button>

                                    <x-modal name="showProgramModal" class="hidden">
                                        <div class="flex shrink-0 items-center text-xl font-medium text-slate-800">
                                            {{ __('operational-plan.show.add_subprogram') }}
                                        </div>
                                        <div
                                            class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                                            <form method="post"
                                                action="{{ route('operational-plan.departments.activities.store', [$operationalPlan, $department, $program, $subProgram]) }}">
                                                @csrf


                                                <div class="mb-4">
                                                    <label for="title"
                                                        class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.show.strategic_goals.program.activity.name') }}</label>
                                                    <input type="text" name="title" id="title"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                        placeholder="{{ __('operational-plan.show.strategic_goals.program.activity.name') }}"
                                                        required>
                                                </div>

                                                <div class="mb-4">
                                                    <label for="yearly_target"
                                                        class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.show.strategic_goals.program.activity.yearly_target') }}</label>
                                                    <input type="number" name="yearly_target" id="yearly_target"
                                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                        placeholder="{{ __('operational-plan.show.strategic_goals.program.activity.yearly_target') }}"
                                                        min="1" step="1"
                                                        value="1"
                                                        required>
                                                </div>
                                                <button type="submit"
                                                    class="bg-primary-500 my-6 text-white hover:bg-primary-700 text-sm px-4 py-2 rounded">
                                                    <i class="fa fa-plus"></i>
                                                    {{ __('operational-plan.strategic_goal.show.program.add') }}
                                                </button>
                                            </form>
                                        </div>
                                    </x-modal>
                                </div>
                            </td>
                        </tr>
                        @foreach ($subProgram->activities as $activity)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $activity->title }}
                                </td>
                                <td>
                                    <div class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-center">
                                        {{ $activity->yearly_target }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 flex gap-x-2 justify-center items-center">
                                    <form method="post"
                                        action="{{ route('operational-plan.departments.activities.destroy', [$operationalPlan, $department, $program, $subProgram, $item, $activity]) }}">
                                        @csrf
                                        <button type="submit"
                                            class="bg-red-500 text-white hover:bg-red-700 text-sm px-2 py-1 rounded">
                                            <i class="fa fa-trash"></i>
                                            {{ __('operational-plan.show.delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</x-app-layout>
