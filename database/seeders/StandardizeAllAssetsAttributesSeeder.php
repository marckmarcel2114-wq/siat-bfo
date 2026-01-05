<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoActivo;
use App\Models\AttributeDefinition;

class StandardizeAllAssetsAttributesSeeder extends Seeder
{
    public function run()
    {
        $configs = [
            'Impresora' => [
                [
                    'nombre' => 'Tipo de Impresión',
                    'tipo_dato' => 'select',
                    'orden' => 10,
                    'opciones' => ['Láser Monocromática', 'Láser Color', 'Inyección de Tinta', 'Matricial', 'Térmica', 'Plotter', 'Multifuncional']
                ],
                [
                    'nombre' => 'Conectividad',
                    'tipo_dato' => 'select',
                    'orden' => 20,
                    'opciones' => ['USB', 'Red Ethernet', 'Wi-Fi', 'USB + Red', 'USB + Wi-Fi', 'USB + Red + Wi-Fi']
                ],
                [
                    'nombre' => 'Dúplex Automático',
                    'tipo_dato' => 'select',
                    'orden' => 30,
                    'opciones' => ['Si', 'No']
                ],
            ],
            'Switch' => [
                [
                    'nombre' => 'Cantidad de Puertos',
                    'tipo_dato' => 'select',
                    'orden' => 10,
                    'opciones' => ['4 Puertos', '5 Puertos', '8 Puertos', '16 Puertos', '24 Puertos', '48 Puertos']
                ],
                [
                    'nombre' => 'Velocidad',
                    'tipo_dato' => 'select',
                    'orden' => 20,
                    'opciones' => ['10/100 Mbps', 'Gigabit (10/100/1000)', '10 Gigabit', 'Multigigabit']
                ],
                [
                    'nombre' => 'Administrable',
                    'tipo_dato' => 'select',
                    'orden' => 30,
                    'opciones' => ['Si', 'No (Unmanaged)', 'Smart Managed']
                ],
                [
                    'nombre' => 'PoE (Power over Ethernet)',
                    'tipo_dato' => 'select',
                    'orden' => 40,
                    'opciones' => ['No', 'Si (PoE)', 'Si (PoE+)', 'Si (PoE++)']
                ],
            ],
            'UPS' => [
                [
                    'nombre' => 'Capacidad',
                    'tipo_dato' => 'select',
                    'orden' => 10,
                    'opciones' => ['500 VA', '600 VA', '750 VA', '1000 VA', '1200 VA', '1500 VA', '2000 VA', '3000 VA', 'Más de 3 KVA']
                ],
                [
                    'nombre' => 'Topología',
                    'tipo_dato' => 'select',
                    'orden' => 20,
                    'opciones' => ['Offline / Standby', 'Interactiva', 'Online / Doble Conversión']
                ],
                [
                    'nombre' => 'Tipo de Montaje',
                    'tipo_dato' => 'select',
                    'orden' => 30,
                    'opciones' => ['Torre', 'Rack', 'Convertible Torre/Rack']
                ],
            ],
            'Tablet' => [
                [
                    'nombre' => 'Almacenamiento',
                    'tipo_dato' => 'select',
                    'orden' => 10,
                    'opciones' => ['16 GB', '32 GB', '64 GB', '128 GB', '256 GB', '512 GB', '1 TB']
                ],
                [
                    'nombre' => 'Memoria RAM',
                    'tipo_dato' => 'select',
                    'orden' => 15,
                    'opciones' => ['2 GB', '3 GB', '4 GB', '6 GB', '8 GB', '12 GB', '16 GB']
                ],
                [
                    'nombre' => 'Sistema Operativo',
                    'tipo_dato' => 'select',
                    'orden' => 20,
                    'opciones' => ['Android', 'iPadOS', 'Windows']
                ],
                [
                    'nombre' => 'Conectividad Celular',
                    'tipo_dato' => 'select',
                    'orden' => 30,
                    'opciones' => ['Solo Wi-Fi', '4G / LTE', '5G']
                ],
            ],
            'Monitor' => [
                [
                    'nombre' => 'Tamaño de Pantalla',
                    'tipo_dato' => 'select',
                    'orden' => 10,
                    'opciones' => ['18.5 pulgadas', '19 pulgadas', '20 pulgadas', '21.5 pulgadas', '22 pulgadas', '23.8 pulgadas', '24 pulgadas', '27 pulgadas', '32 pulgadas', 'Ultrawide']
                ],
                [
                    'nombre' => 'Resolución',
                    'tipo_dato' => 'select',
                    'orden' => 20,
                    'opciones' => ['HD (1366x768)', 'HD+ (1600x900)', 'FHD (1920x1080)', 'WUXGA (1920x1200)', 'QHD (2K)', '4K UHD']
                ],
                [
                    'nombre' => 'Entradas de Video',
                    'tipo_dato' => 'select',
                    'orden' => 30,
                    'opciones' => ['VGA', 'HDMI', 'VGA + HDMI', 'HDMI + DisplayPort', 'USB-C / Thunderbolt', 'VGA + HDMI + DP']
                ],
            ],
            'Proyector' => [
                 [
                    'nombre' => 'Lúmenes (Brillo)',
                    'tipo_dato' => 'select',
                    'orden' => 10,
                    'opciones' => ['< 3000', '3000 - 3500', '3600 - 4000', '4000 - 5000', '> 5000']
                ],
                [
                    'nombre' => 'Tecnología',
                    'tipo_dato' => 'select',
                    'orden' => 20,
                    'opciones' => ['DLP', '3LCD', 'Laser']
                ],
            ]
        ];

        foreach ($configs as $typeName => $attributes) {
            // Find types matching name (flexibly)
            $types = TipoActivo::where('nombre', 'like', "%{$typeName}%")->get();

            if ($types->isEmpty()) {
                $this->command->warn("Type '{$typeName}' not found. Skipping.");
                continue;
            }

            foreach ($types as $type) {
                $this->command->info("Configuring strict attributes for: {$type->nombre}");
                
                foreach ($attributes as $attr) {
                    AttributeDefinition::updateOrCreate(
                        [
                            'tipo_activo_id' => $type->id,
                            'nombre' => $attr['nombre']
                        ],
                        [
                            'tipo_dato' => $attr['tipo_dato'],
                            'orden' => $attr['orden'],
                            'opciones' => $attr['opciones']
                        ]
                    );
                }
            }
        }
    }
}
