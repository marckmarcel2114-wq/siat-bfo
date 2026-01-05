<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Configs/Index', [
            'settings' => Setting::all()
        ]);
    }

    public function update(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'value' => 'nullable|string'
        ]);

        $setting->update($validated);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}
