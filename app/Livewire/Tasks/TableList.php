<?php

namespace App\Livewire\Tasks;

use App\Models\Department;
use App\Models\Task;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class TableList extends Component
{
    use WithPagination;
    public $search;
    public $perPage = 15;
    public $date = null;
    public $status = null;
    public $priority;
    public $withTrashed = false;
    public $filter;
    public $department;
    public $departments;
    public $fromDate;
    public $toDate;
    public $assignedBy;
    public $assignableUsers;

    public function mount()
    {
        $this->departments = Department::all();
        $user = auth()->user();
        
        // Set default department based on user permissions
        if ($user->can('task.view-department')) {
            $this->department = $user->department->id;
        } else {
            $this->department = null; // Don't show department filter if no permission
        }
        
        // Set default assigned by based on user permissions
        if ($user->can('task.view-any')) {
            $this->assignedBy = $user->id;
        } else {
            $this->assignedBy = null; // Don't show assigned by filter if no permission
        }
        
        $this->assignableUsers = collect();
        
        // Set assignable users based on permissions
        $this->assignableUsers = $this->getAssignableUsers();
        
        // Set default date values similar to executive plan
        $this->toDate = now()->format('Y-m-d');
        $this->fromDate = now()->startOfMonth()->format('Y-m-d');
    }

    /**
     * Get assignable users based on current user's permissions
     */
    private function getAssignableUsers()
    {
        $user = auth()->user();
        $assignables = collect();

        // User can view any task
        if ($user->can('task.view-any')) {
            return User::all();
        }

        // User can view department tasks
        if ($user->can('task.view-department')) {
            $assignables = $assignables->merge($user->department->users);
        }

        // User can view subordinate tasks
        if ($user->can('task.view-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            $assignables = $assignables->merge($subordinateUsers);
        }

        // Add user's own tasks
        $assignables->push($user);

        return $assignables->unique('id');
    }

    /**
     * Get tasks based on user permissions
     */
    private function getTasksQuery()
    {
        $user = auth()->user();
        $tasks = Task::with('user');

        // User can view any task
        if ($user->can('task.view-any')) {
            return $tasks;
        }

        // User can view department tasks
        if ($user->can('task.view-department')) {
            return $tasks->whereHas('user', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            });
        }

        // User can view subordinate tasks
        if ($user->can('task.view-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            $subordinateUserIds = $subordinateUsers->pluck('id')->toArray();
            return $tasks->whereIn('user_id', $subordinateUserIds);
        }

        // Default: user can only view their own tasks
        return $tasks->where('user_id', $user->id);
    }

    public function includeTrashed()
    {
        $this->withTrashed = !$this->withTrashed;
    }

    public function updatedFilter()
    {
        $this->date = null;
    }

    public function render()
    {
        $status = [
            'بالانتظار' => 'pending',
            'قيد التنفيذ' => 'in_progress',
            'تم التنفيذ' => 'submitted',
            'مكتمل' => 'approved',
        ];

        $statusMatches = array_filter($status, function ($key) {
            return strpos($key, $this->search) !== false;
        }, ARRAY_FILTER_USE_KEY);

        // Start with permission-based query
        $tasks = $this->getTasksQuery();

        // Apply department filter only if user has department-level permissions
        if ($this->department && auth()->user()->can('task.view-department')) {
            if ($this->department !== 'all') {
                $tasks = $tasks->whereHas('user', function ($query) {
                    $query->where('department_id', $this->department);
                });
            }
        }

        // Apply assigned by filter only if user has appropriate permissions
        if ($this->assignedBy && auth()->user()->can('task.view-any')) {
            $tasks = $tasks->where('assigned_by', $this->assignedBy);
        }

        // Apply trashed filter
        if ($this->withTrashed) {
            $tasks = $tasks->withTrashed();
        }

        // Always apply date range filtering
        if ($this->fromDate && $this->toDate) {
            $fromDate = \Carbon\Carbon::parse($this->fromDate)->startOfDay();
            $toDate = \Carbon\Carbon::parse($this->toDate)->endOfDay();
            $tasks = $tasks->whereBetween('task_date', [$fromDate, $toDate]);
        }

        // Apply status filter
        if ($this->status) {
            if($this->status == 'deleted'){
                $tasks = $tasks->onlyTrashed();
            } elseif($this->status == 'overdue'){
                $tasks = $tasks->where('due_date', '<', now()->startOfDay())
                               ->whereNotIn('status', ['submitted', 'approved']);
            } else {
                $tasks = $tasks->where('status', $this->status);
            }
        } else {
            $tasks = $tasks->where('status', '!=', 'submitted');
        }

        // Apply priority filter
        if ($this->priority) {
            $tasks = $tasks->where('priority', $this->priority);
        }

        // Apply date filter
        if ($this->date) {
            $tasks = $tasks->whereDate('task_date', $this->date);
        }

        // Apply search filter with permission-aware search
        if ($this->search) {
            $tasks = $tasks->where(function ($query) use ($statusMatches) {
                $query->whereHas('user', function ($userQuery) {
                    $userQuery->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('title', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhereIn('status', $statusMatches)
                ->orWhere('priority', 'like', '%' . $this->search . '%')
                ->orWhere('task_date', 'like', '%' . $this->search . '%')
                ->orWhere('due_date', 'like', '%' . $this->search . '%')
                ->orWhere('completed_at', 'like', '%' . $this->search . '%');
            });
        }

        // Apply time-based filters
        switch ($this->filter) {
            case 'today':
                $tasks = $tasks->whereDate('task_date', now()->format('Y-m-d'));
                break;
            case 'this_week':
                $tasks = $tasks->whereBetween('task_date', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'this_month':
                $tasks = $tasks->whereMonth('task_date', now()->month);
                break;
            case 'this_year':
                $tasks = $tasks->whereYear('task_date', now()->year);
                break;
        }

        $tasks = $tasks->latest('task_date')
            ->paginate($this->perPage);

        return view(
            'livewire.tasks.table-list',
            [
                'tasks' => $tasks,
            ]
        );
    }

    public function restoreTask($id)
    {
        if (!auth()->user()->can('task.restore-trashed')) return;
        $task = Task::withTrashed()->find($id);
        if ($task && $task->trashed()) {
            $task->restore();
        }
    }

    #[On('set-department')]
    public function setDepartment($id)
    {
        $this->department = $id;
    }

    #[On('set-assigned-by')]
    public function setAssignedBy($id)
    {
        $this->assignedBy = $id;
    }
}
