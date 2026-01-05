<?php

namespace App\Http\Controllers;

use App\Models\NivelCriticidad;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CriticalityController extends Controller
{
    public function create()
    {
        return Inertia::render('Criticality/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:niveles_criticidad,nombre|max:20',
            'nivel_numerico' => 'nullable|integer|min:1|max:5', // Optional default
            'color' => 'nullable|string|max:20',
        ]);

        // Default numeric level if not provided (logic can be improved)
        $nivel = $validated['nivel_numerico'] ?? 1;

        $criticidad = NivelCriticidad::create([
            'nombre' => $validated['nombre'],
            'nivel_numerico' => $nivel,
            'color' => $validated['color'] ?? '#6b7280', // Default gray
        ]);

        if ($request->wantsJson()) {
            return response()->json($criticidad, 201);
        }

        return redirect()->route('criticality.index')->with('success', 'Nivel de criticidad creado exitosamente.');
    }

    public function index()
    {
        return Inertia::render('Criticality/Index', [
            'criticalities' => NivelCriticidad::orderBy('nivel_numerico')->get(),
        ]);
    }

    public function edit($id)
    {
        $crit = NivelCriticidad::findOrFail($id);
        return Inertia::render('Criticality/Edit', [
            'criticality' => $crit,
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|unique:niveles_criticidad,nombre,' . $id . '|max:20',
            'nivel_numerico' => 'nullable|integer',
            'color' => 'nullable|string',
        ]);

        $crit = NivelCriticidad::findOrFail($id);
        $crit->update($validated);

        return redirect()->route('criticality.index')->with('success', 'Criticidad actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $crit = NivelCriticidad::findOrFail($id);
        $crit->delete();

        return redirect()->back()->with('success', 'Criticidad eliminada exitosamente.');
    }
}
