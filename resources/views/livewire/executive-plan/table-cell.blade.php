<td class="text-gray-900 border-r border-b border-gray-400 group-hover:bg-primary-50 group-hover:border-gray-50 transition-all ease-in-out duration-150"
    x-data="{ CellModal: false, CellModalNew{{ $date->format('ymd') }}: false }" x-on:dblclick="@if($cell) window.open('{{ route('executive-plan.cell.show', $cell->id) }}', '_blank') @else CellModal = true @endif">

    @if ($cell)
        <div class="flex flex-row gap-2 items-center cursor-pointer">

            {{-- <a href="{{ route('executive-plan.cell.show', $cell->id) }}" class="flex-1 text-gray-900 whitespace-no-wrap">
                <i class="fas fa-eye text-primary-base"></i>
                
            </a> --}}
            <span class="text-md text-primary-500 text-wrap break-words block w-full text-center"
                style="text-break: word-wrap; text-overflow: ellipsis;   max-height: 90px; overflow: hidden;"
            >
                {{ $cell->value }}

            </span>

            @if ($isEditable && $showEditControls)
                <div x-data="{ showDeleteModal: false, cellIdToDelete: null }">
    
                    <!-- Delete Button -->
                    <button type="button"
                        x-on:click.stop="showDeleteModal = true; cellIdToDelete = '{{ $cell->id }}'"
                        class="text-red-400 hover:text-red-700 focus:outline-none focus:ring focus:ring-red-500">
                        <i class="fas fa-trash"></i>
                    </button>

                    <!-- Confirmation Modal -->
                    <div x-show="showDeleteModal" style="display: none;"
                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                        <div class="bg-white rounded-lg p-6 w-96" @click.away="showDeleteModal = false">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('executive-plan.delete_cell') }}</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                {{ __('executive-plan.delete_cell_confirmation') }}
                            </p>
                            <div class="mt-4 flex justify-end space-x-3">
                                <!-- Confirm Button -->
                                <button type="button"
                                    x-on:click.stop="showDeleteModal = false; $wire.deleteCell(cellIdToDelete)"
                                    class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-md hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-500">
                                    {{ __('executive-plan.delete') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @else

        @if ($isEditable && $showEditControls)
        @php
            $key = $date->format('ymd') . '.' . $column->id;
        @endphp
        <div wire:key="{{ $key }}" autocomplete="off" x-data="{ inputValue: '', hasSaved: false }">
                <input type="text" list="autocomplete-list-{{ $key }}"
                    class="block w-full py-5 bg-transparent border-0 text-center focus:border-1 focus:bg-white focus:ring focus:ring-1 focus:ring-primary-100"
                {{ $disabled }} x-model="inputValue"
                x-on:keyup.enter.debounce.150ms="if (!hasSaved) { $wire.saveNewCellData(inputValue, '{{ $date->format('Y-m-d') }}', '{{ $column->id }}'); hasSaved = true; }"
                x-on:input="$wire.fetchSuggestions(inputValue)" wire:ignore
                x-on:blur="if (!hasSaved) { $wire.saveNewCellData(inputValue, '{{ $date->format('Y-m-d') }}', '{{ $column->id }}'); hasSaved = true; }">

                @if ($suggestions)
                    <datalist id="autocomplete-list-{{ $key }}">
                        @foreach ($suggestions as $suggestion)
                            <option value="{{ $suggestion }}">
                                {{ $suggestion }}
                            </option>
                        @endforeach
                    </datalist>
                @endif
            </div>
        @endif


    @endif

    @if ($isEditable && $showEditControls)
        @if ($cell)
            <!-- Modal for existing cell -->
            <x-modal name="CellModal" class="hidden" :key="'CellModal-' . $cell->id">
                <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                    {{ __('executive-plan.add_cell') }}
                </div>
                <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                    <form wire:submit.prevent="saveCellData('{{ $cell->id }}')">
                        @csrf
                        <div class="mb-4">
                            <label for="value-{{ $cell->id }}"
                                class="block text-sm font-medium text-slate-700">{{ __('executive-plan.cell.value') }}</label>
                            <input type="text" name="name" id="value-{{ $cell->id }}"
                                wire:model="cellData.{{ $cell->id }}"
                                placeholder="{{ __('executive-plan.cell.value') }}" required
                                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                        </div>
                        <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
                            <button
                                class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
                                type="submit" wire:loading.attr="disabled" @click="CellModal = false">
                                {{ __('executive-plan.save') }}
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endif
    @elseif(!$isEditable || !$showEditControls)
        <!-- Read-only modal -->
        <x-modal name="CellModal" class="hidden">
            <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                {{ __('executive-plan.cell.value') }}
            </div>
            <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                <div class="mb-4">
                    <span class="text-md text-gray-500 text-wrap break-words block w-full text-center">
                        {{ $cell?->value }}
                    </span>
                </div>
            </div>
        </x-modal>
    @endif
</td>
