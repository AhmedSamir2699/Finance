<?php

namespace App\Livewire\Manage\Forms;

use App\Models\Department;
use App\Models\RequestFormDepartment;
use Livewire\Component;

class Visibility extends Component
{
    public $departments;
    public $form;

    public function mount($form)
    {
        $this->departments = Department::all();
        $this->form = $form;
    }

    public function updateVisibility($departmentId)
    {
        $department = RequestFormDepartment::where('request_form_id',$this->form->id)->where('department_id', $departmentId)->first();
        
        if ($department) {
            $department->delete();
        } else {
            RequestFormDepartment::create(['department_id' => $departmentId, 'request_form_id' => $this->form->id]);
        }
    }

    public function render()
    {
        return view('livewire.manage.forms.visibility');
    }
}
