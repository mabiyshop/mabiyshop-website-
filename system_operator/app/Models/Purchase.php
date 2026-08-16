<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function shop(){
        return $this->hasOne(ShopInfo::class , 'seller_id', 'shop_id');
    }

    public function purchaseProducts(){
    	return $this->hasMany(ProductPurchase::class);
    }
}
