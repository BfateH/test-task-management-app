<?php

namespace App\Observers;

use App\Enums\TaskStatus;
use App\Models\Task;

class TaskObserver
{
    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        //
    }

    public function updating(Task $task)
    {
        if ($task->isDirty('status') &&
            $task->status === TaskStatus::COMPLETED &&
            is_null($task->actual_date_of_execution)) {
            $task->actual_date_of_execution = now();
        }

        if ($task->isDirty('status') &&
            $task->getOriginal('status') === TaskStatus::COMPLETED &&
            $task->status !== TaskStatus::COMPLETED) {
            $task->actual_date_of_execution = null;
        }
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
