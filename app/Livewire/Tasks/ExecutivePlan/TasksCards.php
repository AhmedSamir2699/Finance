<?php

namespace App\Livewire\Tasks\ExecutivePlan;

use App\Models\ExecutivePlanCell;
use Livewire\Component;

class TasksCards extends Component
{
    public function render()
    {

        $tasks = ExecutivePlanCell::query();


        
        $todaysTasks = (clone $tasks)
            ->whereDate('date', '>=', now()->startOfDay())
            ->whereDate('date', '<=', now()->endOfDay())
            ->count();
        
        $monthsTasks = (clone $tasks)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereDate('date', '>=', now()->startOfMonth())
            ->whereDate('date', '<=', now()->endOfMonth())
            ->count();
        
        $completedTasks = (clone $tasks)
            ->whereIn('status', ['completed'])
            ->whereDate('date', '>=', now()->startOfMonth())
            ->whereDate('date', '<=', now()->endOfMonth())
            ->count();
        

        return view('livewire.tasks.executive-plan.tasks-cards',[
            'todaysTasks' => $todaysTasks,
            'monthsTasks' => $monthsTasks,
            'completedTasks' => $completedTasks,
        ]);
    }
}
