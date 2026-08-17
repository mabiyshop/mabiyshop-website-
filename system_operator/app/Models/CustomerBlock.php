<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerBlock extends Model
{
    protected $table = 'customer_blocks';

    protected $fillable = [
        'user_id',
        'phone',
        'ip_address',
        'status',
        'reason',
        'blocked_by',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
