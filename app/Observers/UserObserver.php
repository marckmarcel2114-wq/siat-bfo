<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "saving" event.
     */
    public function saving(User $user): void
    {
        // Automatically sync the 'name' field from first/last name
        // This keeps the database fast (no dynamic concat) and standardized
        if ($user->isDirty(['first_name', 'last_name'])) {
            $user->name = trim($user->first_name . ' ' . $user->last_name);
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Initial Job History
        if ($user->job_title_id || $user->city_id || $user->branch_id) {
            $user->jobHistory()->create([
                'cargo_id' => $user->job_title_id,
                'city_id' => $user->city_id,
                'branch_id' => $user->branch_id,
                'assignment_type' => $user->historyAssignmentType ?? 'permanent',
                'start_date' => now(), // Or hire_date if present? Ideally NOW for assignment.
                'notes' => $user->historyNote ?? 'Asignación inicial',
            ]);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // 1. History Management
        if ($user->isDirty(['job_title_id', 'city_id', 'branch_id'])) {
            // Close previous active history
            \App\Models\UserJobHistory::where('user_id', $user->id)
                ->whereNull('end_date')
                ->update(['end_date' => now()]);

            // Create new history
            $user->jobHistory()->create([
                'cargo_id' => $user->job_title_id,
                'city_id' => $user->city_id,
                'branch_id' => $user->branch_id,
                'assignment_type' => $user->historyAssignmentType ?? 'permanent',
                'start_date' => now(),
                'notes' => $user->historyNote ?? 'Actualización de asignación', // Use transient context
            ]);
        }
        
        // 2. Auto-Deactivation Logic
        if ($user->isDirty('termination_date') && $user->termination_date) {
             if (\Carbon\Carbon::parse($user->termination_date)->isPast() && $user->is_active) {
                // We don't want to trigger an infinite loop if we just update is_active.
                // Since this is 'updated', firing save() again will recurse unless we guard it.
                // However, saveQuietly() avoids triggering events.
                $user->is_active = false;
                $user->saveQuietly();
             }
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
