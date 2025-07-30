<?php

namespace App\Livewire\Users\Reports;

use App\Models\Department;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\User;

class UsersList extends Component
{
 use WithPagination;


    public $search;
    public $perPage = 5;
    public $selectedDepartment = 'all';

    public function render()
    {

        
        $departments = Department::all();
        
        if ($this->selectedDepartment !== 'all') {
            $users = User::where('department_id', $this->selectedDepartment)->with('department');
        } else {
            $users = User::with('department');
        }

        if (!empty($this->search)){
            $users->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhere('phone', 'like', '%' . $this->search . '%')
            ->orWhereHas('department', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            })
            ->orWhereHas('roles', function ($query) {
                $query->where('display_name', 'like', '%' . $this->search . '%');
            });

        }


        $users = $users->paginate($this->perPage);


        return view('livewire.users.reports.users-list', [
            'users' => $users,
            'departments' => $departments,
        ]);
    }
}
