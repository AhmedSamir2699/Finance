<div>
    @php
        $departmentOptions = $departments
            ->map(fn($d) => ['id' => $d->id, 'name' => $d->name])
            ->prepend(['id' => 'all', 'name' => __('tasks.department.all_departments')])
            ->values();
        $assignableUserOptions = $assignableUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->values();
    @endphp
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
    @endpush
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            function initTomSelects() {
                document.querySelectorAll('.searchable-select').forEach(function(el) {
                    if (!el.tomselect) {
                        new TomSelect(el, {
                            create: false,
                            allowEmptyOption: true,
                            persist: false
                        });
                    }
                });
            }
            document.addEventListener('DOMContentLoaded', initTomSelects);
            document.addEventListener('livewire:update', initTomSelects);
        </script>
    @endpush
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="flex gap-3">
            @if (auth()->user()->can('task.view-department') && $department !== null)
                <div class="flex flex-wrap gap-4 items-center mb-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">القسم</label>
                        <livewire:filters.department-dropdown :selected="$department" wire:key="department-dropdown" />
                    </div>
                </div>
            @endif

            @if (auth()->user()->can('task.view-any') && $assignedBy !== null)
                <div class="flex flex-wrap gap-4 items-center mb-3">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">المكلف</label>
                        <livewire:filters.assigned-by-dropdown :selected="$assignedBy" wire:key="assigned-by-dropdown" />
                    </div>
                </div>
            @endif
        </div>

        <div class="flex flex-wrap gap-3 items-center">
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('tasks.status.label') ?? 'الحالة' }}</label>
                <select wire:model.live="status" class="rounded border-gray-300 px-3 py-2 text-sm min-w-[120px]">
                    <option value="">{{ __('tasks.status.default') }}</option>
                    <option value="pending">{{ __('tasks.status.pending') }}</option>
                    <option value="in_progress">{{ __('tasks.status.in_progress') }}</option>
                    <option value="submitted">{{ __('tasks.status.submitted') }}</option>
                    <option value="approved">{{ __('tasks.status.completed') }}</option>
                    <option value="overdue">{{ __('tasks.status.overdue') }}</option>
                    @can('task.view-trashed')
                        <option value="deleted">{{ __('tasks.status.deleted') }}</option>
                    @endcan
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('tasks.priority.label') ?? 'الأولوية' }}</label>
                <select wire:model.live="priority" class="rounded border-gray-300 px-3 py-2 text-sm min-w-[120px]">
                    <option value="">{{ __('tasks.priority.default') }}</option>
                    <option value="low">{{ __('tasks.priority.low') }}</option>
                    <option value="medium">{{ __('tasks.priority.medium') }}</option>
                    <option value="high">{{ __('tasks.priority.high') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">من التاريخ</label>
                <input type="date" wire:model.live="fromDate" class="rounded border-gray-300 px-3 py-2 text-sm"
                    placeholder="من التاريخ">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">إلى التاريخ</label>
                <input type="date" wire:model.live="toDate" class="rounded border-gray-300 px-3 py-2 text-sm"
                    placeholder="إلى التاريخ">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">{{ __('tasks.search.label') ?? 'بحث' }}</label>
                <input type="text" wire:model.live="search" class="rounded border-gray-300 px-3 py-2 text-sm"
                    placeholder="{{ __('tasks.search.placeholder') }}">
            </div>
            <div>
                <label class="block text-xs text-gray-500 mb-1">عدد النتائج</label>
                <select wire:model.live="perPage" class="rounded border-gray-300 px-3 py-2 text-sm min-w-[80px]">
                    <option>5</option>
                    <option>10</option>
                    <option>15</option>
                    <option>20</option>
                </select>
            </div>
        </div>
    </div>

    @if ($tasks->total() > 0)
        <div class="text-xs text-gray-500 text-center mb-2">
            @if ($tasks->total() == 1)
                {{ __('tasks.results.found_singular') }}
            @else
                {{ __('tasks.results.found', ['count' => $tasks->total()]) }}
            @endif
        </div>
    @endif

    <script>
        Livewire.on('departmentSelected', id => {
            Livewire.find(document.querySelector('[wire\:id]').getAttribute('wire:id')).set('department', id);
        });
        Livewire.on('assignedBySelected', id => {
            Livewire.find(document.querySelector('[wire\:id]').getAttribute('wire:id')).set('assignedBy', id);
        });
    </script>

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
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('tasks.table.assigned_by') }}
                        </th>
                        @can('task.restore-trashed')
                            @if (($withTrashed ?? false) || ($status ?? null) === 'deleted')
                                <th
                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                    استعادة
                                </th>
                            @endif
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr class="bg-white hover:bg-gray-100 cursor-pointer"
                            @click="window.open('{{ route('tasks.show', $task) }}', '_blank')">
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <div class="flex items-center gap-2">
                                    <i
                                        class="fas fa-flag text-{{ $task->priority == 'low' ? 'gray' : ($task->priority == 'medium' ? 'yellow' : 'red') }}-500 fa-xs"></i>
                                    <p class="text-gray-900 whitespace-no-wrap">{{ $task->title }}</p>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b hidden md:table-cell border-gray-200 text-sm ">
                                <div class="flex gap-2 justify-center items-center">
                                    <div class="flex-0">
                                        @if ($task->due_date > now()->format('Y-m-d'))
                                            <i class="fas fa-clock text-gray-400 fa-xs"></i>
                                        @elseif($task->due_date == now()->format('Y-m-d'))
                                            <i class="fas fa-exclamation-triangle text-yellow-500 fa-xs"></i>
                                        @else
                                            <i class="fas fa-exclamation-circle text-red-500 fa-xs"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            من {{ Carbon\Carbon::parse($task->task_date)->format('d/m/Y') }}</p>
                                        </p>
                                        <p class="text-gray-900 whitespace-no-wrap">
                                            إلى {{ Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}</p>
                                        </p>
                                    </div>
                                </div>

                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    @if ($task->deleted_at)
                                        <span class="text-red-500">{{ __('tasks.status.deleted') }}</span>
                                    @else
                                        @if ($task->status === 'approved')
                                            <span
                                                class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">
                                                {{ __('tasks.status.completed') }}
                                            </span>
                                        @elseif ($task->status === 'submitted')
                                            <span
                                                class="px-2 py-1 font-semibold leading-tight text-indigo-700 bg-indigo-100 rounded-full">
                                                {{ __('tasks.status.submitted') }}
                                            </span>
                                        @elseif($task->status === 'in_progress')
                                            <span
                                                class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">
                                                {{ __('tasks.status.in_progress') }}
                                            </span>
                                        @elseif($task->due_date && $task->due_date < now()->startOfDay() && !in_array($task->status, ['submitted', 'approved']))
                                            <span
                                                class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">
                                                {{ __('tasks.status.overdue') }}
                                            </span>
                                        @else
                                            <span
                                                class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-gray-100 rounded-full">
                                                {{ __('tasks.status.pending') }}
                                            </span>
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
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                {{ $task->assignedBy->name ?? '-' }}
                            </td>
                            @can('task.restore-trashed')
                                @if (($withTrashed ?? false) || ($status ?? null) === 'deleted')
                                    <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                        <button wire:click.stop="restoreTask({{ $task->id }})"
                                            class="px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600">استعادة</button>
                                    </td>
                                @endif
                            @endcan
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ ($withTrashed ?? false) || ($status ?? null) === 'deleted' ? 6 : 5 }}"
                                class="px-5 py-5 bg-white border-b border-gray-200 text-sm text-center">
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

</div>
