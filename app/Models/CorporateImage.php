<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CorporateImage extends Model
{
    use HasPublishStatus, SoftDeletes;

    protected $fillable = [
        'title', 'image', 'alt_text', 'sort_order', 'status',
    ];
}
