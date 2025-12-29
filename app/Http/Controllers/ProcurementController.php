<?php

namespace App\Http\Controllers;

use App\Models\Procurement;
use App\Models\City;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class ProcurementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $query = Procurement::with(['requester', 'city', 'authorizer']);

        // Filter: Regular users see only theirs.
        // Admins see all? Or City Admins see their city's?
        if ($user->role === 'city_admin') {
            $query->where('city_id', $user->city_id);
        } elseif ($user->role === 'user') {
            $query->where('requester_id', $user->id);
        }

        return Inertia::render('Procurements/Index', [
            'procurements' => $query->latest()->paginate(15),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Procurements/Create', [
            'cities' => City::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => 'required|exists:cities,id',
            'items' => 'required|array', // [{ name, quantity, estimated_price }]
            'justification' => 'required|string',
        ]);

        Procurement::create([
            'requester_id' => Auth::id(),
            'city_id' => $validated['city_id'],
            'status' => 'draft',
            'items' => $validated['items'],
            'justification' => $validated['justification'],
        ]);

        return redirect()->route('procurements.index')->with('success', 'Solicitud creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Procurement $procurement)
    {
        return Inertia::render('Procurements/Show', [
            'procurement' => $procurement->load(['requester', 'city', 'authorizer']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Procurement $procurement)
    {
        // Only draft can be edited?
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Procurement $procurement)
    {
        // Handle Authorization Upload
        if ($request->hasFile('auth_document')) {
             $validated = $request->validate([
                'auth_document' => 'required|file|mimes:pdf|max:10240',
            ]);
            
            $path = $request->file('auth_document')->store('procurements/auths', 'public');
            
            $procurement->update([
                'authorization_document_path' => $path,
                'status' => 'approved',
                'authorized_by' => Auth::id(),
                'authorized_at' => now(),
            ]);
            
            return back()->with('success', 'Solicitud autorizada y firmada.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Procurement $procurement)
    {
        //
    }
}
