<x-app-layout>
    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Select2 Custom Styling */
        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 36px;
            padding-left: 12px;
            color: #374151;
        }
        
        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
        }
        
        .select2-container--default .select2-results__option--highlighted[aria-selected] {
            background-color: #3b82f6;
        }
        
        .select2-dropdown {
            border: 1px solid #d1d5db;
            border-radius: 0.375rem;
        }
        
        .select2-results__option {
            padding: 8px 12px;
        }
        
        .select2-results__group {
            font-weight: bold;
            color: #6b7280;
            padding: 8px 12px;
            background-color: #e5e7eb;
        }
    </style>

    <script>
        // Live preview functionality
        function updatePreview() {
            const titleElement = document.getElementById('title');
            const urlElement = document.getElementById('url');
            const iconElement = document.getElementById('icon');
            const parentSelect = document.getElementById('parent_id');
            const isActiveElement = document.getElementById('is_active');
            const visibilityElement = document.getElementById('visibility');
            
            const title = titleElement ? titleElement.value || '-' : '-';
            const url = urlElement ? urlElement.value || '-' : '-';
            const icon = iconElement ? iconElement.value || '-' : '-';
            const parent = parentSelect && parentSelect.options[parentSelect.selectedIndex] ? parentSelect.options[parentSelect.selectedIndex].text : '{{ __("sidebar.preview_details.none") }}';
            const isActive = isActiveElement ? isActiveElement.checked : false;
            const visibility = visibilityElement ? visibilityElement.value : 'authenticated';
            
            // Safely get Select2 data
            let permissions = '{{ __("sidebar.preview_details.none") }}';
            try {
                const $permissionsSelect = $('#permissions');
                if ($permissionsSelect.length && $permissionsSelect.hasClass('select2-hidden-accessible')) {
                    const select2Data = $permissionsSelect.select2('data');
                    if (select2Data && select2Data.length > 0) {
                        permissions = select2Data.map(item => item.text).join(', ');
                    }
                }
            } catch (e) {
                console.log('Select2 not ready yet');
            }
            
            // Detect if URL is external
            const isExternal = url.startsWith('http://') || url.startsWith('https://') || url.startsWith('//');
            
            // Update preview elements with null checks
            const previewDetailTitle = document.getElementById('preview-detail-title');
            const previewDetailUrl = document.getElementById('preview-detail-url');
            const previewDetailIcon = document.getElementById('preview-detail-icon');
            const previewDetailParent = document.getElementById('preview-detail-parent');
            const previewDetailPermissions = document.getElementById('preview-detail-permissions');
            const previewDetailStatus = document.getElementById('preview-detail-status');
            const previewDetailExternal = document.getElementById('preview-detail-external');
            const previewDetailVisibility = document.getElementById('preview-detail-visibility');
            
            if (previewDetailTitle) previewDetailTitle.textContent = title;
            if (previewDetailUrl) previewDetailUrl.textContent = url;
            if (previewDetailIcon) {
                previewDetailIcon.innerHTML = `<i class="${icon}"></i>`;
            }
            if (previewDetailParent) previewDetailParent.textContent = parent;
            if (previewDetailPermissions) previewDetailPermissions.textContent = permissions;
            if (previewDetailStatus) previewDetailStatus.textContent = isActive ? '{{ __("sidebar.preview_details.active") }}' : '{{ __("sidebar.preview_details.inactive") }}';
            if (previewDetailExternal) previewDetailExternal.textContent = isExternal ? '{{ __("sidebar.preview_details.yes") }}' : '{{ __("sidebar.preview_details.no") }}';
            if (previewDetailVisibility) {
                let visibilityText = '{{ __("sidebar.visibility_authenticated") }}';
                if (visibility === 'all') {
                    visibilityText = '{{ __("sidebar.visibility_all") }}';
                } else if (visibility === 'guest') {
                    visibilityText = '{{ __("sidebar.visibility_guest") }}';
                }
                previewDetailVisibility.textContent = visibilityText;
            }
            
            // Update live preview
            const previewLink = document.getElementById('preview-nav-link');
            if (previewLink) {
                previewLink.innerHTML = `
                    <i class="${icon}"></i>
                    <span>${title}</span>
                `;
                previewLink.href = url || '#';
                previewLink.style.display = isActive ? 'flex' : 'none';
            }
            
            // Update icon preview in input field
            const iconPreview = document.getElementById('icon-preview');
            if (iconPreview) {
                iconPreview.className = icon;
            }
        }

        // Initialize Select2 for permissions dropdown
        $(document).ready(function() {
            $('#permissions').select2({
                placeholder: '{{ __("sidebar.permissions_placeholder") }}',
                allowClear: true,
                data: @json($permissions),
                templateResult: function(data) {
                    if (data.children) {
                        return $('<span class="font-semibold text-gray-700">' + data.text + '</span>');
                    }
                    return data.text;
                },
                templateSelection: function(data) {
                    return data.text;
                }
            });

            // Update preview when permissions change
            $('#permissions').on('change', updatePreview);

            // Add event listeners for live preview
            const inputs = ['title', 'url', 'icon', 'parent_id', 'is_active', 'visibility'];
            inputs.forEach(inputId => {
                const element = document.getElementById(inputId);
                if (element) {
                    element.addEventListener('input', updatePreview);
                    element.addEventListener('change', updatePreview);
                }
            });

            // Initial preview update after Select2 is ready
            setTimeout(updatePreview, 200);
        });
    </script>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    
    <div class="container mt-4 mx-auto">
        <div class="py-2 px-4">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Form -->
                <div class="bg-white rounded-lg shadow p-6 border">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('sidebar.form_details') }}</h3>
                    <form action="{{ route('manage.sidebar-links.update', $sidebarLink) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="space-y-4">
                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-medium text-slate-700 py-2">
                                    {{ __('sidebar.title') }} *
                                </label>
                                <input type="text" name="title" id="title" value="{{ old('title', $sidebarLink->title) }}" 
                                       class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" required>
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- URL -->
                            <div>
                                <label for="url" class="block text-sm font-medium text-slate-700 py-2">
                                    {{ __('sidebar.url') }}
                                </label>
                                <input type="text" name="url" id="url" value="{{ old('url', $sidebarLink->url) }}" 
                                       class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"
                                       placeholder="https://example.com or dashboard">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('sidebar.url_help') }} 
                                    <span class="text-blue-600">{{ __('sidebar.external_detection') }}</span>
                                </p>
                                @error('url')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Icon -->
                            <div>
                                <label for="icon" class="block text-sm font-medium text-slate-700 py-2">
                                    {{ __('sidebar.icon') }} *
                                </label>
                                <div class="relative">
                                    <input type="text" name="icon" id="icon" value="{{ old('icon', $sidebarLink->icon) }}" 
                                           class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i id="icon-preview" class="{{ $sidebarLink->icon }} text-gray-400"></i>
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('sidebar.icon_help') }}
                                </p>
                                @error('icon')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Parent Link -->
                            <div>
                                <label for="parent_id" class="block text-sm font-medium text-slate-700 py-2">
                                    {{ __('sidebar.parent_link') }}
                                </label>
                                <select name="parent_id" id="parent_id" 
                                        class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                                    <option value="">{{ __('sidebar.no_parent') }}</option>
                                    @foreach($parentLinks as $link)
                                        <option value="{{ $link->id }}" {{ old('parent_id', $sidebarLink->parent_id) == $link->id ? 'selected' : '' }}>
                                            {{ $link->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Permissions -->
                            <div>
                                <label for="permissions" class="block text-sm font-medium text-slate-700 py-2">
                                    {{ __('sidebar.permissions') }}
                                </label>
                                <select name="permissions[]" id="permissions" multiple 
                                        class="w-full">
                                </select>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('sidebar.permissions_help') }}
                                </p>
                                @error('permissions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Visibility -->
                            <div>
                                <label for="visibility" class="block text-sm font-medium text-slate-700 py-2">
                                    {{ __('sidebar.visibility') }}
                                </label>
                                <select name="visibility" id="visibility" 
                                        class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                                    <option value="all" {{ old('visibility', $sidebarLink->visibility ?? 'authenticated') == 'all' ? 'selected' : '' }}>
                                        {{ __('sidebar.visibility_all') }}
                                    </option>
                                    <option value="authenticated" {{ old('visibility', $sidebarLink->visibility ?? 'authenticated') == 'authenticated' ? 'selected' : '' }}>
                                        {{ __('sidebar.visibility_authenticated') }}
                                    </option>
                                    <option value="guest" {{ old('visibility', $sidebarLink->visibility ?? 'authenticated') == 'guest' ? 'selected' : '' }}>
                                        {{ __('sidebar.visibility_guest') }}
                                    </option>
                                </select>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('sidebar.visibility_help') }}
                                </p>
                                @error('visibility')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $sidebarLink->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                    {{ __('sidebar.is_active') }}
                                </label>
                            </div>

                            <!-- Submit Button -->
                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-8 rounded focus:outline-none focus:shadow-outline">
                                    {{ __('sidebar.update_link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Preview -->
                <div class="">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('sidebar.preview') }}</h3>
                    
                    <!-- Live Preview -->
                    <div class="p-4 bg-gray-50 rounded-lg bg-primary-900 mb-6">
                        <nav class="space-y-1">
                            <a id="preview-nav-link" href="#" 
                               class="bg-primary-900 flex gap-3 items-center px-6 py-2 text-primary-50 hover:bg-primary-alt hover:bg-opacity-25 hover:text-secondary-100 transition duration-150 ease-in-out">
                                <i class="{{ $sidebarLink->icon }} me-3"></i>
                                <span>{{ $sidebarLink->title }}</span>
                            </a>
                        </nav>
                    </div>

                    <!-- Details Preview -->
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">{{ __('sidebar.preview_details.title') }}:</span>
                                <span id="preview-detail-title" class="text-gray-900">{{ $sidebarLink->title }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">{{ __('sidebar.preview_details.url') }}:</span>
                                <span id="preview-detail-url" class="text-gray-900">{{ $sidebarLink->url }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">{{ __('sidebar.preview_details.icon') }}:</span>
                                <span id="preview-detail-icon" class="text-gray-900"><i class="{{ $sidebarLink->icon }}"></i></span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">{{ __('sidebar.preview_details.parent') }}:</span>
                                <span id="preview-detail-parent" class="text-gray-900">{{ $sidebarLink->parent ? $sidebarLink->parent->title : __('sidebar.preview_details.none') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">{{ __('sidebar.preview_details.permissions') }}:</span>
                                <span id="preview-detail-permissions" class="text-gray-900">{{ $sidebarLink->permission ?: __('sidebar.preview_details.none') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">{{ __('sidebar.preview_details.status') }}:</span>
                                <span id="preview-detail-status" class="text-gray-900">{{ $sidebarLink->is_active ? __('sidebar.preview_details.active') : __('sidebar.preview_details.inactive') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">{{ __('sidebar.preview_details.external') }}:</span>
                                <span id="preview-detail-external" class="text-gray-900">{{ $sidebarLink->is_external ? __('sidebar.preview_details.yes') : __('sidebar.preview_details.no') }}</span>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">{{ __('sidebar.preview_details.visibility') }}:</span>
                                <span id="preview-detail-visibility" class="text-gray-900">
                                    @if($sidebarLink->visibility === 'all')
                                        {{ __('sidebar.visibility_all') }}
                                    @elseif($sidebarLink->visibility === 'guest')
                                        {{ __('sidebar.visibility_guest') }}
                                    @else
                                        {{ __('sidebar.visibility_authenticated') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 