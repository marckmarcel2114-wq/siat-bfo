<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Rename branch_types to tipos_sucursal if it exists
        if (Schema::hasTable('branch_types')) {
            Schema::rename('branch_types', 'tipos_sucursal');
        }

        if (Schema::hasTable('tipos_sucursal')) {
            Schema::table('tipos_sucursal', function (Blueprint $table) {
                if (Schema::hasColumn('tipos_sucursal', 'name')) {
                    $table->renameColumn('name', 'nombre');
                }
                if (Schema::hasColumn('tipos_sucursal', 'description')) {
                    $table->renameColumn('description', 'descripcion');
                }
            });

            // Set strict ordering as requested by user
            $order = [
                'Sucursal' => 1,
                'Agencia' => 2,
                'ATM' => 3,
                'Oficina Externa' => 4,
                'PAF' => 5,
            ];

            foreach ($order as $name => $sort) {
                DB::table('tipos_sucursal')->where('nombre', $name)->update(['sort_order' => $sort]);
            }
        }

        // 2. Consolidate all points of attention into 'ubicaciones'
        $sucursalTypeId = DB::table('tipos_sucursal')->where('nombre', 'Sucursal')->value('id');
        $agenciaTypeId = DB::table('tipos_sucursal')->where('nombre', 'Agencia')->value('id');
        $atmTypeId = DB::table('tipos_sucursal')->where('nombre', 'ATM')->value('id');

        if (Schema::hasTable('sucursales')) {
            $branches = DB::table('sucursales')->get();
            foreach ($branches as $b) {
                // Determine type based on name or previous type if it existed
                $typeId = $agenciaTypeId;
                if (stripos($b->nombre, 'Sucursal') !== false) {
                    $typeId = $sucursalTypeId;
                }

                DB::table('ubicaciones')->updateOrInsert(
                    ['nombre' => $b->nombre, 'ciudad_id' => $b->ciudad_id],
                    [
                        'tipo_ubicacion_id' => $typeId,
                        'codigo_ubicacion' => $b->codigo ?? null,
                        'created_at' => $b->created_at,
                        'updated_at' => $b->updated_at,
                    ]
                );
            }
        }

        if (Schema::hasTable('atms')) {
            $atms = DB::table('atms')->get();
            foreach ($atms as $a) {
                DB::table('ubicaciones')->updateOrInsert(
                    ['nombre' => $a->nombre, 'ciudad_id' => $a->ciudad_id],
                    [
                        'tipo_ubicacion_id' => $atmTypeId,
                        'codigo_ubicacion' => $a->code ?? null,
                        'created_at' => $a->created_at ?? now(),
                        'updated_at' => $a->updated_at ?? now(),
                    ]
                );
            }
        }

        // 3. Cleanup redundant tables and fix foreign keys
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('sucursales');
        Schema::dropIfExists('atms');
        Schema::dropIfExists('tipos_ubicacion');
        Schema::enableForeignKeyConstraints();

        // 4. Update 'ubicaciones' structure to point to 'tipos_sucursal'
        if (Schema::hasTable('ubicaciones')) {
            Schema::table('ubicaciones', function (Blueprint $table) {
                if (Schema::hasColumn('ubicaciones', 'tipo_ubicacion_id')) {
                    $table->renameColumn('tipo_ubicacion_id', 'tipo_sucursal_id');
                }
            });

            // Set foreign key
            Schema::table('ubicaciones', function (Blueprint $table) {
                 if (Schema::hasColumn('ubicaciones', 'tipo_sucursal_id')) {
                    $table->foreign('tipo_sucursal_id')->references('id')->on('tipos_sucursal');
                 }
            });
        }
    }

    public function down(): void
    {
        // Not implemented for this cleanup
    }
};
