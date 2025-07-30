<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow-md overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                    <div class="flex justify-between px-6 py-3">
                        <h2 class="text-2xl font-semibold text-gray-700">{{ __('manage.elections.index.headline') }}</h2>
                        <a href="{{ route('manage.elections.create') }}"
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{ __('manage.elections.index.create') }}
                        </a>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200 border-b">
                        <thead class="bg-gray-100">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('manage.elections.index.table.name') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('manage.elections.index.table.start_date') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('manage.elections.index.table.end_date') }}
                                </th>
                                <th scope="col"
                                    class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('manage.elections.index.table.state') }}
                                </th>
                                <th scope="col" class="relative px-6 py-3">
                                    {{ __('manage.elections.index.table.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($elections as $election)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $election->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $election->start_date->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $election->end_date->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">
                                            @if ($election->is_future)
                                                <span
                                                    class="text-yellow-500 font-semibold">{{ __('manage.elections.state.future') }}</span>
                                            @elseif ($election->is_past)
                                                <span
                                                    class="text-red-500 font-semibold">{{ __('manage.elections.state.past') }}</span>
                                            @elseif (!$election->is_public)
                                                <span
                                                    class="text-gray-500 font-semibold">{{ __('manage.elections.state.inactive') }}</span>
                                            @else
                                                <span
                                                    class="text-green-500 font-semibold">{{ __('manage.elections.state.active') }}</span>
                                            @endif
                                    <td
                                        class="flex justify-center gap-5 px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div x-data="{ showShareModalElectionId{{ $election->id }}: false }">

                                            <button @click="showShareModalElectionId{{ $election->id }} = true"
                                                class="text-primary-600 hover:text-primary-900">
                                                <i class="fa fa-plus"></i>
                                                <span>{{ __('manage.elections.index.table_actions.share') }}</span>
                                            </button>
    
                                            <x-modal name="showShareModalElectionId{{ $election->id }}" class="hidden">
                                                <div
                                                    class="flex shrink-0 items-center pb-4 text-xl font-medium text-slate-800">
                                                    {{ __('manage.elections.index.table_actions.share') }}
                                                    {{ $election->name }} 
                                                </div>

                                                <div class="flex flex-col gap-4">
                                                    <p class="text-sm text-gray-500">
                                                        {{ __('manage.elections.index.table_actions.share_text') }}
                                                    </p>
                                                    <div class="flex items-center gap-2">
                                                        <input type="text" id="share-link"
                                                            class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                            value="{{ route('election.show', $election->id) }}"
                                                            readonly />
                                                        <button
                                                            onclick="copyToClipboard('{{ route('election.show', $election->id) }}')"
                                                            class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-600 transition duration-200">
                                                            {{ __('manage.elections.index.table_actions.copy') }}
                                                        </button>
                                                    </div>
                                                </div>

                                            </x-modal>
                                        </div>
                                        <a href="{{ route('manage.elections.edit', $election) }}"
                                            class="text-primary-600 hover:text-primary-900">
                                            {{ __('manage.elections.index.table_actions.edit') }}
                                        </a>
                                        <a href="{{ route('manage.elections.show', $election) }}"
                                            class="text-primary-600 hover:text-primary-900">
                                            {{ __('manage.elections.index.table_actions.show') }}
                                        </a>
                                        <form action="{{ route('manage.elections.destroy', $election) }}" method="POST"
                                            onsubmit="return confirm('{{ __('manage.elections.index.table_actions.delete_confirm') }}');">
                                            @csrf
                                            <button type="submit"
                                                class="text-red-600 hover:text-red-900">{{ __('manage.elections.index.table_actions.delete') }}</button>
                                        </form>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap" colspan="4">
                                        <div class="text-sm text-gray-500 text-center">No elections found.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="bg-white px-4 py-3  sm:px-6">
                        {{ $elections->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('{{ __('manage.elections.index.table_actions.copy_link_success') }}');
            }, function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>

</x-app-layout>
