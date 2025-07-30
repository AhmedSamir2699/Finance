<?php

namespace App\Livewire\Filters;

use Livewire\Component;
use App\Models\User;

class AssignedByDropdown extends Component
{
    public $search = '';
    public $selected;
    public $open = false;

    public function mount($selected = null)
    {
        $this->selected = $selected;
    }

    public function updatedSelected($value)
    {
        $this->dispatch('set-assigned-by', id: $value);
        $this->open = false;
    }

    public function toggleOpen()
    {
        $this->open = !$this->open;
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        $user = auth()->user();
        $users = collect();

        // User can view any user
        if ($user->can('task.view-any')) {
            $users = User::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->get();
        }
        // User can view department users
        elseif ($user->can('task.view-department')) {
            if ($user->department) {
                $users = $user->department->users;
            }
        }
        // User can view subordinate users
        elseif ($user->can('task.view-subordinates')) {
            $users = $user->subordinateUsers();
        }

        // Apply search filter if users are not empty
        if ($users->isNotEmpty() && $this->search) {
            $users = $users->filter(function ($user) {
                return str_contains(strtolower($user->name), strtolower($this->search));
            });
        }

        return view('livewire.filters.assigned-by-dropdown', [
            'users' => $users,
        ]);
    }
} 