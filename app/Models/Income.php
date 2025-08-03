<?php

namespace App\Models;

use Carbon\Carbon;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Income extends Model
{
    use SoftDeletes, HasFactory;

    public $table = 'incomes';

    protected $dates = [
        'entry_date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $fillable = [
        'income_category_id',
        'entry_date',
        'amount',
        'description',
        'finance_item_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function income_category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    public function finance_item()
    {
        return $this->belongsTo(FinanceItem::class, 'finance_item_id');
    }

     public static function totalLeafAmount()
    {
        return self::get()->sum('amount');
    }
}
