<?php

namespace App\Livewire\Evaluate;

use App\Models\User;
use App\Models\EvaluationCriteria;
use App\Models\EvaluationScore;
use Livewire\Component;

class Random extends Component
{
    public $users;
    public $criteria;
    public $scores = [];

    public function mount()
    {
        $this->criteria = EvaluationCriteria::where('is_active', true)->orderBy('name')->get();
        
        $this->users = User::with([
            'evaluationScores' => function ($query) {
                $query->where('evaluated_at', now()->toDateString());
            },
            'timesheets' => function ($query) {
                $query->whereDate('start_at', now());
            }
        ])->whereDoesntHave('evaluationScores', function ($query) {
            $query->where('evaluated_at', now()->toDateString());
        })->whereHas('timesheets', function ($query) {
            $query->whereDate('start_at', now());
        })->limit(5)->get();
    }

    public function evaluate($userId, $scores)
    {
        // Validate that all active criteria are evaluated
        $activeCriteria = $this->criteria->pluck('id')->toArray();
        $evaluatedCriteria = array_keys(array_filter($scores, function($score) {
            return !empty($score) && $score !== '';
        }));
        
        // Check if all criteria are evaluated
        $missingCriteria = array_diff($activeCriteria, $evaluatedCriteria);
        if (!empty($missingCriteria)) {
            flash()->error(__('evaluate.all_criteria_required'));
            return;
        }
        
        // Validate scores
        foreach ($scores as $criteriaId => $score) {
            $criteria = EvaluationCriteria::find($criteriaId);
            if (!$criteria || !$criteria->is_active) {
                continue;
            }
            
            // Validate score is not empty
            if (empty($score) || $score === '') {
                flash()->error(__('evaluate.all_criteria_required'));
                return;
            }
            
            // Validate score is within range
            if ($score < $criteria->min_value || $score > $criteria->max_value) {
                flash()->error(__('evaluate.score_out_of_range', ['criteria' => $criteria->name]));
                return;
            }
            
            // Create or update evaluation score
            EvaluationScore::updateOrCreate(
                [
                    'user_id' => $userId,
                    'criteria_id' => $criteriaId,
                    'evaluated_at' => now()->toDateString()
                ],
                ['score' => $score]
            );
        }

        // Refresh users list
        $this->mount();
        
        // Show success message
        flash()->success(__('evaluate.evaluation_saved'));
    }

    public function render()
    {
        return view('livewire.evaluate.random');
    }
}
