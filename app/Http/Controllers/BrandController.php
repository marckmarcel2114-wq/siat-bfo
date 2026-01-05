<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BrandController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Brands/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:marcas,nombre|max:255',
        ]);

        $marca = Marca::create([
            'nombre' => $validated['nombre']
        ]);

        if ($request->wantsJson()) {
            return response()->json($marca, 201);
        }

        return redirect()->route('brands.index')->with('success', 'Marca creada exitosamente.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Brands/Index', [
            'brands' => Marca::withCount('modelos')->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $marca = Marca::findOrFail($id);
        return Inertia::render('Brands/Edit', [
            'brand' => $marca,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:marcas,nombre,' . $id . '|max:255',
        ]);

        $marca = Marca::findOrFail($id);
        $marca->update([
            'nombre' => $validated['nombre']
        ]);

        return redirect()->route('brands.index')->with('success', 'Marca actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $marca = Marca::findOrFail($id);
        if ($marca->modelos()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar la marca porque tiene modelos asociados.');
        }
        $marca->delete();

        return redirect()->back()->with('success', 'Marca eliminada exitosamente.');
    }
    
    // Add index/update/delete later if needed for full CRUD
}
