<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * View own profile, or a patient's profile only within the 72h window
     * around an active assigned service (3 days before to 1 day after).
     * Completed/cancelled services do not grant access (data minimisation).
     */
    public function view(User $user, User $model): bool
    {
        if ($user->id === $model->id) {
            return true;
        }

        if ($user->isProfessional()) {
            return $user->servicesAsProfessional()
                ->where('patient_id', $model->id)
                ->whereIn('status', ['confirmed', 'accepted', 'in_progress'])
                ->whereDate('date', '>=', now()->subDay()->toDateString())
                ->whereDate('date', '<=', now()->addDays(3)->toDateString())
                ->exists();
        }

        return false;
    }

    public function update(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }

    public function delete(User $user, User $model): bool
    {
        return $user->id === $model->id;
    }
}
