<?php
namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
class FinanceItem extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'finance_items';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'name',
        'amount',
        'parent_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(FinanceItem::class, 'parent_id');
    }

    public function getLeafAmountsSum()
    {
        if ($this->children->isEmpty()) {
            return $this->amount;
        }

        return $this->children->sum(function ($child) {
            return $child->getLeafAmountsSum();
        });
    }

    public function getFullPathAttribute()
    {
        $path = [$this->name];
        $item = $this;

        while ($item->parent) {
            $item = $item->parent;
            array_unshift($path, $item->name); // add to beginning
        }

        return implode(' → ', $path);
    }

    public static function totalLeafAmount()
    {
        return self::with('children')
            ->get()
            ->filter(fn($item) => $item->children->isEmpty()) // leaf only
            ->sum('amount');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
    /** Return all descendant IDs including self (MySQL 8+/Postgres via CTE; fallback to PHP) */
    public function descendantIdsIncludingSelf(): array
    {
        $rootId = (int) $this->id;

        // Try a recursive CTE (supported on MySQL 8+/Postgres)
        try {
            $rows = DB::query()
                ->withExpression('subtree', function ($q) use ($rootId) {
                    $q->from('finance_items')
                        ->select('id', 'parent_id')
                        ->where('id', $rootId)
                        ->unionAll(
                            DB::table('finance_items as fi')
                                ->select('fi.id', 'fi.parent_id')
                                ->join('subtree', 'fi.parent_id', '=', 'subtree.id')
                        );
                })
                ->from('subtree')
                ->select('id')
                ->get();
            return $rows->pluck('id')->map(fn($v) => (int) $v)->all();
        } catch (\Throwable $e) {
            // Fallback: build subtree in PHP from one shot of all items
            static $all = null, $byParent = null;
            if ($all === null) {
                $all = self::select('id', 'parent_id')->get();
                $byParent = [];
                foreach ($all as $it) {
                    $byParent[$it->parent_id ?? 0][] = (int) $it->id;
                }
            }
            $out = [];
            $stack = [$rootId];
            while ($stack) {
                $id = array_pop($stack);
                $out[] = $id;
                foreach ($byParent[$id] ?? [] as $childId) {
                    $stack[] = $childId;
                }
            }
            return $out;
        }
    }


    /** Optional: total RUNNING BALANCE of the whole branch (incomes - expenses) */
    public function branchRunningTotal(): float
    {
        $ids = $this->descendantIdsIncludingSelf();
        return (float) self::whereIn('id', $ids)->sum('running_total');
    }


    /** Sum of EXPENSES for this item + all descendants */
    public function branchExpenseTotal(): float
    {
        $ids = $this->descendantIdsIncludingSelf();
        return (float) \App\Models\Expense::whereIn('finance_item_id', $ids)->sum('amount');
    }

    /** Sum of this item’s configured budget “amount” + all descendants’ “amount” */
    public function branchBudgetTotal(): float
    {
        $ids = $this->descendantIdsIncludingSelf();
        return (float) self::whereIn('id', $ids)->sum('amount'); // uses finance_items.amount
    }

    /** Remaining branch budget = budget - expenses */
    public function branchRemainingBudget(): float
    {
        return $this->branchBudgetTotal() - $this->branchExpenseTotal();
    }

     public function budgets()
    {
        return $this->hasMany(Budget::class, 'finance_item_id');
    }
}
