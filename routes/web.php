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
use App\Http\Controllers\AssetStatusController;
use App\Http\Controllers\CriticalityController;
use App\Http\Controllers\AssetMaintenanceController;
use App\Http\Controllers\AssetTypeController;

Route::get('/test', fn () => Inertia::render('Test'));



Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class);
    Route::post('/users/{user}/end-temporary', [UserController::class, 'endTemporary'])->name('users.end-temporary');
    Route::put('/users/{user}/job-history/{history}', [UserController::class, 'updateJobHistory'])->name('users.job-history.update');
    Route::delete('/users/{user}/job-history/{history}', [UserController::class, 'destroyJobHistory'])->name('users.job-history.destroy');
    Route::resource('cities', CityController::class);
    Route::resource('branches', BranchController::class);
    
    
    Route::get('/assets/export/{format}', [AssetController::class, 'export'])->name('assets.export');
    Route::resource('assets', AssetController::class);
    Route::post('/asset-types/{asset_type}/definitions', [App\Http\Controllers\AssetTypeController::class, 'storeDefinition'])->name('asset-types.definitions.store');
    Route::put('/asset-types/{asset_type}/definitions/{definition}', [App\Http\Controllers\AssetTypeController::class, 'updateDefinition'])->name('asset-types.definitions.update');
    Route::delete('/asset-types/{asset_type}/definitions/{definition}', [App\Http\Controllers\AssetTypeController::class, 'destroyDefinition'])->name('asset-types.definitions.destroy');
    Route::resource('asset-types', AssetTypeController::class);
    Route::resource('asset-status', AssetStatusController::class); // Added
    Route::resource('criticality', CriticalityController::class); // Added
    // Assignments Workflow
    Route::get('/assignments/create', [AssetAssignmentController::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [AssetAssignmentController::class, 'store'])->name('assignments.store');
    Route::get('/assignments/{asset}/return', [AssetAssignmentController::class, 'returnForm'])->name('assignments.return');
    Route::post('/assignments/{asignacion}/return', [AssetAssignmentController::class, 'processReturn'])->name('assignments.process_return');
    Route::get('/assignments/{id}/download/{type}', [AssetAssignmentController::class, 'downloadActa'])->name('assignments.download');

    // Maintenance Workflow
    Route::get('/maintenance/create', [AssetMaintenanceController::class, 'create'])->name('maintenances.create');
    Route::post('/maintenance', [AssetMaintenanceController::class, 'store'])->name('maintenances.store');
    Route::get('/maintenance/{id}/finish', [AssetMaintenanceController::class, 'finishForm'])->name('maintenances.finish');
    Route::put('/maintenance/{id}', [AssetMaintenanceController::class, 'update'])->name('maintenances.update');
    Route::resource('procurements', ProcurementController::class);
    Route::resource('atms', App\Http\Controllers\AtmController::class);
    Route::resource('branch-types', App\Http\Controllers\TipoSucursalController::class);
    
    // Supervisor Tasks
    Route::resource('tasks', App\Http\Controllers\SupervisorTaskController::class);
    Route::post('/tasks/execution/{id}', [App\Http\Controllers\SupervisorTaskController::class, 'updateExecution'])->name('tasks.execution.update');
    
    // Specs Management (Hardware)
    Route::get('/assets/{asset}/specs', [App\Http\Controllers\AssetSpecsController::class, 'edit'])->name('assets.specs.edit');
    Route::put('/assets/{asset}/specs', [App\Http\Controllers\AssetSpecsController::class, 'update'])->name('assets.specs.update');

    // Network Management
    Route::get('/assets/{asset}/network', [App\Http\Controllers\AssetNetworkController::class, 'edit'])->name('assets.network.edit');
    Route::put('/assets/{asset}/network', [App\Http\Controllers\AssetNetworkController::class, 'update'])->name('assets.network.update');

    // DEV: Import Assets Route & Migration
    Route::get('/dev/run-tasks', function () {
    set_time_limit(300); // Allow 5 minutes
    try {
        // 1. Run Migrations
        Illuminate\Support\Facades\Artisan::call('migrate');
        $output = Illuminate\Support\Facades\Artisan::output();
        
        Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'AssetImportSeeder']);
        $output .= "\n" . Illuminate\Support\Facades\Artisan::output();
        
        return '<pre>' . $output . '</pre><br>Tareas completadas. <a href="/assets">Ir a Activos</a>';
    } catch (\Exception $e) {
        return '<pre>Error: ' . $e->getMessage() . '</pre>';
    }
    });

    // Software Management
    Route::get('/assets/{asset}/software', [App\Http\Controllers\AssetSoftwareController::class, 'edit'])->name('assets.software.edit');
    Route::put('/assets/{asset}/software', [App\Http\Controllers\AssetSoftwareController::class, 'update'])->name('assets.software.update');
    Route::post('/assets/{asset}/software/log', [App\Http\Controllers\AssetSoftwareController::class, 'logUpdate'])->name('assets.software.log');

    Route::resource('software', App\Http\Controllers\SoftwareController::class);
    Route::post('/software/install', [App\Http\Controllers\SoftwareController::class, 'installOnAsset'])->name('software.install');
    Route::put('/software/install/{id}', [App\Http\Controllers\SoftwareController::class, 'updateInstallation'])->name('software.upgrade');
    Route::delete('/software/install/{id}', [App\Http\Controllers\SoftwareController::class, 'uninstallFromAsset'])->name('software.uninstall');
    
    // API Helpers
    Route::get('/api/marcas/{marca}/modelos', [AssetController::class, 'getModels'])->name('api.models');
    Route::get('/api/types/{type}/attributes', [AssetController::class, 'getAttributes'])->name('api.attributes');
    Route::get('/api/software-catalog/list', [App\Http\Controllers\SoftwareCatalogController::class, 'apiList'])->name('api.software-catalog');

    // Configuration Catalogs
    Route::resource('brands', App\Http\Controllers\BrandController::class);
    Route::resource('models', App\Http\Controllers\ModelController::class);
    Route::resource('owners', App\Http\Controllers\OwnerController::class);
    Route::resource('job-titles', App\Http\Controllers\JobTitleController::class);
    
    // Software Catalog
    Route::resource('software-catalog', App\Http\Controllers\SoftwareCatalogController::class);
    Route::post('software-catalog/{software}/versions', [App\Http\Controllers\SoftwareCatalogController::class, 'storeVersion'])->name('software-catalog.versions.store');
    Route::put('software-catalog/versions/{version}', [App\Http\Controllers\SoftwareCatalogController::class, 'updateVersion'])->name('software-catalog.versions.update');
    Route::delete('software-catalog/versions/{version}', [App\Http\Controllers\SoftwareCatalogController::class, 'destroyVersion'])->name('software-catalog.versions.destroy');

    // System Configurations
    Route::get('configs', [App\Http\Controllers\SettingController::class, 'index'])->name('configs.index');
    Route::put('configs/{setting}', [App\Http\Controllers\SettingController::class, 'update'])->name('configs.update');
});



    
    // Quick Add Endpoints (handled by resource store, but aliases if needed or just use consistent naming)
    // The previous post('/brands', ...) is covered by resource('brands') which includes POST /brands -> store
    
    
require __DIR__.'/settings.php';
