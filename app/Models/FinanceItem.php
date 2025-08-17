<?php
namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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


}
