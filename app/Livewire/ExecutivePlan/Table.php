<?php

namespace App\Livewire\ExecutivePlan;

use App\Models\ExecutivePlan;
use App\Models\ExecutivePlanCell;
use App\Models\ExecutivePlanColumn;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;


class Table extends Component
{

    public $columns;
    public $cells;
    public $isEditable;
    public $showEditControls = false;
    protected $persist = ['showEditControls'];
    public $month;
    public $year;
    public $user;
    public $daysInMonth;
    public $hijri;
    public $name;
    public $renderKey = 0;

    public function mount($columns, $cells, $month, $year, $user = null)
    {
        $this->user = $user == null ? auth()->user() : $user;

        $this->columns = $columns;
        $this->cells = $cells;
    }

    #[On('refreshTableCells')]
    public function refreshTableCells($columns = false)
    {
        if ($columns) {
            $this->columns = ExecutivePlanColumn::where('user_id', $this->user->id)
                ->where('month', $this->month)
                ->where('year', $this->year)
                ->orderBy('order')
                ->get();
        }

        $startOfMonth = Carbon::create($this->year, $this->month, 1)->toDateString();
        $endOfMonth = Carbon::create($this->year, $this->month)->endOfMonth()->toDateString();

        $this->cells = $this->user->cells()
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get(['id', 'date', 'executive_plan_column_id', 'value']);

        $this->renderKey = time();
    }

    public function addColumn()
    {
        $this->validate([
            'name' => 'required|string|max:255',
        ]);

        $lastOrder = ExecutivePlanColumn::where('user_id', $this->user->id)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->max('order');

        ExecutivePlanColumn::create([
            'user_id' => $this->user->id,
            'month' => $this->month,
            'year' => $this->year,
            'name' => $this->name,
            'order' => $lastOrder + 1,
        ]);

        $this->name = ''; // Clear the form
        return redirect()->to(request()->header('Referer'));
    }

    public function toggleEditControls()
    {
        if ($this->isEditable) {
            $this->showEditControls = !$this->showEditControls;
        }
    }


    public function moveUp($day)
{
    if ($day == 1) {
        return;
    }

    $previousDate = Carbon::create($this->year, $this->month, $day - 1)->toDateString();
    $currentDate = Carbon::create($this->year, $this->month, $day)->toDateString();

    DB::statement("
        UPDATE executive_plan_cells
        SET date = CASE 
            WHEN date = ? THEN ?  -- Move previousDate → currentDate
            WHEN date = ? THEN ?  -- Move currentDate → previousDate
        END
        WHERE user_id = ? AND date IN (?, ?)
    ", [$previousDate, $currentDate, $currentDate, $previousDate, $this->user->id, $previousDate, $currentDate]);

    $this->dispatch('refreshTableCells');
}



public function moveDown($day)
{
    if ($day == $this->daysInMonth) {
        return;
    }

    $nextDate = Carbon::create($this->year, $this->month, $day + 1)->toDateString();
    $currentDate = Carbon::create($this->year, $this->month, $day)->toDateString();

    DB::statement("
        UPDATE executive_plan_cells
        SET date = CASE 
            WHEN date = ? THEN ?  -- Move nextDate → currentDate
            WHEN date = ? THEN ?  -- Move currentDate → nextDate
        END
        WHERE user_id = ? AND date IN (?, ?)
    ", [$nextDate, $currentDate, $currentDate, $nextDate, $this->user->id, $nextDate, $currentDate]);

    $this->dispatch('refreshTableCells');
}

    

    public function deleteDay($day)
    {
        $this->user->cells()->whereDate('date', Carbon::create($this->year, $this->month, $day)->toDateString())
            ->delete();

        $this->dispatch('refreshTableCells');
    }
    public function render()
    {
        return view('livewire.executive-plan.table', [
            'showEditControls' => $this->showEditControls,
        ]);
    }
}
