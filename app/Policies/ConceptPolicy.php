<?php

namespace App\Policies;

use App\Models\Concept;
use App\Models\User;

class ConceptPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function create(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function update(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function delete(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function restore(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }

    public function forceDelete(User $user, Concept $concept): bool
    {
        return $user->id === $concept->domain->user_id;
    }
}