<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    public function asset(){
        return $this->belongsTo(CurrentAsset::class, 'payment_method');
    }

    public function branch()
    {
        return $this->belongsTo(Admins::class, 'branch_id', 'id');
    }
}
