<?php

namespace App\Models;

use App\Models\Concerns\HasPublishStatus;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasPublishStatus;

    protected $fillable = ['code', 'name', 'status'];
}
