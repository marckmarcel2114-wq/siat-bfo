<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\DefinicionAtributo;
use App\Models\AtributoActivo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

use App\Models\SoftwareLog;
use Illuminate\Support\Facades\Auth;

class AssetSoftwareController extends Controller
{
    /**
     * Show the form for editing the specified asset's software attributes.
     */
    public function edit(Activo $asset)
    {
        $asset->load(['tipoActivo', 'atributos.definicion']);

        // Fetch Software Definitions via Many-to-Many relationship
        $definitions = $asset->tipoActivo->definitions()
            ->where('category', 'software')
            ->orderBy('orden')
            ->get();

        // Map current values using definition ID as key
        $currentAttributes = $asset->atributos->pluck('valor', 'definicion_atributo_id')->toArray();

        return Inertia::render('Assets/Software/Edit', [
            'asset' => $asset,
            'definitions' => $definitions,
            'currentAttributes' => $currentAttributes,
        ]);
    }

    /**
     * Update the specified asset's software attributes in storage.
     */
    public function update(Request $request, Activo $asset)
    {
        $validated = $request->validate([
            'atributos' => 'array',
        ]);

        DB::transaction(function () use ($asset, $validated) {
            foreach ($validated['atributos'] as $id => $valor) {
                // Skip empty keys or null values
                if (empty($id)) continue;
                
                // Get implementation to check for changes
                $definition = DefinicionAtributo::find($id);
                if (!$definition) continue;

                $currentAttr = AtributoActivo::where('activo_id', $asset->id)
                    ->where('definicion_atributo_id', $id)
                    ->first();

                $oldValue = $currentAttr ? $currentAttr->valor : 'N/A';

                // Only update and log if value changed
                if ($oldValue !== (string)$valor) {
                    AtributoActivo::updateOrCreate(
                        ['activo_id' => $asset->id, 'definicion_atributo_id' => $id],
                        ['valor' => $valor]
                    );

                    // Automatic log for version updates
                    SoftwareLog::create([
                        'asset_id' => $asset->id,
                        'action' => 'update',
                        'software_name' => $definition->nombre,
                        'version' => $valor,
                        'performed_at' => now(),
                        'performed_by' => Auth::id(),
                        'notes' => "Cambio automático de: {$oldValue} a: {$valor}"
                    ]);
                }
            }
        });

        return redirect()->route('assets.show', $asset->id)->with('success', 'Información de software actualizada e historial registrado.');
    }

    /**
     * Manual log registration for specific upgrades/actions
     */
    public function logUpdate(Request $request, Activo $asset)
    {
        $validated = $request->validate([
            'software_name' => 'required|string|max:255',
            'version' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'action' => 'required|string|in:install,update,remove'
        ]);

        SoftwareLog::create([
            'asset_id' => $asset->id,
            'action' => $validated['action'],
            'software_name' => $validated['software_name'],
            'version' => $validated['version'],
            'performed_at' => now(),
            'performed_by' => Auth::id(),
            'notes' => $validated['notes']
        ]);

        return back()->with('success', 'Evento de software registrado correctamente.');
    }
}
