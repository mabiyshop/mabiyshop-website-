<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaConversionEvent extends Model
{
    protected $fillable = [
        'order_id',
        'event_name',
        'event_id',
        'status',
        'attempts',
        'last_error',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];
}
