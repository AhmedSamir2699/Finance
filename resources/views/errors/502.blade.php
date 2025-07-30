<x-app-layout>
    <div class="container mt-4 mx-auto">
        <div class="flex flex-col items-center justify-center py-12 bg-white shadow-lg rounded-lg gap-6">
            <h1 class="text-xl font-semibold text-gray-700">
                <i class="fas fa-plug  text-primary-300 fa-4x"></i>
            </h1>
            <p class="text-lg text-gray-500">
                {{ __('errors.502.description') }}
            </p>

            <small class="text-gray-500 w-full block text-center">
                {{ __('errors.502.code') }}
            </small>

        </div>
    </div>

</x-app-layout>