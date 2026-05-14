<?php

namespace App\Policies;

use App\Models\GeneratedQuestion;
use App\Models\User;

class GeneratedQuestionPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }

    public function create(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }

    public function delete(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }

    public function restore(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }

    public function forceDelete(User $user, GeneratedQuestion $generatedQuestion): bool
    {
        return $user->id === $generatedQuestion->concept->domain->user_id;
    }
}