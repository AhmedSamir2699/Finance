<div class="bg-white shadow rounded-lg" x-data="{ expanded: false }">
    <div class="px-6 py-4 border-b border-gray-200 cursor-pointer" @click="expanded = !expanded">
        <div class="flex gap-2 items-center justify-between">
            <h2 class="text-lg font-medium text-gray-900">{{ __('tasks.history.title') }} ({{ $task->histories->count() }})</h2>
            <button type="button" 
                class="text-primary-600 hover:text-primary-800 transition-colors duration-200">
                <i class="fas fa-chevron-left" x-show="!expanded" x-cloak></i>
                <i class="fas fa-chevron-down" x-show="expanded" x-cloak></i>
            </button>
        </div>
    </div>
    <div class="px-6 py-4" x-show="expanded" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95">
        <div class="space-y-4" wire:poll.5s>
            @foreach ($task->histories()->latest('id')->get() as $history)
                <div class="flex gap-2 items-start  p-4 bg-gray-50 rounded-lg">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-primary-100 rounded-full flex gap-2 items-center justify-center">
                            <i class="fas fa-history text-primary-600 text-sm"></i>
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex gap-2 items-center  mb-1">
                            <span class="text-sm font-medium text-gray-900">
                                {{ $history->action }}
                            </span>
                            <span class="text-sm text-gray-500">
                                {{ __('tasks.by') }}
                            </span>
                            <a href="{{ route('users.show', $history->user->id) }}" 
                               class="text-sm font-medium text-primary-600 hover:text-primary-800">
                                {{ $history->user->name }}
                            </a>
                        </div>
                        <div class="flex gap-2 items-center  text-xs text-gray-500">
                            <i class="fas fa-clock"></i>
                            <span>{{ Carbon\Carbon::parse($history->created_at)->diffForHumans() }}</span>
                            <span class="text-gray-300">•</span>
                            <span>{{ $history->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
            
            @if($task->histories()->count() === 0)
                <div class="text-center py-8">
                    <i class="fas fa-history text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">{{ __('tasks.history.no_history') }}</p>
                </div>
            @endif
        </div>
    </div>
</div>