<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GpsDevice extends Model
{
    protected $fillable = [
        'model',
        'imei',
        'serial_number',
        'status',
    ];
}
