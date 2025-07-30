<div>
    <!-- Search and Filters -->
    <div class="mb-6 bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('static-pages.search') }}
                </label>
                <input type="text" id="search" wire:model.live="search" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                    placeholder="{{ __('static-pages.search_placeholder') }}">
            </div>

            <!-- Status Filter -->
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('static-pages.status') }}
                </label>
                <select id="status" wire:model.live="status" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">{{ __('static-pages.all_statuses') }}</option>
                    <option value="active">{{ __('static-pages.active') }}</option>
                    <option value="inactive">{{ __('static-pages.inactive') }}</option>
                </select>
            </div>

            <!-- Visibility Filter -->
            <div>
                <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('static-pages.visibility') }}
                </label>
                <select id="visibility" wire:model.live="visibility" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <option value="">{{ __('static-pages.all_visibilities') }}</option>
                    <option value="all">{{ __('static-pages.visibility_all') }}</option>
                    <option value="authenticated">{{ __('static-pages.visibility_authenticated') }}</option>
                    <option value="guest">{{ __('static-pages.visibility_guest') }}</option>
                </select>
            </div>

            <!-- Create Button -->
            <div class="flex items-end">
                <a href="{{ route('manage.static-pages.create') }}" 
                    class="w-full bg-primary-600 text-white px-4 py-2 rounded-md hover:bg-primary-700 transition duration-200 text-center">
                    {{ __('static-pages.create') }}
                </a>
            </div>
        </div>
    </div>

    <!-- Pages Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" 
                            wire:click="sortBy('title')">
                            {{ __('static-pages.title') }}
                            @if($sortBy === 'title')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" 
                            wire:click="sortBy('slug')">
                            {{ __('static-pages.slug') }}
                            @if($sortBy === 'slug')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('static-pages.visibility') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" 
                            wire:click="sortBy('views')">
                            {{ __('static-pages.views') }}
                            @if($sortBy === 'views')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer" 
                            wire:click="sortBy('created_at')">
                            {{ __('static-pages.created_at') }}
                            @if($sortBy === 'created_at')
                                <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ml-1"></i>
                            @endif
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('static-pages.status') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('static-pages.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pages as $page)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $page->title }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-500">{{ $page->slug }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($page->visibility === 'all') bg-green-100 text-green-800
                                    @elseif($page->visibility === 'authenticated') bg-blue-100 text-blue-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ __("static-pages.visibility_{$page->visibility}") }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ number_format($page->views) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $page->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                    @if($page->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                    {{ $page->is_active ? __('static-pages.active') : __('static-pages.inactive') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-between space-x-2 gap-3">
                                    <!-- Copy Link Button -->
                                    <button wire:click="copyLink({{ $page->id }})" 
                                        class="text-blue-600 hover:text-blue-900" 
                                        title="{{ __('static-pages.copy_link') }}">
                                        <i class="fas fa-copy"></i>
                                    </button>

                                    <!-- View Button -->
                                    <a href="{{ $page->public_url }}" target="_blank" 
                                        class="text-green-600 hover:text-green-900" 
                                        title="{{ __('static-pages.view') }}">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Edit Button -->
                                    <a href="{{ route('manage.static-pages.edit', $page->id) }}" 
                                        class="text-indigo-600 hover:text-indigo-900" 
                                        title="{{ __('static-pages.edit') }}">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <!-- Toggle Status Button -->
                                    <button wire:click="toggleStatus({{ $page->id }})" 
                                        class="text-yellow-600 hover:text-yellow-900" 
                                        title="{{ $page->is_active ? __('static-pages.deactivate') : __('static-pages.activate') }}">
                                        <i class="fas fa-{{ $page->is_active ? 'pause' : 'play' }}"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <button wire:click="deletePage({{ $page->id }})" 
                                        class="text-red-600 hover:text-red-900" 
                                        title="{{ __('static-pages.delete') }}"
                                        onclick="return confirm('{{ __('static-pages.confirm_delete') }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                {{ __('static-pages.no_pages_found') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($pages->hasPages())
            <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                {{ $pages->links() }}
            </div>
        @endif
    </div>

    <!-- Copy to Clipboard Script -->
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('copyToClipboard', (url) => {
                navigator.clipboard.writeText(url).then(() => {
                    console.log('URL copied to clipboard');
                });
            });
        });
    </script>
</div> 