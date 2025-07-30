<?php

namespace App\Livewire\Users;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class TimesheetView extends Component
{
    public User $user;
    public $date;
    public $timesheets;
    public $summary;
    public $first_start_at;
    public $last_end_at;
    public $is_last_shift_active;
    public $last_timesheet_id;
    public $formatted_present_time;
    public $formatted_break_time;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->date = now()->format('Y-m-d');
        $this->loadTimesheetData();
    }

    public function loadTimesheetData()
    {
        $this->timesheets = $this->user->timesheets()
            ->whereDate('start_at', $this->date)
            ->orderBy('start_at')
            ->get();

        $this->summary = $this->user->getAttendanceSummaryForDate($this->date, $this->timesheets);

        $this->first_start_at = null;
        $this->last_end_at = null;
        $this->is_last_shift_active = false;
        $this->last_timesheet_id = null;

        if ($this->timesheets->isNotEmpty()) {
            $this->first_start_at = Carbon::parse($this->timesheets->first()->start_at)->format('H:i:s');
            $lastTimesheet = $this->timesheets->last();

            if ($lastTimesheet->end_at) {
                $this->last_end_at = Carbon::parse($lastTimesheet->end_at)->format('H:i:s');
            } else {
                $this->is_last_shift_active = true;
                $this->last_timesheet_id = $lastTimesheet->id;
            }
        }

        $presentHours = floor($this->summary['present_minutes'] / 60);
        $presentMins = $this->summary['present_minutes'] % 60;
        $this->formatted_present_time = sprintf('%d ساعة، %d دقيقة', $presentHours, $presentMins);

        $breakHours = floor($this->summary['break_minutes'] / 60);
        $breakMins = $this->summary['break_minutes'] % 60;
        $this->formatted_break_time = sprintf('%d ساعة، %d دقيقة', $breakHours, $breakMins);
    }

    public function previousDay()
    {
        $this->date = Carbon::parse($this->date)->subDay()->format('Y-m-d');
        $this->loadTimesheetData();
    }

    public function nextDay()
    {
        $this->date = Carbon::parse($this->date)->addDay()->format('Y-m-d');
        $this->loadTimesheetData();
    }

    public function render()
    {
        return view('livewire.users.timesheet-view');
    }
}
