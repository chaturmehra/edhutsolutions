<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'name', 'logo', 'sort_order', 'status',
    ];
}
