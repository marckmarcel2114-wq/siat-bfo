<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
// Assuming PuntoRed model exists or we just use raw IDs, but let's check
use App\Models\PuntoRed;

class AssetNetworkController extends Controller
{
    /**
     * Show the form for editing the specified asset's network configuration.
     */
    public function edit(Activo $asset)
    {
        $asset->load(['tipoActivo', 'ubicacion']);
        
        // Fetch Puntos de Red (Network Points)
        // Usually filtered by location, but let's fetch all or filtered if possible
        $puntosRed = [];
        if (class_exists(PuntoRed::class)) {
            $puntosRed = PuntoRed::with('ubicacion')->get(); 
        }

        return Inertia::render('Assets/Network/Edit', [
            'asset' => $asset,
            'puntos_red' => $puntosRed
        ]);
    }

    /**
     * Update the specified asset's network configuration.
     */
    public function update(Request $request, Activo $asset)
    {
        $validated = $request->validate([
            'ip_address' => 'nullable|ipv4',
            'mac_ethernet' => 'nullable|string|max:17',
            'mac_wifi' => 'nullable|string|max:17',
            'punto_red_id' => 'nullable|exists:puntos_red,id',
        ]);

        $asset->update($validated);

        return redirect()->route('assets.show', $asset->id)->with('success', 'Configuración de red actualizada correctamente.');
    }
}
