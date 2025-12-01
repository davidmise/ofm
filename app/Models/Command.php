<?php

namespace App\Models;

use App\Models\Truck;
use App\Models\GpsDevice;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Command extends Model
{
    protected $fillable = [
        'truck_id',
        'device_id',
        'sim_id',
        'user_id',
        'command_type',
        'payload',
        'status',
        'response',
        'sent_at',
        'executed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'sent_at' => 'datetime',
        'executed_at' => 'datetime',
    ];

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function device()
    {
        return $this->belongsTo(GpsDevice::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
