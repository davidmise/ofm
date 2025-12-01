<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SimAssignment;

class SimAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return SimAssignment::with(['sim', 'device', 'truck'])->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'sim_id' => 'required|exists:sim_cards,id',
            'device_id' => 'nullable|exists:gps_devices,id',
            'truck_id' => 'nullable|exists:trucks,id',
            'assigned_at' => 'required|date',
            'ended_at' => 'nullable|date|after:assigned_at',
            'status' => 'required|in:active,history',
        ]);

        $assignment = SimAssignment::create($validated);

        return response()->json($assignment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return SimAssignment::with(['sim', 'device', 'truck'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $assignment = SimAssignment::findOrFail($id);

        $validated = $request->validate([
            'sim_id' => 'required|exists:sim_cards,id',
            'device_id' => 'nullable|exists:gps_devices,id',
            'truck_id' => 'nullable|exists:trucks,id',
            'assigned_at' => 'required|date',
            'ended_at' => 'nullable|date|after:assigned_at',
            'status' => 'required|in:active,history',
        ]);

        $assignment->update($validated);

        return response()->json($assignment);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $assignment = SimAssignment::findOrFail($id);
        $assignment->delete();

        return response()->json(null, 204);
    }
}
