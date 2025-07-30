<?php

namespace App\Livewire\Manage;

use Livewire\Component;
use App\Models\AttendanceSetting;
use App\Models\Department;
use App\Models\User;

class AttendanceSettings extends Component
{
    public $scope_type = 'global';
    public $scope_id;
    public $late_arrival_tolerance = 10;
    public $early_leave_tolerance = 5;
    public $settings = [];
    
    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->settings = AttendanceSetting::all();
    }
    
    public function setScope($scope)
    {
        $this->scope_type = $scope;
        $this->scope_id = null; // Reset scope_id when changing scope
    }

    public function save()
    {
        $this->validate([
            'late_arrival_tolerance' => 'required|integer|min:0',
            'early_leave_tolerance' => 'required|integer|min:0',
        ]);
        
        AttendanceSetting::updateOrCreate(
            ['scope_type' => $this->scope_type, 'scope_id' => $this->scope_id],
            [
                'late_arrival_tolerance' => $this->late_arrival_tolerance,
                'early_leave_tolerance' => $this->early_leave_tolerance,
            ]
        );
        $this->loadSettings();
        $this->dispatch('toastr', ['message' => __('manage.attendance_settings.success'), 'type' => 'success']);
    }

    public function delete($settingId)
    {
        AttendanceSetting::find($settingId)->delete();
        $this->loadSettings();
        $this->dispatch('toastr', ['message' => 'Setting deleted.', 'type' => 'success']);
    }

    public function render()
    {
        $departments = Department::all();
        $users = User::all();
        
        $setting = AttendanceSetting::where('scope_type', $this->scope_type)
            ->when($this->scope_id, function($q) {
                $q->where('scope_id', $this->scope_id);
            })
            ->first();
            
        if($setting) {
            $this->late_arrival_tolerance = $setting->late_arrival_tolerance;
            $this->early_leave_tolerance = $setting->early_leave_tolerance;
        } else {
            $this->late_arrival_tolerance = 10;
            $this->early_leave_tolerance = 5;
        }
        
        return view('livewire.manage.attendance-settings', [
            'departments' => $departments,
            'users' => $users
        ]);
    }
}
