<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\GpsDevice;

class GpsDeviceController extends Controller
{
    /**
     * Display a listing of GPS devices with optional search
     */
    public function index(Request $request)
    {
        $query = GpsDevice::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('device_id', 'like', "%{$search}%")
                  ->orWhere('imei', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 10));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'model' => 'required|string',
            'imei' => 'required|string|unique:gps_devices',
            'serial_number' => 'nullable|string',
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        $gpsDevice = GpsDevice::create($validated);

        return response()->json($gpsDevice, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return GpsDevice::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $gpsDevice = GpsDevice::findOrFail($id);

        $validated = $request->validate([
            'model' => 'required|string',
            'imei' => 'required|string|unique:gps_devices,imei,' . $gpsDevice->id,
            'serial_number' => 'nullable|string',
            'status' => 'required|in:active,maintenance,inactive',
        ]);

        $gpsDevice->update($validated);

        return response()->json($gpsDevice);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $gpsDevice = GpsDevice::findOrFail($id);
        $gpsDevice->delete();

        return response()->json(null, 204);
    }
}
