<x-app-layout>
    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        <!-- Page Header -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900">{{ __('static-pages.public.index') }}</h1>
                <p class="mt-2 text-gray-600">{{ __('static-pages.public.description') }}</p>
            </div>
        </div>

        <!-- Pages Grid -->
        @if($pages->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($pages as $page)
                    <div class="bg-white shadow rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-200">
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                                <a href="{{ $page->public_url }}" class="hover:text-primary-600 transition-colors duration-200">
                                    {{ $page->title }}
                                </a>
                            </h3>
                            
                            @if($page->meta_description)
                                <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit($page->meta_description, 150) }}</p>
                            @endif

                            <div class="flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center space-x-4">
                                    <span><i class="fas fa-eye mr-1"></i>{{ number_format($page->views) }}</span>
                                    <span><i class="fas fa-calendar mr-1"></i>{{ $page->created_at->format('Y-m-d') }}</span>
                                </div>
                                <a href="{{ $page->public_url }}" 
                                    class="text-primary-600 hover:text-primary-700 font-medium">
                                    {{ __('static-pages.read_more') }} <i class="fas fa-arrow-right ml-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-white shadow rounded-lg p-8 text-center">
                <div class="text-gray-400 mb-4">
                    <i class="fas fa-file-alt text-6xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('static-pages.no_pages_available') }}</h3>
                <p class="text-gray-600">{{ __('static-pages.no_pages_available_description') }}</p>
            </div>
        @endif
    </div>
</x-app-layout> 