<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow-md overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                    <div class="px-6 py-4">
                        <h2 class="text-2xl font-semibold text-gray-700">
                            {{ __('manage.elections.results.headline', ['name' => $election->name]) }}
                            <small>
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
                            </small>
                        </h2>
                        <div class="flex flex-row justify-between gap-3">
                            <p class="text-gray-500 mt-1">
                                {{ __('manage.elections.results.duration', ['start' => $election->start_date->format('Y-m-d'), 'end' => $election->end_date->format('Y-m-d')]) }}
                            </p>
                            <p class="text-gray-500 mt-1">
                                {{ __('manage.elections.results.total_votes', ['total' => $votes->count()]) }}
                                <a href="{{ route('manage.elections.votes', $election->id) }}"
                                    class="text-blue-500 hover:text-blue-700">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </p>
                            <p class="text-gray-500 mt-1">
                                {{ __('manage.elections.results.total_candidates', ['total' => $election->candidates_collection->count()]) }}
                            </p>
                            <p class="text-gray-500 mt-1">
                                {{ __('manage.elections.results.total_voters', ['total' => $election->votes()->distinct('ip_address')->count()]) }}
                            </p>
                        </div>
                    </div>


                    @php
                        // Get vote counts per candidate
                        $voteCounts = $votes->groupBy('candidate_id')->map->count();

                        // Prepare full list with 0 default
                        $candidates = $election->candidates_collection->keyBy('id');
                        $totalVotes = $votes->count();

                        // Build results: each candidate with their vote count (default 0)
                        $results = $candidates->map(function ($candidate, $id) use ($voteCounts) {
                            return [
                                'name' => $candidate['name'],
                                'votes' => $voteCounts[$id] ?? 0,
                            ];
                        });

                        // Sort by vote count descending
                        $sortedResults = $results->sortByDesc('votes');
                    @endphp


                    <div class="px-6 py-4">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('manage.elections.results.candidate') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('manage.elections.results.votes') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-start text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        {{ __('manage.elections.results.percentage') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($sortedResults as $candidate)
                                    @php
                                        $percentage =
                                            $totalVotes > 0 ? round(($candidate['votes'] / $totalVotes) * 100, 2) : 0;
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $candidate['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $candidate['votes'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $percentage }}%
                                        </td>
                                    </tr>
                                @endforeach


                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
