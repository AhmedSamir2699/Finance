<x-app-layout>

    <div class="w-full flex justify-between items-center mb-2" x-data="{ AddPermissionModal: false }">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        @can('role.create')
            <a @click="AddPermissionModal = true"
                class="bg-primary-500 cursor-pointer  ml-28 text-white active:bg-primary-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none  ease-linear transition-all duration-150">
                <i class="fa fa-plus"></i>
                {{ __('roles.permissions.create') }}
            </a>

            <x-modal name="AddPermissionModal" class="hidden">
                <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                    {{ __('departments.index.add_modal_label') }}
                </div>
                <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                    <form method="post" action="{{ route('manage.roles.store.permissions', $role) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="name"
                                class="block text-sm font-medium text-slate-700">{{ __('roles.permissions.slug') }}</label>
                            <input type="text" name="name" id="name"
                                placeholder="group.component.action" required
                                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                        </div>
                        <div class="mb-4">
                            <label for="display_name"
                                class="block text-sm font-medium text-slate-700">{{ __('roles.permissions.name') }}</label>
                            <input type="text" name="display_name" id="display_name"
                                placeholder="{{ __('roles.permissions.name') }}" required
                                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                        </div>
            
                        <div class="flex shrink-0 flex-wrap items-center pt-4 justify-end">
                            <button
                                class="rounded-md bg-green-600 py-2 px-4 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none ml-2"
                                type="submit">
                                {{__('departments.index.save')}}
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        @endcan
    </div>

    <div class="bg-white shadow-md rounded px-8 pt-6 mt-5 pb-8 mb-4">

        <h1 class="text-2xl font-bold mb-4">{{ __('roles.permissions.title', ['role' => $role->display_name]) }}</h1>


        <livewire:manage.roles.permissions-editor :role="$role" />
    </div>

</x-app-layout>
