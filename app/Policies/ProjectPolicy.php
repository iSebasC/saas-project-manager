<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view the project.
     */
    public function view(User $user, Project $project): bool
    {
        // User must belong to the same company
        return $user->company_id === $project->company_id;
    }

    /**
     * Determine whether the user can create projects.
     */
    public function create(User $user): bool
    {
        // Any authenticated user with a company can create projects
        return $user->company_id !== null;
    }

    /**
     * Determine whether the user can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        // User must belong to the same company AND be a member of the project
        return $user->company_id === $project->company_id
            && $project->members->contains($user->id);
    }

    /**
     * Determine whether the user can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        // Only admins can delete the project (check role in pivot table)
        if ($user->company_id !== $project->company_id) {
            return false;
        }
        
        $membership = $project->members()->where('user_id', $user->id)->first();
        return $membership && $membership->pivot->role === 'admin';
    }
}
