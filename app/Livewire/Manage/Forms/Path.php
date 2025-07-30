<?php

namespace App\Livewire\Manage\Forms;

use App\Models\Department;
use App\Models\RequestFormPathSteps;
use App\Models\Role;
use Livewire\Component;

class Path extends Component
{
    public $form;
    public $path = null;
    public $departments;
    public $roles;
    public $stepform = [
        'department_id' => null,
        'user_id' => null,
        'role_id' => null,
        'step_order' => 0,
    ];
    public $department_users;
    public $saveIsDisabled = true;

    public function mount($form)
    {
        $this->form = $form;
        $this->path = $form->path()->with('department')->orderBy('step_order')->get() ?? collect();
        $this->departments = Department::all();
        $this->roles = Role::all();
    }

    public function storeStep()
    {
        $this->stepform ['step_order'] = $this->form->path()->count() + 1;
        $this->form->path()->create($this->stepform);
        $this->path = $this->form->path()->orderBy('step_order')->get();
        $this->stepform = [
            'department_id' => null,
            'user_id' => null,
            'role_id' => null,
            'step_order' => $this->form->path()->count() + 1,

        ];
        $this->saveIsDisabled = true;
    }

    public function deleteStep($id)
    {
        $this->form->path()->find($id)->delete();
        $this->path = $this->form->path()->orderBy('step_order')->get();
    }

    public function updateStepOrder($orderedIds)
    {
        foreach ($orderedIds as $item) {
            RequestFormPathSteps::find($item['value'])->update(['step_order' => $item['order']]);

       
        }
    
        $this->path = $this->form->path()->orderBy('step_order')->get();

    }

    public function render()
    {
        $this->department_users = $this->stepform['department_id']
            ? Department::find($this->stepform['department_id'])->users()->get()
            : collect();

        $this->roles = $this->department_users->map(function ($user) {
            return $user->roles->first();
        })->flatten()->unique('id');



        if ($this->stepform['department_id'] && ($this->stepform['user_id'] || $this->stepform['role_id'])) {
            $this->saveIsDisabled = false;
        } 

        return view('livewire.manage.forms.path');
    }
}
