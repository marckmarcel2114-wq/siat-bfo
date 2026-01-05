<?php

namespace App\Http\Controllers;

use App\Models\JobTitle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class JobTitleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('JobTitles/Index', [
            'jobTitles' => JobTitle::orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('JobTitles/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Flexible input handling for Quick Add compatibility
        if (!$request->has('name') && $request->has('nombre')) {
            $request->merge(['name' => $request->input('nombre')]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_titles,name',
            'nombre' => 'nullable|string|max:255', // Just to prevent 422 if only nombre is sent before merging
        ]);

        $jobTitle = JobTitle::create([
            'name' => mb_strtoupper($validated['name']),
        ]);

        if ($request->wantsJson()) {
            return response()->json($jobTitle, 201);
        }

        return redirect()->route('job-titles.index')->with('success', 'Cargo creado correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobTitle $jobTitle)
    {
        return Inertia::render('JobTitles/Edit', [
            'jobTitle' => $jobTitle,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobTitle $jobTitle)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:job_titles,name,' . $jobTitle->id,
        ]);

        $jobTitle->update([
            'name' => mb_strtoupper($validated['name']),
        ]);

        return redirect()->route('job-titles.index')->with('success', 'Cargo actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobTitle $jobTitle)
    {
        if ($jobTitle->users()->exists()) {
            return back()->with('error', 'No se puede eliminar el cargo porque tiene usuarios asignados.');
        }

        $jobTitle->delete();

        return redirect()->route('job-titles.index')->with('success', 'Cargo eliminado correctamente.');
    }
}
