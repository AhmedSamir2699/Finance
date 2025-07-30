<?php

namespace App\Livewire\Tasks\ExecutivePlan;

use App\Models\Department;
use App\Models\ExecutivePlan;
use App\Models\ExecutivePlanCell;
use App\Models\Task;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

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
    public $from;
    public $to;

    public function mount()
    {
        $this->departments = Department::all();
        $this->department = auth()->user()->department->id;
        $this->to = now()->format('Y-m-d');
        $this->from = now()->startOfMonth()->format('Y-m-d');
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


        if ($this->department) {
            if ($this->department !== 'all') {
                $tasks = ExecutivePlanCell::whereHas('user', function ($query) {
                    $query->where('department_id', $this->department);
                })->with('user')
                    ->when($this->withTrashed, function ($query) {
                        return $query->withTrashed();
                    });
            } else {
                $tasks = ExecutivePlanCell::with('user')
                    ->when($this->withTrashed, function ($query) {
                        return $query->withTrashed();
                    });
            }
        }

        if ($this->status) {
            if($this->status == 'past_due'){
                $tasks = $tasks->where('status', 'pending')->whereDate('date', '<', now()->startOfDay());
            }else{
                $tasks = $tasks->where('status', $this->status);
            }
        } 

        if ($this->date) {

            $tasks = $tasks->whereDate('date', $this->date);
        }

        if ($this->search) {
            $tasks = $tasks
                ->whereHas('user', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                ->orWhere('value', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orWhereIn('status', $statusMatches)
                ->orWhere('date', 'like', '%' . $this->search . '%');
        }

        
        $tasks->whereBetween('date', [$this->from, $this->to]);

        $tasks = $tasks->orderBy('date', 'desc')
            ->paginate($this->perPage);
        return view(
            'livewire.tasks.executive-plan.table-list',
            [
                'tasks' => $tasks,
            ]
        );
    }
}
