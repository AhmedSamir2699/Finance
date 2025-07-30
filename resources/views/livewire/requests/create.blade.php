<div>
    @if (!$selectedForm)

        <div class="flex flex-col py-5">
            <select wire:model.live="selectedCategory" id="category"
                class="w-full md:w-1/2 p-2 border border-gray-300 rounded-md px-12">
                <option value="">{{ __('requests.create.select_category') }}</option>
                @foreach ($categories as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>


        @if ($selectedCategory)
            {{-- Request Forms table --}}
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-3">

                @foreach ($forms as $form)
                    <div
                        class="flex flex-col gap-4 justify-between items-center border border-primary-base rounded bg-white py-3">
                        <h3 class="text-xl py-2">{{ $form->title }}</h3>
                        <h4>{{ $form->description }}</h4>
                        <button wire:click="selectForm({{ $form->id }})"
                            class="bg-primary-500 px-8 text-white py-1 rounded">
                            {{ __('requests.create.start_new') }}
                        </button>
                    </div>
                @endforeach

            </div>
        @endif

    @endif

    @if ($selectedForm)
        <div class="flex flex-col gap-4 p-5 my-5 rounded bg-white border border-primary-base">
            <h3 class="text-2xl">{{ $selectedForm->category->name .' - '. $selectedForm->title }}</h3>
            <h4 class="text-xl">{{ $selectedForm->description }}</h4>
            <hr class="my-5">
            <form action="{{ route('requests.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <input type="hidden" name="form_id" value="{{ $selectedForm->id }}">
                @foreach ($selectedForm->fields as $field)
                    <div class="flex flex-col gap-2">
                        <label for="{{ $field->name }}">{{ $field->name }}</label>
                        @if ($field->type === 'text')
                            <input type="text" name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'textarea')
                            <textarea name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2" @if ($field->is_required) required @endif></textarea>
                        @elseif ($field->type === 'select')
                            <select name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2 text-center"
                                @if ($field->is_required) required @endif>
                                @foreach (json_decode($field->options) as $option)
                                    <option value="{{ $option }}">{{ $option }}</option>
                                @endforeach
                            </select>
                        @elseif ($field->type === 'radio')
                            @foreach ($field->options as $option)
                                <label for="{{ $field->name }}_{{ $option }}">{{ $option }}</label>
                                <input type="radio" name="{{ $field->name }}"
                                    id="{{ $field->name }}_{{ $option }}" value="{{ $option }}"
                                    @if ($field->is_required) required @endif>
                            @endforeach
                        @elseif ($field->type === 'checkbox')
                            @foreach ($field->options as $option)
                                <label for="{{ $field->name }}_{{ $option }}">{{ $option }}</label>
                                <input type="checkbox" name="{{ $field->name }}"
                                    id="{{ $field->name }}_{{ $option }}" value="{{ $option }}"
                                    @if ($field->is_required) required @endif>
                            @endforeach
                        @elseif ($field->type === 'file')
                            <input type="file" name="files[{{ $field->name }}][]" multiple id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'date')
                            <input type="date" name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'time')
                            <input type="time" name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'datetime')
                            <input type="datetime-local" name="{{ $field->name }}"
                                id="{{ $field->name }}" class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'number')
                            <input type="number" name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'email')
                            <input type="email" name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'tel')
                            <input type="tel" name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'url')
                            <input type="url" name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @elseif ($field->type === 'color')
                            <input type="color" name="{{ $field->name }}" id="{{ $field->name }}"
                                class="border border-gray-300 rounded-md p-2"
                                @if ($field->is_required) required @endif>
                        @endif

                    </div>
                @endforeach
                <div class="col-span-2 text-center py-5">
                    <button type="submit" class="bg-primary-500 px-12 text-white py-1 rounded mx-auto ">
                        {{ __('requests.create.submit') }}
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
