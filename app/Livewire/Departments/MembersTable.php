<?php

namespace App\Livewire\Departments;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;


class MembersTable extends Component
{
    use WithPagination;


    public $search;
    public $perPage = 5;
    public $department;

    public function mount($department = null)
    {
        $this->department = $department;
    }

    public function render()
    {

        $users = User::with('department');
        if (!is_null($this->department)){
            $users->where('department_id', $this->department->id);
        }

        if (!empty($this->search)){
            $users->where('name', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orWhereHas('roles', function ($query) {
                $query->where('display_name', 'like', '%' . $this->search . '%');
            })
            ->when($this->department, function ($query) {
                $query->where('department_id', $this->department->id);
            });

            if (is_null($this->department)){
                $users->orWhereHas('department', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
            }
        }


        $users = $users->paginate($this->perPage);


        return view('livewire.departments.members-table',[
            'users' => $users
        ]);
    }
}
