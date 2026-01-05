<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Activo;
use App\Models\Asignacion;
use App\Models\Mantenimiento;
use App\Models\Ubicacion;
use App\Models\SolicitudCompra; // If exists, otherwise Procurement
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Asset Statistics
        $totalAssets = Activo::count();
        
        // Group by Status Name
        $assetsByStatus = Activo::join('estados_activo', 'activos.estado_activo_id', '=', 'estados_activo.id')
            ->select('estados_activo.nombre as status', DB::raw('count(*) as count'))
            ->groupBy('estados_activo.nombre')
            ->pluck('count', 'status');

        $assignedCount = $assetsByStatus['Asignado'] ?? 0;
        $maintenanceCount = ($assetsByStatus['En Mantenimiento'] ?? 0) + ($assetsByStatus['En Reparación'] ?? 0);

        // 2. Financial Stats (Total Value)
        $totalValue = Activo::sum('valor_adquisicion');

        // 3. Recent Activities (Assignments)
        $recentAssignments = Asignacion::with(['usuario', 'activo.tipoActivo'])
            ->where('es_actual', true) // Filter? Or just latest regardless of current status (traceability)
            ->latest('fecha_asignacion')
            ->take(5)
            ->get();
        
        // 4. Maintenances in progress (detailed count if needed, otherwise use status)
        // Let's get actual open maintenances count from table to be precise
        $activeMaintenances = Mantenimiento::where('estado_mantenimiento_id', 1)->count(); // 1 = En Proceso

        // 5. Assets by City (for Charts)
        // We join ubicaciones -> ciudades
        $assetsByCity = Activo::join('ubicaciones', 'activos.ubicacion_id', '=', 'ubicaciones.id')
            ->join('ciudades', 'ubicaciones.ciudad_id', '=', 'ciudades.id')
            ->select('ciudades.nombre as name', DB::raw('count(*) as value'))
            ->groupBy('ciudades.nombre')
            ->get();

        // 6. Assets by Type (Donut Chart potential)
        $assetsByType = Activo::join('tipos_activo', 'activos.tipo_activo_id', '=', 'tipos_activo.id')
            ->select('tipos_activo.nombre as name', DB::raw('count(*) as value'))
            ->groupBy('tipos_activo.nombre')
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => [
                'totalAssets' => $totalAssets,
                'totalValue' => $totalValue,
                'statusCounts' => $assetsByStatus,
                'assignedCount' => $assignedCount,
                'maintenanceCount' => $activeMaintenances,
            ],
            'charts' => [
                'assetsByCity' => $assetsByCity,
                'assetsByType' => $assetsByType
            ],
            'recentAssignments' => $recentAssignments,
        ]);
    }
}
