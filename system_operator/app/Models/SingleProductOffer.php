<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SingleProductOffer extends Model
{
    use HasFactory;
    protected $table = "single_product_offers";

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }
}
