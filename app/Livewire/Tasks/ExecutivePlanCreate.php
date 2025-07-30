<?php

namespace App\Livewire\Tasks;

use App\Models\User;
use App\Models\ExecutivePlanCell;
use Livewire\Component;
use Carbon\Carbon;

class ExecutivePlanCreate extends Component
{
    public $fromDate;
    public $toDate;
    public $selectedUser = '';
    public $selectedDepartment = '';
    public $search = '';
    public $perPage = 10;

    public function mount()
    {
        $this->fromDate = now()->format('Y-m-d');
        $this->toDate = now()->format('Y-m-d');
    }

    /**
     * Get assignable users based on permissions
     */
    public function getAssignableUsers()
    {
        $user = auth()->user();
        $assignables = collect();

        // User can assign to any user
        if ($user->can('task.create-any')) {
            $assignables = User::all();
        } else {
            // Add department users
            if ($user->department) {
                $assignables = $assignables->merge($user->department->users);
            }

            // Add subordinates
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->isNotEmpty()) {
                $assignables = $assignables->merge($subordinateUsers);
            }
        }

        // Remove duplicates
        $assignables = $assignables->unique('id');

        // Filter based on assign permissions
        $assignables = $assignables->filter(function ($assignee) use ($user) {
            return $this->canAssignToUser($user, $assignee);
        });

        // Filter by selected department if specified
        if ($this->selectedDepartment) {
            $assignables = $assignables->filter(function ($assignee) {
                return $assignee->department_id == $this->selectedDepartment;
            });
        }

        return $assignables;
    }

    /**
     * Check if user can assign tasks to a specific user
     */
    public function canAssignToUser($assigner, $assignee)
    {
        // User must have assign permission
        if (!$assigner->can('task.assign')) {
            return false;
        }

        // User can assign to themselves
        if ($assigner->id === $assignee->id) {
            return true;
        }

        // User can assign to department members
        if ($assigner->department_id === $assignee->department_id) {
            return true;
        }

        // User can assign to subordinates
        $subordinateRoleNames = $assigner->allSubordinates();
        $assigneeRoles = $assignee->roles->pluck('name');
        if ($assigneeRoles->intersect($subordinateRoleNames)->count() > 0) {
            return true;
        }

        // User can assign to anyone
        if ($assigner->can('task.create-any')) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can view executive plan cells for a specific user
     */
    public function canViewUserCells($targetUser)
    {
        $user = auth()->user();
        
        // User can view any user's cells
        if ($user->can('task.view-any')) {
            return true;
        }

        // User can view department cells
        if ($user->can('task.view-department') && $user->department_id === $targetUser->department_id) {
            return true;
        }

        // User can view subordinate cells
        if ($user->can('task.view-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->contains('id', $targetUser->id)) {
                return true;
            }
        }

        return false;
    }

    public function createTaskFromCell($cellId)
    {
        $cell = ExecutivePlanCell::find($cellId);
        if (!$cell) {
            flash()->error(__('tasks.executive_plan.cell_not_found'));
            return;
        }

        $user = auth()->user();
        
        // Check if user can assign to this cell's user
        if (!$this->canAssignToUser($user, $cell->user)) {
            flash()->error(__('tasks.assign.permission_error'));
            return;
        }

        // Check if user can view this cell
        if (!$this->canViewUserCells($cell->user)) {
            flash()->error(__('tasks.executive_plan.view_permission_error'));
            return;
        }

        // Redirect to task create with only the cell ID
        return redirect()->route('tasks.create', [
            'from_executive_plan' => true,
            'cell_id' => $cellId
        ]);
    }

    /**
     * Reset user selection when department changes
     */
    public function updatedSelectedDepartment()
    {
        $this->selectedUser = '';
    }

    /**
     * Get departments that the current user can manage
     */
    public function getDepartments()
    {
        $user = auth()->user();
        $departments = collect();

        // User can view any department
        if ($user->can('task.view-any')) {
            return \App\Models\Department::all();
        }

        // User can view department tasks
        if ($user->can('task.view-department')) {
            if ($user->department) {
                $departments->push($user->department);
            }
        }

        // User can view subordinate tasks - get departments of subordinates
        if ($user->can('task.view-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->isNotEmpty()) {
                $subordinateDepartments = $subordinateUsers->pluck('department')->filter()->unique('id');
                $departments = $departments->merge($subordinateDepartments);
            }
        }

        // Remove duplicates and return
        return $departments->unique('id');
    }

    public function render()
    {
        $user = auth()->user();
        $assignableUsers = $this->getAssignableUsers();

        // Filter users based on search
        if ($this->search) {
            $assignableUsers = $assignableUsers->filter(function ($user) {
                return str_contains(strtolower($user->name), strtolower($this->search));
            });
        }

        // Filter by selected user if specified
        if ($this->selectedUser) {
            $assignableUsers = $assignableUsers->where('id', $this->selectedUser);
        }

        // Get cells for each user
        $usersWithCells = $assignableUsers->map(function ($user) {
            $cellsQuery = $user->cells();

            // Apply date range filter
            if ($this->fromDate && $this->toDate) {
                if ($this->fromDate === $this->toDate) {
                    // If same date, show single date
                    $selectedDate = Carbon::parse($this->fromDate)->startOfDay();
                    $cellsQuery = $cellsQuery->whereDate('date', $selectedDate);
                } else {
                    // If different dates, show range
                    $fromDate = Carbon::parse($this->fromDate)->startOfDay();
                    $toDate = Carbon::parse($this->toDate)->endOfDay();
                    $cellsQuery = $cellsQuery->whereBetween('date', [$fromDate, $toDate]);
                }
            } else {
                // Default to today if no dates selected
                $today = Carbon::now()->startOfDay();
                $cellsQuery = $cellsQuery->whereDate('date', $today);
            }

            $cells = $cellsQuery->get();

            return [
                'user' => $user,
                'cells' => $cells
            ];
        })->filter(function ($userData) {
            // Only show users who have cells in the selected date range
            return $userData['cells']->count() > 0;
        });

        // Convert to array for pagination
        $usersWithCellsArray = $usersWithCells->values()->toArray();
        
        // Create paginator manually
        $currentPage = request()->get('page', 1);
        $perPage = $this->perPage;
        $offset = ($currentPage - 1) * $perPage;
        $paginatedData = array_slice($usersWithCellsArray, $offset, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedData,
            count($usersWithCellsArray),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'pageName' => 'page']
        );

        return view('livewire.tasks.executive-plan-create', [
            'usersWithCells' => $paginator,
            'departments' => $this->getDepartments()
        ]);
    }
} 