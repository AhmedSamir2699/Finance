<x-app-layout>
    <div class="mt-1" x-data="voteComponent()">

        <!-- Election Info -->
        <div class="w-full rounded-md shadow-md bg-white p-4 border border-gray-300">
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-semibold text-gray-700">{{ $election->name }}</h1>
            </div>

            <div class="mb-4">
                <p class="text-gray-700">{{ $election->description }}</p>
            </div>

            @if ($state == 'voted')
                <div class="text-green-500 font-semibold">
                    {{ __('election.vote.already_voted') }}
                </div>
            @elseif($state == 'future')
                <div class="text-gray-500 font-semibold">
                    {{ __('election.vote.not_started') }}
                    <br />
                    {{ __('election.vote.start_date', ['date' => $election->start_date->format('d/m/Y')]) }}
                </div>
            @elseif($state == 'past')
                <div class="text-gray-500 font-semibold">
                    {{ __('election.vote.ended') }}
                    <br />
                    {{ __('election.vote.end_date', ['date' => $election->end_date->format('d/m/Y')]) }}
                </div>
            @else
                <!-- Step 1: Input Name & National ID -->
                <template x-if="!isSubmitted">
                    <div>
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700">{{ __('election.vote.name') }}</label>
                            <input type="text" id="name" x-model="name"
                                class="mt-1 w-full px-4 py-2 border rounded-md"
                                placeholder="{{ __('election.vote.name_placeholder') }}" required />
                        </div>
                        <div class="mb-4">
                            <label for="nationalId"
                                class="block text-gray-700">{{ __('election.vote.national_id') }}</label>
                            <input type="text" id="nationalId" x-model="nationalId"
                                class="mt-1 w-full px-4 py-2 border rounded-md"
                                placeholder="{{ __('election.vote.national_id_placeholder') }}" required maxlength="10"
                                pattern="\d{10}" />
                            <p x-show="nationalId && !isNationalIdValid()" class="text-red-500 text-sm mt-1">
                                {{ __('election.vote.national_id_error') }}
                            </p>
                        </div>
                        <button x-on:click="isSubmitted = true" :disabled="!name || !isNationalIdValid()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md mt-4"
                            :class="{ 'bg-gray-400 cursor-not-allowed': !isNationalIdValid() }">
                            {{ __('election.vote.next_step') }}
                        </button>
                    </div>
                </template>

                <!-- Step 2: Display Candidates -->
                <template x-if="isSubmitted">
                    <div>
                        <div class="mb-4">
                            <p class="text-gray-700">
                                {{ __('election.vote.select_candidates') }}
                            </p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <template x-for="candidate in candidates" :key="candidate.id">
                                <div :class="{
                                    'bg-primary-base text-white': selectedCandidates.includes(candidate.id),
                                    'bg-gray-100 text-gray-800': !selectedCandidates.includes(candidate.id),
                                    'cursor-not-allowed opacity-70': selectedCandidates.length === 5 && !
                                        selectedCandidates.includes(candidate.id)
                                }"
                                    class="p-4 rounded-md shadow-sm border border-gray-300 cursor-pointer"
                                    x-on:click="toggleCandidate(candidate.id)">
                                    <span class="flex items-center">

                                        <span x-text="candidate.name"></span>
                                    </span>
                                </div>
                            </template>
                        </div>
                        <button x-on:click="submitVote" :disabled="selectedCandidates.length < 1"
                            class="px-4 py-2 bg-green-600 text-white rounded-md mt-12"
                            :class="{ '!bg-gray-400 cursor-not-allowed': selectedCandidates.length < 1 }">
                            {{ __('election.vote.submit_vote') }}
                        </button>
                    </div>
                </template>
            @endif

        </div>
    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        function voteComponent() {
            return {
                name: '',
                nationalId: '',
                isSubmitted: false,
                selectedCandidates: [],
                candidates: @json($election->candidates_collection),
                isNationalIdValid() {
                    return /^\d{10}$/.test(this.nationalId);
                },
                toggleCandidate(id) {
                    const idx = this.selectedCandidates.indexOf(id);
                    if (idx > -1) {
                        this.selectedCandidates.splice(idx, 1);
                    } else if (this.selectedCandidates.length < 5) {
                        this.selectedCandidates.push(id);
                    }
                },
                submitVote() {
                    if (this.selectedCandidates.length < 1) {
                        return;
                    }

                    const data = {
                        name: this.name,
                        nationalId: this.nationalId,
                        selectedCandidates: this.selectedCandidates
                    };

                    fetch("{{ route('election.vote.store', [$election->id]) }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').getAttribute('content')
                            },
                            body: JSON.stringify(data)
                        })
                        .then(response => response.json())
                        .then(data => {
                            swal.fire({
                                title: 'Vote Submitted!',
                                text: 'Your vote has been successfully submitted.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        })
                        .catch(error => {
                            console.error('Error submitting vote:', error);
                            alert('An error occurred while submitting your vote.');
                        });
                }
            }
        }
    </script>
</x-app-layout>
