<div class="flex flex-col">
    <div class="flex items-center justify-end mb-2">
        <button wire:click="toggleAssignedByMeOnly" class="px-4 py-2 rounded bg-blue-500 text-white hover:bg-blue-700 focus:outline-none">
            {{ $assignedByMeOnly ? __('tasks.need-approvals.by_others') : __('tasks.need-approvals.by_me') }}
        </button>
    </div>
    <div class="mt-6" wire:poll.10000ms>
        <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
            <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                {{ __('tasks.table.title') }}</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                {{ __('tasks.table.status') }}</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                {{ __('tasks.table.department') }}</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                {{ __('tasks.table.submitter') }}</th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                {{ __('tasks.table.assigned_by') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tasks as $task)
                            <tr class="bg-white hover:bg-gray-100 cursor-pointer"
                                @click="window.location.href = '{{ route('tasks.show', $task) }}'">
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <div class="flex items-center gap-2">
                                        <i
                                            class="fas fa-flag text-{{ $task->priority == 'low' ? 'gray' : ($task->priority == 'medium' ? 'yellow' : 'red') }}-500 fa-xs"></i>
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $task->title }}</p>
                                    </div>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        <x-task-status-badge :status="$task->status" :task="$task" />
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $task->user->department->name }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ $task->user->name }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200 text-sm">
                                    <p class="text-gray-900 whitespace-no-wrap">
                                        {{ optional($task->assignedBy)->name ?? '-' }}
                                    </p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6"
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
