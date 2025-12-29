<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\BranchType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $branchTypes = BranchType::orderBy('sort_order', 'asc')
            ->get();
        $query = City::withCount(['atms']);

        // Add count for each branch type dynamically
        foreach ($branchTypes as $type) {
            $query->withCount([
                'branches as type_' . $type->id . '_count' => function ($q) use ($type) {
                    $q->where('branch_type_id', $type->id);
                }
            ]);
        }

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
        }

        $cities = $query->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Cities/Index', [
            'cities' => $cities,
            'branchTypes' => $branchTypes,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Cities/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:cities',
            'code' => 'required|string|max:10|unique:cities',
        ]);

        $city = City::create($validated);

        if ($request->wantsJson()) {
            return response()->json($city);
        }

        return redirect()->route('cities.index')->with('success', 'Ciudad creada correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        return Inertia::render('Cities/Edit', [
            'city' => $city,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, City $city)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('cities')->ignore($city->id)],
            'code' => ['required', 'string', 'max:10', Rule::unique('cities')->ignore($city->id)],
        ]);

        $city->update($validated);

        return redirect()->route('cities.index')->with('success', 'Ciudad actualizada correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        // Check for dependencies? Foreign keys might block this.
        // We set cascade on branches? No, foreignId()->constrained()->cascadeOnDelete() in branches table (Step 132/146 I updated it?)
        // Let's check branches migration content or just assume standard Laravel behavior.
        // Actually step 53 created branches with: $table->foreignId('city_id')->constrained()->cascadeOnDelete();
        // So deleting a city deletes all its branches. Dangerous but standard for normalized cascade.
        // But assets inside branches? Assets have nullOnDelete for location_id (Step 56).
        // Users have nullOnDelete for city_id (Step 74/84).
        
        $city->delete();
        return redirect()->route('cities.index')->with('success', 'Ciudad eliminada correctamente.');
    }
}
