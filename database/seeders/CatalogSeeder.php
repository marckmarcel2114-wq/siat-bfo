<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Marca;
use App\Models\Modelo;
use App\Models\TipoActivo;
use App\Models\EstadoActivo;
use App\Models\NivelCriticidad;
use App\Models\Propietario;
use App\Models\Proveedor;

use App\Models\TipoSucursal;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // 0. Tipos de Sucursal (Consolidated & Ordered)
        $tiposUbi = [
            'Sucursal' => 'Punto de atención principal en ciudades sede.',
            'Agencia' => 'Puntos de atención secundarios y regionales.',
            'ATM' => 'Cajeros automáticos independientes y en puntos externos.',
            'Oficina Externa' => 'Módulos en empresas u otras entidades.',
            'PAF' => 'Puntos Auxiliares Financieros (Corresponsales).',
        ];
        
        $order = 1;
        foreach ($tiposUbi as $nombre => $descripcion) {
            TipoSucursal::updateOrCreate(
                ['nombre' => $nombre],
                [
                    'descripcion' => $descripcion,
                    'sort_order' => $order++,
                    'activo' => true
                ]
            );
        }

        // 1. Tipos de Activo
        $tipos = ['Laptop', 'Desktop', 'Servidor', 'Impresora', 'Monitor', 'Switch', 'Router', 'UPS', 'Tablet'];
        foreach ($tipos as $t) {
            TipoActivo::firstOrCreate(['nombre' => $t]);
        }

        // 2. Marcas
        $marcas = ['Dell', 'HP', 'Lenovo', 'Cisco', 'Epson', 'APC', 'Samsung', 'Apple'];
        foreach ($marcas as $m) {
            Marca::firstOrCreate(['nombre' => $m]);
        }

        // 3. Modelos (Basic Linking)
        $modelsData = [
            'Dell' => ['Latitude 5420', 'OptiPlex 7090', 'PowerEdge R740'],
            'HP' => ['ProBook 450', 'LaserJet Pro M404', 'EliteDesk 800'],
            'Lenovo' => ['ThinkPad T14', 'ThinkCentre M720'],
            'Cisco' => ['Catalyst 2960', 'ISR 4331'],
            'Epson' => ['EcoTank L3150'],
            'APC' => ['Smart-UPS 1500'],
            'Samsung' => ['Galaxy Tab S7'],
            'Apple' => ['MacBook Pro M1']
        ];

        // Cache types
        $laptop = TipoActivo::where('nombre', 'Laptop')->first()->id;
        $desktop = TipoActivo::where('nombre', 'Desktop')->first()->id;
        $server = TipoActivo::where('nombre', 'Servidor')->first()->id;
        $printer = TipoActivo::where('nombre', 'Impresora')->first()->id;
        $tablet = TipoActivo::where('nombre', 'Tablet')->first()->id;
        $ups = TipoActivo::where('nombre', 'UPS')->first()->id;
        $switch = TipoActivo::where('nombre', 'Switch')->first()->id;
        $router = TipoActivo::where('nombre', 'Router')->first()->id;

        foreach ($modelsData as $marcaName => $models) {
            $marca = Marca::where('nombre', $marcaName)->first();
            if ($marca) {
                foreach ($models as $mod) {
                    // Simple heuristic for type
                    $typeId = $laptop; // Default
                    if (str_contains($mod, 'OptiPlex') || str_contains($mod, 'EliteDesk') || str_contains($mod, 'ThinkCentre')) $typeId = $desktop;
                    if (str_contains($mod, 'PowerEdge') || str_contains($mod, 'ISR')) $typeId = $server; // ISR is router actually
                    if (str_contains($mod, 'ISR') || str_contains($mod, 'Catalyst')) $typeId = $switch; // Catalyst is switch
                    if (str_contains($mod, 'ISR')) $typeId = $router; 
                    if (str_contains($mod, 'LaserJet') || str_contains($mod, 'EcoTank')) $typeId = $printer;
                    if (str_contains($mod, 'Tab')) $typeId = $tablet;
                    if (str_contains($mod, 'UPS')) $typeId = $ups;

                    Modelo::firstOrCreate([
                        'nombre' => $mod, 
                        'marca_id' => $marca->id,
                        'tipo_activo_id' => $typeId
                    ]);
                }
            }
        }

        // 4. Estados de Activo
        $estados = [
            ['nombre' => 'Disponible', 'descripcion' => 'Listo para asignar'],
            ['nombre' => 'Asignado', 'descripcion' => 'En custodia de un usuario'],
            ['nombre' => 'En Mantenimiento', 'descripcion' => 'En revisión técnica'],
            ['nombre' => 'Baja', 'descripcion' => 'Retirado del inventario'],
            ['nombre' => 'Robado/Perdido', 'descripcion' => 'No habido']
        ];
        foreach ($estados as $e) {
            // Remove description as it doesn't exist in schema
            EstadoActivo::firstOrCreate(['nombre' => $e['nombre']]);
        }

        // 5. Niveles de Criticidad
        $niveles = [
            ['nombre' => 'Alta', 'nivel_numerico' => 10, 'descripcion' => 'Interrupción crítica para el negocio'],
            ['nombre' => 'Media', 'nivel_numerico' => 5, 'descripcion' => 'Afecta operatividad parcial'],
            ['nombre' => 'Baja', 'nivel_numerico' => 1, 'descripcion' => 'No afecta continuidad']
        ];
        foreach ($niveles as $n) {
             NivelCriticidad::firstOrCreate(
                ['nombre' => $n['nombre']],
                ['nivel_numerico' => $n['nivel_numerico']]
            );
        }

        // 6. Propietarios
        Propietario::firstOrCreate(['nombre' => 'Banco Ficticio S.A.']);
        Propietario::firstOrCreate(['nombre' => 'Leasing Corp.']);

        // 7. Proveedores
        Proveedor::firstOrCreate(
            ['nombre' => 'TechnoSys Bolivia'],
            ['telefono' => '70012345', 'email' => 'ventas@technosys.boo', 'tipo_proveedor' => 'Hardware']
        );
        Proveedor::firstOrCreate(
            ['nombre' => 'InfoNet Soluciones'],
            ['telefono' => '60098765', 'email' => 'soporte@infonet.boo', 'tipo_proveedor' => 'Mantenimiento']
        );
    }
}
