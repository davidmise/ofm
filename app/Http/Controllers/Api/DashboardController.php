<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Command;
use App\Models\Driver;
use App\Models\GpsDevice;
use App\Models\SimCard;
use App\Models\Truck;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    /**
     * Return aggregated metrics and lightweight collections for the dashboard.
     */
    public function summary(): JsonResponse
    {
        $statusAggregates = Truck::selectRaw(
            "SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count, " .
            "SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maintenance_count, " .
            "SUM(CASE WHEN status = 'inactive' THEN 1 ELSE 0 END) as inactive_count"
        )->first();

        $stats = [
            'totalTrucks' => Truck::count(),
            'activeDrivers' => Driver::where('status', 'active')->count(),
            'simCards' => SimCard::count(),
            'gpsDevices' => GpsDevice::count(),
            'trucksInOperation' => (int) ($statusAggregates->active_count ?? 0),
            'trucksInMaintenance' => (int) ($statusAggregates->maintenance_count ?? 0),
            'trucksInactive' => (int) ($statusAggregates->inactive_count ?? 0),
            'commandsSent' => Command::whereBetween('created_at', [Carbon::today(), Carbon::tomorrow()])->count(),
            'alerts' => Command::where('status', 'pending')->count(),
        ];

        $recentTrucks = Truck::with(['driver:id,name'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get(['id', 'plate_number', 'status', 'client', 'destination', 'driver_id']);

        $topDrivers = Driver::select('id', 'name', 'performance_score', 'status')
            ->orderByDesc('performance_score')
            ->limit(5)
            ->get();

        return response()->json([
            'stats' => $stats,
            'recentTrucks' => $recentTrucks,
            'topDrivers' => $topDrivers,
        ]);
    }
}
