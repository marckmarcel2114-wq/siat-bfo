<?php

namespace App\Http\Controllers;

use App\Models\TipoSucursal;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class TipoSucursalController extends Controller
{
    public function index(Request $request)
    {
        $query = TipoSucursal::withCount('ubicaciones');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('nombre', 'like', "%{$search}%");
        }

        $branchTypes = $query->orderBy('sort_order', 'asc')
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('BranchTypes/Index', [
            'branchTypes' => $branchTypes, // Keep key for frontend compatibility if needed
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('BranchTypes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tipos_sucursal',
            'descripcion' => 'nullable|string|max:500',
            'color' => 'required|string|max:20',
            'sort_order' => 'required|integer|min:0',
        ]);

        $branchType = TipoSucursal::create($validated);

        if ($request->wantsJson()) {
            return response()->json($branchType);
        }

        return redirect()->route('branch-types.index')->with('success', 'Tipo de sucursal creado correctamente.');
    }

    public function edit(TipoSucursal $branchType)
    {
        return Inertia::render('BranchTypes/Edit', [
            'branchType' => $branchType,
        ]);
    }

    public function update(Request $request, TipoSucursal $branchType)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255', Rule::unique('tipos_sucursal')->ignore($branchType->id)],
            'descripcion' => 'nullable|string|max:500',
            'color' => 'required|string|max:20',
            'sort_order' => 'required|integer|min:0',
        ]);

        $branchType->update($validated);

        return redirect()->route('branch-types.index')->with('success', 'Tipo de sucursal actualizado correctamente.');
    }

    public function destroy(TipoSucursal $branchType)
    {
        if ($branchType->ubicaciones()->count() > 0) {
            return back()->with('error', 'No se puede eliminar este tipo porque tiene ubicaciones asociadas.');
        }

        $branchType->delete();

        return redirect()->route('branch-types.index')->with('success', 'Tipo de sucursal eliminado correctamente.');
    }
}
