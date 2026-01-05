<?php

namespace App\Http\Controllers;

use App\Models\EstadoActivo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AssetStatusController extends Controller
{
    public function create()
    {
        return Inertia::render('AssetStatus/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:estados_activo,nombre|max:50',
        ]);

        $status = EstadoActivo::create([
            'nombre' => $validated['nombre']
        ]);

        if ($request->wantsJson()) {
            return response()->json($status, 201);
        }

        return redirect()->route('asset-status.index')->with('success', 'Estado creado exitosamente.');
    }

    public function index()
    {
        return Inertia::render('AssetStatus/Index', [
            'statuses' => EstadoActivo::all(),
        ]);
    }

    public function edit($id)
    {
        $status = EstadoActivo::findOrFail($id);
        return Inertia::render('AssetStatus/Edit', [
            'status' => $status,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:estados_activo,nombre,' . $id . '|max:50',
        ]);

        $status = EstadoActivo::findOrFail($id);
        $status->update($validated);

        return redirect()->route('asset-status.index')->with('success', 'Estado actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $status = EstadoActivo::findOrFail($id);
        // Check usage if needed
        $status->delete();

        return redirect()->back()->with('success', 'Estado eliminado exitosamente.');
    }
}
