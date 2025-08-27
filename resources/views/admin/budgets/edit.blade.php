<x-app-layout>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto">
        <form method="POST" action="{{ route('finance-items.update',$budget) }}" enctype="multipart/form-data"
            class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-white p-6 rounded-md shadow-md">
            @csrf

            <div class="col-span-1">
                <label for="parent_id" class="block text-sm font-medium text-slate-700 py-2">{{ trans('cruds.budgets.fields.parent') }}</label>
                <select class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('parent_id') border-red-500 @enderror" name="parent_id"
                    id="parent_id">
                    @foreach ($financeItems as $id => $entry)
                        <option value="{{ $id }}" {{ old('finance_item_id',$budget->finance_item_id) == $id ? 'selected' : '' }}>
                            {{ $entry }}</option>
                    @endforeach
                </select>
                @if ($errors->has('parent'))
                    <div class="invalid-feedback">
                        {{ $errors->first('parent') }}
                    </div>
                @endif
                <span class="help-block">{{ trans('cruds.budgets.fields.parent_helper') }}</span>
            </div>

            {{-- Goal --}}
            <div class="col-span-1">
                <label for="goal" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.budgets.fields.goal') }} <span class="text-red-500">*</span>
                </label>
                <input type="number" name="goal" id="goal" step="0.01"
                    class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('goal') border-red-500 @enderror"
                    value="{{ old('goal', $budget->goal) }}"
                    required>
                @error('goal')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.budgets.fields.goal_helper') }}</p>
            </div>

            {{-- Amount --}}
            <div class="col-span-1">
                <label for="amount" class="block text-sm font-medium text-slate-700 py-2">
                    {{ trans('cruds.budgets.fields.amount') }} <span class="text-red-500">*</span>
                </label>
                <input type="number" name="amount" id="amount" step="0.01"
                    class="appearance-none rounded border border-gray-300 px-4 py-2 w-full text-sm focus:outline-none focus:ring-2 focus:ring-primary-500 @error('amount') border-red-500 @enderror"
                    value="{{ old('amount', $budget->amount) }}"
                    required>
                @error('amount')
                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">{{ trans('cruds.budgets.fields.amount_helper') }}</p>
            </div>
            
            
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
