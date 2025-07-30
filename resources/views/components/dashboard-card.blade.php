@props(['href', 'icon', 'title', 'subtitle'])

<div class="w-full">
    <a href="{{ $href }}" class="flex items-center px-5 py-6 bg-white rounded-lg shadow-sm hover:shadow-md transition">
        <div class="p-3 bg-primary-600 bg-opacity-75 rounded-full">
            <i class="{{ $icon }} text-white"></i>
        </div>
        <div class="mx-5">
            <h4 class="text-md font-semibold text-gray-700">{{ $title }}</h4>
            <div class="text-gray-500">{{ $subtitle }}</div>
        </div>
    </a>
</div> 