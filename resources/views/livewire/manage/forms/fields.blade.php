<div x-data="{ addFieldModal: false, openedAccordion: null }">
    <button class="my-4 bg-primary-600 text-white py-1 px-4 rounded hover:text-secondary-base" @click="addFieldModal = true">
        {{ __('manage.forms.fields.add_button') }}
    </button>
    <div class="flex flex-col px-4">
        <!-- Button to add a new field -->

        <div wire:sortable="updateFieldOrder">
            @forelse ($fields as $index => $formField)
                <div wire:sortable.item="{{ $formField['id'] }}" wire:key="field-{{ $formField['id'] }}"
                    class="flex flex-col border border-gray-200 bg-white shadow rounded my-2 overflow-hidden">
                    <!-- Header Section (Drag Handle, Field Name, Type, Actions) -->
                    <div class="flex justify-between items-center py-2 px-4 gap-3">

                        <div class="flex items-center gap-3 cursor-move mx-1 flex-0" wire:sortable.handle>
                            <i class="fas fa-grip-lines text-gray-400 cursor-move"></i>
                            <p class="text-sm text-gray-500 mx-3">{{ __('manage.forms.fieldstypes.' . $formField['type']) }}</p>
                            <strong class="text-lg font-semibold text-gray-700 mx-3">{{ $formField['name'] }}</strong>
                        </div>
                        <div class="flex flex-1">
                            @if (count(json_decode($formField['options'])))  
                            <button @click="$refs.options{{ $formField['id'] }}.classList.toggle('hidden'); openedAccordion = openedAccordion == {{ $formField['id'] }} ? null : {{ $formField['id'] }}"
                                class="text-gray-500 hover:text-gray-700">
                                
                                <i class="fas fa-caret-down" x-show="openedAccordion == {{ $formField['id'] }}"></i>
                                <i class="fas fa-caret-left" x-show="openedAccordion != {{ $formField['id'] }}"></i>

                            </button>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2 gap-3 justify-between flex-0">
                            <button class="text-blue-500 hover:text-blue-700"
                                wire:click="editField({{ $index }})">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="text-red-500 hover:text-red-700"
                                wire:click="deleteField({{ $formField['id'] }})">
                                <i class="fas fa-trash"></i>
                            </button>

                        </div>
                    </div>
                    <!-- Expandable Options Section (if has_options) -->
                    @if (count(json_decode($formField['options'])))
                        <div x-ref="options{{ $formField['id'] }}" class="hidden px-4 pb-4 bg-gray-50">
                            <div class="space-y-2">
                                @foreach (json_decode($formField['options']) as $option)
                                    <div class="flex items-center space-x-2">
                                        <i class="text-gray-500 mx-2">-</i>
                                        <p>{{ $option }}</p>
                                    </div>
                                @endforeach

                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-sm text-gray-500 text-center py-4">{{ __('No fields found.') }}</div>
            @endforelse
        </div>



    </div>

    <x-modal name="addFieldModal">
        <form wire:submit.prevent="storeField">
            @csrf
            <div class="bg-white px-4 py-3  sm:px-6">
                <div class="grid grid-cols-1 gap-6">
                    <div class="col-span-6 sm:col-span-4">
                        <label for="name"
                            class="block text-sm font-medium text-gray-700">{{ __('manage.forms.fields.name') }}</label>
                        <input type="text" name="name" id="name" autocomplete="name" wire:model="field.name"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <label for="type"
                            class="block text-sm font-medium text-gray-700">{{ __('manage.forms.fields.type') }}</label>
                        <select id="type" name="type" autocomplete="type" wire:model="field.type"
                            wire:change.live="changeFieldType"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">

                            @foreach ($fieldTypes as $type => $options)
                                <option value="{{ $type }}">{{ __('manage.forms.fieldstypes.' . $type) }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    @if ($hasOptions)
                        @if ($field['type'] == 'select')
                            <div class="col-span-6 sm:col-span-4">
                                <label for="options" class="block text-sm font-medium text-gray-700">
                                    {{ __('manage.forms.fields.options') }}
                                </label>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach ($field['options'] as $key => $option)
                                    <div class="col-span-6 sm:col-span-4 flex items-center justify-between">
                                            <input type="text" name="options.{{ $key }}"
                                                id="options.{{ $key }}"
                                                autocomplete="options.{{ $key }}"
                                                wire:model="field.options.{{ $key }}"
                                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <button type="button"
                                                wire:click.prevent="removeOption({{ $key }})"
                                                class="text-white hover:text-red-300 bg-red-800 px-2 mx-2 rounded">&times;</button>


                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click.prevent="addOption"
                                    class="mt-2 text-blue-600 hover:text-blue-800">
                                    + {{ __('manage.forms.fields.add_option') }}
                                </button>
                            </div>
                        @elseif($field['type'] == 'radio' || $field['type'] == 'checkbox')
                            <div class="col-span-6 sm:col-span-4">
                                <label for="options" class="block text-sm font-medium text-gray-700">
                                    {{ __('manage.forms.fields.options') }}
                                </label>
                                <div class="grid grid-cols-1 gap-3">
                                    @foreach ($field['options'] as $key => $option)
                                        <div class="col-span-6 sm:col-span-4 flex items-center justify-between">
                                            <input type="text" name="options.{{ $key }}"
                                                id="options.{{ $key }}"
                                                autocomplete="options.{{ $key }}"
                                                wire:model="field.options.{{ $key }}"
                                                class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <button type="button"
                                                wire:click.prevent="removeOption({{ $key }})"
                                                class="text-white hover:text-red-300 bg-red-800 px-2 mx-2 rounded">&times;</button>

                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click.prevent="addOption"
                                    class="mt-2 text-blue-600 hover:text-blue-800">
                                    + {{ __('manage.forms.fields.add_option') }}
                                </button>
                            </div>
                        @endif
                    @endif



                    <div class="col-span-6 sm:col-span-4">
                        <label for="required"
                            class="block text-sm font-medium text-gray-700">{{ __('manage.forms.fields.required') }}</label>
                        <input type="checkbox" name="required" id="required" autocomplete="required"
                            wire:model="field.required"
                            class="mt-1 focus:ring-primary-500 focus:border-primary-500  shadow-sm sm:text-sm border-gray-300 rounded-md">
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{ __('manage.forms.fields.submit') }}
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </x-modal>
</div>
