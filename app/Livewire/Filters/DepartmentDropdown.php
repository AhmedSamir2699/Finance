<?php

namespace App\Livewire\Filters;

use Livewire\Component;
use App\Models\Department;

class DepartmentDropdown extends Component
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
        $this->dispatch('set-department', id: $value);
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
        $departments = collect();

        // User can view any department
        if ($user->can('task.view-any')) {
            $departments = Department::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
                ->orderBy('name')
                ->get();
        }
        // User can view department tasks
        elseif ($user->can('task.view-department')) {
            if ($user->department) {
                $departments = collect([$user->department]);
            }
        }
        // User can view subordinate tasks - get departments of subordinates
        elseif ($user->can('task.view-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->isNotEmpty()) {
                $subordinateDepartments = $subordinateUsers->pluck('department')->filter()->unique('id');
                $departments = $subordinateDepartments;
            }
        }

        // Apply search filter if departments are not empty
        if ($departments->isNotEmpty() && $this->search) {
            $departments = $departments->filter(function ($department) {
                return str_contains(strtolower($department->name), strtolower($this->search));
            });
        }

        return view('livewire.filters.department-dropdown', [
            'departments' => $departments,
        ]);
    }
} 