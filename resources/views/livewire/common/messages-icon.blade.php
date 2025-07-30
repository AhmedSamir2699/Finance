<div class="relative">
    <a href="{{ route('messages.index') }}" class="flex mx-4 focus:outline-none text-secondary-base">
        @if ($hasNewMessages)
            <i class="fas fa-envelope"></i>
            <span
                class="absolute top-0 right-2 inline-flex items-center justify-center p-1 text-xs font-bold leading-none text-red-100 transform -translate-y-1/2 bg-red-600 rounded-full animate-pulse"></span>
        @else
            <i class="fas fa-envelope text-gray-400"></i>
        @endif

    </a>
</div>
