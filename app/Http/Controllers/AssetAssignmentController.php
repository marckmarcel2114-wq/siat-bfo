<?php

namespace App\Http\Controllers;

use App\Models\AssetAssignment;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AssetAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Maybe a list of history?
        // Usually accessed via Asset details, but a global list might be useful.
        return Inertia::render('AssetAssignments/Index', [
            'assignments' => AssetAssignment::with(['asset.type', 'user', 'assigner'])->latest()->paginate(20),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $assetId = $request->query('asset_id');
        $asset = null;
        if ($assetId) {
            $asset = Asset::find($assetId);
        }

        return Inertia::render('AssetAssignments/Create', [
            'preselectedAsset' => $asset,
            'users' => User::orderBy('name')->get(), // Potential bottleneck if thousands of users, but okay for start
            // Assets that are 'free'
            'availableAssets' => Asset::where('status', 'free')->with('type')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'asset_id' => 'required|exists:assets,id',
            'user_id' => 'required|exists:users,id',
            'assigned_at' => 'required|date',
            'act_document' => 'required|file|mimes:pdf|max:10240', // 10MB max
            'details' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $asset = Asset::findOrFail($validated['asset_id']);

        if ($asset->status !== 'free') {
            return back()->withErrors(['asset_id' => 'Este activo no está disponible para asignación.']);
        }

        // Handle File Upload
        $path = null;
        if ($request->hasFile('act_document')) {
            $path = $request->file('act_document')->store('assignments/acts', 'public');
        }

        $assignment = AssetAssignment::create([
            'asset_id' => $validated['asset_id'],
            'user_id' => $validated['user_id'],
            'assigned_by' => Auth::id(),
            'assigned_at' => $validated['assigned_at'],
            'act_document_path' => $path,
            'details' => $validated['details'],
            'notes' => $validated['notes'],
        ]);

        // Update Asset Status
        $asset->update(['status' => 'assigned']);

        return redirect()->route('assets.show', $asset)->with('success', 'Activo asignado correctamenete.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssetAssignment $assetAssignment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssetAssignment $assetAssignment)
    {
        // Typically we update return info here?
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssetAssignment $assetAssignment)
    {
        // Logic for "Return" asset
        if ($request->has('return_action')) {
            $validated = $request->validate([
                'returned_at' => 'required|date',
                'return_document' => 'nullable|file|mimes:pdf|max:10240',
                'return_notes' => 'nullable|string',
            ]);

            $path = $assetAssignment->return_document_path;
            if ($request->hasFile('return_document')) {
                $path = $request->file('return_document')->store('assignments/returns', 'public');
            }

            $assetAssignment->update([
                'returned_at' => $validated['returned_at'],
                'return_document_path' => $path,
                'notes' => $assetAssignment->notes . "\n[Devolución]: " . $validated['return_notes'],
            ]);

            // Free the asset
            $assetAssignment->asset->update(['status' => 'free']);

            return back()->with('success', 'Activo devuelto correctamente.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssetAssignment $assetAssignment)
    {
        //
    }
}
