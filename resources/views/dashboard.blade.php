<x-app-layout>
    <div class="space-y-8">
        <!-- Top Row Widgets -->
        <div class="grid grid-cols-1 md:grid-cols-3 md:gap-6">
            <div class="col-span-2">
            <livewire:dashboard.user-info />
        </div>
        
        <livewire:dashboard.check-in-out />
            
        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Column: Tasks -->
            <div class="">
                <x-dashboard-table :title="__('dashboard.tasks.today')">
                    <x-slot name="header">
                        <th class="px-5 py-3">{{ __('tasks.title') }}</th>
                        <th class="px-5 py-3">{{ __('tasks.priority.label') }}</th>
                        <th class="px-5 py-3">{{ __('tasks.status.label') }}</th>
                    </x-slot>
                    @forelse($todaysTasks as $task)
                        <tr class="cursor-pointer hover:bg-gray-50" onclick="window.location.href = '{{ route('tasks.show', $task) }}'">
                            <td class="px-5 py-5">{{ $task->title }}</td>
                            <td class="px-5 py-5"><x-task-priority-badge :priority="$task->priority" /></td>
                            <td class="px-5 py-5"><x-task-status-badge :status="$task->status" :task="$task" /></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-5">{{ __('dashboard.no_tasks_today') }}</td>
                        </tr>
                    @endforelse
                </x-dashboard-table>
            </div>

            <!-- Right Column: Events -->
            <div class="">
                @can('executive-plan.view-self')
                    @if ($todaysEvents->count() > 0)
                        <x-dashboard-table :title="__('dashboard.events.today')">
                            <x-slot name="header">
                                
                            </x-slot>
                            @forelse($todaysEvents as $event)
                                <tr>
                                    <td class="px-5 py-5 text-start">{{ $event->id }}</td>
                                    <td class="px-5 py-5 text-start">{{ $event->column->name }}</td>
                                    <td class="px-5 py-5 text-start">{{ $event->value }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-5 py-5">{{ __('dashboard.no_events_today') }}</td>
                                </tr>
                            @endforelse
                        </x-dashboard-table>
                    @endif
                @endcan
            </div>
        </div>
    </div>
</x-app-layout>
