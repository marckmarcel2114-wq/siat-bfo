<?php

namespace App\Http\Controllers;

use App\Models\Software;
use App\Models\SoftwareVersion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class SoftwareCatalogController extends Controller
{
    /**
     * Display a listing of the software catalog.
     */
    public function index()
    {
        $software = Software::withCount('versions')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Configs/Software/Index', [
            'softwareList' => $software
        ]);
    }

    /**
     * Store a newly created software.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fabricante' => 'nullable|string|max:255',
            'tipo' => 'required|string',
            'descripcion' => 'nullable|string'
        ]);

        $software = Software::create($validated);

        if ($request->wantsJson()) {
            return response()->json($software);
        }

        return redirect()->back()->with('success', 'Software creado correctamente.');
    }

    /**
     * Show detailed view of software and its versions.
     */
    public function show(Software $softwareCatalog)
    {
        $softwareCatalog->load(['versions' => function($q) {
            $q->orderBy('version', 'desc');
        }]);

        return Inertia::render('Configs/Software/Show', [
            'software' => $softwareCatalog
        ]);
    }

    /**
     * Update the specified software.
     */
    public function update(Request $request, $id)
    {
        $software = Software::findOrFail($id);
        
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'fabricante' => 'nullable|string|max:255',
            'tipo' => 'required|string',
            'descripcion' => 'nullable|string'
        ]);

        $software->update($validated);

        return redirect()->back()->with('success', 'Software actualizado correctamente.');
    }

    /**
     * Remove the specified software.
     */
    public function destroy($id)
    {
        $software = Software::findOrFail($id);
        $software->delete();

        return redirect()->route('software-catalog.index')->with('success', 'Software eliminado.');
    }

    // --- Version Management ---

    public function storeVersion(Request $request, $softwareId)
    {
        $validated = $request->validate([
            'version' => 'required|string|max:255',
            'fecha_lanzamiento' => 'nullable|date',
            'eol_date' => 'nullable|date',
            'descripcion' => 'nullable|string'
        ]);

        $validated['software_id'] = $softwareId;

        $version = SoftwareVersion::create($validated);

        if ($request->wantsJson()) {
            return response()->json($version);
        }

        return redirect()->back()->with('success', 'Versión registrada correctamente.');
    }

    public function updateVersion(Request $request, $versionId)
    {
        $version = SoftwareVersion::findOrFail($versionId);

        $validated = $request->validate([
            'version' => 'required|string|max:255',
            'fecha_lanzamiento' => 'nullable|date',
            'eol_date' => 'nullable|date',
            'descripcion' => 'nullable|string'
        ]);

        $version->update($validated);

        return redirect()->back()->with('success', 'Versión actualizada correctamente.');
    }

    public function destroyVersion($versionId)
    {
        $version = SoftwareVersion::findOrFail($versionId);
        $version->delete();

        return redirect()->back()->with('success', 'Versión eliminada.');
    }

    // --- API for Selectors ---
    public function apiList()
    {
        return Software::with(['versions' => function($q) {
            $q->select('id', 'software_id', 'version', 'fecha_lanzamiento');
        }])->select('id', 'nombre', 'fabricante', 'tipo')->orderBy('nombre')->get();
    }
}
