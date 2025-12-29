<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\City;
use App\Models\BranchType;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Branch::with(['city', 'type']);

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('branches.name', 'like', "%{$search}%")
                  ->orWhere('branches.code', 'like', "%{$search}%")
                  ->orWhere('branches.address', 'like', "%{$search}%")
                  ->orWhereHas('city', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $branches = $query->join('cities', 'branches.city_id', '=', 'cities.id')
            ->select('branches.*')
            ->orderBy('cities.name')
            ->orderBy('branches.name')
            ->paginate(100)
            ->withQueryString();

        return Inertia::render('Branches/Index', [
            'branches' => $branches,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Branches/Create', [
            'cities' => City::orderBy('name')->get(),
            'types' => BranchType::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'branch_type_id' => 'required|exists:branch_types,id',
            'code' => 'nullable|string|max:20',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phones' => 'nullable|string|max:255',
        ]);

        $branch = Branch::create($validated);

        if ($request->wantsJson()) {
            return response()->json($branch->load('city'));
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
            'cities' => City::orderBy('name')->get(),
            'types' => BranchType::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'branch_type_id' => 'required|exists:branch_types,id',
            'code' => 'nullable|string|max:20',
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phones' => 'nullable|string|max:255',
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
