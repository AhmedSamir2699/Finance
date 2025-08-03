<?php

namespace App\Livewire\FinanceItems;

use App\Models\FinanceItem;
use Livewire\Component;

class Cards extends Component
{
    public function render()
    {
        $totalBudget = FinanceItem::totalLeafAmount();
        return view('livewire.finance-items.cards', [
            'totalBudget' => $totalBudget,
        ]);
    }
}
