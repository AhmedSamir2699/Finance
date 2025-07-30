@props(['active' => false, 'isDropdown' => false, 'dropdownItems' => [], 'badgeCount' => null])

@php
    $classes = $active
        ? 'flex items-center px-6 py-2 mt-4 text-secondary-base bg-primary-alt bg-opacity-25 transition duration-150 ease-in-out'
        : 'flex items-center px-6 py-2 mt-4 text-primary-50 hover:bg-primary-alt hover:bg-opacity-25 hover:text-secondary-100 transition duration-150 ease-in-out';
@endphp

@if ($isDropdown && !empty($dropdownItems))
    <div x-data="{ open: false }" class="relative">
        <a @click="open = !open" {{ $attributes->merge(['class' => $classes]) }}
            >
            {{ $slot }}

            <i class="fas fa-chevron-down mr-2" x-show="open" x-cloak></i>
            <i class="fas fa-chevron-left mr-2" x-show="!open" x-cloak></i>

            @if ($badgeCount && $badgeCount > 0)
                <span
                    class="absolute left-5 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-primary-base bg-secondary-300 rounded-full">
                    {{ $badgeCount }}
                </span>
            @endif
        </a>
        <!-- Dropdown menu -->
        <div x-show="open" class="bg-primary-alt bg-opacity-10 left-0  w-full shadow-lg bg-white z-10">
            @foreach ($dropdownItems as $item)
                <a href="{{ $item['url'] }}"
                    class="block px-6 py-2 text-secondary-base hover:bg-primary-base hover:bg-opacity-25 hover:text-secondary-100 transition duration-150 ease-in-out relative">
                    <i class="fas fa-chevron-left ml-2"></i>
                    {{ $item['label'] }}
                    @if (isset($item['badgeCount']) && $item['badgeCount'] > 0)
                        <span
                            class="absolute left-5 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-secondary-base bg-primary-900 rounded-full">
                            {{ $item['badgeCount'] }}
                        </span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
@else
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
        @if ($badgeCount && $badgeCount > 0)
            <span
                class="ml-2 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                {{ $badgeCount }}
            </span>
        @endif
    </a>
@endif
