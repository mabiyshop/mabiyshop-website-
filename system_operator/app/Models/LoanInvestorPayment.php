<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanInvestorPayment extends Model
{
    use HasFactory;

    public function asset(){
        return $this->belongsTo(CurrentAsset::class, 'payment_method');
    }

    public function loans(){
        return $this->belongsTo(Loan::class, 'loan');
    }

    public function investors(){
        return $this->belongsTo(Investor::class, 'investor');
    }
}
