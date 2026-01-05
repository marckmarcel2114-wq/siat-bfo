<?php

namespace App\Http\Controllers;

use App\Models\Propietario;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Propietario::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'like', "%{$search}%");
        }

        $owners = $query->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Owners/Index', [
            'owners' => $owners,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Owners/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:propietarios',
        ]);

        $owner = Propietario::create($validated);

        if ($request->wantsJson()) {
            return response()->json($owner, 201);
        }

        return redirect()->route('owners.index')->with('success', 'Propietario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Propietario $owner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Propietario $owner)
    {
        return Inertia::render('Owners/Edit', [
            'owner' => $owner,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Propietario $owner)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:propietarios,nombre,' . $owner->id,
        ]);

        $owner->update($validated);

        return redirect()->route('owners.index')->with('success', 'Propietario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Propietario $owner)
    {
        // Add check for dependencies if needed (e.g. assets)
        if (\App\Models\Activo::where('propietario_id', $owner->id)->exists()) {
            return back()->with('error', 'No se puede eliminar porque tiene activos asignados.');
        }

        $owner->delete();

        return redirect()->route('owners.index')->with('success', 'Propietario eliminado correctamente.');
    }
}
