<?php

namespace App\Http\Controllers;

use App\Models\Activo;
use App\Models\TipoActivo;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\EstadoActivo;
use App\Models\NivelCriticidad;
use App\Models\Propietario;
use App\Models\Proveedor;
use App\Models\TipoSucursal;
use App\Models\Ubicacion;
use App\Models\DefinicionAtributo;
use App\Models\AtributoActivo;
use App\Models\AsignacionRed;
use App\Models\PuntoRed;
use App\Models\Asignacion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Eager load 'definicion' for attributes to access their names
        // Default Sort: City -> Branch (Ubicacion) -> Type -> Code for Grouped View
        $query = Activo::with(['tipoActivo', 'modelo.marca', 'ubicacion.ciudad.sucursales', 'ubicacion.tipo', 'estadoActivo', 'atributos.definicion', 'propietario', 'nivelCriticidad'])
                    ->join('ubicaciones', 'activos.ubicacion_id', '=', 'ubicaciones.id')
                    ->join('ciudades', 'ubicaciones.ciudad_id', '=', 'ciudades.id')
                    ->join('tipos_sucursal', 'ubicaciones.tipo_sucursal_id', '=', 'tipos_sucursal.id')
                    ->join('tipos_activo', 'activos.tipo_activo_id', '=', 'tipos_activo.id')
                    ->select('activos.*') // Avoid column collision
                    ->orderBy('ciudades.nombre')
                    ->orderBy('tipos_sucursal.sort_order') // Order by sort_order
                    ->orderBy('ubicaciones.nombre')
                    ->orderBy('tipos_activo.nombre');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo_activo', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%");
            });
        }

        if ($request->filled('city_id')) {
            $query->whereHas('ubicacion', function($q) use ($request) {
                $q->where('ciudad_id', $request->city_id);
            });
        }
        
        if ($request->filled('ubicacion_id')) {
            $query->where('ubicacion_id', $request->ubicacion_id);
        }

        if ($request->filled('tipo_activo_id')) {
            $query->where('tipo_activo_id', $request->tipo_activo_id);
        }
        
         if ($request->filled('estado_activo_id')) {
            $query->where('estado_activo_id', $request->estado_activo_id);
        }

        // --- EAV Filtering (Normalized) ---
        // Helper to filter by definition name and value
        $filterEav = function($attributeName, $value) use ($query) {
             $query->whereHas('atributos', function($q) use ($attributeName, $value) {
                $q->whereHas('definicion', function($dq) use ($attributeName) {
                    $dq->where('nombre', $attributeName);
                })->where('valor', $value);
            });
        };

        if ($request->filled('procesador')) $filterEav('Procesador', $request->procesador);
        if ($request->filled('generacion')) $filterEav('Generación', $request->generacion);
        if ($request->filled('ram_capacidad')) $filterEav('Capacidad de Memoria', $request->ram_capacidad);
        if ($request->filled('ram_tipo')) $filterEav('Tipo de Memoria', $request->ram_tipo);
        if ($request->filled('disco_capacidad')) $filterEav('Capacidad de Disco', $request->disco_capacidad);
        if ($request->filled('disco_tipo')) $filterEav('Tipo de Disco', $request->disco_tipo);


        // Helper for distinct values (Normalized)
        $getDistinctValues = function($attributeName) {
            return AtributoActivo::whereHas('definicion', fn($q) => $q->where('nombre', $attributeName))
                ->where('valor', '!=', '')
                ->distinct()
                ->orderBy('valor')
                ->pluck('valor');
        };

        return Inertia::render('Assets/Index', [
            'assets' => $query->latest()->paginate(25)->withQueryString(),
            'tipos' => TipoActivo::orderBy('nombre')->get(),
            'estados' => EstadoActivo::all(),
            'cities' => \App\Models\City::orderBy('nombre')->get(),
            'ubicaciones' => Ubicacion::with('ciudad')->orderBy('nombre')->get(), 
            'propietarios' => Propietario::orderBy('nombre')->get(),
            'procesadores' => $getDistinctValues('Procesador'),
            'generaciones' => $getDistinctValues('Generación'),
            'ram_capacidades' => $getDistinctValues('Capacidad de Memoria'),
            'ram_tipos' => $getDistinctValues('Tipo de Memoria'),
            'disco_capacidades' => $getDistinctValues('Capacidad de Disco'),
            'disco_tipos' => $getDistinctValues('Tipo de Disco'),
            'filters' => $request->only(['search', 'city_id', 'ubicacion_id', 'tipo_activo_id', 'estado_activo_id', 'procesador', 'generacion', 'ram_capacidad', 'ram_tipo', 'disco_capacidad', 'disco_tipo']),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Assets/Create', [
            'tipos_activo' => TipoActivo::orderBy('nombre')->get(),
            'marcas' => Marca::orderBy('nombre')->get(),
            'estados_activo' => EstadoActivo::all(), // pre-fill id=1 (activo) in frontend
            'niveles_criticidad' => NivelCriticidad::orderBy('nivel_numerico')->get(),
            'propietarios' => Propietario::all(),
            'ubicaciones' => Ubicacion::with('ciudad')->orderBy('nombre')->get(),
            'branch_types' => TipoSucursal::all(), // Needed for QuickAdd Location
            'cities' => \App\Models\City::orderBy('nombre')->get(), 
            'puntos_red' => PuntoRed::with('ubicacion')->get(),
            // Models will be loaded via API based on brand/type selection
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // General
            'codigo_activo' => 'required|string|unique:activos,codigo_activo',
            'numero_serie' => 'required|string|unique:activos,numero_serie',
            'tipo_activo_id' => 'required|exists:tipos_activo,id',
            'marca_id' => 'nullable|exists:marcas,id', 
            'modelo_id' => 'required|exists:modelos,id',
            'estado_activo_id' => 'required|exists:estados_activo,id',
            'criticidad_id' => 'required|exists:niveles_criticidad,id',
            'propietario_id' => 'required|exists:propietarios,id',
            
            // Ubicación / Custodia
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            'usuario_responsable_id' => 'nullable|exists:users,id',
            
            // Técnico (EAV)
            'atributos' => 'nullable|array',
            
            // Red
            'ip_address' => 'nullable|ip',
            'mac_ethernet' => 'nullable|string', 
            'mac_wifi' => 'nullable|string',
            'punto_red_id' => 'nullable|exists:puntos_red,id',
            
            // Financiero
            'fecha_adquisicion' => 'nullable|date',
            'valor_adquisicion' => 'nullable|numeric',
            'vida_util_anios' => 'nullable|integer',
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Crear Activo Core
            $activo = Activo::create([
                'codigo_activo' => $validated['codigo_activo'],
                'numero_serie' => $validated['numero_serie'],
                'tipo_activo_id' => $validated['tipo_activo_id'],
                'modelo_id' => $validated['modelo_id'], 
                'estado_activo_id' => $validated['estado_activo_id'],
                'criticidad_id' => $validated['criticidad_id'],
                'propietario_id' => $validated['propietario_id'],
                'ubicacion_id' => $validated['ubicacion_id'],
                'fecha_adquisicion' => $validated['fecha_adquisicion'] ?? null,
                'valor_adquisicion' => $validated['valor_adquisicion'] ?? 0,
                'vida_util_anios' => $validated['vida_util_anios'] ?? null,
            ]);

            // 2. Guardar Atributos EAV (Normalized)
            if (!empty($validated['atributos'])) {
                foreach ($validated['atributos'] as $nombre => $valor) {
                    if ($valor !== null) {
                       // Find Definition ID
                       // Find Definition ID via many-to-many relation
                       $definition = TipoActivo::findOrFail($validated['tipo_activo_id'])
                            ->definitions()
                            ->where('nombre', $nombre)
                            ->first();

                       if ($definition) {
                            AtributoActivo::create([
                                'activo_id' => $activo->id,
                                'definicion_atributo_id' => $definition->id,
                                'valor' => (string) $valor
                            ]);
                       }
                    }
                }
            }

            // 3. Configuración de Red (Si aplica)
            if (!empty($validated['ip_address']) || !empty($validated['mac_ethernet']) || !empty($validated['punto_red_id'])) {
                AsignacionRed::create([
                    'activo_id' => $activo->id,
                    'punto_red_id' => $validated['punto_red_id'] ?? null,
                    'ip_address' => $validated['ip_address'] ?? null,
                    'mac_ethernet' => $validated['mac_ethernet'] ?? null,
                    'mac_wifi' => $validated['mac_wifi'] ?? null,
                    'fecha_asignacion' => now(),
                    'es_actual' => true,
                ]);
            }

            // 4. Asignación Inicial (Custodia)
            if (!empty($validated['usuario_responsable_id'])) {
                Asignacion::create([
                   'activo_id' => $activo->id,
                   'usuario_id' => $validated['usuario_responsable_id'],
                   'fecha_asignacion' => now(),
                   'es_actual' => true,
                ]);
            }
        });

        return redirect()->route('assets.show', $validated['codigo_activo'])->with('success', 'Activo CMDB registrado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Allow generic lookup by ID or Code
        $activo = Activo::with([
            'tipoActivo', 
            'modelo.marca', 
            'ubicacion.ciudad',
            'atributos.definicion', // Eager load definition
            'nivelCriticidad',
            'propietario',
            'estadoActivo',
            'mantenimientos.proveedor',
            'assignments.usuario', // For History
            'softwareInstallations.license', // Phase 24
            'softwareInstallations.registrador',
            'softwareLogs.performer',
            'networkAssignment.puntoRed'
        ])->where('id', $id)->orWhere('codigo_activo', $id)->firstOrFail();

        $availableLicenses = \App\Models\SoftwareLicense::whereColumn('seats_used', '<', 'seats_total')
            ->orderBy('nombre')
            ->get();

        return Inertia::render('Assets/Show', [
            'asset' => $activo,
            'availableLicenses' => $availableLicenses
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Activo $asset)
    {
        $asset->load(['atributos.definicion', 'modelo']); // Load EAV and relations

        return Inertia::render('Assets/Edit', [
            'asset' => $asset,
            'tipos_activo' => TipoActivo::orderBy('nombre')->get(),
            'marcas' => Marca::orderBy('nombre')->get(),
            'modelos' => $asset->modelo ? Modelo::where('marca_id', $asset->modelo->marca_id)->get() : [], 
            'estados_activo' => EstadoActivo::all(), 
            'niveles_criticidad' => NivelCriticidad::orderBy('nivel_numerico')->get(),
            'propietarios' => Propietario::all(),
            'ubicaciones' => Ubicacion::with('ciudad')->orderBy('nombre')->get(),
            'branch_types' => TipoSucursal::all(),
            'cities' => \App\Models\City::orderBy('nombre')->get(),
            'current_network' => AsignacionRed::where('activo_id', $asset->id)->where('es_actual', true)->first(),
            'current_assignment' => Asignacion::where('activo_id', $asset->id)->where('es_actual', true)->first(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Activo $activo)
    {
         $validated = $request->validate([
            // General
            'codigo_activo' => 'required|string|unique:activos,codigo_activo,'.$activo->id,
            'numero_serie' => 'required|string|unique:activos,numero_serie,'.$activo->id,
            'tipo_activo_id' => 'required|exists:tipos_activo,id',
            'modelo_id' => 'required|exists:modelos,id',
            'estado_activo_id' => 'required|exists:estados_activo,id',
            'criticidad_id' => 'required|exists:niveles_criticidad,id',
            'propietario_id' => 'required|exists:propietarios,id',
            'ubicacion_id' => 'required|exists:ubicaciones,id',
            
            // EAV
            'atributos' => 'nullable|array',
            
            // Financiero
            'fecha_adquisicion' => 'nullable|date',
            'valor_adquisicion' => 'nullable|numeric',
        ]);
        
        DB::transaction(function () use ($validated, $activo) {
             $activo->update([
                'codigo_activo' => $validated['codigo_activo'],
                'numero_serie' => $validated['numero_serie'],
                'tipo_activo_id' => $validated['tipo_activo_id'],
                'modelo_id' => $validated['modelo_id'],
                'estado_activo_id' => $validated['estado_activo_id'],
                'criticidad_id' => $validated['criticidad_id'],
                'propietario_id' => $validated['propietario_id'],
                'ubicacion_id' => $validated['ubicacion_id'],
                'fecha_adquisicion' => $validated['fecha_adquisicion'] ?? $activo->fecha_adquisicion,
                'valor_adquisicion' => $validated['valor_adquisicion'] ?? $activo->valor_adquisicion,
             ]);
             
             // Update EAV (Normalized)
             if (!empty($validated['atributos'])) {
                 foreach ($validated['atributos'] as $nombre => $valor) {
                    // Find Definition ID via many-to-many relation
                    $definition = TipoActivo::findOrFail($validated['tipo_activo_id'])
                        ->definitions()
                        ->where('nombre', $nombre)
                        ->first();
                        
                    if ($definition) {
                        AtributoActivo::updateOrCreate(
                            ['activo_id' => $activo->id, 'definicion_atributo_id' => $definition->id],
                            ['valor' => (string) $valor]
                        );
                    }
                 }
             }
        });

        return redirect()->route('assets.show', $activo->id)->with('success', 'Activo actualizado.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Activo $activo)
    {
        $activo->delete();
        return redirect()->route('assets.index')->with('success', 'Activo eliminado.');
    }
    
    // API Helper
    public function getModels(Request $request, $marcaId) {
        return Modelo::where('marca_id', $marcaId)->orderBy('nombre')->get();
    }

    // UPDATED to use new model (Centralized)
    public function getAttributes(Request $request, $typeId) {
        return TipoActivo::findOrFail($typeId)->definitions()->orderBy('orden')->get();
    }

    public function export(Request $request, $format)
    {
        $query = Activo::with(['tipoActivo', 'modelo.marca', 'ubicacion.ciudad', 'estadoActivo', 'atributos.definicion']);

        // --- Apply Filters (Sync with index) ---
         if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_activo', 'like', "%{$search}%")
                    ->orWhere('numero_serie', 'like', "%{$search}%")
                    ->orWhereHas('modelo', function ($mq) use ($search) {
                        $mq->where('nombre', 'like', "%{$search}%")
                            ->orWhereHas('marca', function ($brq) use ($search) {
                                $brq->where('nombre', 'like', "%{$search}%");
                            });
                    });
            });
        }
        
        // ... (Other filters are standard FKs)
        if ($request->filled('city_id')) $query->whereHas('ubicacion', fn($q) => $q->where('ciudad_id', $request->city_id));
        if ($request->filled('ubicacion_id')) $query->where('ubicacion_id', $request->ubicacion_id);
        if ($request->filled('tipo_activo_id')) $query->where('tipo_activo_id', $request->tipo_activo_id);
        if ($request->filled('estado_activo_id')) $query->where('estado_activo_id', $request->estado_activo_id);
        if ($request->filled('propietario_id')) $query->where('propietario_id', $request->propietario_id);

        // --- EAV Filtering (Normalized) ---
        $filterEav = function($attributeName, $value) use ($query) {
             $query->whereHas('atributos', function($q) use ($attributeName, $value) {
                $q->whereHas('definicion', function($dq) use ($attributeName) {
                    $dq->where('nombre', $attributeName);
                })->where('valor', $value);
            });
        };

        if ($request->filled('procesador')) $filterEav('Procesador', $request->procesador);
        if ($request->filled('generacion')) $filterEav('Generación', $request->generacion);
        if ($request->filled('ram_capacidad')) $filterEav('Capacidad de Memoria', $request->ram_capacidad);
        if ($request->filled('ram_tipo')) $filterEav('Tipo de Memoria', $request->ram_tipo);
        if ($request->filled('disco_capacidad')) $filterEav('Capacidad de Disco', $request->disco_capacidad);
        if ($request->filled('disco_tipo')) $filterEav('Tipo de Disco', $request->disco_tipo);

        $assets = $query->get(); // Removed latest() to preserve the complex ordering defined above

        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.inventory', [
                'assets' => $assets,
                'fecha' => now()->format('d/m/Y H:i')
            ])->setPaper('letter', 'landscape');

            return $pdf->download('Inventario_Activos_' . now()->format('Ymd_His') . '.pdf');
        }

        // --- CSV Export ---
        $filename = 'Inventario_Activos_' . now()->format('Ymd_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($assets) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, ['Ciudad', 'Tipo Sucursal', 'Sucursal', 'Código', 'Tipo Activo', 'Marca', 'Modelo', 'Serie', 'Estado', 'Procesador', 'Generación', 'Capacidad RAM', 'Tipo RAM', 'Capacidad Disco', 'Tipo Disco']);

            foreach ($assets as $asset) {
                // Helper to safely get value by definition name from loaded relation
                $getAttr = function($name) use ($asset) {
                    // Because 'atributos' is a collection of AtributoActivo, and each has 'definicion'.
                    // We must find the one where definicion->nombre === $name.
                    // Accessor 'nombre' works but is slower to iterate?
                    // Let's iterate.
                    $found = $asset->atributos->first(function($attr) use ($name) {
                        return $attr->definicion && $attr->definicion->nombre === $name;
                    });
                    return $found ? $found->valor : '-';
                };

                fputcsv($file, [
                    $asset->ubicacion->ciudad->nombre ?? '-',
                    $asset->ubicacion->tipo->nombre ?? '-',
                    $asset->ubicacion->nombre ?? '-',
                    $asset->codigo_activo,
                    $asset->tipoActivo->nombre,
                    $asset->modelo->marca->nombre ?? '-',
                    $asset->modelo->nombre ?? '-',
                    $asset->numero_serie,
                    $asset->estadoActivo->nombre,
                    $getAttr('Procesador'),
                    $getAttr('Generación'),
                    $getAttr('Capacidad de Memoria'),
                    $getAttr('Tipo de Memoria'),
                    $getAttr('Capacidad de Disco'),
                    $getAttr('Tipo de Disco'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
