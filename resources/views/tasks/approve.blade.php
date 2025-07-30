<x-app-layout>
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="container mt-4 mx-auto">
        <div class="flex flex-col bg-white py-2 px-4 rounded-md shadow-md border">
            <h4 class="text-xl font-semibold text-gray-900">{{ $task->title }}</h4>
            <form action="{{ route('tasks.approve.store', $task->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="flex flex-col gap-4 mt-4">
                    <div class="flex flex-col gap-2">
                        <label for="actualTime" class="text-gray-700">{{ __('tasks.submit.actualTime') }}</label>
                        <input type="number" name="actualTime" id="actualTime" min="0" max="100"
                            class="rounded-md border border-gray-400 border-b block py-2 px-4 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"
                            required
                            readonly
                            value="{{ number_format($actualTime, 0) }}">
                    </div>
                </div>
                <div class="flex flex-col gap-4 mt-4">
                    <div class="flex flex-col gap-2">
                        <label for="quality" class="text-gray-700">{{ __('tasks.submit.quality') }}</label>
                        <input type="number" name="quality" id="quality" min="0" max="100"
                            class="rounded-md border border-gray-400 border-b block py-2 px-4 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"
                            required>
                    </div>
                </div>
                <div class="flex flex-col gap-4 mt-4">
                    <div class="flex flex-col gap-2">
                        <label for="comment" class="text-gray-700">{{ __('tasks.submit.description') }}</label>
                        <textarea name="comment" id="comment" rows="5"
                            class="rounded-md border border-gray-400 border-b block py-2 px-4 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none"
                            required></textarea>
                    </div>
                </div>

                    <button type="submit"
                        class="block w-1/3 mx-auto bg-primary-base text-white rounded-md py-2 px-4 my-5 text-center">{{ __('tasks.submit.send') }}</button>

            </form>
        </div>
    </div>

</x-app-layout>
