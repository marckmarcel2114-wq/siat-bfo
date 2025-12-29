<?php

namespace App\Http\Controllers;

use App\Models\BranchType;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class BranchTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = BranchType::withCount('branches');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $branchTypes = $query->orderBy('sort_order', 'asc')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        // Inject ATM count manually since they are in a separate table
        $branchTypes->getCollection()->transform(function ($type) {
            if ($type->name === 'ATM') {
                $type->branches_count = \App\Models\Atm::count();
            }
            return $type;
        });

        return Inertia::render('BranchTypes/Index', [
            'branchTypes' => $branchTypes,
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
            'name' => 'required|string|max:255|unique:branch_types',
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|max:20',
            'sort_order' => 'required|integer|min:0',
        ]);

        $branchType = BranchType::create($validated);

        if ($request->wantsJson()) {
            return response()->json($branchType);
        }

        return redirect()->route('branch-types.index')->with('success', 'Tipo de sucursal creado correctamente.');
    }

    public function edit(BranchType $branchType)
    {
        return Inertia::render('BranchTypes/Edit', [
            'branchType' => $branchType,
        ]);
    }

    public function update(Request $request, BranchType $branchType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('branch_types')->ignore($branchType->id)],
            'description' => 'nullable|string|max:500',
            'color' => 'required|string|max:20',
            'sort_order' => 'required|integer|min:0',
        ]);

        $branchType->update($validated);

        return redirect()->route('branch-types.index')->with('success', 'Tipo de sucursal actualizado correctamente.');
    }

    public function destroy(BranchType $branchType)
    {
        if ($branchType->branches()->count() > 0) {
            return back()->with('error', 'No se puede eliminar este tipo porque tiene sucursales asociadas.');
        }

        $branchType->delete();

        return redirect()->route('branch-types.index')->with('success', 'Tipo de sucursal eliminado correctamente.');
    }
}
