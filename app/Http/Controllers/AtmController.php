<?php

namespace App\Http\Controllers;

use App\Models\Atm;
use App\Models\Branch;
use App\Models\City;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AtmController extends Controller
{
    public function index(Request $request)
    {
        $query = Atm::with(['city', 'parent'])
            ->leftJoin('cities', 'atms.city_id', '=', 'cities.id')
            ->select('atms.*')
            ->orderBy('cities.name')
            ->orderBy('atms.name');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('atms.name', 'like', "%{$search}%")
                  ->orWhere('atms.address', 'like', "%{$search}%")
                  ->orWhere('cities.name', 'like', "%{$search}%");
            });
        }

        $atms = $query->paginate(100)
            ->withQueryString();

        return Inertia::render('Atms/Index', [
            'atms' => $atms,
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Atms/Create', [
            'cities' => City::orderBy('name')->get(),
            'potentialParents' => Branch::orderBy('name')->get(),
            'branchTypes' => \App\Models\BranchType::orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'parent_id' => 'nullable|string', // Validated manually below
            'address' => 'nullable|string|max:255',
            'phones' => 'nullable|string|max:255',
        ]);

        if ($data['parent_id'] === 'none' || $data['parent_id'] === '') {
            $data['parent_id'] = null;
        }

        $atm = Atm::create($data);

        if ($request->wantsJson()) {
            return response()->json($atm->load('city', 'parent'));
        }

        return redirect()->route('atms.index')->with('success', 'ATM creado correctamente.');
    }

    public function edit($id)
    {
        $atm = Atm::findOrFail($id);

        return Inertia::render('Atms/Edit', [
            'atm' => $atm,
            'cities' => City::orderBy('name')->get(),
            'potentialParents' => Branch::orderBy('name')->get(),
            'branchTypes' => \App\Models\BranchType::orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $atm = Atm::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'city_id' => 'required|exists:cities,id',
            'parent_id' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'phones' => 'nullable|string|max:255',
        ]);

        if ($data['parent_id'] === 'none' || $data['parent_id'] === '') {
            $data['parent_id'] = null;
        }

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
