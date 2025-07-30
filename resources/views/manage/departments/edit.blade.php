<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    <div class="flex flex-col mt-10" x-data="{ tab: 'edit' }">
        <div class="m-auto w-2/3">
            <div class="flex flex-col mt-4">
                <div class="flex flex-row justify-between gap-1">
                    <a @click="tab = 'edit'"
                        class="block rounded-t-md hover:shadow-lg text-lg font-semibold  text-primary-base px-5 py-2 transition ease-in-out cursor-pointer"
                        :class="tab === 'edit' ? 'bg-primary-base text-white' : 'bg-white text-primary-base'">
                        {{ __('departments.edit.tab') }}
                    </a>
                    <a @click="tab = 'executive'"
                        class="block rounded-t-md hover:shadow-lg text-lg font-semibold  text-primary-base px-5 py-2 transition ease-in-out cursor-pointer"
                        :class="tab === 'executive' ? 'bg-primary-base text-white' : 'bg-white text-primary-base'">
                        {{ __('departments.edit.executive_plan_columns') }}
                    </a>

                </div>

                <div x-show="tab === 'edit'" class="bg-white rounded-b p-5" id="edit" role="tabpanel"
                    aria-labelledby="edit-tab">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('manage.departments.update', $department) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-2 sm:grid-cols-2 gap-6 mt-4">

                                    <x-text-input for="fullname" label="{{ __('departments.edit.name') }}" name="name"
                                        value="{{ $department->name }}" required />

                                    <x-text-input for="description" label="{{ __('departments.edit.description') }}"
                                        name="description" value="{{ $department->description }}" required />


                                    <div class="block w-full h-full text-center justify-end mt-4 col-span-2">
                                        <button
                                            class="px-4 py-2 mx-auto bg-green-800 text-white rounded-md hover:bg-green-700 focus:outline-none focus:bg-green-700">{{ __('users.edit.save') }}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div x-show="tab === 'executive'" class="bg-white rounded-b p-5" id="executive" role="tabpanel"
                    aria-labelledby="executive-tab">
                    <div class="card">
                        <div class="card-body">
                            <livewire:departments.edit.executive-plan-columns :department="$department" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
