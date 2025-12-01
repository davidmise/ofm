<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SimCard extends Model
{
    protected $fillable = [
        'iccid',
        'phone_number',
        'network_provider',
        'expiry_date',
        'status',
        'sim_type',
    ];
}
