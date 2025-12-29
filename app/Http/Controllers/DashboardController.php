<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Asset;
use App\Models\Procurement;
use App\Models\AssetAssignment;
use App\Models\City;
use App\Models\Maintenance;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Asset Statistics
        $totalAssets = Asset::count();
        $assetsByStatus = Asset::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        // 2. Procurement Stats
        $pendingProcurements = Procurement::where('status', 'pending')->count();
        $myRequests = Procurement::where('requester_id', auth()->id())->latest()->take(5)->get();

        // 3. Recent Activities (Assignments)
        $recentAssignments = AssetAssignment::with(['user', 'asset.type'])
            ->latest('assigned_at')
            ->take(5)
            ->get();
        
        // 4. Maintenances in progress
        $activeMaintenances = Asset::whereIn('status', ['maintenance', 'repair'])->count();

        // 5. Assets by City (for National Supervisor)
        $assetsByCity = City::withCount('assets')->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalAssets' => $totalAssets,
                'statusCounts' => $assetsByStatus,
                'pendingProcurements' => $pendingProcurements,
                'activeMaintenances' => $activeMaintenances,
            ],
            'charts' => [
                'assetsByCity' => $assetsByCity->map(fn($c) => ['name' => $c->name, 'value' => $c->assets_count]),
            ],
            'recentAssignments' => $recentAssignments,
            'myRequests' => $myRequests,
        ]);
    }
}
