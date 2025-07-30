<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-semibold">{{ __('evaluate.index.headline') }}</h2>
                        <a href="{{ route('evaluate.random') }}" 
                           class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('evaluate.index.create_evaluation') }}
                        </a>
                    </div>

                    <div class="mt-6">
                        <livewire:evaluate.history />
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 