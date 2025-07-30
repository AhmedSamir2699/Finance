<x-app-layout>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto">
        <div class="bg-white py-2 px-4 rounded-md shadow-md border" x-data="{ 
            type: '{{ $executivePlanData ? $executivePlanData['type'] : ($clonedTask ? $clonedTask->type : 'scheduled') }}',
            taskDate: '{{ $executivePlanData ? \Carbon\Carbon::parse($executivePlanData['task_date'])->format('Y-m-d') : ($clonedTask ? $clonedTask->formatted_task_date : Carbon\Carbon::now()->format('Y-m-d')) }}', 
            dueDate: '{{ $executivePlanData ? \Carbon\Carbon::parse($executivePlanData['due_date'])->format('Y-m-d') : ($clonedTask ? $clonedTask->formatted_due_date : Carbon\Carbon::now()->format('Y-m-d')) }}',
            estimatedValue: '{{ $clonedTask ? $clonedTask->estimated_time : '10' }}',
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
            <form action="{{ route('tasks.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 gap-3"
                enctype="multipart/form-data" @submit="submitting = true; convertEstimatedTime()">
                @csrf
                <input type="hidden" name="add_more" :value="addMore">
                @if ($clonedTask)
                    <input type="hidden" name="cloned_task_id" value="{{ $clonedTask->id }}">
                @endif
                <input type="hidden" name="estimated_time" id="estimated_time_minutes" value="">
                <div class="mb-4">
                    <label for="title"
                        class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.title') }}</label>
                    <input type="text" name="title" id="title" placeholder="{{ __('calendar.event.title') }}"
                        required wire:model="title"
                        maxlength="50"
                        @change=""
                        value="{{ $executivePlanData ? $executivePlanData['title'] : ($clonedTask ? $clonedTask->title : '') }}"
                        class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                </div>

                @can('task.assign')
                    <div class="mb-4">
                        <label for="assignees"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.assign') }}</label>
                        <select name="assignees[]" id="assignees" multiple
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                            <option value="" disabled>{{ __('calendar.event.select_assignee') }}</option>
                            @foreach ($assignables as $user)
                                @if ($user->id !== auth()->id() || $executivePlanData)
                                    <option value="{{ $user->id }}"
                                        {{ ($executivePlanData && $executivePlanData['assignee'] == $user->id) || ($clonedTask && $clonedTask->user_id == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
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
                            <option value="low"
                                {{ $clonedTask && $clonedTask->priority == 'low' ? 'selected' : '' }}>
                                {{ __('tasks.priority.low') }}</option>
                            <option value="medium"
                                {{ $clonedTask && $clonedTask->priority == 'medium' ? 'selected' : '' }}>
                                {{ __('tasks.priority.medium') }}</option>
                            <option value="high"
                                {{ $clonedTask && $clonedTask->priority == 'high' ? 'selected' : '' }}>
                                {{ __('tasks.priority.high') }}</option>
                        </select>
                    </div>

                    <div>
                        <label for="type"
                            class="block text-sm font-medium text-slate-700 py-2">{{ __('tasks.create.type') }}</label>
                        <select wire:model="type" name="type" id="type" @change="type = $event.target.value"
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                            <option value="scheduled"
                                {{ ($executivePlanData && $executivePlanData['type'] == 'scheduled') || ($clonedTask && $clonedTask->type == 'scheduled') ? 'selected' : '' }}>
                                {{ __('tasks.create.scheduled') }}</option>
                            <option value="unscheduled"
                                {{ $clonedTask && $clonedTask->type == 'unscheduled' ? 'selected' : '' }}>
                                {{ __('tasks.create.unscheduled') }}</option>
                            <option value="continous"
                                {{ $clonedTask && $clonedTask->type == 'continous' ? 'selected' : '' }}>
                                {{ __('tasks.create.continous') }}</option>
                            <option value="training"
                                {{ $clonedTask && $clonedTask->type == 'training' ? 'selected' : '' }}>
                                {{ __('tasks.create.training') }}</option>
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
                            <option value="minute" :selected="estimatedUnit === 'minute'">{{ __('tasks.create.minute') }}</option>
                            <option value="hour" :selected="estimatedUnit === 'hour'">{{ __('tasks.create.hour') }}</option>
                            <option value="day" :selected="estimatedUnit === 'day'">{{ __('tasks.create.day') }}</option>
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

                <livewire:messages.upload-attachments />

                @if ($clonedTask && $clonedTask->attachments->count() > 0)
                    <div class="col-span-2 mb-4" x-data="{ showAttachments: true }">
                        <label class="block text-sm font-medium text-slate-700 py-2 cursor-pointer"
                            @click="showAttachments = !showAttachments">
                            {{ __('tasks.attachments') }} ({{ $clonedTask->attachments->count() }})
                            <i class="fas fa-chevron-down" x-show="showAttachments" x-cloak></i>
                            <i class="fas fa-chevron-left" x-show="!showAttachments" x-cloak></i>
                        </label>
                        <div x-show="showAttachments" class="bg-gray-50 p-4 rounded-md border">
                            <div class="flex justify-between items-center mb-3">
                                <p class="text-sm text-gray-600">{{ __('tasks.clone.attachments_note') }}</p>
                                <div class="flex space-x-2">
                                    <button type="button"
                                        @click="$el.closest('.bg-gray-50').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = true)"
                                        class="text-xs text-blue-600 hover:text-blue-800 underline">
                                        {{ __('tasks.clone.select_all') }}
                                    </button>
                                    <button type="button"
                                        @click="$el.closest('.bg-gray-50').querySelectorAll('input[type=checkbox]').forEach(cb => cb.checked = false)"
                                        class="text-xs text-red-600 hover:text-red-800 underline">
                                        {{ __('tasks.clone.deselect_all') }}
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                @foreach ($clonedTask->attachments as $attachment)
                                    <div
                                        class="flex items-center justify-between p-2 bg-white rounded border hover:bg-gray-50">
                                        <div class="flex items-center space-x-2 flex-1 min-w-0">
                                            <i class="fas fa-{{ $attachment->icon }} text-gray-400 flex-shrink-0"></i>
                                            <span class="text-sm truncate" title="{{ $attachment->name }}">
                                                {{ $attachment->name }}
                                            </span>
                                        </div>
                                        <label class="flex items-center space-x-2 cursor-pointer">
                                            <input type="checkbox" name="clone_attachments[]"
                                                value="{{ $attachment->id }}" checked
                                                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                            <span class="text-xs text-gray-500">{{ __('tasks.clone.include') }}</span>
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                <div class="mb-4 col-span-2">
                    <div class="col-span-2" x-data="{ descriptionAccordion: true }">
                        <label for="description" @click="descriptionAccordion = !descriptionAccordion"
                            class="block text-primary-base  mx-auto  py-2 cursor-pointer">
                            {{ __('tasks.create.description') }}
                            <i class="fas fa-chevron-left" x-show="!descriptionAccordion" x-cloak></i>
                            <i class="fas fa-chevron-down" x-show="descriptionAccordion" x-cloak></i>
                        </label>

                        <div x-show="descriptionAccordion">
                            <x-text-editor required name="description" class="mt-3" value="{!! isset($executivePlanData)
                                ? $executivePlanData['description']
                                : ($clonedTask
                                    ? $clonedTask->description
                                    : '') !!}" />
                        </div>
                    </div>

                    <div class="mb-4 text-center col-span-2 flex flex-col md:flex-row justify-center gap-2">
                        <button type="submit"
                            class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-8 rounded focus:outline-none focus:shadow-outline"
                            :disabled="submitting">
                            <span
                                x-show="!submitting">{{ $clonedTask ? __('tasks.clone.title') : __('tasks.create.create') }}</span>
                            <span x-show="submitting">{{ __('tasks.create.saving') ?? 'Saving...' }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script>
        flatpickr("#due_date", {
            dateFormat: "Y-m-d" // Enforce YYYY-MM-DD format
        });
        flatpickr("#task_date", {
            dateFormat: "Y-m-d" // Enforce YYYY-MM-DD format
        });

        function convertEstimatedTime() {
            const value = document.getElementById('estimated_time_value').value;
            const unit = document.getElementById('estimated_time_unit').value;
            let minutes = 0;

            switch (unit) {
                case 'minute':
                    minutes = parseInt(value);
                    break;
                case 'hour':
                    minutes = parseInt(value) * 60;
                    break;
                case 'day':
                    minutes = parseInt(value) * 60 * 24;
                    break;
            }

            document.getElementById('estimated_time_minutes').value = minutes;
        }

        function updateDueDate() {
            const taskDate = document.getElementById('task_date').value;
            const estimatedValue = document.getElementById('estimated_time_value').value;
            const estimatedUnit = document.getElementById('estimated_time_unit').value;
            
            if (taskDate && estimatedValue && estimatedUnit) {
                const taskDateObj = new Date(taskDate);
                let estimatedMinutes = 0;
                
                switch(estimatedUnit) {
                    case 'minute':
                        estimatedMinutes = parseInt(estimatedValue);
                        break;
                    case 'hour':
                        estimatedMinutes = parseInt(estimatedValue) * 60;
                        break;
                    case 'day':
                        estimatedMinutes = parseInt(estimatedValue) * 60 * 24;
                        break;
                }
                
                // Calculate new due date by adding estimated time to task date
                const newDueDate = new Date(taskDateObj.getTime() + (estimatedMinutes * 60 * 1000));
                
                // Format the new due date
                const year = newDueDate.getFullYear();
                const month = String(newDueDate.getMonth() + 1).padStart(2, '0');
                const day = String(newDueDate.getDate()).padStart(2, '0');
                const formattedDueDate = `${year}-${month}-${day}`;
                
                // Update the due date field
                document.getElementById('due_date').value = formattedDueDate;
                
                // Update Alpine.js data
                if (window.Alpine) {
                    const component = Alpine.$data(document.querySelector('[x-data]'));
                    if (component) {
                        component.dueDate = formattedDueDate;
                    }
                }
            }
        }
    </script>
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let $assignees = $('#assignees');
            if ($assignees.length) {
                try {
                    $assignees.select2('destroy');
                } catch (e) {}
                $assignees.select2({
                    placeholder: "{{ __('calendar.event.select_assignee') }}",
                    width: '100%',
                    allowClear: true,
                    closeOnSelect: false
                });

                // Clear search text after selecting an item
                $assignees.on('select2:select', function(e) {
                    let $searchfield = $(this).data('select2').dropdown.$search || $(this).data('select2')
                        .selection.$search;
                    if ($searchfield && $searchfield.length) {
                        $searchfield.val('');
                    }
                });
            }
        });
    </script>
    <style>
        .select2-container--default .select2-selection--multiple {
            background-color: #fff;
            border-radius: 0.375rem;
            border: 1px solid #cbd5e1;
            min-height: 2.5rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #3b82f6;
            color: #fff;
            border: none;
            border-radius: 0.375rem;
            padding-left: 1.2em !important;
            padding-right: 1.2em !important;
            margin-top: 0.25rem;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #fff !important;
            border-radius: 50%;
            margin-right: 0.5rem;
            font-weight: bold;
            font-size: 1.1em;
            padding: 0 0.4em;
            transition: background 0.2s;
            border-left: none;
            border-right: none;
            border-top-left-radius: 0;
            border-bottom-left-radius: 0;
            border-top-right-radius: 4px;
            margin-left: 0;
            border-bottom-right-radius: 4px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background: #dc2626 !important;
            color: #fff !important;
        }
    </style>

</x-app-layout>
