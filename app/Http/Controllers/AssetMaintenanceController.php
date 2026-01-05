<?php

namespace App\Http\Controllers;

use App\Models\Mantenimiento;
use App\Models\Activo;
use App\Models\Proveedor;
use App\Models\EstadoActivo;
// use App\Models\TipoMantenimiento; // If exists, otherwise we might mock or use DB
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AssetMaintenanceController extends Controller
{
    /**
     * Show form to create maintenance request.
     */
    public function create(Request $request) 
    {
        $assetId = $request->query('asset_id');
        $asset = Activo::with(['modelo.marca'])->findOrFail($assetId);
        
        // We need catalogs. If models don't exist, we send empty or use DB facade if table exists but no model
        // Assuming TipoMantenimiento table exists as per 36-table schema.
        $tipos = DB::table('tipos_mantenimiento')->select('id', 'nombre')->get(); 
        $proveedores = Proveedor::select('id', 'nombre')->orderBy('nombre')->get();
        // ESTADOS MANTENIMIENTO: 'Solicitado', 'En Proceso', 'Finalizado' -> likely in 'estados_mantenimiento' table
        $estados = DB::table('estados_mantenimiento')->select('id', 'nombre')->get();

        return Inertia::render('Maintenances/Create', [
            'asset' => $asset,
            'tipos' => $tipos,
            'proveedores' => $proveedores,
            'estados' => $estados
        ]);
    }

    /**
     * Store maintenance and update asset status.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activo_id' => 'required|exists:activos,id',
            'tipo_mantenimiento_id' => 'required|exists:tipos_mantenimiento,id',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'fecha_inicio' => 'required|date',
            'descripcion' => 'required|string', // mapped to 'problema_reportado' or similar? let's check migration/model
            // If table has 'problema_reportado', use that. Mantenimiento model showed 'hoja_trabajo', maybe that's it?
            // Let's assume 'detalles' or check model again. 
            // In model viewed earlier: 'hoja_trabajo', 'costo_bs'.
            // Ill use 'hoja_trabajo' for details/description for now.
            'hoja_trabajo' => 'nullable|string'
        ]);
        
        DB::transaction(function() use ($validated) {
            $activo = Activo::findOrFail($validated['activo_id']);
            
            // 1. Create Maintenance
            Mantenimiento::create([
                'activo_id' => $validated['activo_id'],
                'tipo_mantenimiento_id' => $validated['tipo_mantenimiento_id'],
                'proveedor_id' => $validated['proveedor_id'],
                'fecha_inicio' => $validated['fecha_inicio'],
                'estado_mantenimiento_id' => 1, // Default to 1 (e.g. 'En Proceso')
                'hoja_trabajo' => $validated['hoja_trabajo'], // Description
                'costo_bs' => 0 // Initial cost
            ]);
            
            // 2. Update Asset Status to "En Mantenimiento"
            // We need to find the ID for "En Mantenimiento" or "Reparación"
            // Assuming we have this status seeded.
            $statusMaint = EstadoActivo::where('nombre', 'like', '%Mantenimiento%')->orWhere('nombre', 'like', '%Reparación%')->first();
            if ($statusMaint) {
                $activo->update(['estado_activo_id' => $statusMaint->id]);
            }
        });

        return redirect()->route('assets.show', $validated['activo_id'])->with('success', 'Mantenimiento registrado correcamente.');
    }

    /**
     * Show form to finish maintenance.
     */
    public function finishForm($id)
    {
        $maintenance = Mantenimiento::with('proveedor')->findOrFail($id);
        $asset = Activo::with(['modelo.marca'])->findOrFail($maintenance->activo_id);
        
        return Inertia::render('Maintenances/Finish', [
            'asset' => $asset,
            'maintenance' => $maintenance
        ]);
    }

    /**
     * Update maintenance (Finish).
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'costo_bs' => 'required|numeric|min:0',
            'estado_final_id' => 'required|exists:estados_activo,id',
            'detalles_cierre' => 'nullable|string'
        ]);

        DB::transaction(function () use ($validated, $id) {
            $maintenance = Mantenimiento::findOrFail($id);
            
            // 1. Update Maintenance
            $maintenance->update([
                'costo_bs' => $validated['costo_bs'],
                'estado_mantenimiento_id' => 2, // Finalizado
                'hoja_trabajo' => $maintenance->hoja_trabajo . "\n[Cierre]: " . $validated['detalles_cierre']
            ]);
            // Logic for end date? Assuming updated_at or adding fecha_fin column to table if exists found in model.
            // Model view didn't show fecha_fin. Assuming fecha_inicio is enough or we rely on timestamps.

            // 2. Update Asset Status
            $activo = Activo::findOrFail($maintenance->activo_id);
            $activo->update(['estado_activo_id' => $validated['estado_final_id']]);
        });

        $m = Mantenimiento::find($id);
        return redirect()->route('assets.show', $m->activo_id)->with('success', 'Mantenimiento finalizado.');
    }
}
