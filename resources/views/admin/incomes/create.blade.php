<x-app-layout>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto">
        {{-- <div class="bg-white py-2 px-4 rounded-md shadow-md border" x-data="{ 
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
        }" x-init="updateDueDate()"> --}}
        <form method="POST" action="{{ route('incomes.store') }}" enctype="multipart/form-data"
            class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-6 rounded-md shadow-md">
            @csrf

            {{-- Income Category --}}
            <div class="col-span-2">
                <label for="income_category_id" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.income_category') }}
                </label>
                <select name="income_category_id" id="income_category_id"
                    class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('income_category') border-red-500 @enderror">
                    @foreach ($income_categories as $id => $entry)
                        <option value="{{ $id }}" {{ old('income_category_id') == $id ? 'selected' : '' }}>
                            {{ $entry }}</option>
                    @endforeach
                </select>
                @error('income_category')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.income_category_helper') }}</p>
            </div>

            {{-- Entry Date --}}
            <div class="col-span-1">
                <label for="entry_date" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.entry_date') }} <span class="text-red-500">*</span>
                </label>
                <input type="date" name="entry_date" id="entry_date" value="{{ old('entry_date') }}"
                    class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('entry_date') border-red-500 @enderror"
                    required>
                @error('entry_date')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.entry_date_helper') }}</p>
            </div>

            {{-- Amount --}}
            <div class="col-span-1">
                <label for="amount" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.amount') }} <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" step="0.01" value="{{ old('amount', '') }}"
                    class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('amount') border-red-500 @enderror"
                    required>
                @error('amount')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.amount_helper') }}</p>
            </div>

            {{-- Allocations --}}
            <div class="col-span-2">
                <label class="block text-sm font-medium text-slate-700 py-2">
                    {{ __('Allocate by percentage (must total 100%)') }}
                </label>

                <div x-data="allocationsComponent()" class="space-y-3">
                    <template x-for="(row, idx) in rows" :key="row.key">
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-2 items-center">
                            <div class="md:col-span-7">
                                <select :name="`allocations[${idx}][finance_item_id]`"
                                    class="rounded border border-gray-300 px-3 py-2 w-full">
                                    <option value="">{{ __('Select finance item') }}</option>
                                    @foreach ($finance_items as $id => $entry)
                                        <option value="{{ $id }}"
                                            x-bind:selected="row.finance_item_id == {{ $id }}">
                                            {{ $entry }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-3">
                                <input type="number" step="0.001" min="0"
                                    :name="`allocations[${idx}][percentage]`" x-model.number="row.percentage"
                                    @input="recalc()" class="rounded border border-gray-300 px-3 py-2 w-full"
                                    placeholder="%">
                            </div>
                            <div class="md:col-span-2 text-right">
                                <button type="button" @click="remove(idx)"
                                    class="text-red-600 text-sm font-medium hover:underline">
                                    {{ __('Remove') }}
                                </button>
                            </div>
                        </div>
                    </template>

                    <div class="flex items-center justify-between">
                        <button type="button" @click="add()"
                            class="bg-slate-100 hover:bg-slate-200 text-slate-800 text-sm font-semibold py-1.5 px-3 rounded">
                            + {{ __('Add allocation') }}
                        </button>
                        <div class="text-sm">
                            <span class="font-medium">{{ __('Total') }}:</span>
                            <span x-text="total.toFixed(3)"></span>%
                            <span x-show="Math.abs(total - 100) > 0.01" class="text-red-600">
                                ({{ __('must be 100%') }})
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description --}}
            <div class="col-span-2">
                <label for="description" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.description') }}
                </label>
                <input type="text" name="description" id="description" value="{{ old('description', '') }}"
                    class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('description') border-red-500 @enderror">
                @error('description')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.description_helper') }}</p>
            </div>

            {{-- Finance Item --}}
            {{-- <div class="col-span-2">
                <label for="finance_item_id" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.income.fields.finance_item') }}
                </label>
                <select name="finance_item_id" id="finance_item_id"
                    class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('finance_item') border-red-500 @enderror">
                    @foreach ($finance_items as $id => $entry)
                        <option value="{{ $id }}" {{ old('finance_item_id') == $id ? 'selected' : '' }}>
                            {{ $entry }}</option>
                    @endforeach
                </select>
                @error('finance_item')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.income.fields.finance_item_helper') }}</p>
            </div> --}}

            {{-- Submit Button --}}
            <div class="col-span-2 flex justify-end mt-4">
                <button type="submit"
                    class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-6 rounded focus:outline-none focus:ring focus:ring-primary-300">
                    {{ trans('global.save') }}
                </button>
            </div>
        </form>
    </div>
    </div>
    <script>
        function allocationsComponent() {
            return {
                rows: [
                    // Prefill from old() if validation fails, or from $income->allocations when editing
                    // Example default one row:
                    {
                        key: Date.now(),
                        finance_item_id: '',
                        percentage: 100.000
                    },
                ],
                total: 100.000,
                add() {
                    this.rows.push({
                        key: Date.now() + Math.random(),
                        finance_item_id: '',
                        percentage: 0.000
                    });
                    this.recalc();
                },
                remove(i) {
                    this.rows.splice(i, 1);
                    this.recalc();
                },
                recalc() {
                    this.total = this.rows.reduce((s, r) => s + (parseFloat(r.percentage) || 0), 0);
                }
            }
        }

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

                switch (estimatedUnit) {
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
