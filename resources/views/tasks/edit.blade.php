<x-app-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto">
        <div class="bg-white py-2 px-4 rounded-md shadow-md border" x-data="{ 
            type: '{{ old('type', $task->type) }}',
            taskDate: '{{ old('task_date', $task->task_date ? \Carbon\Carbon::parse($task->task_date)->format('Y-m-d') : '') }}', 
            dueDate: '{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '') }}',
            estimatedValue: '{{ old('estimated_time', $task->estimated_time) }}',
            estimatedUnit: 'minute',
            submitting: false,
            updateDueDate() {
                if (this.taskDate && this.estimatedValue && this.estimatedUnit) {
                    const taskDateObj = new Date(this.taskDate);
                    let estimatedMinutes = 0;
                    
                    switch(this.estimatedUnit) {
                        case 'minute':
                            estimatedMinutes = parseInt(this.estimatedValue);
                            break;
                        case 'hour':
                            estimatedMinutes = parseInt(this.estimatedValue) * 60;
                            break;
                        case 'day':
                            estimatedMinutes = parseInt(this.estimatedValue) * 60 * 24;
                            break;
                    }
                    
                    // Calculate new due date by adding estimated time to task date
                    const newDueDate = new Date(taskDateObj.getTime() + (estimatedMinutes * 60 * 1000));
                    
                    // Format the new due date
                    const year = newDueDate.getFullYear();
                    const month = String(newDueDate.getMonth() + 1).padStart(2, '0');
                    const day = String(newDueDate.getDate()).padStart(2, '0');
                    this.dueDate = `${year}-${month}-${day}`;
                }
            }
        }" x-init="updateDueDate()">
            <form action="{{ route('tasks.update', $task->id) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3" enctype="multipart/form-data" @submit="submitting = true">
                @csrf
                <input type="hidden" name="estimated_time" id="estimated_time_minutes" value="">
                <div class="mb-4">
                    <label for="title"
                        class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.title') }}</label>
                    <input type="text" name="title" id="title" placeholder="{{ __('calendar.event.title') }}"
                        required wire:model="title"
                        value="{{ old('title', $task->title) }}"
                        class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                </div>

                @can('task.assign')
                    <div class="mb-4">
                        <label for="assignee"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.assign') }}</label>
                        <select wire:model="assign" name="assignee" id="assignee"
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                            <option value="">{{ __('calendar.event.select_assignee') }}</option>
                            @foreach ($assignables as $user)
                                @if ($user->id !== auth()->id())
                                    <option value="{{ $user->id }}" {{ old('assignee', $task->user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                @endcan

                <div class="mb-4 grid grid-cols-2 gap-2">
                    <div>
                        <label for="priority"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.priority.label') }}</label>
                        <select wire:model="priority" name="priority" id="priority"
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                            <option value="low" {{ old('priority', $task->priority) == 'low' ? 'selected' : '' }}>{{ __('tasks.priority.low') }}</option>
                            <option value="medium" {{ old('priority', $task->priority) == 'medium' ? 'selected' : '' }}>{{ __('tasks.priority.medium') }}</option>
                            <option value="high" {{ old('priority', $task->priority) == 'high' ? 'selected' : '' }}>{{ __('tasks.priority.high') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="type"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.type') }}</label>
                        <select wire:model="type" name="type" id="type"
                            @change="type = $event.target.value"
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                            <option value="scheduled" {{ old('type', $task->type) == 'scheduled' ? 'selected' : '' }}>{{ __('tasks.create.scheduled') }}</option>
                            <option value="unscheduled" {{ old('type', $task->type) == 'unscheduled' ? 'selected' : '' }}>{{ __('tasks.create.unscheduled') }}</option>
                            <option value="continous" {{ old('type', $task->type) == 'continous' ? 'selected' : '' }}>{{ __('tasks.create.continous') }}</option>
                            <option value="training" {{ old('type', $task->type) == 'training' ? 'selected' : '' }}>{{ __('tasks.create.training') }}</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4 grid gap-2 grid-cols-2">
                    <div>
                        <label for="estimated_time_value"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.estimated_time') }}</label>
                        <input type="number" min="0"
                            x-model="estimatedValue"
                            @input="updateDueDate()"
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"
                            name="estimated_time_value" id="estimated_time_value" required>
                    </div>
                    <div>
                        <label for="estimated_time_unit"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.estimated_time_unit') }}</label>
                        <select name="estimated_time_unit" id="estimated_time_unit"
                            x-model="estimatedUnit"
                            @change="updateDueDate()"
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                            <option value="minute">{{ __('tasks.create.minute') }}</option>
                            <option value="hour">{{ __('tasks.create.hour') }}</option>
                            <option value="day">{{ __('tasks.create.day') }}</option>
                        </select>
                    </div>
                </div>

                    <div>
                        <label for="task_date"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.task_date') }}</label>
                            <input type="text" name="task_date" id="task_date" 
                            x-model="taskDate"
                        @input="updateDueDate()"
                            class="appearance-none rounded border border-gray-400 block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                    </div>
                <div>
                        <label for="due_date"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.due_date') }}</label>
                            <input type="text" name="due_date" id="due_date" 
                            x-model="dueDate"
                            class="appearance-none rounded border border-gray-400 block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                </div>

                <livewire:messages.upload-attachments :task="$task" />

                <div class="col-span-2" x-data="{ descriptionAccordion: true }">
                    <label for="description" @click="descriptionAccordion = !descriptionAccordion"
                        class="block text-primary-base  mx-auto  py-2 cursor-pointer">
                        {{ __('tasks.create.description') }}
                        <i class="fas fa-chevron-left" x-show="!descriptionAccordion" x-cloak></i>
                        <i class="fas fa-chevron-down" x-show="descriptionAccordion" x-cloak></i>
                    </label>

                    <div x-show="descriptionAccordion">
                        <x-text-editor name="description" class="mt-3" :value="$task->description ?? ''" />
                    </div>
                </div>

                <div class="mb-4 text-center col-span-2">
                    <button type="submit"
                        class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-8 rounded focus:outline-none focus:shadow-outline"
                        :disabled="submitting">
                        <span x-show="!submitting">{{ __('tasks.edit.save') }}</span>
                        <span x-show="submitting">{{ __('tasks.create.saving') ?? 'Saving...' }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        flatpickr("#due_date", {
            dateFormat: "Y-m-d"
        });
        flatpickr("#task_date", {
            dateFormat: "Y-m-d"
        });
    </script>
</x-app-layout> 