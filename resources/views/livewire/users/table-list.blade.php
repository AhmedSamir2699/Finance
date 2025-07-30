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

            <input wire:model.live="search" placeholder="{{ __('users.search_placeholder') }}"
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
    </div>

    <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
        <div class="inline-block min-w-full shadow rounded-lg overflow-hidden">
            <table class="min-w-full leading-normal">
                <thead>
                    <tr>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('users.fullname') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('users.department') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 hidden md:table-cell border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('users.latest_activity') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('users.role') }}</th>
                        <th
                            class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            {{ __('users.action') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex items-center">
                                    <div class="ml-3">
                                        <p class="text-gray-900 whitespace-no-wrap">{{ $user->name }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                               @if($user->department)
                                    <a href="{{route('manage.departments.show',[$user->department])}}" class="text-gray-900 whitespace-no-wrap">{{ $user->department->name }}</a>
                                @else
                                    <p class="text-gray-900 whitespace-no-wrap">-</p>
                                @endif
                            </td>
                            <td class="px-5 py-5 border-b hidden md:table-cell border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $user->latest_activity }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <p class="text-gray-900 whitespace-no-wrap">{{ $user->role }}</p>
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm flex flex-col md:flex-row gap-1" x-data="{ showDeleteModal: false }">
                                <a href="{{ route('users.show', $user->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 px-2 py-1 border rounded me-3 my-1 md:my-auto">
                                    <i class="fa fa-eye"></i>
                                    {{ __('users.index.show') }}</a>
                                    
                                <a href="{{ route('manage.users.edit', $user->id) }}"
                                    class="text-indigo-600 hover:text-indigo-900 px-2 py-1 border rounded me-3 my-1 md:my-auto">
                                    <i class="fa fa-edit"></i>
                                    {{ __('users.index.edit') }}</a>

                                    <!-- Delete Button -->
                                    <button type="button"
                                        x-on:click.stop="showDeleteModal = true;"
                                        class="text-red-400 hover:text-red-700 focus:outline-none focus:ring focus:ring-red-500">
                                        <i class="fas fa-trash"></i>
                                    </button>

                                    <!-- Confirmation Modal -->
                                    <div x-show="showDeleteModal" style="display: none;"
                                        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                                        <div class="bg-white rounded-lg p-6 w-96" @click.away="showDeleteModal = false">
                                            <h3 class="text-lg font-medium text-gray-900">{{ __('manage.users.delete.title') }}</h3>
                                            <p class="mt-2 text-sm text-gray-500">
                                                {{ __('manage.users.delete.confirmation') }}
                                            </p>
                                            <div class="mt-4 flex justify-end space-x-3">
                                                <!-- Confirm Button -->
                                                <form action="{{ route('manage.users.destroy', $user->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                                        {{ __('manage.users.delete.button') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>


                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="px-5 py-5 border-t flex flex-col items-center xs:justify-between">
        {{ $users->links() }}
    </div>
</div>
