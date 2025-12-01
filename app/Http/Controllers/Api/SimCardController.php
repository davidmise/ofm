<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\SimCard;

class SimCardController extends Controller
{
    /**
     * Display a listing of SIM cards with optional search
     */
    public function index(Request $request)
    {
        $query = SimCard::query();

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('phone_number', 'like', "%{$search}%")
                  ->orWhere('provider', 'like', "%{$search}%")
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
            'iccid' => 'required|string|unique:sim_cards',
            'phone_number' => 'nullable|string',
            'network_provider' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,expiring',
            'sim_type' => 'required|in:data,voice,mixed',
        ]);

        $simCard = SimCard::create($validated);

        return response()->json($simCard, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return SimCard::findOrFail($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $simCard = SimCard::findOrFail($id);

        $validated = $request->validate([
            'iccid' => 'required|string|unique:sim_cards,iccid,' . $simCard->id,
            'phone_number' => 'nullable|string',
            'network_provider' => 'nullable|string',
            'expiry_date' => 'nullable|date',
            'status' => 'required|in:active,inactive,expiring',
            'sim_type' => 'required|in:data,voice,mixed',
        ]);

        $simCard->update($validated);

        return response()->json($simCard);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $simCard = SimCard::findOrFail($id);
        $simCard->delete();

        return response()->json(null, 204);
    }
}
