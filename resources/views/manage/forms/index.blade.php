<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow-md overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                    <div class="flex justify-between px-6 py-3">
                        <h2 class="text-2xl font-semibold text-gray-700">{{__('manage.forms.index.headline')}}</h2>
                        <a href="{{ route('manage.forms.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{__('manage.forms.index.create')}}
                        </a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 border-b">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{__('manage.forms.index.table.name')}}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{__('manage.forms.index.table.category')}}
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{__('manage.forms.index.table.description')}}
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    {{__('manage.forms.index.table.actions')}}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($forms as $form)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $form->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $form->category->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $form->description }}</div>
                                    </td>
                                    <td class="flex justify-center gap-5 px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('manage.forms.edit', $form) }}" class="text-primary-600 hover:text-primary-900">
                                            {{__('manage.forms.index.table_actions.edit')}}
                                        </a>
                                        <a href="{{ route('manage.forms.field-position', $form) }}" class="text-secondary-600 hover:text-secondary-900">
                                            {{__('manage.forms.index.table_actions.field_position')}}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap" colspan="4">
                                        <div class="text-sm text-gray-500 text-center">No forms found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="bg-white px-4 py-3  sm:px-6">
                        {{ $forms->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>
