<?php

namespace App\Livewire\Evaluate;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EvaluationScore;
use App\Models\User;
use Carbon\Carbon;

class History extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFilter = '';
    public $criteriaFilter = '';
    public $userFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'dateFilter' => ['except' => ''],
        'criteriaFilter' => ['except' => ''],
        'userFilter' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    public function updatingCriteriaFilter()
    {
        $this->resetPage();
    }

    public function updatingUserFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = EvaluationScore::with(['user', 'criteria'])
            ->orderBy('evaluated_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->search) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->dateFilter) {
            $query->whereDate('evaluated_at', $this->dateFilter);
        }

        if ($this->criteriaFilter) {
            $query->where('criteria_id', $this->criteriaFilter);
        }

        if ($this->userFilter) {
            $query->where('user_id', $this->userFilter);
        }

        $evaluations = $query->paginate(15);

        // Get filter options
        $criteria = \App\Models\EvaluationCriteria::orderBy('name')->get();
        $users = User::orderBy('name')->get();

        return view('livewire.evaluate.history', [
            'evaluations' => $evaluations,
            'criteria' => $criteria,
            'users' => $users,
        ]);
    }

    public function clearFilters()
    {
        $this->reset(['search', 'dateFilter', 'criteriaFilter', 'userFilter']);
        $this->resetPage();
    }
} 