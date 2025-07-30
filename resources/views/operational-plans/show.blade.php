<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-4 mx-auto">
        <div class="">
            <div class="">
                <ul class="space-y-3">
                    {{-- Summary Department --}}
                    <li class="bg-white border rounded shadow-sm" x-data="{ expandSummary: false }"
                    :class="expandSummary ? 'rounded-lg border-2 border-primary-500' : 'bg-white'">
                        
                        <div class=" p-6 cursor-pointer" @click="expandSummary = !expandSummary">
                            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                                <i class="fa fa-list text-primary-500"></i>
                                {{ __('operational-plan.show.summary.title') }}
                                <i class="fa fa-chevron-left text-sm" x-show="!expandSummary"></i>
                                <i class="fa fa-chevron-down text-sm" x-show="expandSummary"></i>
                            </h3>
                        </div>

                        <div x-show="expandSummary" class="flex-col gap-2 mx-3">

                            <div class="flex items-center gap-2 mb-2">
                                <div x-data="{ showAddSummaryGoal: false }" class="mt-3">
                                    <button @click="showAddSummaryGoal = true"
                                        class="mt-2 flex items-center gap-2 bg-primary-600 hover:bg-primary-700 text-white text-sm px-2 py-1 rounded">
                                        <i class="fa fa-plus"></i>
                                        {{ __('operational-plan.show.add_strategic_goal') }}
                                    </button>

                                    <x-modal name="showAddSummaryGoal" class="hidden">
                                        <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                                            {{ __('operational-plan.show.add_strategic_goal') }}
                                        </div>
                                        <div
                                            class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                                            <form method="post"
                                                action="{{ route('operational-plan.summary.goals.store', $operationalPlan) }}">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="name"
                                                        class="block text-sm font-medium text-slate-700">{{ __('operational-plan.show.strategic_goals.name') }}</label>
                                                    <input type="text" name="name" id="name"
                                                        placeholder="{{ __('operational-plan.show.strategic_goals.name') }}" required
                                                        class="appearance-none rounded border border-gray-400 border-b block pr-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
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
                            </div>
                            <table class="w-full text-sm text-center text-gray-500 rounded-b-md mt-3">
                                <thead class="text-xs text-white uppercase bg-primary-500">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __('operational-plan.show.strategic_goal') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __('operational-plan.show.programs_count') }}
                                        </th>
                                        <th scope="col" class="px-6 py-3">
                                            {{ __('operational-plan.show.actions') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($operationalPlan->summaryPrograms as $summaryProgram)
                                        {{-- styled with primary class --}}
                                        <tr @click="window.location.href='{{ route('operational-plan.departments.strategic-goals.show', [$operationalPlan, $department, $goal]) }}'"
                                            class="bg-white @if (!$loop->last) border-b border-primary-700 @endif hover:bg-primary-100 hover:cursor-pointer">
                                            <td class="py-4 font-medium text-gray-900 whitespace-nowrap">

                                                {{ $summaryProgram->title }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                {{ $goal->programs->count() }}
                                            </td>
                                            <td class="px-6 py-4 flex gap-x-2 justify-center items-center">
                                                <form method="post"
                                                    action="{{ route('operational-plan.departments.strategic-goals.destroy', [$operationalPlan, $department, $goal]) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="bg-red-500 text-white hover:bg-red-700 text-sm px-2 py-1 rounded">
                                                        <i class="fa fa-trash"></i>
                                                        {{ __('operational-plan.show.delete') }}
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="bg-white hover:bg-primary-100 hover:cursor-pointer">
                                            <td colspan="3" class="py-4 font-medium text-gray-900 whitespace-nowrap">
                                                {{ __('operational-plan.show.no_strategic_goals') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </li>

                    {{-- Normal Departments --}}
                    @foreach ($operationalPlan->departments as $department)
                        <li class="bg-white border rounded shadow-sm"
                            :class="ExpandDepartment{{ $department->id }} ? 'rounded-lg border-2 border-primary-500' :
                                'bg-white'"
                            x-data="{ ExpandDepartment{{ $department->id }}: false, showGoalModal: false }">
                            <div class="p-6 cursor-pointer" @click="ExpandDepartment{{ $department->id }} = !ExpandDepartment{{ $department->id }}">
                                {{-- Department Title --}}
                                <h4 class="text-xl font-bold text-gray-800 flex items-center gap-2 hover:cursor-pointer px-3">
                                    <i class="fa fa-building"></i>
                                    {{ $department->title }}
                                    <i class="fa fa-chevron-left text-sm"
                                        x-show="!ExpandDepartment{{ $department->id }}"></i>
                                    <i class="fa fa-chevron-down text-sm"
                                        x-show="ExpandDepartment{{ $department->id }}"></i>
                                </h4>
                            </div>

                            <div x-show="ExpandDepartment{{ $department->id }}" class="flex-col gap-2 mt-2 mx-3">
                                <div class="flex items-center gap-2 mb-2">
                                                                    <div class="flex items-center gap-2 mb-2">
                                    <div x-data="{}" class="">
                                        <button @click="showGoalModal = true"
                                            class="flex items-center gap-2 bg-primary-500 text-white hover:bg-primary-700 text-sm px-2 py-1 rounded">
                                            <i class="fa fa-plus"></i>
                                            <span>{{ __('operational-plan.show.strategic_goal') }}</span>
                                        </button>

                                        <x-modal name="showGoalModal" class="hidden">
                                            <div
                                                class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                                                {{ __('operational-plan.show.add_strategic_goal') }}
                                            </div>
                                            <div
                                                class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                                                <form method="post"
                                                    action="{{ route('operational-plan.departments.strategic-goals.store', [$operationalPlan, $department]) }}">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label for="name"
                                                            class="block text-sm font-medium text-slate-700">{{ __('operational-plan.show.strategic_goals.name') }}</label>
                                                        <input type="text" name="title" id="name"
                                                            placeholder="{{ __('operational-plan.show.strategic_goals.name') }}"
                                                            required
                                                            class="appearance-none rounded border border-gray-400 border-b block pr-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
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
                                </div>

                                </div>
                                <table class="w-full text-sm text-center text-gray-500 rounded-b-md mt-3">
                                    <thead class="text-xs text-white uppercase bg-primary-500">
                                        <tr>
                                            <th scope="col" class="px-6 py-3">
                                                {{ __('operational-plan.show.strategic_goal') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                {{ __('operational-plan.show.programs_count') }}
                                            </th>
                                            <th scope="col" class="px-6 py-3">
                                                {{ __('operational-plan.show.actions') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($department->strategicGoals as $goal)
                                            {{-- styled with primary class --}}
                                            <tr @click="window.location.href='{{ route('operational-plan.departments.strategic-goals.show', [$operationalPlan, $department, $goal]) }}'"
                                                class="bg-white @if (!$loop->last) border-b border-primary-700 @endif hover:bg-primary-100 hover:cursor-pointer">
                                                <td class="py-4 font-medium text-gray-900 whitespace-nowrap">
                                                    {{ $goal->title !== "" ? $goal->title : __('operational-plan.show.unnamed_goal') }}
                                                </td>
                                                <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                                    {{ $goal->programs->count() }}
                                                </td>
                                                <td class="px-6 py-4 flex gap-x-2 justify-center items-center">
                                                    <form method="post"
                                                        action="{{ route('operational-plan.departments.strategic-goals.destroy', [$operationalPlan, $department, $goal]) }}">
                                                        @csrf
                                                        <button type="submit"
                                                            class="bg-red-500 text-white hover:bg-red-700 text-sm px-2 py-1 rounded">
                                                            <i class="fa fa-trash"></i>
                                                            {{ __('operational-plan.show.delete') }}
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="bg-white hover:bg-primary-100 hover:cursor-pointer">
                                                <td colspan="3"
                                                    class="py-4 font-medium text-gray-900 whitespace-nowrap">
                                                    {{ __('operational-plan.show.no_strategic_goals') }}
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                        </li>
                    @endforeach

                    {{-- Add New Department --}}
                    <li x-data="{ showAddDepartment: false }">

                        <button @click="showAddDepartment = true"
                            class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <i class="fa fa-plus text-primary-500"></i>
                            {{ __('operational-plan.show.add_department') }}
                        </button>

                        <x-modal name="showAddDepartment" class="hidden">
                            <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                                {{ __('departments.index.add_modal_label') }}
                            </div>
                            <div
                                class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                                <form method="post"
                                    action="{{ route('operational-plan.departments.store', $operationalPlan) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="name"
                                            class="block text-sm font-medium text-slate-700">{{ __('departments.index.name') }}</label>
                                        <input type="text" name="title" id="name"
                                            placeholder="{{ __('departments.index.name') }}" required
                                            class="appearance-none rounded border border-gray-400 border-b block pr-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                                    </div>

                                    <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
                                        <button
                                            class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
                                            type="submit">
                                            {{ __('departments.index.save') }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>
                    </li>
                </ul>
            </div>

        </div>
    </div>

</x-app-layout>
