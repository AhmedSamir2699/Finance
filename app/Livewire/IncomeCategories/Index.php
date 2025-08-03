<?php

namespace App\Livewire\IncomeCategories;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\IncomeCategory;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $incomeCategories = IncomeCategory::query()
            ->when($this->search, fn($query) => $query->where('name', 'like', '%' . $this->search . '%'))
            ->orderBy('id', 'desc')
            ->paginate($this->perPage);

        return view('livewire.income-categories.index', compact('incomeCategories'));
    }
}