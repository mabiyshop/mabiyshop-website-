<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentAsset extends Model
{
    use HasFactory;
    protected $table = "current_asset";

    public function branch()
    {
        return $this->belongsTo(Admins::class, 'branch_id', 'id');
    }
}
