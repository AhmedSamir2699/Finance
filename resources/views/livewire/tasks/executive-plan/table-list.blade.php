<div class="mt-6" wire:pol.15000ms>
    <div class="mt-3 flex flex-row flex-wrap gap-y-3">


        <div class="block relative mt-2 me-3 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <i class="fa fa-sort-amount-up h-4 w-4 fill-current text-gray-500"></i>
            </span>

            <select wire:model.live="perPage"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                <option>5</option>
                <option>10</option>
                <option>15</option>
                <option>20</option>
            </select>

        </div>


        <div class="block relative mt-2 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                    <path
                        d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                    </path>
                </svg>
            </span>

            <input wire:model.live="search" placeholder="{{ __('tasks.search.placeholder') }}"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
        </div>




        <div class="block relative mt-2 ms-3 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <i class="fa fa-filter
                 h-4 w-4 fill-current text-gray-500"></i>
            </span>
            <select wire:model.live="status"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                <option value="">الحالة: {{ __('tasks.status.default') }}</option>
                <option value="past_due">{{ __('tasks.status.past_due') }}</option>
                <option value="pending">{{ __('tasks.status.pending') }}</option>
                <option value="completed">{{ __('tasks.status.completed') }}</option>


            </select>
        </div>

        <div class="block relative mt-2 ms-3 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <i class="fa fa-calandar
                 h-4 w-4 fill-current text-gray-500"></i>
            </span>
            <div class="flex gap-2">
                <input type="date" wire:model.live="from"
                max="{{ $to }}"
                    class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                <input type="date" wire:model.live="to"
                min="{{ $from }}"
                    class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
            </div>
        </div>

        <div class="block relative mt-2 ms-3 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <i class="fa fa-users
                 h-4 w-4 fill-current text-gray-500"></i>
            </span>
            <select wire:model.live="department"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                <option value="all">{{ __('tasks.department.all_departments') }}</option>
                @foreach ($departments as $filteredDepartment)
                    <option value="{{ $filteredDepartment->id }}" @if ($filteredDepartment->id == $department) selected @endif>
                        {{ $filteredDepartment->name }}</option>
                @endforeach
            </select>
        </div>




    </div>

    @if($tasks->total() > 0)
        <div class="text-xs text-gray-500 text-center mb-2">
            @if($tasks->total() == 1)
                {{ __('tasks.results.found_singular') }}
            @else
                {{ __('tasks.results.found', ['count' => $tasks->total()]) }}
            @endif
        </div>
    @endif

    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('tasks.table.title') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 hidden md:table-cell border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('tasks.table.task_date') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('tasks.table.status') }}</th>
                        @if (isset($department))
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                {{ __('tasks.table.assigned_to') }}</th>
                        @else
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                {{ __('tasks.table.assigned_by') }}</th>
                        @endif


                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr class="bg-white hover:bg-gray-100 cursor-pointer"
                            @click="window.open('{{ route('executive-plan.cell.show', $task->id) }}', '_blank')">
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <div class="flex flex-col items-start gap-2">
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $task->column->name }}</p>
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $task->value }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b hidden md:table-cell border-gray-200 text-sm ">
                                <div class="flex gap-2 justify-center items-center">
                                    <div class="flex-0">
                                        @if ($task->date > now()->format('Y-m-d'))
                                            <i class="fas fa-clock text-gray-400 fa-xs"></i>
                                        @elseif($task->date == now()->format('Y-m-d'))
                                            <i class="fas fa-exclamation-triangle text-yellow-500 fa-xs"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-red-500 fa-xs"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            {{ Carbon\Carbon::parse($task->date)->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>

                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    @if ($task->deleted_at)
                                        <span class="text-red-500">{{ __('tasks.status.deleted') }}</span>
                                    @else
                                        @if ($task->status === 'completed')
                                            <span
                                                class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                                {{ __('tasks.status.completed') }}
                                            </span>
                                        @else
                                            @if(\Carbon\Carbon::parse($task->date)->format('Y-m-d') < now()->format('Y-m-d'))
                                                <span
                                                    class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">
                                                    {{ __('tasks.status.past_due') }}
                                                </span>
                                            @else
                                                <span
                                                    class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">
                                                    {{ __('tasks.status.pending') }}
                                                </span>
                                            @endif
                                        @endif
                                    @endif
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                @if (isset($department))
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        <a
                                            href="{{ route('users.show', $task->user->id) }}">{{ $task->user->name }}</a>
                                    </p>
                                @else
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $task->assignedBy->name ?? '-' }}
                                    </p>
                                @endif

                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-5 bg-white border-b border-gray-200 text-sm text-center">
                                {{ __('tasks.no_tasks') }}
                            </td>
                        </tr>

                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="px-5 py-5 border-t flex flex-col items-center xs:justify-between">
        {{ $tasks->links() }}
    </div>
</div>
