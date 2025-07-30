<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-4 mx-auto">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                <div x-data="{ showProgramModal: false }" <button @click="showProgramModal = true"
                    class="flex items-center gap-2 bg-primary-500 text-white hover:bg-primary-700 text-sm px-2 py-1 rounded">
                    <i class="fa fa-plus"></i>
                    <span>{{ __('operational-plan.strategic_goal.show.program.add') }}</span>
                    </button>

                    <x-modal name="showProgramModal" class="hidden">
                        <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                            {{ __('operational-plan.strategic_goal.show.program.add') }}
                        </div>
                        <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                            <form method="post"
                                action="{{ route('operational-plan.departments.programs.store', [$operationalPlan, $department, $strategicGoal]) }}">
                                @csrf
                                <div class="mb-4">
                                    <label for="title"
                                        class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.strategic_goal.show.program.name') }}</label>
                                    <input type="text" name="title" id="title"
                                        class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                        placeholder="{{ __('operational-plan.strategic_goal.show.program.name') }}"
                                        required>
                                </div>

                                <button type="submit"
                                    class="bg-primary-500 text-white hover:bg-primary-700 text-sm px-4 py-2 rounded">
                                    <i class="fa fa-plus"></i>
                                    {{ __('operational-plan.strategic_goal.show.program.add') }}
                                </button>
                            </form>
                        </div>
                    </x-modal>
                </div>
            </div>

            @forelse ($strategicGoal->programs as $program)
                <div class="p-6 bg-white border rounded shadow-sm" x-data="{ expandProgramId{{ $program->id }}: false }">

                    <div class="flex items-center gap-x-2"
                        @click="expandProgramId{{ $program->id }} = !expandProgramId{{ $program->id }}">
                        <i class="fa fa-circle text-primary-500"></i>
                        <span class="text-gray-900 font-medium text-lg whitespace-nowrap">
                            {{ $program->title != "" ? $program->title : __('operational-plan.show.unnamed_program') }}
                        </span>
                        <i class="fa fa-chevron-down text-gray-500" x-show="expandProgramId{{ $program->id }}"
                            x-cloak></i>
                        <i class="fa fa-chevron-left text-gray-500" x-show="!expandProgramId{{ $program->id }}"
                            x-cloak></i>
                    </div>
                    <table class="w-full text-sm text-start text-gray-500 rounded-b-md mt-3"
                        x-show="expandProgramId{{ $program->id }}" x-cloak>
                        <thead class="text-xs text-white uppercase bg-primary-500">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-start">
                                    {{ __('operational-plan.strategic_goal.show.program.name') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-center">
                                    {{ __('operational-plan.show.total_budget') }}
                                </th>
                                <th scope="col" class="px-6 py-3 text-center">
                                    {{ __('operational-plan.show.total_activities') }}
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
                                            <span>{{ __('operational-plan.show.add_subprogram') }}</span>
                                        </button>

                                        <x-modal name="showProgramModal" class="hidden">
                                            <div
                                                class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                                                {{ __('operational-plan.show.add_subprogram') }}
                                            </div>
                                            <div
                                                class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                                                <form method="post"
                                                    action="{{ route('operational-plan.departments.subprograms.store', [$operationalPlan, $department, $program]) }}">
                                                    @csrf
                                                    <div class="mb-4">
                                                        <label for="title"
                                                            class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.strategic_goal.show.program.name') }}</label>
                                                        <input type="text" name="title" id="title"
                                                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                                            placeholder="{{ __('operational-plan.strategic_goal.show.program.name') }}"
                                                            required>
                                                    </div>

                                                    <button type="submit"
                                                        class="bg-primary-500 text-white hover:bg-primary-700 text-sm px-4 py-2 rounded">
                                                        <i class="fa fa-plus"></i>
                                                        {{ __('operational-plan.strategic_goal.show.program.add') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </x-modal>
                                    </div>
                                </td>
                            </tr>
                            @foreach ($program->subPrograms as $subProgram)
                                <tr class="bg-white border-b hover:bg-gray-50">
                                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                        {{ $subProgram->title }}
                                    </td>
                                    <td>
                                        <div class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-center">
                                            {{ $subProgram->total_budget }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap text-center">
                                            {{ $subProgram->activities_count }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 flex gap-x-2 justify-center items-center">
                                        <a href="{{ route('operational-plan.departments.subprograms.show', [$operationalPlan, $department, $program, $subProgram]) }}"
                                            class="bg-blue-500 text-white hover:bg-blue-700 text-sm px-2 py-1 rounded">
                                            <i class="fa fa-tasks"></i>
                                            {{ __('operational-plan.show.manage_activities') }}
                                        </a>
                                        <form method="post"
                                            action="{{ route('operational-plan.departments.subprograms.destroy', [$operationalPlan, $department, $program, $subProgram]) }}">
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
            @empty
                <div class="p-6 bg-white border rounded shadow-sm">
                    <div class="flex items-center gap-x-2">
                        <span class="text-gray-900 font-medium text-lg whitespace-nowrap">
                            {{ __('operational-plan.strategic_goal.show.program.no_programs') }}
                        </span>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

</x-app-layout>
