<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use App\Models\City;
use App\Models\Branch;
use App\Models\BranchType;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('referencia/agencias.txt');
        if (!File::exists($path)) {
            $this->command->error("File not found: $path");
            return;
        }

        $content = File::get($path);
        $lines = explode("\n", $content);

        $currentCity = null;
        $currentBranch = null;
        
        // Cache types
        $types = BranchType::pluck('id', 'name'); // ['Sucursal' => 1, ...]
        
        // Default city list for fallback or detection
        $knownCities = ['La Paz', 'El Alto', 'Oruro', 'Cochabamba', 'Sucre', 'Santa Cruz', 'Tarija', 'Potosí', 'Beni', 'Pando'];

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Detect City / Sucursal Principal
            if (str_starts_with($line, 'Sucursal ')) {
                $cityName = trim(str_replace('Sucursal ', '', $line));
                // Handle "El Alto" special case if needed, but "Sucursal El Alto" works.
                
                // Create or find City
                $currentCity = City::firstOrCreate(['name' => $cityName], ['code' => strtoupper(substr($cityName, 0, 3))]);
                
                // Create the Sucursal branch itself
                $currentBranch = Branch::create([
                    'city_id' => $currentCity->id,
                    'branch_type_id' => $types['Sucursal'] ?? 1, // Default to 1 if not found
                    'name' => $line,
                    'address' => '', // Will be filled by next lines
                ]);
                continue;
            }

            // Detect Agencies
            if (str_starts_with($line, 'Agencia ')) {
                if (!$currentCity) {
                   // Fallback: If no city set yet, maybe assign to a default or skip
                   // For now, assuming file order is correct.
                   continue; 
                }
                
                $currentBranch = Branch::create([
                    'city_id' => $currentCity->id,
                    'branch_type_id' => $types['Agencia'] ?? 2,
                    'name' => $line,
                    'address' => '',
                ]);
                continue;
            }

            // Detect Oficina Externa
            if (str_starts_with($line, 'Oficina Externa ')) {
                if (!$currentCity) continue;
                 $currentBranch = Branch::create([
                    'city_id' => $currentCity->id,
                    'branch_type_id' => $types['Oficina Externa'] ?? 3,
                    'name' => $line,
                    'address' => '',
                ]);
                continue;
            }

            // Detect ATM Header (Standalone ATM block)
            if ($line === 'ATM') {
                 // Next line might be name or address?
                 // In the file, sometimes "ATM" is a header line, then "Aeropuerto..."
                 $currentBranch = null; // Reset to avoid adding address to previous agency
                 continue;
            }
            
            // Detect lines that look like addresses or phones
            if ($currentBranch) {
                if (str_starts_with($line, 'Teléfonos:')) {
                    $currentBranch->phones = trim(str_replace('Teléfonos:', '', $line));
                    $currentBranch->save();
                } elseif (str_starts_with($line, 'Teléfono:')) {
                    $currentBranch->phones = trim(str_replace('Teléfono:', '', $line));
                    $currentBranch->save();
                } elseif (str_starts_with($line, 'ATM:')) {
                    // This line describes an ATM inside this branch or at this location. 
                    // Should we create a separate ATM entity? 
                    // The user wants "Puntos de red", equipment assignments. 
                    // An ATM is also a Branch type in our types list.
                    // For now, let's treat "ATM:" lines as creating a separate ATM branch associated with the same city?
                    // Or just a note? 
                    // looking at file: "ATM: Av. 16 de julio..."
                    // This looks like a separate physical location (the machine itself).
                    // Let's create an ATM branch.
                    if ($currentCity) {
                        Branch::create([
                            'city_id' => $currentCity->id,
                            'branch_type_id' => $types['ATM'] ?? 3,
                            'name' => 'ATM - ' . $currentBranch->name,
                            'address' => trim(str_replace('ATM:', '', $line)),
                        ]);
                    }
                } else {
                    // Assuming it's an address line if it's not a phone or ATM
                    if (empty($currentBranch->address)) {
                        $currentBranch->address = $line;
                        $currentBranch->save();
                    } else {
                        // Append to address
                        $currentBranch->address .= ' ' . $line;
                        $currentBranch->save();
                    }
                }
            } else {
                // No current branch, maybe this is a standalone ATM line or detail?
                // Example: line 50 "Aeropuerto El Alto" after line 49 "ATM"
                // This implies "ATM Aeropuerto El Alto"
                if ($line !== 'ATM' && $currentCity) {
                     // Check if previous line was ATM? Difficult with loop.
                     // But we set currentBranch = null on 'ATM'.
                     // Let's create a generic ATM branch for this line
                     $currentBranch = Branch::create([
                        'city_id' => $currentCity->id,
                        'branch_type_id' => $types['ATM'] ?? 3,
                        'name' => 'ATM ' . $line,
                        'address' => 'Verificar dirección',
                    ]);
                }
            }
        }
    }
}
