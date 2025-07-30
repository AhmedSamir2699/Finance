<x-app-layout>
    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        <!-- Page Header -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">{{ __('static-pages.edit') }}</h2>
                <p class="mt-1 text-sm text-gray-600">{{ __('static-pages.edit_description') }}</p>
            </div>

            <form action="{{ route('manage.static-pages.update', $staticPage->id) }}" method="POST" class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <!-- Title -->
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('static-pages.title') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="title" name="title" value="{{ old('title', $staticPage->title) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                placeholder="{{ __('static-pages.title_placeholder') }}">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Slug -->
                        <div>
                            <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('static-pages.slug') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="slug" name="slug" value="{{ old('slug', $staticPage->slug) }}" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                placeholder="{{ __('static-pages.slug_placeholder') }}">
                            @error('slug')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Visibility -->
                        <div>
                            <label for="visibility" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('static-pages.visibility') }} <span class="text-red-500">*</span>
                            </label>
                            <select id="visibility" name="visibility" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <option value="authenticated" {{ old('visibility', $staticPage->visibility) === 'authenticated' ? 'selected' : '' }}>
                                    {{ __('static-pages.visibility_authenticated') }}
                                </option>
                                <option value="all" {{ old('visibility', $staticPage->visibility) === 'all' ? 'selected' : '' }}>
                                    {{ __('static-pages.visibility_all') }}
                                </option>
                                <option value="guest" {{ old('visibility', $staticPage->visibility) === 'guest' ? 'selected' : '' }}>
                                    {{ __('static-pages.visibility_guest') }}
                                </option>
                            </select>
                            @error('visibility')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $staticPage->is_active) ? 'checked' : '' }}
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">{{ __('static-pages.is_active') }}</span>
                            </label>
                        </div>

                        <!-- Page Info -->
                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('static-pages.page_info') }}</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div><strong>{{ __('static-pages.views') }}:</strong> {{ number_format($staticPage->views) }}</div>
                                <div><strong>{{ __('static-pages.created_at') }}:</strong> {{ $staticPage->created_at->format('Y-m-d H:i') }}</div>
                                @if($staticPage->updated_at != $staticPage->created_at)
                                    <div><strong>{{ __('static-pages.updated_at') }}:</strong> {{ $staticPage->updated_at->format('Y-m-d H:i') }}</div>
                                @endif
                                @if($staticPage->creator)
                                    <div><strong>{{ __('static-pages.created_by') }}:</strong> {{ $staticPage->creator->name }}</div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Meta Description -->
                        <div>
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('static-pages.meta_description') }}
                            </label>
                            <textarea id="meta_description" name="meta_description" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                placeholder="{{ __('static-pages.meta_description_placeholder') }}">{{ old('meta_description', $staticPage->meta_description) }}</textarea>
                            @error('meta_description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Meta Keywords -->
                        <div>
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('static-pages.meta_keywords') }}
                            </label>
                            <input type="text" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $staticPage->meta_keywords) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500"
                                placeholder="{{ __('static-pages.meta_keywords_placeholder') }}">
                            @error('meta_keywords')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Public URL -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('static-pages.public_url') }}
                            </label>
                            <div class="flex">
                                <input type="text" value="{{ $staticPage->public_url }}" readonly
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-l-md bg-gray-50 text-gray-500">
                                <button type="button" onclick="copyToClipboard('{{ $staticPage->public_url }}')"
                                    class="px-3 py-2 bg-gray-200 border border-gray-300 border-l-0 rounded-r-md hover:bg-gray-300">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Editor -->
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                        {{ __('static-pages.content') }} <span class="text-red-500">*</span>
                    </label>
                    <x-text-editor name="content" :value="old('content', $staticPage->content)" />
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <a href="{{ route('manage.static-pages.index') }}"
                        class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        {{ __('static-pages.cancel') }}
                    </a>
                    <button type="submit"
                        class="px-4 py-2 bg-primary-600 border border-transparent rounded-md text-sm font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                        {{ __('static-pages.update') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Copy to clipboard function and slug generation -->
    <script>
        function generateSlug(text) {
            return text
                .toLowerCase()
                .replace(/[^a-z0-9\u0600-\u06FF\s-]/g, '')  // Allow Arabic characters
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .replace(/^-+|-+$/g, '');
        }

        document.getElementById('title').addEventListener('input', function() {
            const title = this.value;
            const slug = generateSlug(title);
            document.getElementById('slug').value = slug;
        });

        // Also generate slug on page load if title is already filled
        document.addEventListener('DOMContentLoaded', function() {
            const title = document.getElementById('title').value;
            if (title && !document.getElementById('slug').value) {
                const slug = generateSlug(title);
                document.getElementById('slug').value = slug;
            }
        });

        // Handle meta keywords formatting (replace spaces with commas)
        document.getElementById('meta_keywords').addEventListener('blur', function() {
            let value = this.value;
            // Replace multiple spaces with single comma
            value = value.replace(/\s+/g, ',');
            // Remove multiple consecutive commas
            value = value.replace(/,+/g, ',');
            // Remove leading/trailing commas
            value = value.replace(/^,+|,+$/g, '');
            this.value = value;
        });

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                toastr().success('{{ __("static-pages.messages.link_copied") }}');
            });
        }
    </script>
</x-app-layout> 