<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\Activo;
use App\Models\User;
use App\Models\EstadoActivo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class AssetAssignmentController extends Controller
{
    /**
     * Show form to assign an asset.
     */
    public function create(Request $request)
    {
        $assetId = $request->query('asset_id');
        $asset = null;
        if ($assetId) {
            $asset = Activo::with(['tipoActivo', 'modelo.marca', 'ubicacion.ciudad'])->findOrFail($assetId);
        }

        // Fetch Checklist Definitions (Security & Software)
        $checklistDefinitions = [];
        // Checklist definitions related to 'software' and 'security' categories were removed during database standardization.
        // Returning empty array to prevent crash. If checklist is needed, it should be reimplemented with new schema.
        $checklistDefinitions = [];

        return Inertia::render('AssetAssignments/Create', [
            'asset' => $asset,
            'users' => User::with('ubicacion')->orderBy('name')->get(),
            'checklistDefinitions' => $checklistDefinitions
        ]);
    }

    /**
     * Process the assignment.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'activo_id' => 'required|exists:activos,id',
            'usuario_id' => 'required|exists:users,id',
            'observaciones' => 'nullable|string',
            'ubicacion_destino_id' => 'nullable|exists:ubicaciones,id',
            'details' => 'nullable|array' // Checklist answers
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $activo = Activo::with(['tipoActivo', 'modelo.marca', 'atributos'])->findOrFail($validated['activo_id']);
                $usuario = User::with(['ubicacion.ciudad'])->findOrFail($validated['usuario_id']);
                
                // 1. Close any existing current assignment (just in case)
                Asignacion::where('activo_id', $activo->id)
                    ->where('es_actual', true)
                    ->update(['es_actual' => false, 'fecha_devolucion' => now(), 'observaciones' => 'Cierre automático por nueva asignación (Warning)']);

                // 2. Create Assignment
                $asignacion = Asignacion::create([
                    'activo_id' => $activo->id,
                    'usuario_id' => $usuario->id,
                    'fecha_asignacion' => now(),
                    'es_actual' => true,
                    'details' => $request->input('details'), // Save checklist answers
                    'observaciones' => $validated['observaciones']
                ]);

                // 3. Update Asset Status and Location
                $estadoAsignado = EstadoActivo::where('nombre', 'Asignado')->first(); // Ensure seed matches
                $activo->estado_activo_id = $estadoAsignado ? $estadoAsignado->id : 2; // Fallback ID
                if (!empty($validated['ubicacion_destino_id'])) {
                    $activo->ubicacion_id = $validated['ubicacion_destino_id'];
                } else {
                     // Default to user's location if available, else keep asset's
                     if ($usuario->ubicacion_id) $activo->ubicacion_id = $usuario->ubicacion_id;
                }
                $activo->save();

                // 4. Generate PDF
                $pdf = Pdf::loadView('pdf.acta_entrega', [
                    'asignacion' => $asignacion,
                    'activo' => $activo,
                    'usuario' => $usuario,
                    'fecha' => now()->format('d/m/Y'),
                    'observaciones' => $validated['observaciones'] ?? ''
                ]);

                $filename = 'acta_entrega_' . $asignacion->id . '_' . Str::random(8) . '.pdf';
                $path = 'actas/entrega/' . $filename;
                
                Storage::disk('public')->put($path, $pdf->output());

                // 5. Save Path
                $asignacion->acta_entrega_path = $path;
                $asignacion->save();
            });

            return redirect()->route('assets.index')->with('success', 'Activo asignado y Acta generada.');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al procesar asignación: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Show form to return an asset.
     */
    public function returnForm(Activo $asset)
    {
         $assignment = Asignacion::where('activo_id', $asset->id)->where('es_actual', true)->with('usuario')->firstOrFail();
         
         return Inertia::render('AssetAssignments/Return', [
             'asset' => $asset->load(['tipoActivo', 'modelo.marca']),
             'assignment' => $assignment
         ]);
    }

    /**
     * Process the return.
     */
    public function processReturn(Request $request, Asignacion $asignacion)
    {
        $validated = $request->validate([
             'observaciones' => 'nullable|string',
             'estado_final_id' => 'required|exists:estados_activo,id' // Free, Maintenance, etc.
        ]);
        
        DB::transaction(function () use ($validated, $asignacion) {
             $asignacion->load(['activo', 'usuario']);
             
             // 1. Update Assignment
             $asignacion->update([
                 'es_actual' => false,
                 'fecha_devolucion' => now(),
                 // 'observaciones_devolucion' => ... 
             ]);
             
             // 2. Update Asset
             $asignacion->activo->update([
                 'estado_activo_id' => $validated['estado_final_id']
             ]);
             
             // 3. Generate PDF Return
             $pdf = Pdf::loadView('pdf.acta_devolucion', [
                'asignacion' => $asignacion,
                'activo' => $asignacion->activo,
                'usuario' => $asignacion->usuario,
                'fecha' => now()->format('d/m/Y'),
                'observaciones' => $validated['observaciones'] ?? ''
             ]);
             
            $filename = 'acta_devolucion_' . $asignacion->id . '_' . Str::random(8) . '.pdf';
            $path = 'actas/devolucion/' . $filename;
            
            Storage::disk('public')->put($path, $pdf->output());
            
            $asignacion->acta_devolucion_path = $path;
            $asignacion->save();
        });
        
        return redirect()->route('assets.show', $asignacion->activo_id)->with('success', 'Activo devuelto y Acta generada.');
    }

    public function downloadActa(Request $request, $id, $type)
    {
        $asignacion = Asignacion::findOrFail($id);
        
        $path = $type === 'entrega' ? $asignacion->acta_entrega_path : $asignacion->acta_devolucion_path;
        
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'El documento no existe.');
        }
        
        return Storage::disk('public')->download($path);
    }
}
