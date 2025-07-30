<div class="mt-6">
    <div class="mt-3 flex flex-row">

        <div class="block relative mt-2 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <svg viewBox="0 0 24 24" class="h-4 w-4 fill-current text-gray-500">
                    <path
                        d="M10 4a6 6 0 100 12 6 6 0 000-12zm-8 6a8 8 0 1114.32 4.906l5.387 5.387a1 1 0 01-1.414 1.414l-5.387-5.387A8 8 0 012 10z">
                    </path>
                </svg>
            </span>

            <input wire:model.live="search" placeholder="{{ __('departments.index.search_placeholder') }}"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
        </div>

        <div class="block relative mt-2 ms-3 sm:mt-0">
            <span class="absolute inset-y-0 left-0 flex items-center pl-2">
                <i class="fa fa-sort-amount-up h-4 w-4 fill-current text-gray-500"></i>
            </span>

            <select wire:model.live="perPage"
                class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none">
                <option>5</option>
                <option>10</option>
                <option>15</option>
                <option>20</option>
            </select>
        </div>

        <div class="flex justify-start mx-3">
            <button @click="AddDepartmentModal = true"
                class="rounded-md bg-primary-base py-1 px-3 border border-transparent text-center text-sm text-white transition-all shadow-md hover:shadow-lg focus:bg-green-700 focus:shadow-none active:bg-green-700 hover:bg-green-700 active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none">
                {{__('departments.index.add_button')}}
            </button>
        </div>
    </div>

    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('departments.index.name') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('departments.index.count') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('departments.index.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($departments as $department)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex items-center">
                                    <div class="ml-3">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $department->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $department->users_count }}</p>
                            </td>
                          
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm flex flex-col md:flex-row gap-1">
                                <a href="{{ route('manage.departments.show', $department->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 px-2 py-1 border rounded me-3 my-1 md:my-auto">
                                    <i class="fa fa-eye"></i>
                                    {{ __('departments.index.show') }}</a>
                                    
                                <a href="{{ route('manage.departments.edit', $department->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 px-2 py-1 border rounded me-3 my-1 md:my-auto">
                                    <i class="fa fa-edit"></i>
                                    {{ __('departments.index.edit') }}</a>

                                    <a href="{{ route('manage.departments.destroy', $department->id) }}"
                                    class="text-red-600 hover:text-red-900 px-2 py-1 border rounded me-3 my-1 md:my-auto">
                                    <i class="fa fa-trash"></i>
                                    {{ __('departments.index.delete') }}</a>

                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="px-5 py-5 border-t flex flex-col items-center xs:justify-between">
        {{ $departments->links() }}
    </div>
</div>
