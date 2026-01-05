<?php

namespace App\Http\Controllers;

use App\Models\TipoActivo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoActivo::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'like', "%{$search}%");
        }

        $assetTypes = $query->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('AssetTypes/Index', [
            'assetTypes' => $assetTypes,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('AssetTypes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_activo',
        ]);

        $assetType = TipoActivo::create($validated);

        if ($request->wantsJson()) {
            return response()->json($assetType);
        }

        return redirect()->route('asset-types.index')->with('success', 'Tipo de activo creado correctamente.');
    }

    public function edit(TipoActivo $assetType)
    {
        return Inertia::render('AssetTypes/Edit', [
            'assetType' => $assetType->load('definitions'),
        ]);
    }

    public function update(Request $request, TipoActivo $assetType)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_activo,nombre,' . $assetType->id,
        ]);

        $assetType->update($validated);

        return redirect()->route('asset-types.index')->with('success', 'Tipo de activo actualizado correctamente.');
    }

    public function storeDefinition(Request $request, TipoActivo $assetType)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_dato' => 'required|string|in:text,number,select,date,boolean',
            'orden' => 'nullable|integer',
            'opciones' => 'nullable|array', // Allow passing options (e.g. for creating new)
        ]);

        $assetType->definitions()->create($validated);

        return back()->with('success', 'Campo personalizado agregado.');
    }

    public function updateDefinition(Request $request, TipoActivo $assetType, $definitionId)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo_dato' => 'required|string|in:text,number,select,date',
            'orden' => 'nullable|integer',
            'opciones' => 'nullable|array',
        ]);

        $definition = $assetType->definitions()->findOrFail($definitionId);
        $definition->update($validated);

        return back()->with('success', 'Campo actualizado.');
    }

    public function destroyDefinition(TipoActivo $assetType, $definitionId)
    {
        $assetType->definitions()->findOrFail($definitionId)->delete();
        return back()->with('success', 'Campo eliminado.');
    }

    public function destroy(TipoActivo $assetType)
    {
        if ($assetType->assets()->count() > 0) {
            return back()->with('error', 'No se puede eliminar este tipo porque tiene activos asociados.');
        }

        $assetType->delete();

        return redirect()->route('asset-types.index')->with('success', 'Tipo de activo eliminado correctamente.');
    }
}
