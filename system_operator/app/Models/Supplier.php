<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    public function purchases(){
        return $this->hasMany(Purchase::class);
    }

    public function payments(){
        return $this->hasMany(SupplierPayment::class);
    }

    public function return(){
        return $this->hasMany(PurchasesReturn::class);
    }
}
