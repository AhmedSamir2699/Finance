<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use Livewire\Component;

class Cards extends Component
{
    public function render()
    {
        $totalexpenses = Expense::totalLeafAmount();
        return view('livewire.expenses.cards', [
            'totalexpenses' => $totalexpenses,
        ]);
    }
}
