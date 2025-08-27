<?php

namespace App\Livewire\Budgets;

use App\Models\Budget;
use App\Models\FinanceItem;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    /** Current parent FINANCE ITEM context */
    public ?int $parentFiId = null;
    public ?FinanceItem $parentFiModel = null;

    /**
     * Works for routes like:
     *   /budgets               → top-level finance items
     *   /budgets/{financeItem} → children of that finance item
     *
     * Make sure your route parameter is named {financeItem} and bound to FinanceItem.
     */
    public function mount(?FinanceItem $financeItem = null, ?int $parentFiId = null): void
    {
        if ($financeItem) {
            $this->parentFiId = $financeItem->id;
            $this->parentFiModel = $financeItem;
        } elseif ($parentFiId) {
            $this->parentFiId = $parentFiId;
            $this->parentFiModel = FinanceItem::find($parentFiId);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    /** In-page navigation (no URL change) – navigate into a FINANCE ITEM’s children */
    public function showChildrenFi(int $fiId): void
    {
        $this->parentFiId = $fiId;
        $this->parentFiModel = FinanceItem::findOrFail($fiId);
        $this->resetPage();
    }

    public function clearParent(): void
    {
        $this->parentFiId = null;
        $this->parentFiModel = null;
        $this->resetPage();
    }
    public function goBackOneLevel(): void
    {
        if (!$this->parentFiId)
            return;

        // Ensure we have the current node loaded
        $current = $this->parentFiModel ?: FinanceItem::with('parent')->find($this->parentFiId);

        if ($current && $current->parent_id) {
            $this->parentFiId = (int) $current->parent_id;
            $this->parentFiModel = FinanceItem::find($this->parentFiId);
        } else {
            // already at root → go to top
            $this->parentFiId = null;
            $this->parentFiModel = null;
        }

        $this->resetPage();
    }

    public function render()
    {
        $query = Budget::query()
            ->with([
                'financeItem.parent',   // so we can show parent name
                'financeItem.children', // to know if it has children → clickable
            ])
            ->when($this->parentFiId, function ($q) {
                $q->whereHas('financeItem', fn($fi) => $fi->where('parent_id', $this->parentFiId));
            }, function ($q) {
                $q->whereHas('financeItem', fn($fi) => $fi->whereNull('parent_id'));
            })
            ->when(filled($this->search), function ($q) {
                // search against the finance item name
                $like = '%' . $this->search . '%';
                $q->whereHas('financeItem', fn($fi) => $fi->where('name', 'like', $like));
            })
            ->orderByDesc('id');

        $items = $query->paginate($this->perPage);


        $childrenTotals = [];
        $branchTotals = [];

        foreach ($items as $budget) {
            $fi = $budget->financeItem; // guard
            if (!$fi) {
                $childrenTotals[$budget->id] = 0;
                $branchTotals[$budget->id] = 0;
                continue;
            }

            // Total for self + descendants:
            $branch = $fi->branchBudgetTotal();

            // Children-only = branch - self.amount
            $self = (float) ($fi->amount ?? 0);
            $childrenTotals[$budget->id] = max(0, $branch - $self);
            $branchTotals[$budget->id] = $branch;
        }

        return view('livewire.budgets.index', [
            'items' => $items,
            'childrenTotals' => $childrenTotals,
            'branchTotals' => $branchTotals,
        ]);
    }
}
