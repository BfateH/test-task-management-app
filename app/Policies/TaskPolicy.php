<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class TaskPolicy
{
    public function changeStatus(User $user, Task $task): Response
    {
        $isExecutorOrProducer = $task->executor_id === $user->id || $task->producer_id === $user->id;

        return $isExecutorOrProducer
            ? Response::allow()
            : Response::deny('Нельзя менять статус чужой задачи.');
    }

    public function toArchive(User $user, Task $task): Response
    {
        $isProducer = $task->producer_id === $user->id;

        return $isProducer
            ? Response::allow()
            : Response::deny('Нельзя архивировать чужую задачу.');
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Task $task): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Task $task): Response
    {
        return $task->producer_id === $user->id
            ? Response::allow()
            : Response::deny('Вы можете редактировать только свои задачи.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Task $task): Response
    {
        return $task->producer_id === $user->id
            ? Response::allow()
            : Response::deny('Вы можете удалять только свои задачи.');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Task $task): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Task $task): bool
    {
        //
    }
}
