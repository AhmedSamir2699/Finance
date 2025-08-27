<?php

namespace App\Livewire\Budgets;

use App\Models\Budget;
use App\Models\FinanceItem;
use Livewire\Component;

class Cards extends Component
{
    public function render()
    {
        $totalBudget = 0;

        $items = Budget::get();
        $totalBudget = $items->sum('amount');
       

        return view('livewire.budgets.cards', [
            'totalBudget' => $totalBudget,
        ]);
    }

    private function sumSubtree(FinanceItem $node): float
    {
        $sum = (float) ($node->amount ?? 0);

        // ensure relation loaded (defensive)
        if (!$node->relationLoaded('childrenRecursive')) {
            $node->load('childrenRecursive');
        }

        foreach ($node->childrenRecursive as $child) {
            $sum += $this->sumSubtree($child);
        }

        return $sum;
    }
}
