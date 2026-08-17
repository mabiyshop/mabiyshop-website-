<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;


class PartialOrderTransaction extends Model
{

    protected $table = 'partial_order_transactions';

    public function statuses()
    {
        return $this->hasOne(Status::class, 'id', 'status');
    }

}