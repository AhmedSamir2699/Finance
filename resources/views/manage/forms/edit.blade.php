<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div x-data="{ tab: 'general' }">
        <div class="overflow-hidden rounded-t-lg border border-gray-100 bg-gray-50 mt-4">
            <ul class="flex items-center text-sm font-medium">
                <li class="flex-1">
                    <a href="#" @click="tab = 'general'"
                        class="flex items-center justify-center gap-2 px-3 py-2"
                        :class="tab == 'general' ? 'bg-white text-gray-700 shadow border-b border-primary-base' : 'text-gray-500 hover:bg-white hover:text-gray-700 hover:shadow'"
                        >
                        {{ __('manage.forms.edit.tabs.general') }}
                    </a>
                </li>
                <li class="flex-1">
                    <a href="#"
                        @click="tab = 'fields'"
                        class="flex items-center justify-center gap-2  px-3 py-2"
                        :class="tab == 'fields' ? 'bg-white text-gray-700 shadow border-b border-primary-base' : 'text-gray-500 hover:bg-white hover:text-gray-700 hover:shadow'"
                        >
                        {{ __('manage.forms.edit.tabs.fields') }}    
                    </a>
                </li>
                <li class="flex-1">
                    <a href="#"
                        @click="tab = 'path'"
                        class="flex items-center justify-center gap-2  px-3 py-2"
                        :class="tab == 'path' ? 'bg-white text-gray-700 shadow border-b border-primary-base' : 'text-gray-500 hover:bg-white hover:text-gray-700 hover:shadow'"
                        
                        >
                        {{ __('manage.forms.edit.tabs.path') }}    
                    </a>
                </li>

                <li class="flex-1">
                    <a href="#"
                        @click="tab = 'visibility'"
                        class="flex items-center justify-center gap-2  px-3 py-2"
                        :class="tab == 'visibility' ? 'bg-white text-gray-700 shadow border-b border-primary-base' : 'text-gray-500 hover:bg-white hover:text-gray-700 hover:shadow'"
                        >
                        {{ __('manage.forms.edit.tabs.visibility') }}
                    </a>
                </li>

            </ul>
        </div>
        <div class="flex flex-col" x-show="tab == 'general'">
            <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow-md overflow-hidden border-b border-gray-200 rounded-b-lg bg-white">
                        <form action="{{ route('manage.forms.update',[$form]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="bg-white px-4 py-3  sm:px-6">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="title"
                                            class="block text-sm font-medium text-gray-700">{{ __('manage.forms.edit.name') }}</label>
                                        <input type="text" name="title" id="title" autocomplete="title"
                                            value="{{ $form->title }}"
                                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="description"
                                            class="block text-sm font-medium text-gray-700">{{ __('manage.forms.edit.description') }}</label>
                                        <textarea name="description" id="description"
                                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">{{ $form->description }}</textarea>
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="background"
                                            class="block text-sm font-medium text-gray-700">{{ __('manage.forms.edit.file') }}</label>
                                        <img src="{{ asset('storage/' . $form->background) }}"
                                            alt="{{ $form->title }}" class="w-1/4">
                                        <input type="file" name="background" id="background"
                                            autocomplete="background"
                                            class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    </div>
                                    <div class="col-span-6 sm:col-span-4">
                                        <label for="category"
                                            class="block text-sm font-medium text-gray-700">{{ __('manage.forms.edit.category') }}</label>
                                        <select id="category" name="category" autocomplete="category"
                                            class="mt-1 focus
                                        :ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                            <option value="">{{ __('manage.forms.edit.category_placeholder') }}
                                            </option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ $category->id == $form->request_form_category_id ? 'selected' : '' }}>
                                                    {{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-span-6 sm:col-span-4">
                                        <button type="submit"
                                            class="inline-flex items-center px-10 py-1 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            {{ __('manage.forms.edit.submit') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="flex gap-3 items-center py-4">
                            <form action="{{ route('manage.forms.destroy', $form) }}" method="POST" class=" w-full mx-auto text-end">
                                @csrf
                                <button type="submit"
                                    onclick="return confirm('هل أنت متأكد من حذف النموذج؟ \r\n \r\n هذا الإجراء لا يمكن التراجع عنه')"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium text-red-600">
                                    <i class="fa fa-trash fa-xs mx-1 p-1"></i>
                                    {{ __('manage.forms.edit.delete_form') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="shadow-md overflow-hidden border-b border-gray-200 sm:rounded-b-lg bg-white p-5" x-show="tab == 'fields'">
            <livewire:manage.forms.fields :form="$form" />
        </div>

        <div class="shadow-md overflow-hidden border-b border-gray-200 sm:rounded-b-lg bg-white p-5" x-show="tab == 'path'">
            <livewire:manage.forms.path :form="$form" />
        </div>

        <div class="shadow-md overflow-hidden border-b border-gray-200 sm:rounded-b-lg bg-white p-5" x-show="tab == 'visibility'">
            <livewire:manage.forms.visibility :form="$form" />
        </div>

    </div>

</x-app-layout>
