<?php

namespace App\Livewire\Incomes;

use App\Models\Income;
use Livewire\Component;

class Cards extends Component
{
    public function render()
    {
        $totalincomes = Income::totalLeafAmount();
        return view('livewire.incomes.cards', [
            'totalincomes' => $totalincomes,
        ]);
    }
}
