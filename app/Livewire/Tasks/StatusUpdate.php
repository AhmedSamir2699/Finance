<?php

namespace App\Livewire\Tasks;

use App\Helpers\NotificationHelper;
use App\Models\Task;
use Livewire\Component;

class StatusUpdate extends Component
{
    public $task;
    public $status;


    public function mount($task)
    {
        $this->task = $task;
        $this->status = $task->status;
    }

    public function updateStatus()
    {
        $user = auth()->user();
        
        // Check if user can update this task's status
        if (!$this->canUpdateTaskStatus($user, $this->task)) {
            toastr()->error(__('tasks.status.permission_error'));
            return;
        }
        
        if ($this->task && $this->task->user_id == auth()->id()) {
            // Get the current task status
            $currentStatus = $this->task->status;
            $taskId = $this->task->getKey();
            
            if ($currentStatus == 'pending' || $currentStatus == 'rejected') {
                
                if($this->task->started_at == null){
                    $this->task->update(['status' => 'in_progress', 'started_at' => now()]);
                }else{
                    $this->task->update(['status' => 'in_progress']);
                }
                
                // Refresh the task data
                $this->task->refresh();
                $this->status = $this->task->status;
                
                // Create history record
                $this->task->histories()->create([
                    'action' => __('tasks.status.started'),
                    'user_id' => auth()->id(),
                ]);

                // Send notification if task is assigned
                if ($this->task->assigned_by != null) {
                    $assignedBy = $this->task->assignedBy;
                    $message = __('tasks.notification.started') . ': ' . $this->task->title;
                    $action_route = 'tasks.show';
                    $params = $taskId;
                    NotificationHelper::sendNotification($assignedBy, $message, $action_route, $params);
                }
                
                // Show success message
                toastr()->success(__('tasks.status.started'));
            }
        }
    }
    
    /**
     * Check if user can update task status
     */
    private function canUpdateTaskStatus($user, $task): bool
    {
        // User can update their own task status
        if ($user->id === $task->user_id && $user->can('task.edit')) {
            return true;
        }

        // User can update tasks they assigned
        if ($user->id === $task->assigned_by && $user->can('task.edit')) {
            return true;
        }

        // User can update any task
        if ($user->can('task.edit-any')) {
            return true;
        }

        // User can update department tasks
        if ($user->can('task.edit-department') && $user->department_id === $task->user->department_id) {
            return true;
        }

        // User can update subordinate tasks
        if ($user->can('task.edit-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->contains('id', $task->user->id)) {
                return true;
            }
        }

        return false;
    }
    public function render()
    {
        return view('livewire.tasks.status-update');
    }
}
