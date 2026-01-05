<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoActivo;
use App\Models\AttributeDefinition;

class SecurityComplianceAttributesSeeder extends Seeder
{
    public function run()
    {
        $computerTypes = ['Laptop', 'Desktop', 'Computadora', 'All-in-One', 'Portátil', 'PC'];
        
        $attributes = [
            // Security / Compliance Fields
            [
                'nombre' => 'Carpetas Compartidas Cerradas',
                'tipo_dato' => 'boolean',
                'orden' => 60,
                'opciones' => []
            ],
            [
                'nombre' => 'NetBank Instalado',
                'tipo_dato' => 'boolean', // Assuming simple Yes/No for presence
                'orden' => 61,
                'opciones' => []
            ],
            [
                'nombre' => 'Ficha Técnica Actualizada',
                'tipo_dato' => 'boolean',
                'orden' => 62,
                'opciones' => []
            ],
            [
                'nombre' => 'Form. Aceptación Responsabilidad',
                'tipo_dato' => 'boolean',
                'orden' => 63,
                'opciones' => []
            ],
            [
                'nombre' => 'Formulario de Excepción',
                'tipo_dato' => 'boolean',
                'orden' => 64,
                'opciones' => []
            ],
            [
                'nombre' => 'BIOS Acceso Bloqueado',
                'tipo_dato' => 'boolean',
                'orden' => 65,
                'opciones' => []
            ],
            [
                'nombre' => 'Antivirus (Kaspersky)',
                'tipo_dato' => 'boolean',
                'orden' => 66,
                'opciones' => []
            ],
            [
                'nombre' => 'USB Bloqueado',
                'tipo_dato' => 'boolean',
                'orden' => 67,
                'opciones' => []
            ],
             [
                'nombre' => 'Admin. Tareas Bloqueado',
                'tipo_dato' => 'boolean',
                'orden' => 68,
                'opciones' => []
            ],
            [
                'nombre' => 'OSC Inventory Instalado',
                'tipo_dato' => 'boolean',
                'orden' => 69,
                'opciones' => []
            ],
            [
                'nombre' => 'Precinto de Seguridad',
                'tipo_dato' => 'boolean',
                'orden' => 70,
                'opciones' => []
            ],
            [
                'nombre' => 'Motivo Excepción',
                'tipo_dato' => 'text',
                'orden' => 71, // Only relevant if Form Excepcion is SI, but we add it as field
                'opciones' => []
            ]
        ];

        $types = TipoActivo::whereIn('nombre', $computerTypes)->get();

        foreach ($types as $type) {
            $this->command->info("Adding security attributes to: {$type->nombre}");
            
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
                        'category' => 'security' // Compliance/Security checklist
                    ]
                );
            }
        }
    }
}
