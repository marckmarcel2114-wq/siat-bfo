<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoActivo;
use App\Models\AttributeDefinition;

class SimpleSoftwareAttributesSeeder extends Seeder
{
    public function run()
    {
        $computerTypes = ['Laptop', 'Desktop', 'Computadora', 'All-in-One', 'Portátil', 'PC'];

        $attributes = [
            [
                'nombre' => 'Sistema Operativo',
                'tipo_dato' => 'select',
                'orden' => 50, // Between Hardware (10-50) and Security (60+)
                'opciones' => [
                    'Microsoft Windows 11 Pro',
                    'Microsoft Windows 11 Home',
                    'Microsoft Windows 10 Pro',
                    'Microsoft Windows 10 Home',
                    'Microsoft Windows 8.1',
                    'macOS',
                    'Linux Ubuntu',
                    'Linux Fedora',
                    'Otro'
                ]
            ],
            [
                'nombre' => 'Versión Sistema Operativo',
                'tipo_dato' => 'select',
                'orden' => 51,
                'opciones' => [
                    '24H2',
                    '23H2',
                    '22H2',
                    '21H2',
                    '21H1',
                    'Sonoma (macOS)',
                    'Ventura (macOS)',
                    'N/A'
                ]
            ],
            [
                'nombre' => 'Versión Office',
                'tipo_dato' => 'select',
                'orden' => 55,
                'opciones' => [
                    'Microsoft Office Hogar y Empresas 2021',
                    'Microsoft Office Hogar y Empresas 2019',
                    'Microsoft Office Hogar y Pequeña Empresa 2010',
                    'Microsoft 365 para negocios',
                    'Microsoft 365 Personal',
                    'LibreOffice',
                    'OpenOffice',
                    'No instalado'
                ]
            ]
        ];

        $types = TipoActivo::whereIn('nombre', $computerTypes)->get();

        foreach ($types as $type) {
            $this->command->info("Adding software attributes to: {$type->nombre}");
            
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
