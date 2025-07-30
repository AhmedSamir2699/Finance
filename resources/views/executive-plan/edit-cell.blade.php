<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto">
        <div class="flex flex-col bg-white py-2 px-4 rounded-md shadow-md border">
            <h4 class="text-xl font-semibold text-gray-900">{{ $cell->title }}</h4>
            <form id="edit-cell-form" action="{{ route('executive-plan.cell.update', [$cell->id]) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col gap-4 mt-4">
                    <div class="flex justify-between items-center gap-2">
                        <div class="flex-1">
                            <label for="column"
                                class="text-gray-700">{{ __('executive-plan.cell.edit.column_name') }}</label>
                            <input type="text" name="column" id="column" readonly disabled
                                value="{{ $cell->column->name }}"
                                class="bg-gray-300 block w-full rounded-md border border-gray-400 border-b block py-2 px-4 w-full bg-white text-sm placeholder-gray-400 text-gray-700">
                        </div>
                        <div class="flex-1">
                            <label for="date"
                                class="text-gray-700">{{ __('executive-plan.cell.edit.date') }}</label>
                            <input type="text" name="date" id="date" readonly disabled
                                value="{{ Carbon\Carbon::parse($cell->date)->format('d/m/Y') }}"
                                class="bg-gray-300 block w-full rounded-md border border-gray-400 border-b block py-2 px-4 w-full bg-white text-sm placeholder-gray-400 text-gray-700">
                        </div>
                    </div>
                    <div class="flex justify-between items-center gap-2">
                        <div class="flex-1">
                            <label for="value"
                                class="text-gray-700">{{ __('executive-plan.cell.edit.cell_name') }}</label>
                            <input type="text" name="value" id="value" value="{{ $cell->value }}"
                                class="rounded-md border border-gray-400 border-b block py-2 px-4 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                        </div>
                        
                    </div>
                    <div class="flex flex-col gap-2">
                        <label for="description" class="text-gray-700">{{ __('executive-plan.cell.edit.description') }}</label>
                        <x-text-editor name="description" :value="$cell->description ?? ''" />
                    </div>

                    <button type="submit"
                        class="block w-1/3 mx-auto bg-primary-base text-white rounded-md py-2 px-4 text-center">
                        {{ __('executive-plan.cell.edit.save') }}
                    </button>

            </form>
        </div>
    </div>

</x-app-layout>


