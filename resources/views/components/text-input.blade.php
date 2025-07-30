@props(['disabled' => false, 'required' => false, 'label' => false, 'for' => false])

<div>
    @if ($label)
        <label class="text-gray-700" for="{{ $for ?? '' }}">{{ $label }} {!! $required ? '<small class="text-red-500">*</small>' : '' !!} </label>
    @endif
    <input @disabled($disabled) id="{{ $for ?? '' }}"
        {{ $attributes->merge(['class' => 'form-input w-full mt-2 rounded-md focus:border-indigo-600']) }}
        {{ $required ? 'required' : '' }}>
</div>
