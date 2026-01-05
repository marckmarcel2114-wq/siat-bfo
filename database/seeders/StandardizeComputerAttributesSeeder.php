<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoActivo;
use App\Models\AttributeDefinition;

class StandardizeComputerAttributesSeeder extends Seeder
{
    public function run()
    {
        // Target Asset Types
        $computerTypes = ['Laptop', 'Desktop', 'Computadora', 'All-in-One', 'Portátil', 'PC'];
        
        // Detailed Attributes to Standardize (with Select Options)
        $attributes = [
            [
                'nombre' => 'Procesador',
                'tipo_dato' => 'select',
                'orden' => 10,
                'opciones' => [
                    'Intel Core i3', 'Intel Core i5', 'Intel Core i7', 'Intel Core i9',
                    'AMD Ryzen 3', 'AMD Ryzen 5', 'AMD Ryzen 7', 'AMD Ryzen 9',
                    'Intel Celeron', 'Intel Pentium', 'Apple M1', 'Apple M2', 'Apple M3'
                ]
            ],
            [
                'nombre' => 'Generación',
                'tipo_dato' => 'select', 
                'orden' => 11,
                'opciones' => [
                    '1ra Gen', '2da Gen', '3ra Gen', '4ta Gen', '5ta Gen',
                    '6ta Gen', '7ma Gen', '8va Gen', '9na Gen', '10ma Gen',
                    '11va Gen', '12va Gen', '13va Gen', '14va Gen',
                    'Serie 3000 (AMD)', 'Serie 4000 (AMD)', 'Serie 5000 (AMD)', 'Serie 7000 (AMD)',
                    'N/A'
                ]
            ],
            [
                'nombre' => 'Memoria RAM', 
                'tipo_dato' => 'select',
                'orden' => 20,
                'opciones' => ['4 GB', '8 GB', '12 GB', '16 GB', '24 GB', '32 GB', '64 GB', '128 GB']
            ],
            [
                'nombre' => 'Tipo Memoria RAM',
                'tipo_dato' => 'select',
                'orden' => 21,
                'opciones' => ['DDR3', 'DDR4', 'DDR5', 'LPDDR3', 'LPDDR4', 'LPDDR5']
            ],
            [
                'nombre' => 'Disco Duro',
                'tipo_dato' => 'select',
                'orden' => 30,
                'opciones' => ['128 GB', '250 GB', '256 GB', '480 GB', '500 GB', '512 GB', '1 TB', '2 TB']
            ],
            [
                'nombre' => 'Tipo Disco',
                'tipo_dato' => 'select', 
                'orden' => 31,
                'opciones' => ['HDD', 'SSD SATA', 'SSD M.2', 'SSD NVMe']
            ],
            [
                'nombre' => 'Tarjeta Madre',
                'tipo_dato' => 'text',
                'orden' => 40,
                'opciones' => []
            ],
            [
                'nombre' => 'Tarjeta de Video',
                'tipo_dato' => 'text',
                'orden' => 50,
                'opciones' => []
            ],
        ];

        $types = TipoActivo::whereIn('nombre', $computerTypes)->get();

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
                        'opciones' => $attr['opciones'],
                        'category' => 'hardware'
                    ]
                );
            }
        }
    }
}
