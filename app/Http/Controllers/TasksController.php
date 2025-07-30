<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\Task;
use App\Models\TaskAttachment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    /**
     * Check if user can view a specific task
     */
    private function canViewTask(User $user, Task $task): bool
    {
        // User can view their own tasks
        if ($user->id === $task->user_id && $user->can('task.view')) {
            return true;
        }

        // User can view tasks they assigned
        if ($user->id === $task->assigned_by && $user->can('task.view')) {
            return true;
        }

        // User can view any task
        if ($user->can('task.view-any')) {
            return true;
        }

        // User can view department tasks
        if ($user->can('task.view-department') && $user->department_id === $task->user->department_id) {
            return true;
        }

        // User can view subordinate tasks
        if ($user->can('task.view-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->contains('id', $task->user->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can edit a specific task
     */
    private function canEditTask(User $user, Task $task): bool
    {
        // User can edit their own pending tasks
        if ($user->id === $task->user_id && $user->can('task.edit') && $task->status === 'pending') {
            return true;
        }

        // User can edit tasks they assigned
        if ($user->id === $task->assigned_by && $user->can('task.edit')) {
            return true;
        }

        // User can edit any task
        if ($user->can('task.edit-any')) {
            return true;
        }

        // User can edit department tasks
        if ($user->can('task.edit-department') && $user->department_id === $task->user->department_id) {
            return true;
        }

        // User can edit subordinate tasks
        if ($user->can('task.edit-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->contains('id', $task->user->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can delete a specific task
     */
    private function canDeleteTask(User $user, Task $task): bool
    {
        // User can delete their own tasks (if not assigned by someone else)
        if ($user->id === $task->user_id && $user->can('task.delete') && 
            $task->status !== 'deleted' && is_null($task->assigned_by)) {
            return true;
        }

        // User can delete tasks they assigned
        if ($user->id === $task->assigned_by && 
            ($user->can('task.delete-subordinates') || $user->can('task.delete-department') || $user->can('task.delete-any'))) {
            return true;
        }

        // User can delete any task
        if ($user->can('task.delete-any')) {
            return true;
        }

        // User can delete department tasks
        if ($user->can('task.delete-department') && $user->department_id === $task->user->department_id) {
            return true;
        }

        return false;
    }

    /**
     * Check if user can approve a specific task
     */
    private function canApproveTask(User $user, Task $task): bool
    {
        // User can approve department tasks
        if ($user->can('task.approve-department') && $user->department_id === $task->user->department_id) {
            return true;
        }

        // User can approve subordinate tasks
        if ($user->can('task.approve-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->contains('id', $task->user->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can comment on a specific task
     */
    private function canCommentOnTask(User $user, Task $task): bool
    {
        // User can comment on their own tasks
        if ($user->id === $task->user_id && $user->can('task.comment')) {
            return true;
        }

        // User can comment on tasks they assigned
        if ($user->id === $task->assigned_by && $user->can('task.comment')) {
            return true;
        }

        // User can comment on any task
        if ($user->can('task.comment-any')) {
            return true;
        }

        // User can comment on department tasks
        if ($user->can('task.comment-department') && $user->department_id === $task->user->department_id) {
            return true;
        }

        // User can comment on subordinate tasks
        if ($user->can('task.comment-subordinates')) {
            $subordinateUsers = $user->subordinateUsers();
            if ($subordinateUsers->contains('id', $task->user->id)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if user can assign tasks to a specific user
     */
    private function canAssignToUser(User $assigner, User $assignee): bool
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
        $subordinateUsers = $assigner->subordinateUsers();
        if ($subordinateUsers->contains('id', $assignee->id)) {
            return true;
        }

        // User can assign to anyone
        if ($assigner->can('task.create-any')) {
            return true;
        }

        return false;
    }

    /**
     * Get assignable users for the current user
     */
    private function getAssignableUsers(User $user): \Illuminate\Support\Collection
    {
        $assignables = collect();

        // Add department users
        if ($user->department) {
            $assignables = $assignables->merge($user->department->users);
        }

        // Add subordinates
        $subordinateUsers = $user->subordinateUsers();
        if ($subordinateUsers->isNotEmpty()) {
            $assignables = $assignables->merge($subordinateUsers);
        }

        // Remove duplicates
        $assignables = $assignables->unique('id');

        // Filter based on assign permissions
        $assignables = $assignables->filter(function ($assignee) use ($user) {
            return $this->canAssignToUser($user, $assignee);
        });

        return $assignables;
    }

    function index()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index')
        ];

        return view('tasks.index', ['breadcrumbs' => $breadcrumbs]);
    }

    function show(Task $task, Request $request)
    {
        $user = auth()->user();
        
        // Check if user can view this specific task
        if (!$this->canViewTask($user, $task)) {
            flash()->error(__('tasks.view.permission_error'));
            return redirect()->route('tasks.index');
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.show', $task) => $task->title
        ];
        $commentsExpanded = false;

        $comments = $task->comments()->with('user')->get();

        if ($request->has('comments')) {
            $commentsExpanded = true;
        }

        return view('tasks.show', [
            'task' => $task,
            'comments' => $comments,
            'breadcrumbs' => $breadcrumbs,
            'commentsExpanded' => $commentsExpanded
        ]);
    }

    function calendar()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.calendar') => __('tasks.calendar.title')
        ];

        return view('tasks.calendar', ['breadcrumbs' => $breadcrumbs]);
    }

    function create(Request $request)
    {
        $user = auth()->user();
        
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.create') => __('breadcrumbs.tasks.create')
        ];

        $clonedTask = null;
        if ($request->has('clone')) {
            $clonedTask = Task::findOrFail($request->clone);
            
            // Check if user can clone this task (must be the assigned_by user and have proper permissions)
            if ($user->id !== $clonedTask->assigned_by || !$this->canViewTask($user, $clonedTask)) {
                flash()->error(__('tasks.clone.permission_error'));
                return redirect()->route('tasks.index');
            }
        }
        
        // Get assignable users based on permissions
        $assignables = $this->getAssignableUsers($user);
        
        // If cloning a task, ensure the original assignee is included in the list
        if ($clonedTask && $clonedTask->user_id) {
            $originalAssignee = User::find($clonedTask->user_id);
            if ($originalAssignee && !$assignables->contains('id', $originalAssignee->id)) {
                $assignables->push($originalAssignee);
            }
        }
        
        // Format dates for cloned task if it exists
        if ($clonedTask) {
            $clonedTask->formatted_task_date = $clonedTask->task_date instanceof \Carbon\Carbon 
                ? $clonedTask->task_date->format('Y-m-d') 
                : $clonedTask->task_date;
            
            $clonedTask->formatted_due_date = $clonedTask->due_date instanceof \Carbon\Carbon 
                ? $clonedTask->due_date->format('Y-m-d') 
                : ($clonedTask->due_date ?: \Carbon\Carbon::now()->format('Y-m-d'));
        }

        // Handle executive plan prefilled data
        $executivePlanData = null;
        if ($request->has('from_executive_plan') && $request->from_executive_plan && $request->has('cell_id')) {
            $cell = \App\Models\ExecutivePlanCell::find($request->cell_id);
            
            if ($cell) {
                // Check if user can assign to this cell's user
                if (!$this->canAssignToUser($user, $cell->user)) {
                    flash()->error(__('tasks.assign.permission_error'));
                    return redirect()->route('tasks.index');
                }

                // Check if user can view this cell
                if (!$user->can('task.view-any') && 
                    !($user->can('task.view-department') && $user->department_id === $cell->user->department_id) &&
                    !($user->can('task.view-subordinates') && $user->subordinateUsers()->contains('id', $cell->user->id))) {
                    flash()->error(__('tasks.executive_plan.view_permission_error'));
                    return redirect()->route('tasks.index');
                }

                $executivePlanData = [
                    'title' => $cell->value,
                    'description' => $cell->description,
                    'task_date' => $cell->date,
                    'due_date' => $cell->date,
                    'assignee' => $cell->user_id,
                    'type' => 'scheduled',
                    'cell_id' => $cell->id
                ];
            }
        }
        
        return view('tasks.create', [
            'breadcrumbs' => $breadcrumbs, 
            'assignables' => $assignables,
            'clonedTask' => $clonedTask,
            'executivePlanData' => $executivePlanData
        ]);
    }

    function needApproval()
    {
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.need-approval') => __('breadcrumbs.tasks.need_approval.title')
        ];

        return view('tasks.need-approval', [
            'breadcrumbs' => $breadcrumbs
        ]);
    }

    function store(Request $request)
    {
        $user = auth()->user();
        
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'task_date' => 'required|date',
            'due_date' => 'nullable|date',
            'type' => 'required|string',
            'estimated_time' => 'nullable|integer|min:0',
            'estimated_time_value' => 'required|numeric|min:0',
            'estimated_time_unit' => 'required|in:minute,hour,day',
            'priority' => 'required|string',
            'assignees' => 'nullable|array',
            'assignee' => 'nullable|exists:users,id',
        ]);
        
        $assignees = $request->input('assignees', []);
        
        if (!empty($assignees)) {
            foreach ($assignees as $assigneeId) {
                $assignee = User::find($assigneeId);
                
                // Validate that user can assign to this specific user
                if (!$assignee || !$this->canAssignToUser($user, $assignee)) {
                    flash()->error(__('tasks.assign.permission_error'));
                    return redirect()->route('tasks.create');
                }
                
                $task = new Task();
                $task->title = $request->title;
                $task->description = $request->description;
                $task->task_date = $request->task_date;
                $task->due_date = $request->due_date;
                $task->type = $request->type;
                // Handle estimated time conversion
                $estimatedTime = 0;
                if (isset($request->estimated_time) && $request->estimated_time > 0) {
                    $estimatedTime = $request->estimated_time;
                } else if ($request->has('estimated_time_value') && $request->has('estimated_time_unit')) {
                    $value = (int) $request->estimated_time_value;
                    $unit = $request->estimated_time_unit;
                    
                    switch($unit) {
                        case 'minute':
                            $estimatedTime = $value;
                            break;
                        case 'hour':
                            $estimatedTime = $value * 60;
                            break;
                        case 'day':
                            $estimatedTime = $value * 60 * 24;
                            break;
                    }
                }
                
                $task->estimated_time = $estimatedTime;
                $task->priority = $request->priority;
                $task->assigned_by = $user->id;
                $task->user_id = $assigneeId;
                $task->save();

                if($request->has('attachments')){
                    foreach ($request->file('attachments') as $attachment) {
                        $path = $attachment->store('attachments');
                        $task->attachments()->create([
                            'name' => $attachment->getClientOriginalName(),
                            'path' => $path,
                            'type' => $attachment->getClientMimeType(),
                            'size' => $attachment->getSize(),
                            'extension' => $attachment->getClientOriginalExtension(),
                            'mime_type' => $attachment->getClientMimeType(),
                            'user_id' => auth()->id(),
                        ]);
                    }
                }

                // Clone attachments if this is a cloned task
                if($request->has('cloned_task_id')){
                    $clonedTask = Task::find($request->cloned_task_id);
                    if($clonedTask && $clonedTask->attachments->count() > 0){
                        // Get selected attachment IDs
                        $selectedAttachmentIds = $request->input('clone_attachments', []);
                        
                        // Only clone if there are selected attachments
                        if(!empty($selectedAttachmentIds)){
                            foreach($clonedTask->attachments as $originalAttachment){
                                // Only clone if attachment is selected
                                if(in_array($originalAttachment->id, $selectedAttachmentIds)){
                                    // Copy the file
                                    $originalPath = storage_path('app/private/' . $originalAttachment->path);
                                    if(file_exists($originalPath)){
                                        $newPath = 'attachments/' . uniqid() . '_' . $originalAttachment->name;
                                        $fullNewPath = storage_path('app/private/' . $newPath);
                                        
                                        // Create directory if it doesn't exist
                                        $dir = dirname($fullNewPath);
                                        if (!is_dir($dir)) {
                                            mkdir($dir, 0755, true);
                                        }
                                        
                                        copy($originalPath, $fullNewPath);
                                        
                                        $task->attachments()->create([
                                            'name' => $originalAttachment->name,
                                            'path' => $newPath,
                                            'type' => $originalAttachment->type,
                                            'size' => $originalAttachment->size,
                                            'extension' => $originalAttachment->extension,
                                            'mime_type' => $originalAttachment->mime_type,
                                            'user_id' => auth()->id(),
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }

                $task->histories()->create([
                    'action' => 'تم إنشاء المهمة',
                    'user_id' => auth()->id(),
                ]);

                NotificationHelper::sendNotification(User::find($assigneeId), 'تم تكليفك بمهمة جديدة', 'tasks.show', $task->id);

                $task->histories()->create([
                    'action' => 'تم تكليف ' . User::find($assigneeId)->name . ' بالمهمة',
                    'user_id' => auth()->id(),
                ]);
            }
            if ($request->has('add_more')) {
                flash()->success(__('tasks.create.success'));
                return redirect()->route('tasks.create');
            } else {
                flash()->success(__('tasks.create.success'));
                return redirect()->route('tasks.index');
            }
        } else {
            // fallback to single assignee or self-assignment
            $task = new Task();
            $task->title = $request->title;
            $task->description = $request->description;
            $task->task_date = $request->task_date;
            $task->due_date = $request->due_date;
            $task->type = $request->type;
            // Handle estimated time conversion
            $estimatedTime = 0;
            if (isset($request->estimated_time) && $request->estimated_time > 0) {
                $estimatedTime = $request->estimated_time;
            } else if ($request->has('estimated_time_value') && $request->has('estimated_time_unit')) {
                $value = (int) $request->estimated_time_value;
                $unit = $request->estimated_time_unit;
                
                switch($unit) {
                    case 'minute':
                        $estimatedTime = $value;
                        break;
                    case 'hour':
                        $estimatedTime = $value * 60;
                        break;
                    case 'day':
                        $estimatedTime = $value * 60 * 24;
                        break;
                }
            }
            
            $task->estimated_time = $estimatedTime;
            $task->priority = $request->priority;
            $task->assigned_by = (isset($request->assignee)) ? auth()->id() : null;
            $task->user_id = (isset($request->assignee)) ? $request->assignee : auth()->id();
            $task->save();

            if($request->has('attachments')){
                foreach ($request->file('attachments') as $attachment) {
                    $path = $attachment->store('attachments');
                    $task->attachments()->create([
                        'name' => $attachment->getClientOriginalName(),
                        'path' => $path,
                        'type' => $attachment->getClientMimeType(),
                        'size' => $attachment->getSize(),
                        'extension' => $attachment->getClientOriginalExtension(),
                        'mime_type' => $attachment->getClientMimeType(),
                        'user_id' => auth()->id(),
                    ]);
                }
            }

            // Clone attachments if this is a cloned task
            if($request->has('cloned_task_id')){
                $clonedTask = Task::find($request->cloned_task_id);
                if($clonedTask && $clonedTask->attachments->count() > 0){
                    // Get selected attachment IDs
                    $selectedAttachmentIds = $request->input('clone_attachments', []);
                    
                    // Only clone if there are selected attachments
                    if(!empty($selectedAttachmentIds)){
                        foreach($clonedTask->attachments as $originalAttachment){
                            // Only clone if attachment is selected
                            if(in_array($originalAttachment->id, $selectedAttachmentIds)){
                                // Copy the file
                                $originalPath = storage_path('app/private/' . $originalAttachment->path);
                                if(file_exists($originalPath)){
                                    $newPath = 'attachments/' . uniqid() . '_' . $originalAttachment->name;
                                    $fullNewPath = storage_path('app/private/' . $newPath);
                                    
                                    // Create directory if it doesn't exist
                                    $dir = dirname($fullNewPath);
                                    if (!is_dir($dir)) {
                                        mkdir($dir, 0755, true);
                                    }
                                    
                                    copy($originalPath, $fullNewPath);
                                    
                                    $task->attachments()->create([
                                        'name' => $originalAttachment->name,
                                        'path' => $newPath,
                                        'type' => $originalAttachment->type,
                                        'size' => $originalAttachment->size,
                                        'extension' => $originalAttachment->extension,
                                        'mime_type' => $originalAttachment->mime_type,
                                        'user_id' => auth()->id(),
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            $task->histories()->create([
                'action' => 'تم إنشاء المهمة',
                'user_id' => auth()->id(),
            ]);

            if ($request->assignee) {
                NotificationHelper::sendNotification(User::find($request->assignee), 'تم تكليفك بمهمة جديدة', 'tasks.show', $task->id);

                $task->histories()->create([
                    'action' => 'تم تكليف ' . User::find($request->assignee)->name . ' بالمهمة',
                    'user_id' => auth()->id(),
                ]);
            }

            if ($request->has('add_more')) {
                flash()->success(__('tasks.create.success'));
                return redirect()->route('tasks.create');
            } else {
                flash()->success(__('tasks.create.success'));
                return redirect()->route('tasks.index');
            }
        }
    }

    function storeComment(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check if user can comment on this specific task
        if (!$this->canCommentOnTask($user, $task)) {
            flash()->error(__('tasks.comment.permission_error'));
            return redirect()->route('tasks.show', $task);
        }

        $task->comments()->create([
            'comment' => $request->comment,
            'user_id' => $user->id,
        ]);

        $task->histories()->create([
            'action' => 'تم إضافة تعليق',
            'user_id' => $user->id,
        ]);

        $notifiedUsers = [];

        foreach ($task->comments as $comment) {
            if ($comment->user_id != $user->id && !in_array($comment->user_id, $notifiedUsers)) {
                $comment->user->sendNotification('تم إضافة تعليق جديد على المهمة', 'tasks.show', $task->id);
                $notifiedUsers[] = $comment->user_id; // Mark this user as notified
            }
        }

        flash()->success(__('tasks.comment.create.success'));
        return redirect()->route('tasks.show', $task);
    }

    function submit(Task $task)
    {
        $user = auth()->user();
        
        // Check if user can submit this specific task (must be the task owner)
        if ($user->id !== $task->user_id) {
            flash()->error(__('tasks.submit.permission_error'));
            return redirect()->route('tasks.show', $task);
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.show', $task) => $task->title,
            route('tasks.submit', $task) => __('tasks.submit.title')
        ];

        return view('tasks.submit', ['task' => $task, 'breadcrumbs' => $breadcrumbs]);
    }

    function submitStore(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check if user can submit this specific task (must be the task owner)
        if ($user->id !== $task->user_id) {
            flash()->error(__('tasks.submit.permission_error'));
            return redirect()->route('tasks.show', $task);
        }

        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $attachment) {
                $path = $attachment->store('attachments');
                $attachments[] = [
                    'name' => $attachment->getClientOriginalName(),
                    'path' => $path
                ];
            }
        }

        $proof = [
            'comment' => nl2br($request->description),
            'user_id' => $user->id,
            'attachments' => $attachments ?? null,
        ];

        $task->proofs = json_encode($proof);
        $task->status = 'submitted';
        $task->submitted_at = now();

        $task->save();

        $task->histories()->create([
            'action' => 'تم تقديم المهمة للمراجعة',
            'user_id' => $user->id,
        ]);

        $users = $task->setupNotificationUsers();

        foreach ($users as $user) {
                NotificationHelper::sendNotification($user, 'تم تقديم المهمة #' . $task->id . ' للمراجعة', 'tasks.show', $task->id);
        }

        flash()->success(__('tasks.submit.success'));
        return redirect()->route('tasks.show', $task);
    }

    function downloadAttachment(Task $task, TaskAttachment $attachment)
    {
        $user = auth()->user();
        
        // Check if user can view this specific task
        if (!$this->canViewTask($user, $task)) {
            flash()->error(__('tasks.view.permission_error'));
            return redirect()->route('tasks.index');
        }

        $filePath = storage_path('app/private/' . $attachment->path);
        if (file_exists($filePath)) {
            return response()->download($filePath, $attachment->name);
        }

        flash()->error(__('tasks.attachment.error'));
        return redirect()->route('tasks.show', $attachment->task);
    }

    function downloadProof(Task $task, $attachment)
    {
        $user = auth()->user();
        
        // Check if user can view this specific task
        if (!$this->canViewTask($user, $task)) {
            flash()->error(__('tasks.view.permission_error'));
            return redirect()->route('tasks.index');
        }
        
        $attachments = json_decode($task->proofs, true)['attachments'];
        $attachment = $attachments[$attachment]['path'];
        return response()->download(storage_path('app/private/' . $attachment));
    }

    function previewAttachment(Task $task, TaskAttachment $attachment)
    {
        $user = auth()->user();
        
        // Check if user can view this specific task
        if (!$this->canViewTask($user, $task)) {
            flash()->error(__('tasks.view.permission_error'));
            return redirect()->route('tasks.index');
        }
        
        $filePath = storage_path('app/private/' . $attachment->path);
        if (!file_exists($filePath)) {
            abort(404);
        }
        $mimeType = $attachment->mime_type ?? mime_content_type($filePath);
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $attachment->name . '"'
        ]);
    }

    function previewProof(Task $task, $proof)
    {
        $user = auth()->user();
        
        // Check if user can view this specific task
        if (!$this->canViewTask($user, $task)) {
            flash()->error(__('tasks.view.permission_error'));
            return redirect()->route('tasks.index');
        }
        
        $proofs = json_decode($task->proofs, true);
        if (!isset($proofs['attachments'][$proof])) {
            abort(404);
        }
        $attachment = $proofs['attachments'][$proof];
        $filePath = storage_path('app/private/' . $attachment['path']);
        if (!file_exists($filePath)) {
            abort(404);
        }
        $mimeType = $attachment['mime_type'] ?? mime_content_type($filePath);
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $attachment['name'] . '"'
        ]);
    }

    function approve(Task $task)
    {
        $user = auth()->user();
        
        // Check if user can approve this specific task
        if (!$this->canApproveTask($user, $task)) {
            flash()->error(__('tasks.approve.permission_error'));
            return redirect()->route('tasks.show', $task);
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.show', $task) => $task->title,
            route('tasks.approve', $task) => __('breadcrumbs.tasks.approve')
        ];

        $actualTime = \Carbon\Carbon::parse($task->started_at)->diffInMinutes($task->submitted_at);

        return view('tasks.approve', ['task' => $task, 'breadcrumbs' => $breadcrumbs, 'actualTime' => $actualTime]);
    }

    function approveStore(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check if user can approve this specific task
        if (!$this->canApproveTask($user, $task)) {
            flash()->error(__('tasks.approve.permission_error'));
            return redirect()->route('tasks.show', $task);
        }

        $task->status = 'approved';
        $task->completed_at = now();
        $task->actual_time = \Carbon\Carbon::parse($task->started_at)->diffInMinutes($task->submitted_at);
        $task->quality_percentage = $request->quality;
        $task->completion_percentage = $request->completion_percentage;
        $users = $task->setupNotificationUsers();
        $users[] = User::find($task->user_id);

        $comment = __('tasks.submit.quality') . ' ' . $task->quality_percentage . '%' . " - " . __('tasks.submit.actualTime') . ' ' . gmdate('H:i:s', $task->actual_time * 60);
        $comment .= $request->comment ? "\n\n" . $request->comment . "\n\n": '';

        if ($request->has('comment')) {
            $task->comments()->create([
                'comment' => nl2br($comment),
                'user_id' => $user->id,
            ]);

            $task->histories()->create([
                'action' => 'تم إضافة تعليق',
                'user_id' => $user->id,
            ]);

            foreach ($users as $user) {
                if ($user->id != auth()->id())
                    NotificationHelper::sendNotification($user, 'تم إضافة تعليق جديد على المهمة', 'tasks.show', $task->id);
            }
        }

        $task->save();

        $task->histories()->create([
            'action' => 'تم الموافقة على المهمة',
            'user_id' => $user->id,
        ]);

        foreach ($users as $user) {
            if ($user->id != auth()->id())
                NotificationHelper::sendNotification($user, 'تم الموافقة على المهمة #' . $task->id, 'tasks.show', $task->id);
        }

        flash()->success(__('tasks.approve.success'));
        return redirect()->route('tasks.show', $task);
    }

    function reject(Task $task)
    {
        $user = auth()->user();
        
        // Check if user can approve this specific task (same logic as approve)
        if (!$this->canApproveTask($user, $task)) {
            flash()->error(__('tasks.reject.permission_error'));
            return redirect()->route('tasks.show', $task);
        }

        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.show', $task) => $task->title,
            route('tasks.reject', $task) => __('breadcrumbs.tasks.reject')
        ];

        return view('tasks.reject', ['task' => $task, 'breadcrumbs' => $breadcrumbs]);
    }

    function rejectStore(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check if user can approve this specific task (same logic as approve)
        if (!$this->canApproveTask($user, $task)) {
            flash()->error(__('tasks.reject.permission_error'));
            return redirect()->route('tasks.show', $task);
        }

        $task->status = 'rejected';
        $task->save();

        $users = $task->setupNotificationUsers();
        $users[] = User::find($task->user_id);

        if ($request->has('comment')) {
            $task->comments()->create([
                'comment' => $request->comment,
                'user_id' => $user->id,
            ]);

            $task->histories()->create([
                'action' => 'تم إضافة تعليق',
                'user_id' => $user->id,
            ]);

            foreach ($users as $user) {
                if ($user->id != auth()->id())
                    NotificationHelper::sendNotification($user, 'تم إضافة تعليق جديد على المهمة', 'tasks.show', $task->id);
            }
        }

        $task->histories()->create([
            'action' => 'تم رفض المهمة المقدمة',
            'user_id' => $user->id,
        ]);

        NotificationHelper::sendNotification(User::find($task->user_id), 'تم رفض المهمة المقدمة #' . $task->id, 'tasks.show', $task->id);

        flash()->success(__('tasks.reject.success'));
        return redirect()->route('tasks.show', $task);
    }

    function destroy(Task $task)
    {
        $user = auth()->user();
        
        // Check if user can delete this specific task
        if (!$this->canDeleteTask($user, $task)) {
            flash()->error(__('tasks.delete.permission_error'));
            return redirect()->route('tasks.index');
        }
        
        $task->histories()->create([
            'action' => 'تم حذف المهمة',
            'user_id' => $user->id,
        ]);
        
        $task->delete();
        flash()->success(__('tasks.delete.success'));
        return redirect()->route('tasks.index');
    }

    function edit(Task $task)
    {
        $user = auth()->user();
        
        // Check if user can edit this specific task
        if (!$this->canEditTask($user, $task)) {
            flash()->error(__('tasks.edit.permission_error'));
            return redirect()->route('tasks.index');
        }
        
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.show', $task) => $task->title,
            route('tasks.edit', $task) => __('breadcrumbs.tasks.edit'),
        ];
        
        // Get assignable users based on permissions
        $assignables = $this->getAssignableUsers($user);
        
        return view('tasks.edit', [
            'task' => $task,
            'assignables' => $assignables,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    function update(Request $request, Task $task)
    {
        $user = auth()->user();
        
        // Check if user can edit this specific task
        if (!$this->canEditTask($user, $task)) {
            flash()->error(__('tasks.edit.permission_error'));
            return redirect()->route('tasks.index');
        }
        
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'task_date' => 'required|date',
                'due_date' => 'nullable|date',
                'type' => 'required|string',
                'estimated_time_value' => 'required|numeric|min:0',
                'estimated_time_unit' => 'required|in:minute,hour,day',
                'priority' => 'required|string',
                'assignee' => 'nullable|exists:users,id',
            ]);
            
            // Validate assignee permissions if changing assignee
            if (isset($validated['assignee']) && $validated['assignee'] != $task->user_id) {
                $newAssignee = User::find($validated['assignee']);
                if (!$newAssignee || !$this->canAssignToUser($user, $newAssignee)) {
                    flash()->error(__('tasks.assign.permission_error'));
                    return redirect()->route('tasks.edit', $task);
                }
            }
            
            $task->title = $validated['title'];
            $task->description = $validated['description'] ?? null;
            $task->task_date = $validated['task_date'];
            $task->due_date = $validated['due_date'] ?? null;
            $task->type = $validated['type'];
            // Handle estimated time conversion if not already converted
            $estimatedTime = $validated['estimated_time_value'];
            if ($request->has('estimated_time_value') && $request->has('estimated_time_unit')) {
                $value = (int) $request->estimated_time_value;
                $unit = $request->estimated_time_unit;
                
                switch($unit) {
                    case 'minute':
                        $estimatedTime = $value;
                        break;
                    case 'hour':
                        $estimatedTime = $value * 60;
                        break;
                    case 'day':
                        $estimatedTime = $value * 60 * 24;
                        break;
                }
            }
            
            $task->estimated_time = $estimatedTime;
            $task->priority = $validated['priority'];
            
            if (isset($validated['assignee'])) {
                $task->user_id = $validated['assignee'];
                $task->assigned_by = $user->id;
            }
            
            $task->save();
            $task->histories()->create([
                'action' => 'تم تعديل المهمة',
                'user_id' => $user->id,
            ]);

            if (isset($validated['assignee'])) {
                $task->histories()->create([
                   'action' => 'تم اعادة تكليفة المهمة للموظف ' . User::find($validated['assignee'])->name,
                   'user_id' => $user->id,
                ]);
                NotificationHelper::sendNotification(User::find($validated['assignee']), 'تم اعادة تكليفة المهمة #' . $task->id . ' للموظف ' . $user->name, 'tasks.show', $task->id);
            }

            flash()->success(__('tasks.edit.success'));
            return redirect()->route('tasks.show', $task);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            flash()->error(__('tasks.edit.validation_error').' '.$e->getMessage());
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            flash()->error(__('tasks.edit.error').' '.$e->getMessage());
            return redirect()->back()->withInput();
        }
    }

    function executivePlanCreate()
    {
        $user = auth()->user();
        
        // Check if user has permission to assign tasks
        if (!$user->can('task.assign')) {
            flash()->error(__('tasks.assign.permission_error'));
            return redirect()->route('tasks.index');
        }
        
        $breadcrumbs = [
            route('dashboard') => __('breadcrumbs.dashboard.index'),
            route('tasks.index') => __('breadcrumbs.tasks.index'),
            route('tasks.executive-plan-create') => __('breadcrumbs.tasks.executive_plan_create')
        ];

        return view('tasks.executive-plan-create', [
            'breadcrumbs' => $breadcrumbs
        ]);
    }
}
