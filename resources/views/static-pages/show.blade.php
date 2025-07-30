<x-app-layout>
    <div class="space-y-6">
        <!-- Breadcrumbs -->
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        <!-- Page Content -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h1 class="text-3xl font-bold text-gray-900">{{ $page->title }}</h1>
            </div>

            <div class="p-6">
                <div class="prose max-w-none">
                    {!! $page->content !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 