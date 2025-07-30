<?php

namespace App\Livewire\ExecutivePlan;

use App\Models\ExecutivePlan;
use App\Models\ExecutivePlanColumn;
use Livewire\Component;

class TableHead extends Component
{
    public $columns;
    public $isEditable;
    public $month;
    public $year;
    public $user;
    public $showEditControls = false;

    public function mount($columns, $month, $year, $user = null, $showEditControls = false)
    {
        $this->month = $month;
        $this->year = $year;
        $this->user = $user == null ? auth()->user() : $user;
        $this->columns = $columns;
        $this->showEditControls = $showEditControls;
            
    }

    public function updatedColumns($value, $key)
    {
        [$index, $field] = explode('.', $key);

        // Find the column in the database and update it
        $column = ExecutivePlanColumn::find($this->columns[$index]['id']);
        if ($column) {
            $column->$field = $value;
            $column->save();
        }

        $this->dispatch('refreshTableCells', true)->to('executive-plan.table');

    }

    public function deleteColumn($id)
    {
        $column = ExecutivePlanColumn::whereId($id)->with('cells')->first(['id']);
    
        if($column) {
            $column->cells()->delete();
            $column->delete();
        }

        $this->dispatch('refreshTableCells', true)->to('executive-plan.table');

    }

    public function orderUp($id)
    {
        $column = ExecutivePlanColumn::find($id);
    
        if (!$column) {
            return;
        }
    
        // Find the previous column in order
        $prevColumn = ExecutivePlanColumn::where('user_id', $column->user_id)
            ->where('month', $column->month)
            ->where('year', $column->year)
            ->where('order', '<', $column->order)
            ->orderByDesc('order')
            ->first();
    
        if ($prevColumn) {
            // Swap order values
            $currentOrder = $column->order;
            $column->order = $prevColumn->order;
            $prevColumn->order = $currentOrder;
    
            $column->save();
            $prevColumn->save();
        }

        $this->dispatch('refreshTableCells', true)->to('executive-plan.table');


    }
    
    
    public function orderDown($id)
    {
        $column = ExecutivePlanColumn::find($id);
    
        if (!$column) {
            return;
        }
    
        // Find the next column in order
        $nextColumn = ExecutivePlanColumn::where('user_id', $column->user_id)
            ->where('month', $column->month)
            ->where('year', $column->year)
            ->where('order', '>', $column->order)
            ->orderBy('order')
            ->first();
    
        if ($nextColumn) {
            // Swap order values
            $currentOrder = $column->order;
            $column->order = $nextColumn->order;
            $nextColumn->order = $currentOrder;
    
            $column->save();
            $nextColumn->save();
        }


        $this->dispatch('refreshTableCells', true)->to('executive-plan.table');

    }
    

    public function render()
    {
        return view('livewire.executive-plan.table-head', [
            'showEditControls' => $this->showEditControls,
        ]);
    }
}
