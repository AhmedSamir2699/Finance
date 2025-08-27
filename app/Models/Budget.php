<?php
namespace App\Models;

use App\Traits\Auditable;
use DateTimeInterface;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
class Budget extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'budgets';
    protected $fillable = [
        'finance_item_id',
        'goal',
    ];

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    public function financeItem()
    {
        return $this->belongsTo(\App\Models\FinanceItem::class, 'finance_item_id');
    }
    
}
