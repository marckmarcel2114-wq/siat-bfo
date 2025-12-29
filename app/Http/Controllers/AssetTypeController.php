<?php

namespace App\Http\Controllers;

use App\Models\AssetType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = AssetType::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $assetTypes = $query->orderBy('name')
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
            'name' => 'required|string|max:255|unique:asset_types',
        ]);

        $assetType = AssetType::create($validated);

        if ($request->wantsJson()) {
            return response()->json($assetType);
        }

        return redirect()->route('asset-types.index')->with('success', 'Tipo de activo creado correctamente.');
    }

    public function edit(AssetType $assetType)
    {
        return Inertia::render('AssetTypes/Edit', [
            'assetType' => $assetType,
        ]);
    }

    public function update(Request $request, AssetType $assetType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:asset_types,name,' . $assetType->id,
        ]);

        $assetType->update($validated);

        return redirect()->route('asset-types.index')->with('success', 'Tipo de activo actualizado correctamente.');
    }

    public function destroy(AssetType $assetType)
    {
        if ($assetType->assets()->count() > 0) {
            return back()->with('error', 'No se puede eliminar este tipo porque tiene activos asociados.');
        }

        $assetType->delete();

        return redirect()->route('asset-types.index')->with('success', 'Tipo de activo eliminado correctamente.');
    }
}
