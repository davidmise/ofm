<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Command;

class CommandController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Command::with(['truck', 'device', 'user'])->latest()->paginate(20);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'truck_id' => 'required|exists:trucks,id',
            'device_id' => 'nullable|exists:gps_devices,id',
            'sim_id' => 'nullable|exists:sim_cards,id',
            'command_type' => 'required|string',
            'payload' => 'nullable|array',
        ]);

        $command = new Command($validated);
        $command->user_id = $request->user()->id; // Assign current user
        $command->status = 'pending';
        $command->sent_at = now();
        $command->save();

        // Mock sending command logic here
        // In a real app, this would dispatch a job or call an external API
        $command->status = 'sent';
        $command->save();

        return response()->json($command, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return Command::with(['truck', 'device', 'user'])->findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Commands are usually immutable logs, but maybe update status?
        $command = Command::findOrFail($id);
        
        $validated = $request->validate([
            'status' => 'required|string',
            'response' => 'nullable|string',
            'executed_at' => 'nullable|date',
        ]);

        $command->update($validated);

        return response()->json($command);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Optional: Delete command log
        $command = Command::findOrFail($id);
        $command->delete();

        return response()->json(null, 204);
    }
}
