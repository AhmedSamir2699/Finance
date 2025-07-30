<?php

namespace App\Livewire\Tasks;

use App\Models\Task;
use App\Models\User;
use Livewire\Component;

class TasksCards extends Component
{
    public $department;
    protected $listeners = ['refreshTasks'];

    public function refreshTasks()
    {
        $this->render();
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
    
    public function render()
    {
        $tasks = $this->getTasksQuery();

        // Apply department filter only if user has department-level permissions
        if ($this->department && auth()->user()->can('task.view-department')) {
            $tasks = $tasks->whereHas('user', function ($query) {
                $query->where('department_id', $this->department->id);
            });
        }
        
        $todaysTasks = (clone $tasks)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('due_date', '>=', now()->startOfDay())
            ->whereDate('due_date', '<=', now()->endOfDay())
            ->count();
        
        $monthsTasks = (clone $tasks)
            ->whereDate('due_date', '>=', now()->startOfMonth())
            ->whereDate('due_date', '<=', now()->endOfMonth())
            ->count();
        
        $completedTasks = (clone $tasks)
            ->whereIn('status', ['submitted', 'approved'])
            ->whereDate('due_date', '>=', now()->startOfMonth())
            ->whereDate('due_date', '<=', now()->endOfMonth())
            ->count();
        
        return view('livewire.tasks.tasks-cards', [
            'todaysTasks' => $todaysTasks,
            'monthsTasks' => $monthsTasks,
            'completedTasks' => $completedTasks,
        ]);
        
    }
}
