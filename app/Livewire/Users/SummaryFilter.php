<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Carbon\Carbon;

class SummaryFilter extends Component
{
    public $timePeriod = '';
    public $startDate;
    public $endDate;
    public $userId;
    public $isCurrentUser = false;

    public function mount($user = null)
    {
        $this->isCurrentUser = $user ? false : true;
        $this->userId = $user ? $user->id : null;
        
        // Set dates from request or default to current month
        $this->startDate = request()->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $this->endDate = request()->get('end_date', now()->endOfMonth()->format('Y-m-d'));
    }

    public function updatedTimePeriod()
    {
        $today = now();
        
        switch($this->timePeriod) {
            case 'today':
                $this->startDate = $today->format('Y-m-d');
                $this->endDate = $today->format('Y-m-d');
                break;
            case 'last_week':
                $this->startDate = $today->subWeek()->startOfWeek()->format('Y-m-d');
                $this->endDate = $today->subWeek()->endOfWeek()->format('Y-m-d');
                break;
            case 'last_month':
                $lastMonth = $today->subMonth();
                $this->startDate = $lastMonth->startOfMonth()->format('Y-m-d');
                $this->endDate = $lastMonth->endOfMonth()->format('Y-m-d');
                break;
            case 'last_year':
                $lastYear = $today->subYear();
                $this->startDate = $lastYear->startOfYear()->format('Y-m-d');
                $this->endDate = $lastYear->endOfYear()->format('Y-m-d');
                break;
            default:
                return; // Don't redirect for custom range
        }
        
        $this->redirectToSummary();
    }

    public function filter()
    {
        $this->redirectToSummary();
    }

    private function redirectToSummary()
    {
        if ($this->isCurrentUser) {
            return redirect()->route('users.summary.my', [
                'start_date' => $this->startDate,
                'end_date' => $this->endDate
            ]);
        } else {
            return redirect()->route('users.summary.show', [
                'user' => $this->userId,
                'start_date' => $this->startDate,
                'end_date' => $this->endDate
            ]);
        }
    }

    public function render()
    {
        return view('livewire.users.summary-filter');
    }
} 