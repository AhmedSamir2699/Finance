@props(['status', 'task'])

@php
    $classes = [
        'approved' => 'bg-green-100 text-green-800',
        'in_progress' => 'bg-yellow-100 text-yellow-800',
        'submitted' => 'bg-indigo-100 text-indigo-800',
        'rejected' => 'bg-red-100 text-red-800',
        'pending' => 'bg-gray-100 text-gray-800',
        'overdue' => 'bg-red-100 text-red-800',
    ];
     $text = [
        'approved' => __('tasks.status.completed'),
        'in_progress' => __('tasks.status.in_progress'),
        'submitted' => __('tasks.status.submitted'),
        'rejected' => __('tasks.status.rejected'),
        'pending' => __('tasks.status.pending'),
        'overdue' => __('tasks.status.overdue'),
    ];
    
    // Check if task is overdue
    $displayStatus = $status;
    $displayClasses = $classes[$status] ?? 'bg-gray-100 text-gray-800';
    $displayText = $text[$status] ?? $status;
    
    if ($task && $task->due_date && $task->due_date < now()->startOfDay() && !in_array($status, ['submitted', 'approved'])) {
        $displayStatus = 'overdue';
        $displayClasses = $classes['overdue'];
        $displayText = $text['overdue'];
    }
@endphp

<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $displayClasses }}">
    {{ $displayText }}
</span> 