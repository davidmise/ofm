<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SimCard;
use App\Models\GpsDevice;
use App\Models\Truck;

class SimAssignment extends Model
{
    protected $fillable = [
        'sim_id',
        'device_id',
        'truck_id',
        'assigned_at',
        'ended_at',
        'status',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function sim()
    {
        return $this->belongsTo(SimCard::class, 'sim_id');
    }

    public function device()
    {
        return $this->belongsTo(GpsDevice::class, 'device_id');
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class, 'truck_id');
    }
}
