<?php

namespace App\Livewire\FinanceItems;

use Livewire\Component;
use App\Models\FinanceItem;
class DragDrop extends Component
{
    public $items;
    public $totalBudget;
    public $allItems;
    public function mount()
    {
        $this->items = FinanceItem::with('children')->whereNull('parent_id')->get();
        $this->totalBudget = FinanceItem::totalLeafAmount();
        $this->allItems = FinanceItem::all();
    }

    public function render()
    {
        return view('livewire.finance-items.drag-drop');
    }
}
