<?php

namespace App\Livewire\Timesheets;

use App\Models\Timesheet;
use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class UnendedTimesheets extends Component
{
    use WithPagination;

    public $timesheets = [];
    public $selectedTimesheets = [];
    public $suggestedEndTimes = [];
    public $customEndTimes = [];
    public $totalCount = 0;
    public $selectedCount = 0;
    public $isLoading = false;
    public $processingTimesheetId = null;

    public function mount()
    {
        $this->clearMessages();
        $this->selectedTimesheets = [];
        $this->loadUnendedTimesheets();
    }

    public function loadUnendedTimesheets()
    {
        $this->isLoading = true;
        
        // Optimize query with eager loading and chunking for large datasets
        $timesheets = Timesheet::whereNull('end_at')
            ->where('start_at', '<', now()->startOfDay())
            ->with(['user' => function($query) {
                $query->select('id', 'name', 'department_id', 'shift_start', 'shift_end');
            }, 'user.department:id,name'])
            ->orderBy('start_at')
            ->limit(1000) // Prevent memory issues with very large datasets
            ->get();

        $this->timesheets = $timesheets->toArray();
        $this->totalCount = $timesheets->count();
        
        $this->initializeEndTimes();
        $this->isLoading = false;
    }

    public function initializeEndTimes()
    {
        $this->suggestedEndTimes = [];
        $this->customEndTimes = [];

        foreach ($this->timesheets as $timesheet) {
            $suggestedEnd = $this->getSuggestedEndTime((object)$timesheet);
            $this->suggestedEndTimes[$timesheet['id']] = $suggestedEnd->format('Y-m-d\TH:i');
            $this->customEndTimes[$timesheet['id']] = $suggestedEnd->format('Y-m-d\TH:i');
        }
    }

    public function getSuggestedEndTime($timesheet)
    {
        $startTime = Carbon::parse($timesheet->start_at);
        $user = (object)$timesheet->user;

        if ($user->shift_start && $user->shift_end) {
            // Parse shift times for the same day as the timesheet
            $timesheetDate = $startTime->toDateString();
            $shiftStart = Carbon::parse($timesheetDate . ' ' . $user->shift_start);
            $shiftEnd = Carbon::parse($timesheetDate . ' ' . $user->shift_end);
            
            // Handle overnight shifts (end time is before start time)
            if ($shiftEnd->lt($shiftStart)) {
                $shiftEnd->addDay();
            }
            
            // Calculate the shift duration
            $shiftDuration = $shiftStart->diffInMinutes($shiftEnd);
            
            // Add the shift duration to the check-in time
            return $startTime->copy()->addMinutes($shiftDuration);
        }

        // Default 8 hours (480 minutes) if no shift settings
        return $startTime->copy()->addMinutes(480);
    }

    public function useSuggestedTime($timesheetId)
    {
        if (isset($this->suggestedEndTimes[$timesheetId])) {
            $this->customEndTimes[$timesheetId] = $this->suggestedEndTimes[$timesheetId];
        }
    }

    public function useAllSuggestedTimes()
    {
        foreach ($this->suggestedEndTimes as $timesheetId => $suggestedTime) {
            $this->customEndTimes[$timesheetId] = $suggestedTime;
        }
        $this->clearMessages();
    }

    public function clearMessages()
    {
        // Messages are now handled by toastr
    }

    public function selectAll()
    {
        $this->selectedTimesheets = collect($this->timesheets)->pluck('id')->toArray();
        $this->selectedCount = count($this->selectedTimesheets);
    }

    public function deselectAll()
    {
        $this->selectedTimesheets = [];
        $this->selectedCount = count($this->selectedTimesheets);
    }

    public function updatedSelectedTimesheets()
    {
        $this->selectedCount = count($this->selectedTimesheets);
    }

    public function updated($property)
    {
        if ($property === 'selectedTimesheets') {
            $this->selectedCount = count($this->selectedTimesheets);
        }
    }

    public function saveSingleTimesheet($timesheetId)
    {
        if (!isset($this->customEndTimes[$timesheetId]) || empty($this->customEndTimes[$timesheetId])) {
            flash()->error(__('timesheets.unended.enter_end_time'));
            return;
        }

        $this->isLoading = true;
        $this->processingTimesheetId = $timesheetId;

        try {
            $result = $this->saveTimesheet($timesheetId, $this->customEndTimes[$timesheetId]);
            
            if ($result['success']) {
                flash()->success($result['message']);
                $this->selectedTimesheets = []; // Clear selections
                $this->selectedCount = 0; // Reset count
                $this->loadUnendedTimesheets(); // Reload to remove saved timesheet
            } else {
                flash()->error($result['message']);
            }
        } finally {
            $this->isLoading = false;
            $this->processingTimesheetId = null;
        }
    }

    public function deleteSingleTimesheet($timesheetId)
    {
        $this->isLoading = true;
        $this->processingTimesheetId = $timesheetId;

        try {
            $timesheet = Timesheet::find($timesheetId);
            
            if (!$timesheet) {
                flash()->error(__('timesheets.unended.timesheet_not_found'));
                return;
            }

            $timesheet->delete();
            flash()->success(__('timesheets.unended.successfully_deleted') . " #{$timesheetId}");
            $this->selectedTimesheets = []; // Clear selections
            $this->selectedCount = 0; // Reset count
            $this->loadUnendedTimesheets(); // Reload to remove deleted timesheet
        } catch (\Exception $e) {
            flash()->error(__('timesheets.unended.error_deleting') . ": " . $e->getMessage());
        } finally {
            $this->isLoading = false;
            $this->processingTimesheetId = null;
        }
    }

    public function saveSelectedTimesheets()
    {
        if (empty($this->selectedTimesheets)) {
            flash()->error(__('timesheets.unended.select_at_least_one'));
            return;
        }

        $this->isLoading = true;

        try {
            $savedCount = 0;
            $errors = [];

            // Batch process for better performance
            $timesheetsToUpdate = [];
            $timesheetIds = $this->selectedTimesheets;

            foreach ($timesheetIds as $timesheetId) {
                if (!isset($this->customEndTimes[$timesheetId]) || empty($this->customEndTimes[$timesheetId])) {
                    $errors[] = "Timesheet #{$timesheetId} has no end time specified";
                    continue;
                }

                $result = $this->saveTimesheet($timesheetId, $this->customEndTimes[$timesheetId]);
                
                if ($result['success']) {
                    $savedCount++;
                } else {
                    $errors[] = $result['message'];
                }
            }

            if ($savedCount > 0) {
                flash()->success(__('timesheets.unended.successfully_fixed') . " ({$savedCount})");
                if (!empty($errors)) {
                                          flash()->warning(__('timesheets.unended.some_errors_occurred') . ": " . implode(', ', $errors));
                }
                $this->selectedTimesheets = []; // Clear selections
                $this->selectedCount = 0; // Reset count
                $this->loadUnendedTimesheets(); // Reload to remove saved timesheets
            } else {
                flash()->error(__('timesheets.unended.no_timesheets_saved') . ". " . implode(', ', $errors));
            }
        } finally {
            $this->isLoading = false;
        }
    }

    private function saveTimesheet($timesheetId, $endTime)
    {
        // Use direct update for better performance
        $timesheet = Timesheet::select('id', 'user_id', 'start_at', 'end_at')
            ->with(['user:id,name'])
            ->find($timesheetId);
        
        if (!$timesheet || $timesheet->end_at) {
            return ['success' => false, 'message' => __('timesheets.unended.timesheet_not_found') . ' ' . __('timesheets.unended.or_already_ended')];
        }

        $endAt = Carbon::parse($endTime);
        $startAt = Carbon::parse($timesheet->start_at);

        // Skip collision check if end time is invalid
        if ($endAt->lte($startAt)) {
            return ['success' => false, 'message' => __('timesheets.unended.end_time_after_start')];
        }

        // Optimized collision check with index hints
        $collision = Timesheet::where('user_id', $timesheet->user_id)
            ->whereDate('start_at', $startAt->toDateString())
            ->where('id', '!=', $timesheet->id)
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists();

        if ($collision) {
            return [
                'success' => false, 
                'message' => __('timesheets.unended.conflicts_with_existing') . " #{$timesheet->id} " . __('timesheets.unended.for') . " {$timesheet->user->name}"
            ];
        }

        // Direct update without model refresh
        Timesheet::where('id', $timesheetId)->update(['end_at' => $endAt]);

        return ['success' => true, 'message' => __('timesheets.unended.successfully_saved') . " #{$timesheet->id}"];
    }

    public function render()
    {
        return view('livewire.timesheets.unended-timesheets');
    }
} 