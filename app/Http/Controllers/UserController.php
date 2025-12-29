<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\City;
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
            'users' => User::with('city')->latest()->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Users/Create', [
            'cities' => City::all(),
            'roles' => ['admin', 'city_admin', 'user'],
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string',
            'city_id' => 'nullable|exists:cities,id',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);

        User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'city_id' => $validated['city_id'],
            'position' => $validated['position'],
            'phone' => $validated['phone'],
            'is_active' => true,
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
            'user' => $user->load('city'),
            'cities' => City::all(),
            'roles' => ['admin', 'city_admin', 'user'],
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
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string',
            'city_id' => 'nullable|exists:cities,id',
            'position' => 'nullable|string',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $user->update([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'city_id' => $validated['city_id'],
            'position' => $validated['position'],
            'phone' => $validated['phone'],
            'is_active' => $validated['is_active'],
        ]);
        
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
}
