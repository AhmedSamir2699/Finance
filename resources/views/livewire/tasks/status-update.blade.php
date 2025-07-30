<div class="bg-white shadow rounded-lg border border-gray-200">
    <div class="px-4 py-3 border-b border-gray-200">
        <h3 class="text-sm font-medium text-gray-900">{{ __('tasks.status.title') }}</h3>
    </div>
    
    <div class="p-4">
        @if ($task->deleted_at)
            <!-- Deleted Status -->
            <div class="flex gap-2 items-center justify-between">
                <div class="flex gap-2 items-center space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex gap-2 items-center justify-center">
                            <i class="fas fa-trash text-red-600 text-sm"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ __('tasks.status.deleted') }}</p>
                        <p class="text-xs text-gray-500">{{ __('tasks.status.deleted_description') }}</p>
                    </div>
                </div>
            </div>
        @else
            @if ($task->status == 'approved')
                <!-- Approved Status -->
                <div class="flex gap-2 items-center justify-between">
                    <div class="flex gap-2 items-center space-x-3">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex gap-2 items-center justify-center">
                                <i class="fas fa-check-circle text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ __('tasks.status.completed') }}</p>
                            <p class="text-xs text-gray-500">{{ __('tasks.status.completed_description') }}</p>
                        </div>
                    </div>
                    <div class="flex-shrink-0">
                        <span class="inline-flex gap-2 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            {{ __('tasks.status.approved') }}
                        </span>
                    </div>
                </div>
                
            @elseif ($task->status == 'submitted')
                <!-- Submitted Status -->
                <div class="space-y-3">
                    <div class="flex gap-2 items-center justify-between">
                        <div class="flex gap-2 items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-indigo-100 rounded-full flex gap-2 items-center justify-center">
                                    <i class="fas fa-paper-plane text-indigo-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('tasks.status.submitted') }}</p>
                                <p class="text-xs text-gray-500">{{ __('tasks.status.submitted_description') }}</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex gap-2 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                {{ __('tasks.status.submitted') }}
                            </span>
                        </div>
                    </div>

                    @if ((auth()->user()->id !== $task->user_id || $task->assigned_by !== null) && (auth()->user()->can('task.approve-subordinate', $task) || auth()->user()->can('task.approve-department', $task)))
                        <div class="flex gap-2 items-center space-x-2 pt-2 border-t border-gray-100">
                            <span class="text-xs text-gray-500">{{ __('tasks.status.actions') }}:</span>
                            <a href="{{ route('tasks.approve', $task) }}"
                                class="inline-flex gap-2 py-1 items-center px-3 rounded-md text-xs font-medium bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                                <i class="fas fa-check mr-1.5"></i>
                                {{ __('tasks.status.approve') }}
                            </a>
                            <a href="{{ route('tasks.reject', $task) }}"
                                class="inline-flex gap-2 items-center px-3 py-1 rounded-md text-xs font-medium bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                                <i class="fas fa-times mr-1.5"></i>
                                {{ __('tasks.status.reject') }}
                            </a>
                        </div>
                    @endif
                </div>
                
            @elseif($task->status == 'in_progress')
                <!-- In Progress Status -->
                <div class="space-y-3">
                    <div class="flex gap-2 items-center justify-between">
                        <div class="flex gap-2 items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="w-8 h-8 bg-yellow-100 rounded-full flex gap-2 items-center justify-center">
                                    <i class="fas fa-clock text-yellow-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('tasks.status.in_progress') }}</p>
                                <p class="text-xs text-gray-500">{{ __('tasks.status.in_progress_description') }}</p>
                            </div>
                        </div>
                        <div class="flex-shrink-0">
                            <span class="inline-flex gap-2 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                {{ __('tasks.status.in_progress') }}
                            </span>
                        </div>
                    </div>

                    <div class="flex gap-2 items-center space-x-2 pt-2 border-t border-gray-100">
                        <span class="text-xs text-gray-500">{{ __('tasks.status.actions') }}:</span>
                        <a href="{{ route('tasks.submit', $task) }}"
                            class="inline-flex gap-2 items-center px-3 py-1 rounded-md text-xs font-medium bg-green-600 text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                            <i class="fas fa-check mr-1.5"></i>
                            {{ __('tasks.complete') }}
                        </a>
                    </div>
                </div>
                
            @else
                @if ($task->status == 'rejected')
                    <!-- Rejected Status -->
                    <div class="space-y-3">
                        <div class="flex gap-2 items-center justify-between">
                            <div class="flex gap-2 items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex gap-2 items-center justify-center">
                                        <i class="fas fa-times-circle text-red-600 text-sm"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('tasks.status.rejected') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('tasks.status.rejected_description') }}</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex gap-2 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ __('tasks.status.rejected') }}
                                </span>
                            </div>
                        </div>

                        @if ($task->user_id == auth()->id())
                            <div class="flex gap-2 items-center space-x-2 pt-2 border-t border-gray-100">
                                <span class="text-xs text-gray-500">{{ __('tasks.status.actions') }}:</span>
                                <button 
                                    wire:click="updateStatus()" 
                                    class="inline-flex gap-2 items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 cursor-pointer">
                                    <i class="fas fa-play mr-1.5"></i>
                                    {{ __('tasks.status.start') }}
                                </button>
                            </div>
                        @endif
                    </div>
                    
                @elseif($task->due_date && $task->due_date < now()->startOfDay() && !in_array($task->status, ['submitted', 'approved']))
                    <!-- Overdue Status -->
                    <div class="space-y-3">
                        <div class="flex gap-2 items-center justify-between">
                            <div class="flex gap-2 items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex gap-2 items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('tasks.status.overdue') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('tasks.status.overdue_description') }}</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex gap-2 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    {{ __('tasks.status.overdue') }}
                                </span>
                            </div>
                        </div>

                        @if ($task->user_id == auth()->id())
                            <div class="flex gap-2 items-center space-x-2 pt-2 border-t border-gray-100">
                                <span class="text-xs text-gray-500">{{ __('tasks.status.actions') }}:</span>
                                <button 
                                    wire:click="updateStatus()" 
                                    class="inline-flex gap-2 items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 cursor-pointer">
                                    <i class="fas fa-play mr-1.5"></i>
                                    {{ __('tasks.status.start') }}
                                </button>
                            </div>
                        @endif
                    </div>
                    
                @else
                    <!-- Pending Status -->
                    <div class="space-y-3">
                        <div class="flex gap-2 items-center justify-between">
                            <div class="flex gap-2 items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-gray-100 rounded-full flex gap-2 items-center justify-center">
                                        <i class="fas fa-clock text-gray-600 text-sm"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ __('tasks.status.pending') }}</p>
                                    <p class="text-xs text-gray-500">{{ __('tasks.status.pending_description') }}</p>
                                </div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="inline-flex gap-2 items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    {{ __('tasks.status.pending') }}
                                </span>
                            </div>
                        </div>

                        @if ($task->user_id == auth()->id())
                            <div class="flex gap-2 items-center space-x-2 pt-2 border-t border-gray-100">
                                <span class="text-xs text-gray-500">{{ __('tasks.status.actions') }}:</span>
                                <button 
                                    wire:click="updateStatus()" 
                                    class="inline-flex gap-2 items-center px-3 py-1 rounded-md text-xs font-medium bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200 cursor-pointer">
                                    <i class="fas fa-play mr-1.5"></i>
                                    {{ __('tasks.status.start') }}
                                </button>
                            </div>
                        @endif
                    </div>
                @endif
            @endif
        @endif
    </div>
</div>
