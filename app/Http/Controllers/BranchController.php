<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\TipoSucursal;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = function ($query) use ($request) {
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where(function ($q) use ($search) {
                    $q->where('ubicaciones.nombre', 'like', "%{$search}%")
                      ->orWhere('ubicaciones.codigo_ubicacion', 'like', "%{$search}%")
                      ->orWhere('ubicaciones.direccion', 'like', "%{$search}%")
                      ->orWhereHas('ciudad', function ($cq) use ($search) {
                          $cq->where('nombre', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('type')) {
                $types = explode(',', $request->input('type'));
                $query->whereIn('tipo_sucursal_id', $types);
            }

            if ($request->filled('city')) {
                $cities = explode(',', $request->input('city'));
                $query->whereIn('ciudad_id', $cities);
            }
        };

        $query = Branch::with(['ciudad', 'tipo', 'padre.tipo']);
        $filters($query);

        $branches = $query
            ->join('ciudades', 'ubicaciones.ciudad_id', '=', 'ciudades.id')
            ->join('tipos_sucursal', 'ubicaciones.tipo_sucursal_id', '=', 'tipos_sucursal.id')
            ->select('ubicaciones.*')
            ->orderBy('ciudades.nombre')
            ->orderBy('tipos_sucursal.sort_order')
            ->orderBy('ubicaciones.nombre')
            ->paginate(100)
            ->withQueryString();

        return Inertia::render('Branches/Index', [
            'branches' => $branches,
            'branchTypes' => TipoSucursal::withCount(['ubicaciones as branches_count' => function ($q) use ($filters) {
                // Apply filters but remove the 'type' filter for the Type distribution itself so we can see other possibilities?
                // Actually user requested "las estadistas se pongan del filtro realizado".
                // If I search "La Paz", I want to see how many agencies/ATMs are in La Paz.
                // If I search "ATM", I want to see 0 agencies? Or just the distribution of the result?
                // Usually "Distribution" means "of the result set".
                $filters($q);
            }])->orderBy('sort_order')->get(),
            'cities' => City::withCount(['ubicaciones as branches_count' => function ($q) use ($filters) {
                $filters($q);
            }])->orderBy('nombre')->get(['id', 'nombre']),
            'filters' => $request->only(['search', 'type', 'city']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Branches/Create', [
            'cities' => City::orderBy('nombre')->get(),
            'types' => TipoSucursal::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ciudad_id' => 'required|exists:ciudades,id',
            'tipo_sucursal_id' => 'required|exists:tipos_sucursal,id',
            'codigo_ubicacion' => 'nullable|string|max:20',
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefonos' => 'nullable|string|max:255',
        ]);

        $branch = Branch::create($validated);

        if ($request->wantsJson()) {
            return response()->json($branch->load('ciudad', 'tipo'));
        }

        return redirect()->route('branches.index')->with('success', 'Sucursal creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return Inertia::render('Branches/Edit', [
            'branch' => $branch,
            'cities' => City::orderBy('nombre')->get(),
            'types' => TipoSucursal::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'ciudad_id' => 'required|exists:ciudades,id',
            'tipo_sucursal_id' => 'required|exists:tipos_sucursal,id',
            'codigo_ubicacion' => 'nullable|string|max:20',
            'nombre' => 'required|string|max:255',
            'direccion' => 'nullable|string|max:255',
            'telefonos' => 'nullable|string|max:255',
        ]);

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Sucursal actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Sucursal eliminada correctamente.');
    }
}
