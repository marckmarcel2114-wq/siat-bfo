<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Branch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Asset::with(['type', 'location.city']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code_internal', 'like', "%{$search}%")
                  ->orWhere('serial_number', 'like', "%{$search}%")
                  ->orWhere('brand', 'like', "%{$search}%")
                  ->orWhere('model', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('asset_type_id', $request->type);
        }

        return Inertia::render('Assets/Index', [
            'assets' => $query->latest()->paginate(15)->withQueryString(),
            'types' => AssetType::all(),
            'filters' => $request->only(['search', 'status', 'type']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Assets/Create', [
            'types' => AssetType::all(),
            'branches' => Branch::with('city')->orderBy('name')->get(),
            'cities' => \App\Models\City::orderBy('name')->get(),
            'branchTypes' => \App\Models\BranchType::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_type_id' => 'required|exists:asset_types,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets',
            'code_internal' => 'nullable|string|max:255|unique:assets',
            'purchase_date' => 'nullable|date',
            'warranty_expiry_date' => 'nullable|date',
            'location_id' => 'nullable|exists:branches,id',
            'network_point' => 'nullable|string',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|mac_address',
            'status' => 'required|in:free,assigned,maintenance,repair,broken,disposed',
            'specs' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $validated['uuid'] = (string) Str::uuid();

        Asset::create($validated);

        return redirect()->route('assets.index')->with('success', 'Activo registrado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Asset $asset)
    {
        return Inertia::render('Assets/Show', [
            'asset' => $asset->load(['type', 'location.city', 'assignments.user', 'maintenances', 'softwareLogs']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Asset $asset)
    {
        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
            'types' => AssetType::all(),
            'branches' => Branch::with('city')->orderBy('name')->get(),
            'cities' => \App\Models\City::orderBy('name')->get(),
            'branchTypes' => \App\Models\BranchType::orderBy('sort_order')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_type_id' => 'required|exists:asset_types,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255|unique:assets,serial_number,' . $asset->id,
            'code_internal' => 'nullable|string|max:255|unique:assets,code_internal,' . $asset->id,
            'purchase_date' => 'nullable|date',
            'warranty_expiry_date' => 'nullable|date',
            'location_id' => 'nullable|exists:branches,id',
            'network_point' => 'nullable|string',
            'ip_address' => 'nullable|ip',
            'mac_address' => 'nullable|mac_address',
            'status' => 'required|in:free,assigned,maintenance,repair,broken,disposed',
            'specs' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $asset->update($validated);

        return redirect()->route('assets.show', $asset)->with('success', 'Activo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Activo eliminado correctamente.');
    }
}
