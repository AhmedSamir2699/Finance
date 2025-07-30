<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-6 space-y-6">
        <!-- Task Header -->
        <div class="flex gap-2">
            <div class="flex gap-3">
                <livewire:tasks.status-update :task="$task" />
            </div>
            
            <div class="flex-1 bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex gap-2 items-center justify-between">
                        <div class="flex-1">
                            <h1 class="text-3xl font-bold text-gray-900">{{ $task->title }}</h1>
                            <div class="mt-2 flex gap-2 items-center gap-1 text-sm text-gray-600">
    
                                @if ($task->assigned_by)
                                    <span class="flex gap-1 items-center">
                                        <i class="fas fa-user text-gray-400 ms-2"></i>
                                        {{ __('tasks.created_by') }}:
                                        <a href="{{ route('users.show', $task->assignedBy) }}"
                                            class="text-primary-600 hover:text-primary-800 me-1">
                                            {{ $task->assignedBy->name }}
                                        </a>
                                    </span>
                                    <span class="flex  gap-1 items-center">
                                        <i class="fas fa-arrow-left text-gray-400 ms-2"></i>
                                        {{ __('tasks.assigned_to') }}:
                                        <a href="{{ route('users.show', $task->user->id) }}"
                                            class="text-primary-600 hover:text-primary-800 me-1">
                                            {{ $task->user->name }}
                                        </a>
                                    </span>
                                @else
                                    <span class="flex gap-1 items-center">
                                        <i class="fas fa-user text-gray-400 ms-2"></i>
                                        {{ __('tasks.created_by') }}:
                                        <a href="{{ route('users.show', $task->user) }}"
                                            class="text-primary-600 hover:text-primary-800 me-1">
                                            {{ $task->user->name }}
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
    
                        <!-- Action Buttons -->
                        <div class="flex gap-3 items-center space-x-3">
    
                            @php
                                $user = auth()->user();
                                $canEdit = ($user->id == $task->user_id && $user->can('task.edit') && $task->status == 'pending') ||
                                    ($user->id == $task->assigned_by && $user->can('task.edit')) ||
                                    ($user->can('task.edit-subordinates') && $user->subordinateUsers()->contains('id', $task->user->id)) ||
                                    ($user->can('task.edit-department') && $user->department_id === $task->user->department_id) ||
                                    $user->can('task.edit-any');
                            @endphp
    
                            @if ($canEdit)
                                <a href="{{ route('tasks.edit', $task->id) }}"
                                    class="inline-flex gap-2 items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <i class="fas fa-edit ms-2"></i>
                                    {{ __('tasks.edit.title') }}
                                </a>
                            @endif
    
                            @if (auth()->user()->id == $task->assigned_by && auth()->user()->can('task.create'))
                                <a href="{{ route('tasks.create', ['clone' => $task->id]) }}"
                                    class="inline-flex gap-2 items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                                    title="{{ __('tasks.clone.title') }} {{ $task->attachments->count() > 0 ? '(' . $task->attachments->count() . ' ' . __('tasks.attachments') . ')' : '' }}">
                                    <i class="fas fa-copy ms-2"></i>
                                    {{ __('tasks.clone.title') }}
                                </a>
                            @endif
    
                            @php
                                $canDelete = ($user->can('task.delete') &&
                                    $task->status != 'deleted' &&
                                    (auth()->user()->id == $task->user_id && is_null($task->assigned_by))) ||
                                    ((auth()->user()->can('task.delete-subordinates') && $user->subordinateUsers()->contains('id', $task->user->id)) ||
                                    (auth()->user()->can('task.delete-department') && $user->department_id === $task->user->department_id) ||
                                    auth()->user()->can('task.delete-any')) ||
                                    auth()->user()->can('task.delete-any');
                            @endphp
    
                            @if ($canDelete)
                                <div x-data="{ showDeleteModal: false }">
                                    <button type="button" x-on:click="showDeleteModal = true"
                                        class="inline-flex gap-2 items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                        <i class="fas fa-trash ms-2"></i>
                                        {{ __('tasks.delete.title') }}
                                    </button>
    
                                    <!-- Delete Confirmation Modal -->
                                    <div x-show="showDeleteModal" style="display: none;"
                                        class="fixed inset-0 z-50 flex gap-2 items-center justify-center bg-black bg-opacity-50">
                                        <div class="bg-white rounded-lg p-6 w-96 max-w-md mx-4"
                                            @click.away="showDeleteModal = false">
                                            <div class="flex gap-2 items-center mb-4">
                                                <div class="flex-shrink-0">
                                                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                                                </div>
                                                <div class="me-3">
                                                    <h3 class="text-lg font-medium text-gray-900">
                                                        {{ __('tasks.delete.title') }}
                                                    </h3>
                                                </div>
                                            </div>
                                            <p class="text-sm text-gray-500 mb-6">
                                                {{ __('tasks.delete.confirmation') }}
                                            </p>
                                            <div class="flex justify-end space-x-3">
                                                <button type="button" @click="showDeleteModal = false"
                                                    class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                    {{ __('common.cancel') }}
                                                </button>
                                                <form action="{{ route('tasks.destroy', $task->id) }}" method="POST"
                                                    class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        {{ __('tasks.delete.title') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
    
                <!-- Task Details -->
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <!-- Date Range -->
                        <div class="flex gap-2 items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if ($task->due_date > now()->format('Y-m-d'))
                                    <i class="fas fa-clock text-green-500"></i>
                                @elseif($task->due_date == now()->format('Y-m-d'))
                                    <i class="fas fa-exclamation-triangle text-yellow-500"></i>
                                @else
                                    <i class="fas fa-exclamation-circle text-red-500"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('tasks.date.range') }}</p>
                                <p class="text-sm text-gray-600">
                                    {{ Carbon\Carbon::parse($task->task_date)->format('d/m/Y') }} -
                                    {{ Carbon\Carbon::parse($task->due_date)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
    
                        <!-- Estimated Time -->
                        <div class="flex gap-2 items-center space-x-3">
                            <div class="flex-shrink-0">
                                <i class="fas fa-clock text-gray-400"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('tasks.estimated_time.label') }}</p>
                                <p class="text-sm text-gray-600">
                                    @php
                                        $hours = floor($task->estimated_time / 60);
                                        $minutes = $task->estimated_time % 60;
                                    @endphp
                                    @if ($hours > 0)
                                        {{ $hours }} {{ __('tasks.estimated_time.hours') }}
                                    @endif
                                    @if ($minutes > 0 || $hours == 0)
                                        {{ $minutes }} {{ __('tasks.estimated_time.minutes') }}
                                    @endif
                                </p>
                            </div>
                        </div>
    
                        <!-- Priority -->
                        <div class="flex gap-2 items-center space-x-3">
                            <div class="flex-shrink-0">
                                <i
                                    class="fas fa-flag text-{{ $task->priority == 'low' ? 'gray' : ($task->priority == 'medium' ? 'yellow' : 'red') }}-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('tasks.priority.label') }}</p>
                                <p class="text-sm text-gray-600">{{ __('tasks.priority.' . $task->priority) }}</p>
                            </div>
                        </div>
    
                        <!-- Type -->
                        <div class="flex gap-2 items-center space-x-3">
                            <div class="flex-shrink-0">
                                @if ($task->type == 'scheduled')
                                    <i class="fas fa-calendar-alt text-blue-500"></i>
                                @elseif($task->type == 'unscheduled')
                                    <i class="fas fa-calendar-times text-orange-500"></i>
                                @elseif($task->type == 'continous')
                                    <i class="fas fa-sync text-green-500"></i>
                                @elseif($task->type == 'training')
                                    <i class="fas fa-graduation-cap text-purple-500"></i>
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ __('tasks.type.label') }}</p>
                                <p class="text-sm text-gray-600">{{ __('tasks.type.' . $task->type) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>



        @if ($task->description)
            <!-- Task Description -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('tasks.description') }}</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="prose max-w-none">
                        {!! $task->description !!}
                    </div>
                </div>
            </div>
        @endif

        <!-- Attachments Section -->
        @if ($task->attachments && $task->attachments->count() > 0)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">
                        {{ __('tasks.attachments') }} ({{ $task->attachments->count() }})
                    </h2>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                        @php
                            $videoExts = ['mp4', 'webm', 'ogg', 'mov'];
                            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                        @endphp
                        @foreach ($task->attachments as $attachment)
                            @php
                                $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));
                                $isImage = in_array($extension, $imageExts);
                                $isVideo = in_array($extension, $videoExts);
                                $previewUrl = route('tasks.attachments.preview', [
                                    'task' => $task->id,
                                    'attachment' => $attachment->id,
                                ]);
                            @endphp
                            <div x-data="{ showPreview: false }">
                                <button type="button"
                                    class="w-full aspect-square bg-gray-50 rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                    @click="showPreview = true">
                                    @if ($isImage)
                                        <img src="{{ $previewUrl }}" alt="{{ $attachment->name }}"
                                            class="w-full h-full object-cover rounded-lg">
                                    @elseif ($isVideo)
                                        <div class="w-full h-full flex gap-2 items-center justify-center">
                                            <img id="thumb-task-{{ $attachment->id }}"
                                                class="w-full h-full object-cover rounded-lg"
                                                style="background:#eee;" />
                                            <video id="video-task-{{ $attachment->id }}" src="{{ $previewUrl }}"
                                                style="display:none"></video>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex flex-col items-center justify-center">
                                            <i class="fas fa-{{ $attachment->icon }} text-gray-400 text-2xl mb-2"></i>
                                            <span
                                                class="text-xs text-gray-500 text-center px-2">{{ $attachment->name }}</span>
                                        </div>
                                    @endif
                                </button>

                                <!-- Preview Modal -->
                                <div x-show="showPreview" style="display: none;"
                                    class="fixed inset-0 z-50 flex gap-2 items-center justify-center bg-black bg-opacity-50">
                                    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto relative"
                                        @click.away="showPreview = false">
                                        <button type="button"
                                            class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                                            @click="showPreview = false">
                                            <i class="fas fa-times text-xl"></i>
                                        </button>
                                        <div class="flex flex-col items-center">
                                            @if ($isImage)
                                                <img src="{{ $previewUrl }}" alt="{{ $attachment->name }}"
                                                    class="max-w-full max-h-[60vh] rounded mb-4">
                                            @elseif ($isVideo)
                                                <video src="{{ $previewUrl }}" controls
                                                    class="max-w-full max-h-[60vh] rounded mb-4"></video>
                                            @else
                                                <div class="flex flex-col items-center mb-4">
                                                    <i
                                                        class="fas fa-{{ $attachment->icon }} text-primary-600 text-4xl mb-4"></i>
                                                    <span class="text-lg font-semibold">{{ $attachment->name }}</span>
                                                </div>
                                            @endif
                                            <a href="{{ $previewUrl }}" download="{{ $attachment->name }}"
                                                class="inline-flex gap-2 items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                <i class="fas fa-download ms-2"></i>
                                                {{ __('tasks.attachment.download') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

        <!-- Proofs Section -->
        @if ($task->proofs)
            @php
                $proof = json_decode($task->proofs);
            @endphp
            @if ($proof && isset($proof->comment))
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-medium text-gray-900">{{ __('tasks.proofs') }}</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="prose max-w-none mb-4">
                            {!! $proof->comment !!}
                        </div>

                        @if ($proof->attachments && is_array($proof->attachments))
                            <div class="border-t border-gray-200 pt-4">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">{{ __('tasks.proof_attachments') }}
                                </h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @php
                                        $videoExts = ['mp4', 'webm', 'ogg', 'mov'];
                                        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'];
                                    @endphp
                                    @foreach ($proof->attachments as $key => $attachment)
                                        @php
                                            $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));
                                            $isImage = in_array($extension, $imageExts);
                                            $isVideo = in_array($extension, $videoExts);
                                            $previewUrl = route('tasks.proofs.preview', [
                                                'task' => $task->id,
                                                'proof' => $key,
                                            ]);
                                        @endphp
                                        <div x-data="{ showPreview: false }">
                                            <button type="button"
                                                class="w-full aspect-square bg-white rounded-lg border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                                                @click="showPreview = true">
                                                @if ($isImage)
                                                    <img src="{{ $previewUrl }}" alt="{{ $attachment->name }}"
                                                        class="w-full h-full object-cover rounded-lg">
                                                @elseif ($isVideo)
                                                    <div class="w-full h-full flex gap-2 items-center justify-center">
                                                        <img id="thumb-proof-{{ $key }}"
                                                            class="w-full h-full object-cover rounded-lg"
                                                            style="background:#eee;" />
                                                        <video id="video-proof-{{ $key }}"
                                                            src="{{ $previewUrl }}" style="display:none"></video>
                                                    </div>
                                                @else
                                                    <div
                                                        class="w-full h-full flex flex-col items-center justify-center">
                                                        <i class="fas fa-file text-gray-400 text-2xl mb-2"></i>
                                                        <span
                                                            class="text-xs text-gray-500 text-center px-2">{{ $attachment->name }}</span>
                                                    </div>
                                                @endif
                                            </button>

                                            <!-- Preview Modal -->
                                            <div x-show="showPreview" style="display: none;"
                                                class="fixed inset-0 z-50 flex gap-2 items-center justify-center bg-black bg-opacity-50">
                                                <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[80vh] overflow-y-auto relative"
                                                    @click.away="showPreview = false">
                                                    <button type="button"
                                                        class="absolute top-4 right-4 text-gray-400 hover:text-gray-600"
                                                        @click="showPreview = false">
                                                        <i class="fas fa-times text-xl"></i>
                                                    </button>
                                                    <div class="flex flex-col items-center">
                                                        @if ($isImage)
                                                            <img src="{{ $previewUrl }}"
                                                                alt="{{ $attachment->name }}"
                                                                class="max-w-full max-h-[60vh] rounded mb-4">
                                                        @elseif ($isVideo)
                                                            <video src="{{ $previewUrl }}" controls
                                                                class="max-w-full max-h-[60vh] rounded mb-4"></video>
                                                        @else
                                                            <div class="flex flex-col items-center mb-4">
                                                                <i
                                                                    class="fas fa-file text-primary-600 text-4xl mb-4"></i>
                                                                <span
                                                                    class="text-lg font-semibold">{{ $attachment->name }}</span>
                                                            </div>
                                                        @endif
                                                        <a href="{{ $previewUrl }}"
                                                            download="{{ $attachment->name }}"
                                                            class="inline-flex gap-2 items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                            <i class="fas fa-download ms-2"></i>
                                                            {{ __('tasks.attachment.download') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        @endif

        <!-- Comments Section -->
        @can('task.comment')
            <div class="bg-white shadow rounded-lg" x-data="{ expanded: {{ $commentsExpanded ? 'true' : 'false' }} }">
                <div class="px-6 py-4 border-b border-gray-200 cursor-pointer" @click="expanded = !expanded">
                    <div class="flex items-center justify-between">
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ __('tasks.comments') }} ({{ $task->comments->count() }})
                        </h2>
                        <button type="button"
                            class="text-primary-600 hover:text-primary-800 transition-colors duration-200">
                            <i class="fas fa-chevron-left" x-show="!expanded" x-cloak></i>
                            <i class="fas fa-chevron-down" x-show="expanded" x-cloak></i>
                        </button>
                    </div>
                </div>

                <div x-show="expanded" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 transform scale-95"
                    x-transition:enter-end="opacity-100 transform scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 transform scale-100"
                    x-transition:leave-end="opacity-0 transform scale-95">

                    @if ($task->comments && $task->comments->count() > 0)
                        <div class="px-6 py-4 space-y-4">
                            @foreach ($task->comments as $comment)
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex gap-3 items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <img class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center"
                                                src="{{ asset('storage/' . $comment->user->profile_picture) }}" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex gap-2 items-center space-x-2 mb-2">
                                                <a href="{{ route('users.show', $comment->user->id) }}"
                                                    class="text-sm font-medium text-primary-600 hover:text-primary-800">
                                                    {{ $comment->user->name }}
                                                </a>
                                                <span class="text-sm text-gray-500">
                                                    {{ Carbon\Carbon::parse($comment->created_at)->diffForHumans() }}
                                                </span>
                                                <span class="text-xs text-gray-400">
                                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                                </span>
                                            </div>
                                            <div class="prose prose-sm max-w-none">
                                                {!! $comment->comment !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Add Comment Form -->
                    <div class="px-6 py-4 border-t border-gray-200">
                        <form method="post" action="{{ route('tasks.comments.store', $task->id) }}">
                            @csrf
                            <div class="mb-4">
                                <x-text-editor name="comment" height="h-32" />
                            </div>
                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    {{ __('tasks.add_comment') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan

        <!-- Task History -->
        @can('task.view-history')
            <livewire:tasks.history :task="$task" />
        @endcan
    </div>

    <!-- Video Thumbnail Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @foreach ($task->attachments as $attachment)
                @php
                    $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));
                    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                @endphp
                @if ($isVideo)
                    (function() {
                        var video = document.getElementById('video-task-{{ $attachment->id }}');
                        var img = document.getElementById('thumb-task-{{ $attachment->id }}');
                        if (video && img) {
                            video.addEventListener('loadeddata', function() {
                                video.currentTime = Math.min(1, video.duration / 2);
                            });
                            video.addEventListener('seeked', function() {
                                var canvas = document.createElement('canvas');
                                canvas.width = video.videoWidth;
                                canvas.height = video.videoHeight;
                                var ctx = canvas.getContext('2d');
                                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                img.src = canvas.toDataURL();
                            });
                            video.load();
                        }
                    })();
                @endif
            @endforeach

            @if ($task->proofs && $proof && isset($proof->attachments) && is_array($proof->attachments))
                @foreach ($proof->attachments as $key => $attachment)
                    @php
                        $extension = strtolower(pathinfo($attachment->path, PATHINFO_EXTENSION));
                        $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov']);
                    @endphp
                    @if ($isVideo)
                        (function() {
                            var video = document.getElementById('video-proof-{{ $key }}');
                            var img = document.getElementById('thumb-proof-{{ $key }}');
                            if (video && img) {
                                video.addEventListener('loadeddata', function() {
                                    video.currentTime = Math.min(1, video.duration / 2);
                                });
                                video.addEventListener('seeked', function() {
                                    var canvas = document.createElement('canvas');
                                    canvas.width = video.videoWidth;
                                    canvas.height = video.videoHeight;
                                    var ctx = canvas.getContext('2d');
                                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                    img.src = canvas.toDataURL();
                                });
                                video.load();
                            }
                        })();
                    @endif
                @endforeach
            @endif
        });
    </script>
</x-app-layout>
