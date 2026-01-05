<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\City;
use App\Models\Branch;
use App\Models\JobTitle;
use App\Models\UserJobHistory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::with(['city', 'jobTitle', 'branch'])->orderBy('last_name')->get(),
            'cities' => City::orderBy('nombre')->get(),
            'branches' => Branch::with('ciudad')->orderBy('nombre')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Create', [
            'cities' => City::all(),
            'branches' => Branch::all(),
            'roles' => ['admin', 'city_admin', 'user'],
            'jobTitles' => JobTitle::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'city_id' => 'nullable|exists:ciudades,id',
            'branch_id' => 'nullable|exists:sucursales,id',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
        ]);

        // Logic to get position name from ID if provided
        $positionName = $validated['position'];
        if ($request->job_title_id) {
            $title = JobTitle::find($request->job_title_id);
            if ($title) {
                $positionName = $title->name;
            }
        }

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'city_id' => $validated['city_id'],
            'branch_id' => $validated['branch_id'] ?? null,
            'position' => $positionName,
            'job_title_id' => $request->job_title_id,
            'phone' => $validated['phone'],
            'is_active' => true,
            'hire_date' => $validated['hire_date'] ?? null,
            'termination_date' => $validated['termination_date'] ?? null,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return Inertia::render('Users/Edit', [
            'user' => $user->load(['city', 'branch', 'jobHistory.jobTitle', 'jobHistory.city', 'jobHistory.branch']),
            'cities' => City::all(),
            'branches' => Branch::all(),
            'roles' => ['admin', 'city_admin', 'user'],
            'jobTitles' => JobTitle::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string',
            'city_id' => 'nullable|exists:ciudades,id',
            'branch_id' => 'nullable|exists:sucursales,id',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
            'job_title_new_name' => 'nullable|string|max:255',
            'hire_date' => 'nullable|date',
            'termination_date' => 'nullable|date',
        ]);

        // Handle Dynamic Job Title Creation
        if ($request->filled('job_title_new_name')) {
            $createdTitle = JobTitle::firstOrCreate([
                'name' => mb_strtoupper($request->job_title_new_name)
            ]);
            $request->merge(['job_title_id' => $createdTitle->id]);
        }

        // Prepare attributes for fill
        $attributes = [
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'city_id' => $validated['city_id'] ?? null,
            'branch_id' => $validated['branch_id'] ?? null,
            'position' => $validated['position'] ?? $user->position,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'job_title_id' => $request->job_title_id, // Use request value which might have been merged
            'hire_date' => $validated['hire_date'] ?? null,
            'termination_date' => $validated['termination_date'] ?? null,
        ];

        // Set transient properties for Observer
        $user->historyAssignmentType = $request->input('assignment_type', 'permanent');
        $user->historyNote = $request->input('notes') ?: 'Actualización de asignación';

        $user->fill($attributes);

        // Sync position name if job title changed
        if ($request->job_title_id && $request->job_title_id != $user->job_title_id) {
             $title = JobTitle::find($request->job_title_id);
             if ($title) {
                 $attributes['position'] = $title->name;
             }
        }
        
        // Re-fill attributes (position might have changed)
        $user->fill($attributes);
        $user->save();


        
        if ($request->filled('password')) {
             $request->validate(['password' => 'string|min:8']);
             $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuario eliminado correctamente.');
    }

    /**
     * Remove the specified job history from storage.
     */
    public function destroyJobHistory(User $user, UserJobHistory $history)
    {
        // Ensure the history belongs to the user
        if ($history->user_id !== $user->id) {
            abort(403);
        }

        $history->delete();

        return back()->with('success', 'Historial eliminado correctamente.');
    }

    public function endTemporary(Request $request, User $user)
    {
        $validated = $request->validate([
            'return_date' => 'required|date',
            'job_title_id' => 'required|exists:job_titles,id',
            'city_id' => 'nullable|exists:ciudades,id',
            'branch_id' => 'nullable|exists:sucursales,id',
            'notes' => 'nullable|string',
        ]);

        // 1. Close current temporary assignment
        $currentHistory = UserJobHistory::where('user_id', $user->id)
            ->whereNull('end_date')
            ->where('assignment_type', 'temporary')
            ->first();

        // If no active temporary assignment found, we can still proceed but it's logically weird.
        // We'll just assume the user wants to force a new state.
        if ($currentHistory) {
            $currentHistory->update(['end_date' => $validated['return_date']]);
        } else {
             // Fallback: Check if there is ANY active history and close it
             UserJobHistory::where('user_id', $user->id)
                ->whereNull('end_date')
                ->update(['end_date' => $validated['return_date']]);
        }

        // 2. Create new permanent record
        UserJobHistory::create([
            'user_id' => $user->id,
            'cargo_id' => $validated['job_title_id'],
            'city_id' => $validated['city_id'],
            'branch_id' => $validated['branch_id'],
            'assignment_type' => 'permanent',
            'start_date' => $validated['return_date'],
            'notes' => $validated['notes'] ?: 'Retorno de asignación temporal',
        ]);

        // 3. Update User
        $user->update([
            'job_title_id' => $validated['job_title_id'],
            'city_id' => $validated['city_id'],
            'branch_id' => $validated['branch_id'],
        ]);

        // Sync position name
        $title = JobTitle::find($validated['job_title_id']);
        if ($title) {
            $user->update(['position' => $title->name]);
        }

        return back()->with('success', 'Interinato finalizado correctamente.');
    }

    public function updateJobHistory(Request $request, User $user, UserJobHistory $history)
    {
        if ($history->user_id !== $user->id) {
            abort(403);
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'notes'      => 'nullable|string',
        ]);

        $history->update($validated);

        return redirect()->back()->with('success', 'Historial actualizado correctamente.');
    }
}
