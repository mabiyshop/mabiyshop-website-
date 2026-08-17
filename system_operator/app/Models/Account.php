<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $table = "accounts";

    public function asset(){
        return $this->belongsTo(CurrentAsset::class, 'payment_method');
    }

    public function branch()
    {
        return $this->belongsTo(Admins::class, 'branch_id', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'common_id', 'id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'common_id', 'id');
    }

    public function account(){
        return $this->belongsTo(CurrentAsset::class, 'common_id');
    }
}
