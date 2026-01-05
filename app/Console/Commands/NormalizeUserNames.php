<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Str;

class NormalizeUserNames extends Command
{
    protected $signature = 'users:normalize-names {--dry-run : Only show what would be changed}';
    protected $description = 'Normalize user names (first_name, last_name) using referencia/nombre.csv';

    public function handle()
    {
        $path = base_path('referencia/nombre.csv');

        if (!file_exists($path)) {
            $this->error("File not found: $path");
            return 1;
        }

        $content = file_get_contents($path);
        // Detect and convert if necessary (though usually UTF-8 is fine)
        if (!mb_check_encoding($content, 'UTF-8')) {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-8');
        }

        $lines = explode("\n", str_replace("\r", "", $content));
        
        // Remove header
        if (isset($lines[0]) && str_contains($lines[0], '1er nombre')) {
            array_shift($lines);
        }

        $this->info("Processing " . count($lines) . " records...");
        
        $stats = [
            'found' => 0,
            'updated' => 0,
            'not_found' => 0,
            'skipped' => 0
        ];

        foreach ($lines as $line) {
            if (empty(trim($line))) continue;

            $data = str_getcsv($line, ';');
            if (count($data) < 4) continue;

            $fn1 = trim($data[0]);
            $fn2 = trim($data[1]);
            $ln1 = trim($data[2]);
            $ln2 = trim($data[3]);

            $targetFirstName = trim($fn1 . ' ' . $fn2);
            $targetLastName = trim($ln1 . ' ' . $ln2);
            $fullTargetName = trim($targetFirstName . ' ' . $targetLastName);

            // Strategy 1: Exact Name Match (Full)
            $user = User::where('name', $fullTargetName)->first();

            // Strategy 2: Exact Name Match (Parts) - sometimes 'name' has extra spaces
            if (!$user) {
                // Try searching with collapsed spaces
                $user = User::whereRaw('REPLACE(name, "  ", " ") = ?', [$fullTargetName])->first();
            }

            // Strategy 3: Construct email slug match
            if (!$user) {
                $slug = Str::slug($fullTargetName); // e.g. "karen-carol-arias-nina"
                $user = User::where('email', 'LIKE', "{$slug}%")->first();
            }

            // Strategy 4: Loose name match (LIKE)
            if (!$user) {
                $user = User::where('name', 'LIKE', '%' . $ln1 . '%' . $ln2 . '%')
                             ->where('name', 'LIKE', '%' . $fn1 . '%')
                             ->first();
            }

            if ($user) {
                $stats['found']++;
                
                $changed = false;
                if ($user->first_name !== $targetFirstName || $user->last_name !== $targetLastName || $user->name !== $fullTargetName) {
                    $changed = true;
                }

                if ($changed) {
                    $this->line("Updating: {$user->name} -> [{$targetFirstName}] [{$targetLastName}]");
                    if (!$this->option('dry-run')) {
                        $user->first_name = $targetFirstName;
                        $user->last_name = $targetLastName;
                        $user->name = $fullTargetName;
                        $user->save();
                    }
                    $stats['updated']++;
                } else {
                    $stats['skipped']++;
                }
            } else {
                $this->warn("Not found: $fullTargetName");
                $stats['not_found']++;
            }
        }

        $this->newLine();
        $this->table(['Total', 'Found', 'Updated', 'Skipped (No Change)', 'Not Found'], [
            [count($lines), $stats['found'], $stats['updated'], $stats['skipped'], $stats['not_found']]
        ]);

        if ($this->option('dry-run')) {
            $this->info('Dry run completed. No actual changes made.');
        } else {
            $this->info('User names normalization completed.');
        }
    }
}
