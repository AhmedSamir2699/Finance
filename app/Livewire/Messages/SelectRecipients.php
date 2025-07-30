<?php

namespace App\Livewire\Messages;

use App\Models\Department;
use App\Models\User;
use Livewire\Component;

class SelectRecipients extends Component
{
    public $selectedDepartments;
    public $selectedUsers = [];
    public $searchUsers;
    public $sendToAll = false;
    public $recipients;

    public function mount($recipients = null)
    {
        $this->selectedDepartments = auth()->user()->department->id;
        if($recipients) {
            $this->selectedUsers = User::find($recipients)->mapWithKeys(function($user) {
                return [$user->id => $user];
            })->toArray();
        }
        
        $this->dispatch('recipients-updated', $this->recipients);
    }

    public function updatedSelectedDepartments($value)
    {
        $this->sendToAll = false;
        $this->searchUsers = '';
    }

    
    public function addRecipient($id)
    {
        if(!key_exists($id, $this->selectedUsers)) $this->selectedUsers[$id] = User::find($id);
    }

    public function removeRecipient($key)
    {
        unset($this->selectedUsers[$key]);
    }


    public function selectAll()
    {
        $this->sendToAll = true;
        $users = User::query();
        if($this->selectedDepartments !== '*') {
            $users = $users->wherehas('department', function ($query) {
                $query->where('id', $this->selectedDepartments);
            });
        }
        foreach ($users->get() as $user) {
            if(key_exists($user->id, $this->selectedUsers)) continue;
            $this->selectedUsers[$user->id] = $user;
        }
        

    }

    public function render()
    {

        $users = User::query();

        if ($this->selectedDepartments !== '*')  $users = $users->wherehas('department', function ($query) {
                $query->where('id', $this->selectedDepartments);
            });
        
        if ($this->searchUsers) $users = $users->where('name', 'like', '%' . $this->searchUsers . '%');

        
        if(is_array($this->selectedUsers)) 
            $this->recipients = implode(',', array_keys($this->selectedUsers));
        else
            $this->recipients = implode(',', array_keys($this->selectedUsers->toArray()));
        


        $this->dispatch('recipients-updated', $this->recipients);

        return view('livewire.messages.select-recipients', [
            'users' => $users->get(),
            'departments' => Department::all(),

        ]);
    }
}
