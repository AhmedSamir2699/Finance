@props(['priority'])

@php
    $classes = [
        'high' => 'bg-red-100 text-red-800',
        'medium' => 'bg-yellow-100 text-yellow-800',
        'low' => 'bg-gray-100 text-gray-800',
    ];
    $text = [
        'high' => __('tasks.priority.high'),
        'medium' => __('tasks.priority.medium'),
        'low' => __('tasks.priority.low'),
    ];
@endphp

<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $classes[$priority] ?? 'bg-gray-100 text-gray-800' }}">
    {{ $text[$priority] ?? $priority }}
</span> 