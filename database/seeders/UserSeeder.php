<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\City;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. National Supervisor (Admin of everything)
        User::firstOrCreate(
            ['email' => 'admin@siat.com'],
            [
                'first_name' => 'Supervisor',
                'last_name' => 'Nacional',
                'name' => 'Supervisor Nacional',
                'email' => 'admin@siat.com',
                'password' => Hash::make('password'),
                'role' => 'admin', // Supervisor Nacional
                'is_active' => true,
            ]
        );

        // 2. City Admin (Oruro)
        $oruro = City::where('name', 'Oruro')->first();
        if ($oruro) {
            User::firstOrCreate(
                ['email' => 'admin.oruro@siat.com'],
                [
                    'first_name' => 'Admin',
                    'last_name' => 'Oruro',
                    'name' => 'Admin Oruro',
                    'email' => 'admin.oruro@siat.com',
                    'password' => Hash::make('password'),
                    'role' => 'city_admin',
                    'city_id' => $oruro->id,
                    'is_active' => true,
                ]
            );
        }

        // 3. City Admin (La Paz)
        $lapaz = City::where('name', 'La Paz')->first();
        if ($lapaz) {
            User::firstOrCreate(
                ['email' => 'admin.lapaz@siat.com'],
                [
                    'first_name' => 'Admin',
                    'last_name' => 'La Paz',
                    'name' => 'Admin La Paz',
                    'email' => 'admin.lapaz@siat.com',
                    'password' => Hash::make('password'),
                    'role' => 'city_admin',
                    'city_id' => $lapaz->id,
                    'is_active' => true,
                ]
            );
        }
        
        // 4. Regular User (Responsable)
        User::firstOrCreate(
            ['email' => 'user@siat.com'],
            [
                'first_name' => 'Juan',
                'last_name' => 'Perez',
                'name' => 'Juan Perez',
                'email' => 'user@siat.com',
                'password' => Hash::make('password'),
                'role' => 'user', 
                'position' => 'Cajero',
                'is_active' => true,
            ]
        );
    }
}
