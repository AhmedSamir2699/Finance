<div wire:poll.15000ms>
    <div class="flex flex-col">
        <div class="mb-8">
            <div class="flex flex-col ">
                <div class="flex flex-row justify-between">
                    <div class="flex flex-row flex-1 gap-1">
                        <button wire:click="changeTab('inbox')"
                            class="hover:bg-white hover:rounded-t-md hover:shadow-lg text-lg font-semibold  text-primary-base px-5 py-2 transition ease-in-out
                            {{ $currentTab === 'inbox' ? 'bg-white rounded-t-md shadow-lg' : '' }}">
                            {{ __('messages.tabs.inbox') }}
                        </button>
                        <button wire:click="changeTab('sent')"
                            class="hover:bg-white hover:rounded-t-md hover:shadow-lg text-lg font-semibold text-primary-800 px-5 py-2 transition ease-in-out
                            {{ $currentTab === 'sent' ? 'bg-white rounded-t-md shadow-lg' : '' }}">

                            {{ __('messages.tabs.sent') }}
                        </button>
                    </div>
                    <div class="flex-0">
                        <a href="{{ route('messages.create') }}"
                            class="block text-lg font-semibold bg-primary-base rounded-t-md shadow-lg text-white hover:text-secondary-300 px-5 py-2 transition ease-in-out">
                            {{ __('messages.tabs.compose') }}
                        </a>
                    </div>
                </div>
                <div class="w-full h-full bg-white rounded-b-lg p-3">
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
                                        <option selected>10</option>
                                        <option>20</option>
                                        <option>50</option>
                                        <option>100</option>
                                    </select>
                                </div>
                            </div>

                            <div class="-mx-4 sm:-mx-8 px-4 sm:px-8 py-4 overflow-x-auto">
                                <div class="inline-block min-w-full overflow-hidden">
                                    <table class="min-w-full leading-normal">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    {{ __('messages.table.subject') }}</th>
                                                @if ($currentTab === 'inbox')
                                                    <th
                                                        class="px-5 py-3 border-b-2 hidden md:table-cell border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                        {{ __('messages.table.from') }}</th>
                                                @else
                                                    <th
                                                        class="px-5 py-3 border-b-2 hidden md:table-cell border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                        {{ __('messages.table.to') }}</th>
                                                @endif
                                                <th
                                                    class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                                    {{ __('messages.table.timestamp') }}</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($messages as $message)
                                                <tr @click="window.location.href = '{{ route('messages.show', $message) }}'"
                                                    class="transition duration-300 ease-in-out border-t border-gray-200 hover:bg-gray-100 hover:cursor-pointer {{ ($currentTab == 'inbox'  && !$message->is_read ) ? 'bg-secondary-50 font-bold' : '' }}">
                                                    <td class="px-5 py-5 text-sm">
                                                        <div class="flex items-center">
                                                            <div class="me-3  {{ $message->attachedFiles->count() ? 'inline-block' : 'hidden' }}">
                                                                <p class="text-gray-900 whitespace-no-wrap">
                                                                    <i class="fas fa-paperclip"></i>
                                                                </p>
                                                            </div>
                                                            <div class="">
                                                                <p class="text-gray-900 whitespace-no-wrap">
                                                                    {{ $message->subject }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        </div>
                                                    </td>
                                                    @if ($currentTab === 'inbox')
                                                        <td class="px-5 py-5 text-sm">
                                                            <p class="text-gray-900 whitespace-no-wrap">
                                                                {{ $message->from->name }}</p>
                                                        </td>
                                                    @else
                                                        <td class="px-5 py-5 text-sm">
                                                            <p class="text-gray-900 whitespace-no-wrap">
                                                                {{ $message->recipients->first()->name}} +{{$message->recipients->count()-1 }}</p>
                                                        </td>
                                                    @endif
                                                    <td class="px-5 py-5 text-sm">
                                                        <p class="text-gray-900 whitespace-no-wrap">
                                                            {{ Carbon\Carbon::parse($message->created_at)->diffForHumans() }}
                                                        </p>
                                                    </td>

                                                </tr>
                                            @empty

                                                <tr>
                                                    <td colspan="5" class="text-center py-4">
                                                        {{ __('messages.no_messages') }}
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="px-5 py-5 flex flex-col items-center xs:justify-between">
                                {{ $messages->links() }}
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
