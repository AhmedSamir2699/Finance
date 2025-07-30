<?php

namespace App\Livewire\Users;

use Livewire\WithPagination;
use Livewire\Component;
use App\Models\User;


class TableList extends Component
{
    use WithPagination;


    public $search;
    public $perPage = 5;

    public function render()
    {

        $users = User::with('department');


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


        return view('livewire.users.table-list', [
            'users' => $users
        ]);
    }
}
