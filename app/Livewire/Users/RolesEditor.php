<?php

namespace App\Livewire\Users;

use Livewire\Component;
use Spatie\Permission\Models\Role;

class RolesEditor extends Component
{
    public $user;
    public $roles;
    public $addingRole = false;
    
    function addRole()
    {
        $this->addingRole = true;
        $this->roles = Role::all();
    }
    function save($role)
    {
        $this->user->assignRole($role);
        $this->addingRole = false;
    }
    function remove($role)
    {
        $this->user->removeRole($role);
    }
    function mount($user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return view('livewire.users.roles-editor');
    }
}
