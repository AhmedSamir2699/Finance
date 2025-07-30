<?php

namespace App\Livewire\Departments\Edit;

use Livewire\Component;

class ExecutivePlanColumns extends Component
{
    public $department;
    public $columns;
    public $newColumnName = '';

    public function mount($department)
    {
        $this->department = $department;
        
        $this->columns = $department->executivePlanColumns()->orderBy('order')->get();
    }

    public function addColumn()
    {
        $name = $this->newColumnName;
        $this->department->executivePlanColumns()->create([
            'name' => $name,
            'order' => $this->columns->count() + 1
        ]);
        $this->newColumnName = '';
        $this->columns = $this->department->executivePlanColumns()->orderBy('order')->get();
    }

    public function removeColumn($columnId)
    {
        $column = $this->columns->find($columnId);
        $column->delete();
        $this->columns = $this->department->executivePlanColumns()->orderBy('order')->get();
    }

    public function moveUp($columnId)
    {
        $column = $this->columns->find($columnId);
        $column->order = $column->order - 1;
        $column->save();

        $previousColumn = $this->columns->where('order', $column->order)->first();
        $previousColumn->order = $previousColumn->order + 1;
        $previousColumn->save();

        $this->columns = $this->columns->sortBy('order');
    }

    public function moveDown($columnId)
    {
        $column = $this->columns->find($columnId);
        $currentOrder = $column->order;
        $column->order = $currentOrder + 1;
        $column->save();

        $nextColumn = $this->columns->where('order', $currentOrder + 1)->last();
        $nextColumn->order = $currentOrder;
        $nextColumn->save();

        $this->columns = $this->columns->sortBy('order');
    }

    public function render()
    {
        return view('livewire.departments.edit.executive-plan-columns');
    }
}
