<?php

namespace App\Livewire\Tasks;

use Livewire\Component;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NeedApprovalTable extends Component
{
    public $assignedByMeOnly = false;

    public function toggleAssignedByMeOnly()
    {
        $this->assignedByMeOnly = !$this->assignedByMeOnly;
    }

    public function render()
    {
        $user = auth()->user();
        $tasksQuery = Task::where('status', 'submitted')->with('user');

        // User can approve any task
        if ($user->can('task.view-any')) {
            $tasksQuery = $tasksQuery->whereNot('user_id', $user->id);
        }
        // User can approve department tasks
        elseif ($user->can('task.approve-department')) {
            $tasksQuery = $tasksQuery->whereHas('user', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->whereNot('user_id', $user->id);
        }
        // User can approve subordinate tasks
        elseif ($user->can('task.approve-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            $subordinateUserIds = $subordinateUsers->pluck('id')->toArray();

            $tasksQuery = $tasksQuery->where(function ($query) use ($subordinateUserIds, $user) {
                $query->where('assigned_by', $user->id)
                    ->orWhereIn('user_id', $subordinateUserIds);
            })->whereNot('user_id', $user->id);
        }
        // Default: user can only see tasks they assigned
        else {
            $tasksQuery = $tasksQuery->where('assigned_by', $user->id)
                ->whereNot('user_id', $user->id);
        }

        if ($this->assignedByMeOnly) {
            $tasksQuery->where('assigned_by', $user->id);
        }

        $tasks = $tasksQuery->paginate();

        return view('livewire.tasks.need-approval-table', [
            'tasks' => $tasks,
            'assignedByMeOnly' => $this->assignedByMeOnly,
        ]);
    }
}
