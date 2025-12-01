<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Truck;

class PublicApiController extends Controller
{
    /**
     * Get all active trucks with their current location (mocked).
     */
    public function getTrucks()
    {
        $trucks = Truck::where('status', 'active')->with('driver')->get()->map(function ($truck) {
            return [
                'id' => $truck->id,
                'plate_number' => $truck->plate_number,
                'driver_name' => $truck->driver ? $truck->driver->name : null,
                'status' => $truck->status,
                'location' => [
                    'lat' => -6.369028 + (rand(-100, 100) / 1000),
                    'lng' => 34.888822 + (rand(-100, 100) / 1000),
                ],
                'last_updated' => now(),
            ];
        });

        return response()->json(['data' => $trucks]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
