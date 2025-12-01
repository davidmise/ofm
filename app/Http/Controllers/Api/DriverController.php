<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Driver;

class DriverController extends Controller
{
    /**
     * Display a listing of drivers with optional search
     */
    public function index(Request $request)
    {
        $query = Driver::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('license_number', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
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
            'name' => 'required|string',
            'license_number' => 'nullable|string|unique:drivers',
            'phone' => 'nullable|string',
            'status' => 'required|in:active,on_leave,inactive',
            'performance_score' => 'numeric|min:0|max:100',
        ]);

        $driver = Driver::create($validated);

        return response()->json($driver, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Driver::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $driver = Driver::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string',
            'license_number' => 'nullable|string|unique:drivers,license_number,' . $driver->id,
            'phone' => 'nullable|string',
            'status' => 'required|in:active,on_leave,inactive',
            'performance_score' => 'numeric|min:0|max:100',
        ]);

        $driver->update($validated);

        return response()->json($driver);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $driver = Driver::findOrFail($id);
        $driver->delete();

        return response()->json(null, 204);
    }
}
