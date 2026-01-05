<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Legacy Types & Locations (Source for migration)
        $this->call([
            TypeSeeder::class, // branch_types
            LocationSeeder::class, // branches / cities
        ]);

        // 2. Users (Admin & Test)
        if (User::count() == 0) {
             User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@siat.boo',
                'password' => bcrypt('password'),
            ]);
            User::factory(10)->create();
        }

        // 3. CMDB Catalogs (Includes TipoUbicacion)
        $this->call(CatalogSeeder::class);

        // 4. Migration to Unified Ubicaciones
        // Needs branches (from step 1) and tipos_ubicacion (from step 3)
        $this->call(MigrateBranchesAtmsToUbicaciones::class);
        
        // 5. Software & Assets
        $this->call([
            SoftwareSeeder::class,
            TestHistorySeeder::class // Needs Ubicaciones and Users
        ]);
    }
}

