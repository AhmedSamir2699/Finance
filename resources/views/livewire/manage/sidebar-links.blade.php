<div x-data="{ 
    reorderModal: false,
    children: {
        @foreach($links as $link)
            @if($link['children'] && count($link['children']) > 0)
                {{ $link['id'] }}: false,
            @endif
        @endforeach
    }
}">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ __('sidebar.manage.sidebar_links') }}</h1>
                <p class="text-gray-600">{{ __('sidebar.manage.sidebar_links_description') }}</p>
            </div>
            <div class="flex space-x-3 gap-3">
                <button @click="reorderModal = true" class="inline-flex justify-between gap-3 items-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-gray-500 text-base font-medium text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-gray-500">
                    <i class="fas fa-sort"></i>
                    {{ __('sidebar.reorder') }}
                </button>
                <a href="{{ route('manage.sidebar-links.create') }}" class="inline-flex justify-between gap-3 items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-500 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-800 focus:ring-primary-500">
                    <i class="fas fa-plus"></i>
                    {{ __('common.create') }}
                </a>
            </div>
        </div>

        <!-- Links Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('sidebar.title') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('sidebar.url') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('sidebar.visibility') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('sidebar.status') }}
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                {{ __('common.actions') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($links as $link)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0">
                                            <i class="{{ $link['icon'] }} text-gray-400"></i>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $link['title'] }}
                                                @if($link['children'] && count($link['children']) > 0)
                                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded ml-2">
                                                        {{ count($link['children']) }} {{ __('sidebar.children') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if($link['url'])
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $link['is_external'] ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                            {{ $link['is_external'] ? __('sidebar.external') : __('sidebar.internal') }}
                                        </span>
                                        <div class="text-xs text-gray-500 mt-1 truncate max-w-xs">
                                            {{ $link['url'] }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">{{ __('sidebar.no_url') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $link['visibility'] === 'all' ? 'bg-blue-100 text-blue-800' : 
                                           ($link['visibility'] === 'authenticated' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ __("sidebar.visibility_{$link['visibility']}") }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button wire:click="toggleStatus({{ $link['id'] }})" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ $link['is_active'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $link['is_active'] ? __('common.active') : __('common.inactive') }}
                                    </button>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2 gap-3">
                                        <a href="{{ route('manage.sidebar-links.edit', $link['id']) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button wire:click="deleteLink({{ $link['id'] }})" 
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('{{ __('common.confirm_delete') }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if($link['children'] && count($link['children']) > 0)
                                            <button @click="children[{{ $link['id'] }}] = !children[{{ $link['id'] }}]" 
                                                    class="text-gray-600 hover:text-gray-900">
                                                <i class="fas fa-chevron-left" x-show="!children[{{ $link['id'] }}]"></i>
                                                <i class="fas fa-chevron-down" x-show="children[{{ $link['id'] }}]"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @if($link['children'] && count($link['children']) > 0)
                                <template x-for="child in children[{{ $link['id'] }}] ? {{ json_encode($link['children']) }} : []" :key="child.id">
                                    <tr class="hover:bg-gray-100 bg-gray-200 border-r border-blue-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8"></div>
                                                <div class="flex-shrink-0">
                                                    <i class="text-gray-400" :class="child.icon"></i>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900" x-text="child.title"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <template x-if="child.url">
                                                <div>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium" 
                                                          :class="child.is_external ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'">
                                                        <span x-text="child.is_external ? '{{ __('sidebar.external') }}' : '{{ __('sidebar.internal') }}'"></span>
                                                    </span>
                                                    <div class="text-xs text-gray-500 mt-1 truncate max-w-xs" x-text="child.url"></div>
                                                </div>
                                            </template>
                                            <template x-if="!child.url">
                                                <span class="text-gray-400">{{ __('sidebar.no_url') }}</span>
                                            </template>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                  :class="child.visibility === 'all' ? 'bg-blue-100 text-blue-800' : 
                                                         (child.visibility === 'authenticated' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800')">
                                                <span x-text="'{{ __('sidebar.visibility_all') }}'.replace('all', child.visibility)"></span>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <button @click="$wire.toggleStatus(child.id)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                    :class="child.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'">
                                                <span x-text="child.is_active ? '{{ __('common.active') }}' : '{{ __('common.inactive') }}'"></span>
                                            </button>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2 gap-3">
                                                <a :href="`{{ route('manage.sidebar-links.edit', 'PLACEHOLDER') }}`.replace('PLACEHOLDER', child.id)" 
                                                   class="text-indigo-600 hover:text-indigo-900">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button @click="$wire.deleteLink(child.id)" 
                                                        class="text-red-600 hover:text-red-900"
                                                        onclick="return confirm('{{ __('common.confirm_delete') }}')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            @endif
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    {{ __('sidebar.no_links_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reorder Modal -->
        <x-modal name="reorderModal" maxWidth="4xl">
            <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                {{ __('sidebar.reorder_links') }}
            </div>
            <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light max-h-[70vh] overflow-y-auto">
                <p class="mb-4">{{ __('sidebar.reorder_description') }}</p>
                
                <livewire:manage.sidebar-reorder />
            </div>
        </x-modal>
    </div>
</div> 