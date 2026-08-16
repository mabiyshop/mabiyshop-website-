<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;
    protected $table = "expenses";

    public function expenses_category(){
        return $this->belongsTo(ExpenseCategory::class, 'expense_category');
    }

    public function payment_methods(){
        return $this->belongsTo(CurrentAsset::class, 'payment_method');
    }

    public function shop(){
        return $this->hasOne(ShopInfo::class , 'seller_id', 'branch_id');
    }
}
