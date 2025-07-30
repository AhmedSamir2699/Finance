<?php

namespace App\Livewire\Departments\Index;

use Livewire\WithPagination;
use App\Models\Department;
use Livewire\Component;

class TableList extends Component
{
    use WithPagination;


    public $search;
    public $perPage = 5;

    function updatingperPage()
    {
        $this->resetPage();
    }

    public function render()
    {

        $departments = Department::withCount('users');
        if (!empty($this->search)){
            $departments->where('name', 'like', '%' . $this->search . '%');
        }

        $departments = $departments->paginate($this->perPage);

        return view('livewire.departments.index.table-list', [
            'departments' => $departments
        ]);
    }
}
