<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="mt-4 mx-auto">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
           <div class="p-6 bg-white border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800 leading-tight">
                    {{ __('operational-plan.create.title') }}
                </h2>
            </div>

            <div class="p-6 bg-white border-b border-gray-200">
                <form action="{{ route('operational-plan.store') }}" method="POST" class="w-1/2">
                    @csrf

                    <div class="mb-4 flex gap-4 items-center">
                        <label for="is_public" class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.create.form.is_public') }}</label>
                        <input type="checkbox" name="is_public" id="is_public" class="inline shadow appearance-none border rounded text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label for="title" class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.create.form.title') }}</label>
                        <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="period" class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.create.form.period') }}</label>
                        <input type="text" name="period" id="period" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block text-gray-700 text-sm font-bold mb-2">{{ __('operational-plan.create.form.description') }}</label>
                        <textarea name="description" id="description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required></textarea>
                    </div>

                    <button type="submit" class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        {{ __('operational-plan.create.form.submit') }}
                    </button>
            </div>
        </div>
    </div>

</x-app-layout>
