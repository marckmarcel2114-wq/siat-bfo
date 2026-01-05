<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\DefinicionAtributo;
use App\Models\AtributoActivo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AssetSpecsController extends Controller
{
    /**
     * Show the form for editing the specified asset's hardware specifications.
     */
    public function edit(Activo $asset)
    {
        $asset->load(['tipoActivo', 'atributos']);

        // Fetch Hardware Definitions via Many-to-Many relationship
        $definitions = $asset->tipoActivo->definitions()
            ->where(function($query) {
                // Hardware is default, or explicit category
                $query->where('category', 'hardware')->orWhereNull('category');
            })
            ->orderBy('orden')
            ->get();

        // Map current values using definition ID as key
        $currentAttributes = $asset->atributos->pluck('valor', 'definicion_atributo_id')->toArray();

        return Inertia::render('Assets/Specs/Edit', [
            'asset' => $asset,
            'definitions' => $definitions,
            'currentAttributes' => $currentAttributes,
        ]);
    }

    /**
     * Update the specified asset's hardware specifications.
     */
    public function update(Request $request, Activo $asset)
    {
        $validated = $request->validate([
            'atributos' => 'array',
        ]);

        DB::transaction(function () use ($asset, $validated) {
            foreach ($validated['atributos'] as $id => $valor) {
                // Skip null, undefined, or empty keys
                if (empty($id) || $valor === null) continue;

                AtributoActivo::updateOrCreate(
                    [
                        'activo_id' => $asset->id,
                        'definicion_atributo_id' => $id
                    ],
                    [
                        'valor' => $valor
                    ]
                );
            }
        });

        return redirect()->route('assets.show', $asset->id)->with('success', 'Especificaciones técnicas actualizadas correctamente.');
    }
}
