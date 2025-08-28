<?php

namespace App\Services;

use App\Enums\TaskStatus;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class TaskService
{

    public function getTasksByGroups(Request $request): array
    {
        $statuses = TaskStatus::cases();
        $in_archive = $request->has('in_archive');

        $query = Task::query()
            ->where('in_archive', $in_archive);

        $this->applyFilters($query, $request);

        $tasksByStatus = $query
            ->orderBy('sort')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->groupBy('status');

        $groupedTasks = [];

        foreach ($statuses as $status) {
            $statusValue = $status->value;
            $tasks = $tasksByStatus->get($statusValue, collect());

            foreach ($tasks as &$task) {
                $task->is_owner = Auth::id() === $task->producer_id;
                $task->is_executor = Auth::id() === $task->executor_id;
            }

            $groupedTasks[$statusValue] = [
                'status' => $statusValue,
                'label' => $status->label(),
                'tasks' => $tasks,
                'class' => $this->getTaskClass($statusValue),
                'icon' => $this->getTaskIcon($statusValue),
                'next_action' => $this->getTaskNextAction($statusValue),
            ];
        }

        return $groupedTasks;
    }

    protected function applyFilters($query, Request $request)
    {
        // Фильтр по статусу
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', TaskStatus::from($request->status));
        }

        // Фильтр по исполнителю
        if ($request->filled('executor_id')) {
            $query->where('executor_id', $request->executor_id);
        }

        // Фильтр по производителю
        if ($request->filled('producer_id')) {
            $query->where('producer_id', $request->producer_id);
        }

        // Фильтр по дате создания (from)
        if ($request->filled('created_from')) {
            $query->whereDate('created_at', '>=', Carbon::parse($request->created_from));
        }

        // Фильтр по дате создания (to)
        if ($request->filled('created_to')) {
            $query->whereDate('created_at', '<=', Carbon::parse($request->created_to));
        }

        // Фильтр по дате выполнения (from)
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', Carbon::parse($request->due_date_from));
        }

        // Фильтр по дате выполнения (to)
        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', Carbon::parse($request->due_date_to));
        }

        // Фильтр по фактической дате выполнения (from)
        if ($request->filled('actual_date_from')) {
            $query->whereDate('actual_date_of_execution', '>=', Carbon::parse($request->actual_date_from));
        }

        // Фильтр по фактической дате выполнения (to)
        if ($request->filled('actual_date_to')) {
            $query->whereDate('actual_date_of_execution', '<=', Carbon::parse($request->actual_date_to));
        }

        // Фильтр по названию задачи
        if ($request->filled('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        // Фильтр по описанию задачи
        if ($request->filled('description')) {
            $query->where('description', 'like', '%' . $request->description . '%');
        }

        return $query;
    }

    private function getTaskClass($statusValue): string
    {
        return match ($statusValue) {
            TaskStatus::COMPLETED->value => 'secondary',
            TaskStatus::NEW->value => 'primary',
            TaskStatus::IN_PROGRESS->value => 'warning',
            default => '',
        };
    }

    private function getTaskIcon($statusValue): string
    {
        return match ($statusValue) {
            TaskStatus::COMPLETED->value => 'bi-check',
            TaskStatus::NEW->value => 'bi-list-task',
            TaskStatus::IN_PROGRESS->value => 'bi-arrow-repeat',
            default => '',
        };
    }

    private function getTaskNextAction($statusValue): ?array
    {
        return match ($statusValue) {
            TaskStatus::COMPLETED->value => null,
            TaskStatus::NEW->value => [
                'icon' => 'bi-arrow-right',
                'title' => 'Взять в работу',
            ],
            TaskStatus::IN_PROGRESS->value => [
                'icon' => 'bi-check',
                'title' => 'Отметить как выполненную',
            ],
            default => null,
        };
    }

    public function changeNextStatus(Task $task): bool
    {
        $currentStatus = $task->status;
        $nextStatus = $currentStatus->nextStatus();

        if ($nextStatus === null) {
            return false;
        }

        $task->status = $nextStatus;
        return $task->save();
    }

    public function changeStatus(Task $task, $data): bool
    {
        $task->update(['status' => $data['status']]);

        $tasks = Task::query()->whereIn('id', collect($data['tasks'])->pluck('id'))->get();
        foreach ($tasks as $task) {
            foreach ($data['tasks'] as $key => $value) {
                if ((int)$task->id === (int)$value['id']) {
                    $task->update(['sort' => (int) $value['sort']]);
                }
            }
        }
        return true;
    }
}
