<?php

namespace App\Livewire\ExecutivePlan;

use App\Models\ExecutivePlanCell;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;


class TableCell extends Component
{
    use WithFileUploads;

    protected $listeners = ['refresh' => '$refresh'];

    public $cell;
    public $column;
    public $date;
    public $user;
    public $disabled;
    public $isEditable;
    public $value;
    public $suggestions = [];
    public $cellData = [];
    public $showEditControls = false;


    public function openCellModal()
    {
        $this->dispatch('openCellModal', date: $this->date);
    }
    public function updatedValue()
    {
        // Check if operation is allowed (prevents race conditions)
        if (!$this->canPerformCellOperation($this->date, $this->column->id, $this->cell?->id)) {
            return;
        }

        $this->cell = ExecutivePlanCell::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'date' => $this->date,
                'executive_plan_column_id' => $this->column->id,
            ],
            [
                'value' => $this->value,
            ]
        );

        $this->cellData[$this->cell->id] = $this->value;
        $this->dispatch('refreshTableCells')->to('executive-plan.table');
    }
    
    public function fetchSuggestions($value)
    {
        $this->updateSuggestions($value);
    }

    private function updateSuggestions($value)
    {
        $this->suggestions = ExecutivePlanCell::where('value', 'like', "%{$value}%")
            ->distinct()
            ->pluck('value')
            ->take(5)
            ->toArray();
    }

    /**
     * Check if a cell already exists for the given parameters
     * 
     * @param string $date
     * @param int $columnId
     * @param int|null $excludeCellId Cell ID to exclude from check (for updates)
     * @return ExecutivePlanCell|null
     */
    private function getExistingCell($date, $columnId, $excludeCellId = null)
    {
        $query = ExecutivePlanCell::where([
            'user_id' => $this->user->id,
            'date' => $date,
            'executive_plan_column_id' => $columnId,
        ]);

        if ($excludeCellId) {
            $query->where('id', '!=', $excludeCellId);
        }

        return $query->first();
    }

    /**
     * Check if a cell operation should be allowed (prevents race conditions)
     * 
     * @param string $date
     * @param int $columnId
     * @param int|null $excludeCellId Cell ID to exclude from check
     * @return bool
     */
    private function canPerformCellOperation($date, $columnId, $excludeCellId = null)
    {
        $existingCell = $this->getExistingCell($date, $columnId, $excludeCellId);
        return $existingCell === null;
    }
    public function mount($cell, $column, $date, $isEditable, $showEditControls = false)
    {
        $this->column = $column;
        $this->cell = $cell;
        $this->date = $date;
        $this->isEditable = $isEditable;
        $this->showEditControls = $showEditControls;
        $this->disabled = $isEditable ? '' : 'disabled';
        $this->user = auth()->user();

        if ($cell) {
            $this->cellData[$cell->id] = $cell->value;
        } else {
            // Initialize for new cell
            $this->cellData['new'] = '';
        }
    }



    public function deleteCell($cellId)
    {
        // Find the cell
        $cell = ExecutivePlanCell::find($cellId);

        if ($cell) {

            // Delete the cell
            $cell->delete();

            $this->dispatch('refreshTableCells')->to('executive-plan.table');
        }
    }

    public function updatedCellData($value, $cellId)
    {
        $this->cellData[$cellId] = $value;
    }

    public function saveNewCellData($value, $date, $column)
    {
        if ($value == '') {
            return;
        }

        // Check if operation is allowed (prevents race conditions)
        if (!$this->canPerformCellOperation($date, $column)) {
            return;
        }

        $this->cell = ExecutivePlanCell::create(
            [
                'user_id' => $this->user->id,
                'date' => $date,
                'executive_plan_column_id' => $column,
                'value' => $value,
            ]
        );

        $this->cellData[$this->cell->id] = $value;
        $this->dispatch('refreshTableCells')->to('executive-plan.table');
    }
    public function saveCellData($cellId)
    {
        // Check if operation is allowed (prevents race conditions)
        if (!$this->canPerformCellOperation($this->date, $this->column->id, $this->cell?->id)) {
            return;
        }

        $cell = ExecutivePlanCell::updateOrCreate(
            [
                'user_id' => $this->user->id,
                'date' => $this->date,
                'executive_plan_column_id' => $this->column->id,
            ],
            [
                'value' => $this->cellData[$cellId],
            ]
        );

        $this->cell = $cell; 
        $this->dispatch('refreshTableCells')->to('executive-plan.table');
    }


    public function render()
    {
        return view('livewire.executive-plan.table-cell', [
            'showEditControls' => $this->showEditControls,
        ]);
    }
}
