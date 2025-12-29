<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

use App\Http\Controllers\UserController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\AssetAssignmentController;
use App\Http\Controllers\ProcurementController;

Route::get('/test', fn () => Inertia::render('Test'));



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::resource('cities', CityController::class);
    Route::resource('branches', BranchController::class);
    
    Route::resource('assets', AssetController::class);
    Route::resource('asset-types', App\Http\Controllers\AssetTypeController::class);
    Route::resource('asset-assignments', AssetAssignmentController::class);
    Route::resource('procurements', ProcurementController::class);
    Route::resource('atms', App\Http\Controllers\AtmController::class);
    Route::resource('branch-types', App\Http\Controllers\BranchTypeController::class);
});


require __DIR__.'/settings.php';

