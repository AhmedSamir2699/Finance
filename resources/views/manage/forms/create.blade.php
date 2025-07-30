<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow-md overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                    <div class="flex justify-between px-6 py-3">
                        <h2 class="text-2xl font-semibold text-gray-700">{{__('manage.forms.create.headline')}}</h2>
                        <a href="{{ route('manage.forms.index') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{__('manage.forms.create.back')}}
                        </a>
                    </div>
                    <form action="{{ route('manage.forms.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="bg-white px-4 py-3  sm:px-6">
                            <div class="grid grid-cols-6 gap-6">
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="title" class="block text-sm font-medium text-gray-700">{{__('manage.forms.create.name')}}</label>
                                    <input required type="text" name="title" id="title" autocomplete="title" class="mt-1 focus
                                    :ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="description" class="block text-sm font-medium text-gray-700">{{__('manage.forms.create.description')}}</label>
                                    <textarea required name="description" id="description" autocomplete="description" class="mt-1 focus
                                    :ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                                </div>
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="background" class="block text-sm font-medium text-gray-700">{{__('manage.forms.create.file')}}</label>
                                    <input type="file" name="background" id="background" required autocomplete="background" class="mt-1 focus
                                    :ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                </div>
                                <div class="col-span-6 sm:col-span-4">
                                    <label for="category" class="block text-sm font-medium text-gray-700">{{__('manage.forms.create.category')}}</label>
                                    <select id="category" required name="category" autocomplete="category" class="mt-1 focus
                                    :ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                        <option value="">{{__('manage.forms.create.category_placeholder')}}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-span-6 sm:col-span-4">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                        {{__('manage.forms.create.submit')}}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>