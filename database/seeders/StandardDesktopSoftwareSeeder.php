<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoActivo;
use App\Models\AttributeDefinition;

class StandardDesktopSoftwareSeeder extends Seeder
{
    public function run()
    {
        $computerTypes = ['Laptop', 'Desktop', 'Computadora', 'All-in-One', 'Portátil', 'PC'];

        $attributes = [
            // Standard Software Checklist (Orden 80-99)
            [
                'nombre' => 'Soft. Correo (Outlook)',
                'tipo_dato' => 'boolean',
                'orden' => 80,
                'opciones' => []
            ],
            [
                'nombre' => 'Soft. Navegador (Edge)',
                'tipo_dato' => 'boolean',
                'orden' => 81,
                'opciones' => []
            ],
            [
                'nombre' => 'Soft. Antivirus (Kaspersky)',
                'tipo_dato' => 'select', // Keeping select for Version control
                'orden' => 82,
                'opciones' => ['12.4.0.867 (Estándar)', 'Otra Versión', 'No Instalado']
            ],
            [
                'nombre' => 'Soft. Protector Pantalla (AD)',
                'tipo_dato' => 'boolean',
                'orden' => 83,
                'opciones' => []
            ],
            [
                'nombre' => 'Soft. Compresor (Winrar)',
                'tipo_dato' => 'select', // Keeping select for Version control
                'orden' => 84,
                'opciones' => ['7.01 (Estándar)', 'Otra Versión', 'No Instalado']
            ],
            [
                'nombre' => 'Soft. Visor (Adobe Reader)',
                'tipo_dato' => 'boolean',
                'orden' => 85,
                'opciones' => []
            ],
            // Office Apps
            [
                'nombre' => 'Soft. Word',
                'tipo_dato' => 'boolean',
                'orden' => 90,
                'opciones' => []
            ],
            [
                'nombre' => 'Soft. Excel',
                'tipo_dato' => 'boolean',
                'orden' => 91,
                'opciones' => []
            ],
            [
                'nombre' => 'Soft. PowerPoint',
                'tipo_dato' => 'boolean',
                'orden' => 92,
                'opciones' => []
            ],
        ];

        $types = TipoActivo::whereIn('nombre', $computerTypes)->get();

        foreach ($types as $type) {
            $this->command->info("Adding Standard Desktop Software attributes to: {$type->nombre}");
            
            foreach ($attributes as $attr) {
                AttributeDefinition::updateOrCreate(
                    [
                        'tipo_activo_id' => $type->id,
                        'nombre' => $attr['nombre']
                    ],
                    [
                        'tipo_dato' => $attr['tipo_dato'],
                        'orden' => $attr['orden'],
                        'opciones' => $attr['opciones'],
                        'category' => 'software' // Software checklist
                    ]
                );
            }
        }
    }
}
