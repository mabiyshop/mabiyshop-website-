<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasesReturn extends Model
{
    use HasFactory;

    protected $table = "purchases_return";

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function returnProducts(){
    	return $this->hasMany(ProductPurchase::class, 'invoice_no', 'reference_no');
    }
}
