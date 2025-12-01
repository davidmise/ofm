<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Truck extends Model
{
    protected $fillable = [
        'plate_number',
        'brand',
        'model',
        'year',
        'status',
        'driver_id',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    /**
     * Get the active SIM assignment for this truck
     */
    public function activeSimAssignment()
    {
        return $this->hasOne(SimAssignment::class, 'truck_id')
            ->where('status', 'active')
            ->latest('assigned_at');
    }

    /**
     * Get the SIM card through active assignment
     */
    public function simCard()
    {
        return $this->hasOneThrough(
            SimCard::class,
            SimAssignment::class,
            'truck_id',      // Foreign key on SimAssignment
            'id',            // Foreign key on SimCard
            'id',            // Local key on Truck
            'sim_id'         // Local key on SimAssignment
        )->where('sim_assignments.status', 'active');
    }

    /**
     * Get the GPS device through active assignment
     */
    public function gpsDevice()
    {
        return $this->hasOneThrough(
            GpsDevice::class,
            SimAssignment::class,
            'truck_id',      // Foreign key on SimAssignment
            'id',            // Foreign key on GpsDevice
            'id',            // Local key on Truck
            'device_id'      // Local key on SimAssignment
        )->where('sim_assignments.status', 'active');
    }
}
