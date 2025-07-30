<x-app-layout>


    <div class="w-full flex justify-between items-center mb-2">
        <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

        @can('role.create')
            <a href="{{ route('manage.roles.create') }}"
                class="bg-primary-500  ml-28 text-white active:bg-primary-600 font-bold uppercase text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none  ease-linear transition-all duration-150">
                <i class="fa fa-plus"></i>
                {{ __('roles.index.create') }}
            </a>
        @endcan
    </div>

    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto w-full">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>

                        <th
                            class="px-5 py-3 border-b-2 hidden md:table-cell border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('roles.index.table.name') }}</th>

                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('roles.index.table.answers_to') }}</th>

                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('roles.index.table.permissions_count') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('roles.index.table.users_count') }}</th>

                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('roles.index.table.actions') }}</th>

                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td class="px-5 py-5 border-b hidden md:table-cell border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    {{ $role->display_name }}</p>
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b hidden md:table-cell border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    {{ $role->superior ? $role->superior->display_name : '-' }}</p>
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    {{ $role->permissions_count }}</p>
                                </p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">
                                    {{ $role->users_count }}</p>
                                </p>
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm flex flex-col md:flex-row gap-1">
                                @can('role.edit')
                                    <a href="{{ route('manage.roles.edit', $role->id) }}"
                                        class="text-primary-600 hover:text-primary-900 px-2 py-1 border rounded me-3 my-1 md:my-auto">
                                        <i class="fa fa-edit"></i>
                                        {{ __('roles.index.table.edit') }}</a>
                                @endcan
                                @can('role.assign-permission')
                                    <a href="{{ route('manage.roles.edit.permissions', $role->id) }}"
                                        class="text-primary-600 hover:text-primary-900 px-2 py-1 border rounded me-3 my-1 md:my-auto">
                                        <i class="fa fa-edit"></i>
                                        {{ __('roles.index.table.permissions') }}</a>
                                @endcan

                                @can('role.delete')
                                    <form action="{{ route('manage.roles.destroy', $role->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-600 hover:text-red-900 px-2 py-1 border rounded me-3 my-1 md:my-auto">
                                            <i class="fa fa-trash"></i>
                                            {{ __('roles.index.table.delete') }}</button>
                                    </form>
                                @endcan

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-app-layout>
