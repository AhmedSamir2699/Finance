<div x-data="{ notificationOpen: $wire.entangle('notificationsOpen') }" 
class="relative" wire:poll.15000ms>
<a href="{{ route('notifications.index') }}" class="flex mx-4 text-secondary-base focus:outline-none">
    @if ($hasUnseenNotifications)
        <i class="fas fa-bell"></i>
        <span
            class="absolute top-0 right-2 inline-flex items-center justify-center p-1 text-xs font-bold leading-none text-red-100 transform -translate-y-1/2 bg-red-600 rounded-full animate-pulse"></span>
    @else
        <i class="fas fa-bell text-gray-400"></i>
    @endif
</a>
    {{-- <button wire:click="showNotifications" class="flex mx-4 text-secondary-base focus:outline-none">
        @if ($hasUnseenNotifications)
            <i class="fas fa-bell"></i>
            <span
                class="absolute top-0 right-2 inline-flex items-center justify-center p-1 text-xs font-bold leading-none text-red-100 transform -translate-y-1/2 bg-red-600 rounded-full animate-pulse"></span>
        @else
            <i class="fas fa-bell text-gray-400"></i>
        @endif
    </button>
    <div x-show="notificationOpen" wire:click="closeNotifications" class="fixed inset-0 z-10 w-full h-full"
        style="display: none;"></div>
    <div x-show="notificationOpen"
        class="absolute left-0 z-10 mt-2 overflow-hidden bg-white rounded-b-lg shadow-xl w-80  border border-primary-300 translate-y-[6.5%]"
        style="width: 20rem; display: none;">
        @if ($notifications)
            @foreach ($notifications as $notification)
                <a href="{{ $notification->action_url ?? '#' }}"
                    class="flex justify-center px-4 py-3 -mx-2 text-gray-600 hover:text-white hover:bg-primary-base border-b border-gray-300 text-right">
                    <p class="flex-1 block mx-0 text-sm text-right">
                        <span class="font-bold" href="#">{{ $notification->content }}</span>
                    </p>
                    <span class="text-xs text-gray-400 ml-auto">{{ $notification->created_at->diffForHumans() }}</span>
                </a>
            @endforeach
            <a href=""
                class="block px-4 py-2 text-sm text-gray-700 bg-primary-base text-white text-center border-b border-r border-l border-primary-300 rounded-b-lg font-bold">
                <i class="fas fa-arrow-right"></i>
                {{ __('notification.view_all') }}
            </a>
        @endif
    </div> --}}
</div>
