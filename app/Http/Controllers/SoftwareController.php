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
        // UNIFIED DASHBOARD STRATEGY:
        // We want to show "Software Catalog Items" that have installations OR licenses.
        
        // 1. Fetch all Software marked as 'active' or existing in catalog
        // Eager load:
        // - versions.installations (to count total installs)
        // - licenses (to count available seats)
        
        $softwareStats = \App\Models\Software::with(['versions.installations', 'licenses'])
            ->orderBy('nombre')
            ->get()
            ->map(function ($soft) {
                $totalInstalls = $soft->versions->sum(fn($v) => $v->installations->count());
                
                // Calculate Licensed Seats Breakdown
                $totalSeats = $soft->licenses->sum('seats_total');
                $usedSeats = $soft->licenses->sum('seats_used');
                
                // Identify if it has "Free" or "Unlimited" licenses
                $hasFreeLicense = $soft->licenses->contains('tipo', 'Free');
                
                return [
                    'id' => $soft->id,
                    'nombre' => $soft->nombre,
                    'fabricante' => $soft->fabricante,
                    'installations_count' => $totalInstalls,
                    'seats_total' => $totalSeats,
                    'seats_used' => $usedSeats,
                    'coverage_percent' => $totalInstalls > 0 ? min(100, round(($usedSeats / $totalInstalls) * 100)) : 100,
                    'licenses' => $soft->licenses->map(fn($l) => [
                        'id' => $l->id,
                        'tipo' => $l->tipo,
                        'seats_total' => $l->seats_total,
                        'seats_used' => $l->seats_used,
                        'scope' => $l->scope
                    ])
                ];
            });

        // Also fetch "Orphaned" Licenses (not linked to software_id yet) for legacy compatibility
        $orphanedLicenses = SoftwareLicense::whereNull('software_id')->with('proveedor')->get();

        return Inertia::render('Software/Index', [
            'softwareStats' => $softwareStats,
            'orphanedLicenses' => $orphanedLicenses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Software/Create', [
            'proveedores' => Proveedor::orderBy('nombre')->get(),
            'softwareCatalog' => \App\Models\Software::orderBy('nombre')->get(['id', 'nombre'])
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'software_id' => 'required|exists:software,id', // NEW: Link to Catalog required
            'nombre' => 'required|string|max:255',
            'key' => 'nullable|string',
            'tipo' => 'required|string',
            'seats_total' => 'required|integer|min:1',
            'fecha_expiracion' => 'nullable|date',
            'proveedor_id' => 'nullable|exists:proveedores,id',
            'observaciones' => 'nullable|string'
        ]);

        SoftwareLicense::create($validated);

        return redirect()->route('software.index')->with('success', 'Licencia registrada y vinculada correctamente.');
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
            'software_version_id' => 'required|exists:software_versions,id', // Strict: Version ID required
            
            // License Options:
            'license_id' => 'nullable|exists:software_licenses,id', // Option A: Existing Shared License
            'new_license_key' => 'nullable|string|min:5', // Option B: New OEM License
            
            'observaciones' => 'nullable|string'
        ]);

        try {
            DB::transaction(function () use ($validated) {
                $license = null;

                // 1. License Handling Strategy
                if (!empty($validated['new_license_key'])) {
                    // STRATEGY B: Register NEW OEM License
                    // Create a specific 1-seat license for this asset
                    // Needs Software Name: We have version_id, lets get the name
                    $version = \App\Models\SoftwareVersion::with('software')->findOrFail($validated['software_version_id']);
                    $softwareName = $version->software->nombre;
                    
                    // Determine Scope (Ideally from Asset's City)
                    $asset = Activo::with('ubicacion.ciudad')->findOrFail($validated['activo_id']);
                    $cityId = $asset->ubicacion?->ciudad_id;

                    $license = SoftwareLicense::create([
                        'nombre' => "OEM: {$softwareName} ({$version->version})",
                        'key' => $validated['new_license_key'],
                        'tipo' => 'OEM',
                        'seats_total' => 1,
                        'seats_used' => 0, // Will increment below
                        'scope' => 'CITY',
                        'city_id' => $cityId,
                        'observaciones' => "Licencia OEM generada automáticamente para el activo: {$asset->codigo_activo}"
                    ]);

                } elseif (!empty($validated['license_id'])) {
                    // STRATEGY A: Use Existing Shared License
                    $license = SoftwareLicense::lockForUpdate()->findOrFail($validated['license_id']);
                    
                    // Seat Availability Check
                    if ($license->seats_total > 0 && $license->seats_used >= $license->seats_total) {
                         throw new \Exception("La licencia seleccionada no tiene cupos disponibles ({$license->seats_used}/{$license->seats_total}).");
                    }
                }
                
                // 2. Duplicate Check (Version based)
                $existsVer = SoftwareInstallation::where('software_version_id', $validated['software_version_id'])
                    ->where('activo_id', $validated['activo_id'])
                    ->exists();
                if ($existsVer) throw new \Exception('Esta versión de software ya está instalada en el equipo.');

                // 3. Create Installation
                SoftwareInstallation::create([
                    'activo_id' => $validated['activo_id'],
                    'license_id' => $license ? $license->id : null,
                    'software_version_id' => $validated['software_version_id'],
                    'fecha_instalacion' => now(),
                    'registrado_por' => Auth::id(),
                    'observaciones' => $validated['observaciones']
                ]);

                // 4. Increment Seat Usage
                if ($license) {
                    $license->increment('seats_used');
                }
                
                // 5. Create Log (History)
                // Retrieve names for logging
                if (!isset($version)) {
                     $version = \App\Models\SoftwareVersion::with('software')->find($validated['software_version_id']);
                }
                \App\Models\SoftwareLog::create([
                     'asset_id' => $validated['activo_id'],
                     'action' => 'install',
                     'software_name' => $version->software->nombre,
                     'version' => $version->version,
                     'performed_at' => now(),
                     'performed_by' => Auth::id(),
                     'notes' => "Instalación Inicial. " . ($license ? "Licencia: {$license->tipo}" : "Sin Licencia")
                ]);
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
