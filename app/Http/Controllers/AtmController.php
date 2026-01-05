<?php

namespace App\Http\Controllers;

use App\Models\Atm;
use App\Models\Branch;
use App\Models\City;
use App\Models\TipoSucursal;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AtmController extends Controller
{
    public function index(Request $request)
    {
        $query = Atm::with(['ciudad', 'tipo', 'padre.tipo']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('ubicaciones.nombre', 'like', "%{$search}%")
                  ->orWhere('ubicaciones.direccion', 'like', "%{$search}%")
                  ->orWhereHas('ciudad', function ($cq) use ($search) {
                      $cq->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        $atmTypeId = TipoSucursal::where('nombre', 'ATM')->value('id');
        $atms = $query->where('ubicaciones.tipo_sucursal_id', $atmTypeId)
            ->join('ciudades', 'ubicaciones.ciudad_id', '=', 'ciudades.id')
            ->leftJoin('ubicaciones as padre', 'ubicaciones.padre_id', '=', 'padre.id')
            ->leftJoin('tipos_sucursal as padre_tipo', 'padre.tipo_sucursal_id', '=', 'padre_tipo.id')
            ->select('ubicaciones.*')
            ->orderBy('ciudades.nombre')
            ->orderBy('padre_tipo.sort_order')
            ->orderBy('padre.nombre')
            ->orderBy('ubicaciones.nombre')
            ->paginate(100)
            ->withQueryString();

        return Inertia::render('Atms/Index', [
            'atms' => $atms,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        $atmTypeId = TipoSucursal::where('nombre', 'ATM')->value('id');
        
        return Inertia::render('Atms/Create', [
            'cities' => City::orderBy('nombre')->get(),
            'branchTypes' => TipoSucursal::orderBy('sort_order')->get(),
            'potentialParents' => Ubicacion::where('tipo_sucursal_id', '!=', $atmTypeId)
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        if ($request->input('padre_id') === 'null') {
            $request->merge(['padre_id' => null]);
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id',
            'padre_id' => 'nullable|exists:ubicaciones,id',
            'direccion' => 'nullable|string|max:255',
        ]);

        $atmTypeId = TipoSucursal::where('nombre', 'ATM')->value('id');
        $data['tipo_sucursal_id'] = $atmTypeId;

        $atm = Atm::create($data);

        if ($request->wantsJson()) {
            return response()->json($atm->load('ciudad', 'tipo'));
        }

        return redirect()->route('atms.index')->with('success', 'ATM creado correctamente.');
    }

    public function edit($id)
    {
        $atm = Atm::findOrFail($id);
        $atmTypeId = TipoSucursal::where('nombre', 'ATM')->value('id');

        return Inertia::render('Atms/Edit', [
            'atm' => $atm,
            'cities' => City::orderBy('nombre')->get(),
            'branchTypes' => TipoSucursal::orderBy('sort_order')->get(),
            'potentialParents' => Ubicacion::where('tipo_sucursal_id', '!=', $atmTypeId)
                ->orderBy('nombre')
                ->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $atm = Atm::findOrFail($id);

        if ($request->input('padre_id') === 'null') {
            $request->merge(['padre_id' => null]);
        }

        $data = $request->validate([
            'nombre' => 'required|string|max:255',
            'ciudad_id' => 'required|exists:ciudades,id',
            'padre_id' => 'nullable|exists:ubicaciones,id',
            'direccion' => 'nullable|string|max:255',
        ]);

        $atm->update($data);

        return redirect()->route('atms.index')->with('success', 'ATM actualizado correctamente.');
    }

    public function destroy($id)
    {
        $atm = Atm::findOrFail($id);
        $atm->delete();

        return redirect()->back()->with('success', 'ATM eliminado correctamente.');
    }
}
