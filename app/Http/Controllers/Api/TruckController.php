<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Truck;

class TruckController extends Controller
{
    /**
     * Display a listing of trucks with optional search and pagination
     */
    public function index(Request $request)
    {
        $query = Truck::with(['driver', 'simCard', 'gpsDevice']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%")
                  ->orWhere('owner', 'like', "%{$search}%")
                  ->orWhereHas('driver', function ($driverQuery) use ($search) {
                      $driverQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $trucks = $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 10));

        return response()->json($trucks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|unique:trucks',
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer',
            'status' => 'required|in:active,maintenance,inactive',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $truck = Truck::create($validated);

        return response()->json($truck, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Truck::with('driver')->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $truck = Truck::findOrFail($id);

        $validated = $request->validate([
            'plate_number' => 'required|unique:trucks,plate_number,' . $truck->id,
            'brand' => 'nullable|string',
            'model' => 'nullable|string',
            'year' => 'nullable|integer',
            'status' => 'required|in:active,maintenance,inactive',
            'driver_id' => 'nullable|exists:drivers,id',
        ]);

        $truck->update($validated);

        return response()->json($truck);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $truck = Truck::findOrFail($id);
        $truck->delete();

        return response()->json(null, 204);
    }
}
