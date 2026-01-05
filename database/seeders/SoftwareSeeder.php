<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SoftwareLicense;
use App\Models\Proveedor;

class SoftwareSeeder extends Seeder
{
    public function run(): void
    {
        $prov = Proveedor::first(); // Grab any provider

        // 1. Office
        SoftwareLicense::create([
            'nombre' => 'Microsoft Office 2021 Pro',
            'key' => 'XC90-DF89-DSDS-9090-JDKL',
            'tipo' => 'Volume',
            'seats_total' => 50,
            'seats_used' => 0,
            'proveedor_id' => $prov ? $prov->id : null,
            'fecha_expiracion' => null // Perpetual
        ]);

        // 2. Windows 11
        SoftwareLicense::create([
            'nombre' => 'Windows 11 Pro',
            'key' => 'OEM-PRE-INSTALLED',
            'tipo' => 'OEM',
            'seats_total' => 100, // Virtual limit
            'seats_used' => 0,
            'proveedor_id' => $prov ? $prov->id : null, 
        ]);

        // 3. Adobe
        SoftwareLicense::create([
            'nombre' => 'Adobe Creative Cloud',
            'key' => 'SUB-2025-USER-KEY',
            'tipo' => 'Subscription',
            'seats_total' => 5,
            'seats_used' => 0,
            'fecha_expiracion' => now()->addYear()
        ]);
        
        // 4. Antivirus
        SoftwareLicense::create([
            'nombre' => 'ESET Endpoint Security',
            'key' => 'ESET-CORP-LIC-001',
            'tipo' => 'Subscription',
            'seats_total' => 200,
            'seats_used' => 0,
            'fecha_expiracion' => now()->addMonths(6)
        ]);
    }
}
