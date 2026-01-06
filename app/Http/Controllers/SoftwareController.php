<?php

namespace App\Http\Controllers;

use App\Models\SoftwareLicense;
use App\Models\SoftwareInstallation;
use App\Models\Proveedor;
use App\Models\Activo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SoftwareController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $licenses = SoftwareLicense::with('proveedor')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Software/Index', [
            'licenses' => $licenses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Software/Create', [
            'proveedores' => Proveedor::orderBy('nombre')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'key' => 'nullable|string',
            'tipo' => 'required|string',
            'seats_total' => 'required|integer|min:1',
            'fecha_expiracion' => 'nullable|date',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'observaciones' => 'nullable|string'
        ]);

        SoftwareLicense::create($validated);

        return redirect()->route('software.index')->with('success', 'Licencia registrada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
         // Show License details + Installations list
         $license = SoftwareLicense::with(['proveedor', 'instalaciones.asset', 'instalaciones.registrador'])
            ->findOrFail($id);
            
         // Fetch assets that might need software (Desktops, Laptops) for the assignment search
         // For now, let's just send the license and let the frontend handle specific searches if needed,
         // or provide a list of "Eligible Assets" (those with no more than X software or specific types)
         // But usually, we just search by code.
            
         return Inertia::render('Software/Show', [
             'license' => $license,
             // Optional: available assets for quick assignment
             'availableAssets' => Activo::whereHas('tipoActivo', function($q) {
                 $q->whereIn('nombre', ['Computadora Desktops', 'Computadora Laptops', 'Laptop', 'Desktop', 'Computadora']);
             })->get(['id', 'codigo_activo', 'numero_serie'])
         ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $license = SoftwareLicense::findOrFail($id);
        return Inertia::render('Software/Edit', [
            'license' => $license,
            'proveedores' => Proveedor::orderBy('nombre')->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $license = SoftwareLicense::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'key' => 'nullable|string',
            'tipo' => 'required|string',
            'seats_total' => 'required|integer|min:1',
            'fecha_expiracion' => 'nullable|date',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'observaciones' => 'nullable|string'
        ]);

        $license->update($validated);

        return redirect()->route('software.index')->with('success', 'Licencia actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $license = SoftwareLicense::findOrFail($id);
        
        // check if has installations
        if ($license->instalaciones()->count() > 0) {
            return back()->with('error', 'No se puede eliminar una licencia que tiene instalaciones activas. Desinstale primero.');
        }

        $license->delete();

        return redirect()->route('software.index')->with('success', 'Licencia eliminada correctamente.');
    }
    
    // Install Software on Asset (Internal API or Action)
    public function installOnAsset(Request $request)
    {
        $validated = $request->validate([
            'activo_id' => 'required|exists:activos,id',
            'license_id' => 'nullable|exists:software_licenses,id',
            'software_version_id' => 'nullable|exists:software_versions,id',
            'observaciones' => 'nullable|string'
        ]);
        
        if (empty($validated['license_id']) && empty($validated['software_version_id'])) {
            return back()->with('error', 'Debe seleccionar un Software o una Licencia.');
        }

        try {
            DB::transaction(function () use ($validated) {
                $license = null;

                // 1. Handle License Logic
                if (!empty($validated['license_id'])) {
                    $license = SoftwareLicense::lockForUpdate()->findOrFail($validated['license_id']);
                    
                    // Check duplicate license on asset
                    $exists = SoftwareInstallation::where('license_id', $license->id)
                        ->where('activo_id', $validated['activo_id'])
                        ->exists();
                    
                    if ($exists) {
                        throw new \Exception('El software ya se encuentra instalado en este equipo.');
                    }

                    if ($license->seats_total > 0 && $license->seats_used >= $license->seats_total) {
                         throw new \Exception('No hay asientos disponibles en esta licencia.');
                    }
                }
                
                // 2. Handle Version Logic (Duplicate check)
                if (!empty($validated['software_version_id'])) {
                     $existsVer = SoftwareInstallation::where('software_version_id', $validated['software_version_id'])
                        ->where('activo_id', $validated['activo_id'])
                        ->exists();
                     if ($existsVer) throw new \Exception('Esta versión de software ya está instalada en el equipo.');
                }

                SoftwareInstallation::create([
                    'activo_id' => $validated['activo_id'],
                    'license_id' => $license ? $license->id : null,
                    'software_version_id' => $validated['software_version_id'] ?? null,
                    'fecha_instalacion' => now(),
                    'registrado_por' => Auth::id(),
                    'observaciones' => $validated['observaciones']
                ]);

                if ($license) {
                    $license->increment('seats_used');
                }
            });

            return back()->with('success', 'Software instalado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
    
    // Uninstall
    public function uninstallFromAsset($id)
    {
        DB::transaction(function () use ($id) {
            $installation = SoftwareInstallation::findOrFail($id);
            
            if ($installation->license_id) {
                // Only decrement if license was attached
                $license = SoftwareLicense::lockForUpdate()->findOrFail($installation->license_id);
                $license->decrement('seats_used');
            }
            
            $installation->delete();
        });
        
        return back()->with('success', 'Software desinstalado.');
    }

    // Update/Upgrade Software Version
    public function updateInstallation(Request $request, $id)
    {
        $validated = $request->validate([
            'software_version_id' => 'required|exists:software_versions,id',
            'fecha_actualizacion' => 'required|date',
            'observaciones' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($id, $validated) {
                $installation = SoftwareInstallation::with(['softwareVersion.software', 'license'])->findOrFail($id);
                $oldVersion = $installation->software_version ? $installation->software_version->version : 'Desconocido';
                $softwareName = $installation->software_version ? $installation->software_version->software->nombre : ($installation->license ? $installation->license->nombre : 'Software');

                // Get new version details
                $newVersionObj = \App\Models\SoftwareVersion::with('software')->findOrFail($validated['software_version_id']);
                
                // Validate that we are upgrading the SAME software (optional, but safer)
                if ($installation->software_version && $installation->software_version->software_id !== $newVersionObj->software_id) {
                    throw new \Exception('La nueva versión no pertenece al mismo software original.');
                }

                // Update Installation
                $installation->update([
                    'software_version_id' => $validated['software_version_id'],
                    'observaciones' => $validated['observaciones'] ? $installation->observaciones . "\n[Upgrade]: " . $validated['observaciones'] : $installation->observaciones
                ]);

                // Create Log
                \App\Models\SoftwareLog::create([
                    'asset_id' => $installation->activo_id,
                    'action' => 'update',
                    'software_name' => $softwareName,
                    'version' => $newVersionObj->version,
                    'performed_at' => $validated['fecha_actualizacion'],
                    'performed_by' => Auth::id(),
                    'notes' => "Actualización de versión: {$oldVersion} -> {$newVersionObj->version}. " . ($validated['observaciones'] ?? '')
                ]);
            });

            return back()->with('success', 'Software actualizado correctamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
