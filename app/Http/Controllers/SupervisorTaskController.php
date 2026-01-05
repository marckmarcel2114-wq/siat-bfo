<?php

namespace App\Http\Controllers;

use App\Models\TareaSupervisor;
use App\Models\EjecucionTarea;
use App\Models\User;
use App\Models\Ubicacion; // For assigning tasks to locations if needed
// use App\Models\EstadoTarea; // Assuming exist or using IDs
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SupervisorTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        
        // 1. Tareas creadas por mí (Supervisor)
        // 2. Tareas asignadas a mí (Ejecutor) -> Logic: if I am in 'ejecuciones_tarea'
        
        $myCreatedTasks = TareaSupervisor::where('supervisor_id', $user->id)
            ->withCount('ejecuciones')
            ->orderByDesc('created_at')
            ->get();
            
        $myAssignedTasks = EjecucionTarea::where('admin_ciudad_id', $user->id)
            ->with(['tarea.supervisor', 'ubicacion']) // Show task details and location
            ->orderByDesc('created_at')
            ->get();

        return Inertia::render('Tasks/Index', [
            'createdTasks' => $myCreatedTasks,
            'assignedTasks' => $myAssignedTasks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // We need users (potential executors) and locations
        return Inertia::render('Tasks/Create', [
            'users' => User::orderBy('name')->get(), // Filter by role 'admin_sucursal' later?
            'locations' => Ubicacion::with('ciudad')->orderBy('nombre')->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'fecha_limite' => 'required|date',
            'assignments' => 'required|array|min:1', // Array of { user_id, ubicacion_id }
            'assignments.*.user_id' => 'required|exists:users,id',
            'assignments.*.ubicacion_id' => 'nullable|exists:ubicaciones,id'
        ]);

        DB::transaction(function () use ($validated) {
            // 1. Create Parent Task
            $tarea = TareaSupervisor::create([
                'supervisor_id' => Auth::id(),
                'titulo' => $validated['titulo'],
                'descripcion' => $validated['descripcion'],
                'fecha_asignacion' => now(),
                'fecha_limite' => $validated['fecha_limite'],
                'estado_tarea_id' => 1 // 'Pendiente' or 'Asignada'
            ]);

            // 2. Create Executions (Child Tasks)
            foreach ($validated['assignments'] as $assign) {
                EjecucionTarea::create([
                    'tarea_id' => $tarea->id,
                    'admin_ciudad_id' => $assign['user_id'],
                    'ubicacion_id' => $assign['ubicacion_id'] ?? null,
                    'estado_ejecucion_id' => 1 // 'Pendiente'
                ]);
            }
        });

        return redirect()->route('tasks.index')->with('success', 'Tarea creada y asignada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Show details + chat + evidence
        // We need to differentiate if viewing Parent Task (Supervisor) or Execution (Technician)
        // Using ID usually implies TareaSupervisor ID or EjecucionTarea ID.
        // Let's assume ID is TareaSupervisor for Supervisor view, OR we handle unique Execution route.
        // Simpler: /tasks/{id} shows TareaSupervisor. Technician sees only his part?
        // Let's make /tasks/{id} show the Parent Task details.
        
        $task = TareaSupervisor::with(['supervisor', 'ejecuciones.adminCiudad', 'ejecuciones.ubicacion', 'ejecuciones.estado'])
            ->findOrFail($id);
            
        return Inertia::render('Tasks/Show', [
            'task' => $task
        ]);
    }
    
    /**
     * Update execution status and upload evidence.
     */
    public function updateExecution(Request $request, $id)
    {
        $validated = $request->validate([
            'observaciones' => 'nullable|string',
            'estado_ejecucion_id' => 'required|exists:estados_ejecucion,id', // Assuming seeded: 1=Pendiente, 2=Finalizado
            'evidencia' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120' // Max 5MB
        ]);

        $execution = EjecucionTarea::findOrFail($id);
        
        // Security check: Only assigned admin or supervisor can update? 
        // For now allowing if Auth user is the admin_ciudad_id
        if (Auth::id() !== $execution->admin_ciudad_id && Auth::id() !== $execution->tarea->supervisor_id) {
            abort(403, 'No tienes permiso para actualizar esta tarea.');
        }

        if ($request->hasFile('evidencia')) {
            $path = $request->file('evidencia')->store('evidencias_tareas', 'public');
            $execution->acta_ejecucion_path = $path;
        }

        $execution->update([
            'observaciones' => $validated['observaciones'],
            'estado_ejecucion_id' => $validated['estado_ejecucion_id'],
            'fecha_ejecucion' => now()
        ]);

        return back()->with('success', 'Avance registrado correctamente.');
    }
}
