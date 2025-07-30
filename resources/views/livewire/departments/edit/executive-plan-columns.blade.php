<div>
    <div class="flex flex-row justify-between items-center mt-4 p-4 m-3 border border-gray-300 rounded">
        <input type="text" wire:model="newColumnName" class="w-2/3 px-2 py-1 mx-3 border border-gray-300 rounded-md"
            placeholder="{{ __('departments.edit.tabs.executive_plan_columns.column_name') }}">
        <button wire:click="addColumn"
            class="flex-1 bg-green-800 text-white px-2 py-1 rounded-md hover:bg-green-700">{{ __('departments.edit.tabs.executive_plan_columns.add_column') }}</button>
    </div>

    @forelse($columns as $column)
        <div class="flex flex-row justify-between items-center mt-4">
            <div class="flex flex-row items-center">
                <div class="flex flex-row items-center gap-2 px-2">
                    <button wire:click="moveUp({{ $column->id }})"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-500 px-2 py-1 rounded-full">
                        <i class="fas fa-chevron-up"></i>
                    </button>
                    <button wire:click="moveDown({{ $column->id }})"
                        class="bg-gray-200 hover:bg-gray-300 text-gray-500 px-2 py-1 rounded-full">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <span class="ml-4">{{ $column->name }}</span>

                <button wire:click="removeColumn({{ $column->id }})"
                    class="text-red-500 px-2 py-1 text-xs rounded-md hover:bg-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

        </div>
    @empty
        <div class="text-center text-gray-500">{{ __('department.no_columns') }}</div>
    @endforelse
</div>
