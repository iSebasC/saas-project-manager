<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Determine whether the user can view the task.
     */
    public function view(User $user, Task $task): bool
    {
        // User must belong to the same company as the project
        return $user->company_id === $task->project->company_id;
    }

    /**
     * Determine whether the user can create tasks.
     */
    public function create(User $user): bool
    {
        // Any authenticated user with a company can create tasks
        return $user->company_id !== null;
    }

    /**
     * Determine whether the user can update the task.
     */
    public function update(User $user, Task $task): bool
    {
        // User must belong to the same company AND be a member of the project
        $project = $task->project;
        
        return $user->company_id === $project->company_id
            && $project->members->contains($user->id);
    }

    /**
     * Determine whether the user can delete the task.
     */
    public function delete(User $user, Task $task): bool
    {
        // User must belong to the same company AND be a member of the project
        $project = $task->project;
        
        return $user->company_id === $project->company_id
            && $project->members->contains($user->id);
    }
}
