<?php

namespace App\Livewire\Dashboard;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BankAccount extends Component
{
    public $checkIn = false;
    public $dayEnded = false;
    public $user;
    public $timespan = '0';
    public $checkInTime;

    public function mount()
    {
        $this->user = Auth::user();
        $this->updateStatus();
    }

    public function checkInOut()
    {
        if ($this->checkIn) {
            $this->user->checkOut();
        } else {
            // Re-checking in after ending the day is considered overtime.
            $this->user->checkIn();
        }

        $this->updateStatus();
    }

    public function endDay()
    {
        $lastTimesheet = $this->user->timesheets()
            ->whereDate('start_at', today())
            ->orderBy('start_at', 'desc')
            ->first();

        if ($lastTimesheet) {
            if (is_null($lastTimesheet->end_at)) {
                $lastTimesheet->end_at = now();
            }
            // Only mark the first "End Day" of the day.
            if (!$this->user->timesheets()->whereDate('start_at', today())->where('is_day_end', true)->exists()) {
                $lastTimesheet->is_day_end = true;
            }
            $lastTimesheet->save();
        }

        $this->updateStatus();
    }

    private function updateStatus()
    {
        $this->checkIn = $this->user->isCheckedIn();

        $lastTimesheetToday = $this->user->timesheets()
            ->whereDate('start_at', today())
            ->orderBy('start_at', 'desc')
            ->first();
        
        $this->dayEnded = $this->user->timesheets()->whereDate('start_at', today())->where('is_day_end', true)->exists();

        $firstTimesheetToday = $this->user->timesheets()
            ->whereDate('start_at', today())
            ->orderBy('start_at', 'asc')
            ->first();

        if ($firstTimesheetToday) {
            $start = Carbon::parse($firstTimesheetToday->start_at);
            $end = $this->dayEnded && $lastTimesheetToday->end_at ? Carbon::parse($lastTimesheetToday->end_at) : now();
            
            $totalMinutes = $start->diffInMinutes($end);
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;
            $this->timespan = sprintf('%d ساعة و %d دقيقة', $hours, $minutes);
            $this->checkInTime = $firstTimesheetToday->start_at;
        } else {
            $this->timespan = '0';
            $this->checkInTime = null;
        }
    }

    public function render()
    {
        if ($this->checkIn) {
            $this->updateStatus();
        }
        
        return view('livewire.dashboard.bank-account');
    }
}
