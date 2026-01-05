<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Models\Marca;
use App\Models\TipoActivo;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModelController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Models/Create', [
            'brands' => Marca::orderBy('nombre')->get(),
            'assetTypes' => TipoActivo::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'marca_id' => 'required|exists:marcas,id',
            'tipo_activo_id' => 'nullable|exists:tipos_activo,id', // Optional linking
        ]);

        // Unique check for Name + Brand
        $exists = Modelo::where('nombre', $validated['nombre'])
                        ->where('marca_id', $validated['marca_id'])
                        ->exists();
        
        if ($exists) {
            return response()->json(['errors' => ['nombre' => ['El modelo ya existe para esta marca.']]], 422);
        }

        $modelo = Modelo::create([
            'nombre' => $validated['nombre'],
            'marca_id' => $validated['marca_id'],
            'tipo_activo_id' => $validated['tipo_activo_id'] ?? null,
        ]);

        if ($request->wantsJson()) {
            return response()->json($modelo, 201);
        }

        return redirect()->back()->with('success', 'Modelo creado exitosamente.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Models/Index', [
            'models' => Modelo::with(['marca', 'tipoActivo'])->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $modelo = Modelo::findOrFail($id);
        return Inertia::render('Models/Edit', [
            'model' => $modelo,
            'brands' => Marca::orderBy('nombre')->get(),
            'assetTypes' => TipoActivo::orderBy('nombre')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'marca_id' => 'required|exists:marcas,id',
        ]);

        $modelo = Modelo::findOrFail($id);
        $modelo->update([
            'nombre' => $validated['nombre'],
            'marca_id' => $validated['marca_id'],
        ]);

        return redirect()->back()->with('success', 'Modelo actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $modelo = Modelo::findOrFail($id);
        if ($modelo->activos()->count() > 0) { // Assuming relationship exists or will be checked
            return redirect()->back()->with('error', 'No se puede eliminar el modelo porque tiene activos asociados.');
        }
        $modelo->delete();

        return redirect()->back()->with('success', 'Modelo eliminado exitosamente.');
    }
}
