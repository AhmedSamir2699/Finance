<div class="flex flex-col gap-3 mx-32">
    @if($criteria->count() > 0)
        @forelse($users as $user)
        <div class="bg-white overflow-hidden shadow rounded-lg p-6" x-data="{ 
            scores: {},
            submitEvaluation() {
                if (Object.keys(this.scores).length === 0) return;
                
                $wire.evaluate({{ $user->id }}, this.scores);
                this.scores = {};
                
                // Clear all inputs
                this.$el.querySelectorAll('input[type=number]').forEach(input => {
                    input.value = '';
                });
            },
            validateScore(criteriaId, minValue, maxValue) {
                const score = this.scores[criteriaId];
                if (score && (score < minValue || score > maxValue)) {
                    // Score is out of range
                    return false;
                }
                return true;
            },
            areAllScoresValid() {
                const criteriaIds = [{{ $criteria->pluck('id')->implode(',') }}];
                const minValues = [{{ $criteria->pluck('min_value')->implode(',') }}];
                const maxValues = [{{ $criteria->pluck('max_value')->implode(',') }}];
                
                // Check if all criteria have scores
                for (let i = 0; i < criteriaIds.length; i++) {
                    const criteriaId = criteriaIds[i];
                    const score = this.scores[criteriaId];
                    
                    // If no score for this criteria, return false
                    if (!score || score === '') {
                        return false;
                    }
                    
                    // Check if score is within range
                    if (score < minValues[i] || score > maxValues[i]) {
                        return false;
                    }
                }
                
                return true;
            }
        }">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    {{ $user->department->name }} - {{ $user->name }}
                </h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                @foreach($criteria as $criterion)
                <div class="flex flex-col">
                    <label class="text-sm font-medium text-gray-700 mb-2">
                        {{ $criterion->name }}
                        <span class="text-xs text-gray-500">({{ $criterion->getScoreRange() }})</span>
                    </label>
                    <input 
                        type="number" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        :class="{
                            'border-red-500 focus:ring-red-500': scores[{{ $criterion->id }}] && (scores[{{ $criterion->id }}] < {{ $criterion->min_value }} || scores[{{ $criterion->id }}] > {{ $criterion->max_value }}),
                            'border-green-500 focus:ring-green-500': scores[{{ $criterion->id }}] && scores[{{ $criterion->id }}] >= {{ $criterion->min_value }} && scores[{{ $criterion->id }}] <= {{ $criterion->max_value }}
                        }"
                        min="{{ $criterion->min_value }}" 
                        max="{{ $criterion->max_value }}"
                        x-model="scores[{{ $criterion->id }}]"
                        value="{{ $criterion->min_value }}"
                        placeholder="{{ $criterion->min_value }} - {{ $criterion->max_value }}"
                        @input="validateScore({{ $criterion->id }}, {{ $criterion->min_value }}, {{ $criterion->max_value }})"
                    />
                    <div x-show="scores[{{ $criterion->id }}] && (scores[{{ $criterion->id }}] < {{ $criterion->min_value }} || scores[{{ $criterion->id }}] > {{ $criterion->max_value }})" 
                         x-transition 
                         class="text-red-500 text-xs mt-1">
                        {{ __('evaluate.score_must_be_between') }} {{ $criterion->min_value }} {{ __('evaluate.and') }} {{ $criterion->max_value }}
                    </div>
                    @if($criterion->description)
                        <p class="text-xs text-gray-500 mt-1">{{ $criterion->description }}</p>
                    @endif
                </div>
                @endforeach
            </div>

            <div class="flex justify-end">
                <button 
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded disabled:bg-gray-400 disabled:cursor-not-allowed"
                    :disabled="!areAllScoresValid()"
                    @click="submitEvaluation()"
                >
                    {{ __('evaluate.save_evaluation') }}
                </button>
            </div>
        </div>
        @empty
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 text-center">
                <p class="text-gray-500">{{ __('evaluate.no_users_found') }}</p>
            </div>
        </div>
        @endforelse
    @else
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:px-6 text-center">
                <p class="text-gray-500">{{ __('evaluate.no_criteria_configured') }}</p>
                <p class="text-sm text-gray-400 mt-2">{{ __('evaluate.contact_admin') }}</p>
            </div>
        </div>
    @endif
</div>
