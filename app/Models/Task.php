<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Task extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'task_date',
        'due_date',
        'estimated_time',
        'actual_time',
        'type',
        'completion_percentage',
        'quality_percentage',
        'priority',
        'description',
        'started_at',
        'submitted_at',
        'completed_at',
        'assigned_by',
        'proofs',
        'status',

    ];



    protected $dates = [
        'task_date',
        'due_date',
        'completed_at',
    ];

    protected $casts = [
        'proofs' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class);
    }

    public function histories()
    {
        return $this->hasMany(TaskHistory::class);
    }

    public function scopeArchived($query)
    {
        return $query->onlyTrashed();
    }

    public function setupNotificationUsers()
    {
        $task = $this;
    
        $superior = $task->user->superior();
    
        $users = collect();
    
        // Add the current authenticated user
        if (!$users->contains('id', auth()->id())) {
            $users->push(User::find(auth()->id()));
        }
    
        // Add the task owner
        if (!$users->contains('id', $task->user_id)) {
            $users->push(User::find($task->user_id));
        }
    
        // Add department-head users if the superior is a department-head
        if ($superior && $superior->name == 'department-head') {
            $departmentHeadUsers = $task->user->department
                ->users()
                ->whereHas('roles', function ($query) {
                    $query->where('name', 'department-head');
                })
                ->get();
    
            foreach ($departmentHeadUsers as $user) {
                if (!$users->contains('id', $user->id)) {
                    $users->push($user);
                }
            }
        } else {
            // Add users matching the superior's role
            $superiorUsers = User::whereHas('roles', function ($query) use ($superior) {
                $query->where('name', $superior?->name);
            })->get();
    
            foreach ($superiorUsers as $user) {
                if (!$users->contains('id', $user->id)) {
                    $users->push($user);
                }
            }
        }
    
        // Add the user who assigned the task, if different from the task owner
        if ($task->assigned_by != $task->user_id) {
            $assignedBy = User::find($task->assigned_by);
            if ($assignedBy && !$users->contains('id', $assignedBy->id)) {
                $users->push($assignedBy);
            }
        }
    
        // Add users who commented on the task, excluding the authenticated user
        foreach ($task->comments as $comment) {
            if ($comment->user_id != auth()->id()) {
                $commentUser = User::find($comment->user_id);
                if ($commentUser && !$users->contains('id', $commentUser->id)) {
                    $users->push($commentUser);
                }
            }
        }
    
        // Filter out any null or invalid users (safety step)
        $users = $users->filter();
        return $users;
    }
    

}
