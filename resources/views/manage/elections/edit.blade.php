<x-app-layout>

    <x-breadcrumbs :breadcrumbs="$breadcrumbs" />

    <div class="flex flex-col mt-2">
        <div class="overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="align-middle inline-block min-w-full sm:px-6 lg:px-8">
                <div class="shadow-md overflow-hidden border-b border-gray-200 sm:rounded-lg bg-white">
                    <div class="flex justify-between px-6 py-3 my-6">
                        <h2 class="text-2xl font-semibold text-gray-700">
                            {{ __('manage.elections.edit.headline') }}
                        </h2>
                        @if ($election->votes()->count() > 0)
                            <form method="POST" id="clearVotes"
                                action="{{ route('manage.elections.clearVotes', $election->id) }}">
                                @csrf
                                <button type="submit"
                                    onclick="if(confirm('{{ __('manage.elections.edit.clear_votes_confirm') }}')) this.closest('form').submit();"
                                    class="items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    {{ __('manage.elections.edit.clear_votes') }}
                                </button>
                            </form>
                        @endif
                        <button type="submit" onclick="document.getElementById('update').submit();"
                            class="items-center w-1/4 px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                            {{ __('manage.elections.edit.save') }}
                        </button>
                    </div>
                    <form method="POST" id="update" action="{{ route('manage.elections.update', $election->id) }}">
                        @csrf
                        <div class="flex flex-row">

                            <div class="px-6 py-4 flex-1">
                                <label for="name"
                                    class="block text-sm font-medium text-gray-700">{{ __('manage.elections.edit.name') }}</label>
                                <input type="text" name="name" id="name" value="{{ $election->name }}"
                                    required
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>

                        </div>
                        <div class="flex flex-row">
                            <div class="px-6 py-4">
                                <label for="start_date"
                                    class="block text-sm font-medium text-gray-700">{{ __('manage.elections.edit.start_date') }}</label>
                                <input type="date" name="start_date" id="start_date"
                                    value="{{ $election->start_date->format('Y-m-d') }}" required
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>
                            <div class="px-6 py-4">
                                <label for="end_date"
                                    class="block text-sm font-medium text-gray-700">{{ __('manage.elections.edit.end_date') }}</label>
                                <input type="date" name="end_date" id="end_date"
                                    value="{{ $election->end_date->format('Y-m-d') }}" required
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md" />
                            </div>

                            <div class="px-6 py-4">
                                <label for="is_public"
                                    class="block text-sm font-medium text-gray-700">{{ __('manage.elections.edit.is_public') }}</label>
                                <select name="is_public" id="is_public" required
                                    class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                                    <option value="1" {{ $election->is_public ? 'selected' : '' }}>
                                        {{ __('manage.elections.edit.public') }}</option>
                                    <option value="0" {{ !$election->is_public ? 'selected' : '' }}>
                                        {{ __('manage.elections.edit.private') }}</option>
                                </select>
                            </div>
                        </div>

                        @php
                            $hasVotes = $election->votes()->count() > 0;
                        @endphp

                        <div x-data="{
                            candidates: {{ $election->candidates_collection }},
                            hasVotes: {{ $hasVotes ? 'true' : 'false' }},
                            addCandidate() {
                                if (!this.hasVotes) this.candidates.push({ name: '' });
                            },
                            removeCandidate(index) {
                                if (!this.hasVotes) this.candidates.splice(index, 1);
                            }
                        }" class="px-6 py-4">
                            <label
                                class="block text-sm font-medium text-gray-700">{{ __('manage.elections.edit.candidates') }}</label>
                            <p class="text-sm text-gray-500 mb-2" x-show="hasVotes">
                                {{ __('manage.elections.edit.candidates_warning') }}
                            </p>
                            <template x-for="(candidate, index) in candidates" :key="index">
                                <div class="flex items-center gap-2 mt-2">
                                    <input type="text" name="candidates[][name]" x-model="candidate.name"
                                        class="mt-1 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                        :readonly="hasVotes" required />
                                    <button type="button" @click="removeCandidate(index)" x-show="!hasVotes"
                                        class="bg-red-500 text-white px-2 py-1 rounded-md hover:bg-red-600 transition duration-200">
                                        {{ __('manage.elections.edit.remove') }}
                                    </button>
                                </div>
                            </template>

                            <button type="button" @click="addCandidate" x-show="!hasVotes"
                                class="bg-white text-blue-500 px-4 py-2 rounded-md hover:bg-blue-100 transition duration-200 mt-4 border border-blue-500 w-full">
                                {{ __('manage.elections.edit.add_candidate') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

</x-app-layout>
