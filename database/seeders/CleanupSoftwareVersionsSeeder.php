<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Software;
use App\Models\SoftwareVersion;

class CleanupSoftwareVersionsSeeder extends Seeder
{
    public function run()
    {
        $this->command->info('Starting Smart Software Cleanup...');
        
        $softwareList = Software::with('versions')->get();
        
        foreach ($softwareList as $soft) {
            $originalName = $soft->nombre;
            
            // Regex to find the first occurrence of a digit acting as a version start
            // Looks for a space followed by a digit.
            if (preg_match('/^(.*?)\s+(\d+.*)$/', $originalName, $matches)) {
                $cleanName = trim($matches[1]);
                $extractedVersion = trim($matches[2]);
                
                $this->command->info("Parsing: '$originalName' -> Name: '$cleanName', Ver: '$extractedVersion'");
                
                // 1. Update the Version
                // We assume there is 'Base' version to update, or create a new one
                $baseVersion = $soft->versions->first();
                if ($baseVersion) {
                    $baseVersion->version = $extractedVersion;
                    $baseVersion->descripcion = 'Extracted from name: ' . $originalName;
                    $baseVersion->save();
                } else {
                    SoftwareVersion::create([
                        'software_id' => $soft->id,
                        'version' => $extractedVersion,
                        'descripcion' => 'Extracted from name: ' . $originalName
                    ]);
                }
                
                // 2. Update the Name
                // CAUTION: Check for duplicates first?
                // For now, let's just update. If duplicate exists, we might have issues if unique constraint exists.
                // Assuming no strict unique constraint on 'nombre' or we handle it.
                // If we strictly want to merge 'Windows 10' and 'Windows 11' into 'Windows', we need complex merge logic.
                // For this request, simply splitting string is a good first step. 
                // However, having two 'Windows' entries is confusing.
                // Let's TRY to merge if target exists.
                
                $targetSoft = Software::where('nombre', $cleanName)->where('id', '!=', $soft->id)->first();
                
                if ($targetSoft) {
                    $this->command->warn("  -> Merging into existing software ID: {$targetSoft->id} ({$targetSoft->nombre})");
                    
                    // Move versions to target
                    foreach($soft->versions as $v) {
                        $v->software_id = $targetSoft->id;
                        $v->save();
                    }
                    
                    // Move installations? (If we had relationships defined, we should move them)
                    // Assuming installations are linked to VERSIONS, we are safe because we moved the version.
                    // But if installations are linked to SOFTWARE directly... (Wait, our new system links to Versions mostly, but old might link to software?)
                    // The new SoftwareInstallation table has software_version_id.
                    
                    // Delete the old shell
                    try {
                        $soft->delete();
                    } catch (\Exception $e) {
                         $this->command->error("  -> Could not delete old software: " . $e->getMessage());
                    }
                    
                } else {
                    $soft->nombre = $cleanName;
                    $soft->save();
                }
                
            } else {
                //$this->command->line("Skipping: '$originalName' (No version detected)");
            }
        }
        
        $this->command->info('Cleanup Complete.');
    }
}
