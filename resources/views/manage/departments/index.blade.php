<x-app-layout>
    
    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />
    
    <div x-data="{ 'AddDepartmentModal': false }">


        <livewire:departments.index.table-list />


        <x-modal name="AddDepartmentModal">
            <div class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                {{ __('departments.index.add_modal_label') }}
            </div>
            <div class="relative border-t border-slate-200 py-4 leading-normal text-slate-600 font-light">
                <form method="post" action="{{ route('manage.departments.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="name"
                            class="block text-sm font-medium text-slate-700">{{ __('departments.index.name') }}</label>
                        <input type="text" name="name" id="name"
                            placeholder="{{ __('departments.index.name') }}" required
                            class="appearance-none rounded border border-gray-400 border-b block pl-8 pr-6 py-2 w-full bg-white text-sm placeholder-gray-400 text-gray-700 focus:bg-white focus:placeholder-gray-600 focus:text-gray-700 focus:outline-none" />
                    </div>
                    <div class="mb-4">
                        <label for="description"
                            class="block text-sm font-medium text-slate-700">{{ __('departments.index.description') }}</label>
                        <input type="text" name="description" id="description"
                            placeholder="{{ __('departments.index.description') }}" required
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
    </div>

</x-app-layout>
