<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full rounded-lg overflow-hidden ">
            <form action="{{ route('manage.roles.store') }}" method="POST">
                @csrf
                <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="name">
                            {{ __('roles.edit.form.english_name') }}
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="name" name="name" type="text" required placeholder="role-name">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="display_name">
                            {{ __('roles.edit.form.arabic_name') }}
                        </label>
                        <input
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                            id="display_name" name="display_name" required type="text" placeholder="إسم الدور">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                            {{ __('roles.edit.form.answers_to') }}
                        </label>
                        <div class="relative">
                            <select
                                class="block appearance-none w-full bg-white border border-gray-400 hover:border-gray-500 px-4 py-2 pr-8 rounded shadow leading-tight focus:outline-none focus:shadow-outline"
                                id="answers_to" name="answers_to">
                                <option value="">{{ __('roles.edit.form.answers_to_select') }}</option>
                                @foreach (\App\Models\Role::get() as $answerToRole)
                                    <option value="{{ $answerToRole->id }}">
                                        {{ $answerToRole->display_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        <div class="flex items
                    -center justify-between">
                            <button
                                class="bg-primary-500 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
                                type="submit">
                                {{ __('roles.edit.form.submit') }}
                            </button>
                        </div>
            </form>
        </div>

</x-app-layout>
