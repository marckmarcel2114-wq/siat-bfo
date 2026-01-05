<?php

namespace Database\Seeders;

use App\Models\JobTitle;
use App\Models\User;
use App\Models\UserJobHistory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class JobTitleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Job Titles from file
        $path = base_path('referencia/cargos.txt');
        
        if (!File::exists($path)) {
            $this->command->error("File not found: $path");
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $titles = collect($lines)
            ->map(fn($line) => mb_strtoupper(trim($line)))
            ->unique()
            ->sort()
            ->values();

        $this->command->info('Seeding ' . $titles->count() . ' job titles...');

        foreach ($titles as $title) {
            JobTitle::firstOrCreate(['name' => $title]);
        }

        // 2. Migrate existing users' positions
        $this->command->info('Migrating user positions...');
        
        $users = User::whereNotNull('position')->where('position', '!=', '')->get();

        foreach ($users as $user) {
            $positionName = mb_strtoupper(trim($user->position));
            
            // Find or create the job title
            $jobTitle = JobTitle::firstOrCreate(['name' => $positionName]);

            // Update user if not already linked (idempotency)
            if ($user->job_title_id !== $jobTitle->id) {
                // Update user
                $user->job_title_id = $jobTitle->id;
                $user->save();

                // Create History entry
                // Assuming start_date is user creation date since we don't have history
                UserJobHistory::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'job_title_id' => $jobTitle->id,
                    ],
                    [
                        'start_date' => $user->created_at,
                        'notes' => 'Migración inicial de cargo: ' . $user->position,
                    ]
                );
            }
        }
        
        $this->command->info('Job Title seeding and migration complete.');
    }
}
