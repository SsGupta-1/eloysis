<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Periods extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'sort_order',
        'status'
    ];
}
