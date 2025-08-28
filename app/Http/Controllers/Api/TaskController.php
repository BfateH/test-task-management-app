<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Http\Resources\Task\MainResource;
use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Http\Resources\UserResource;
use App\Jobs\SendEmailJob;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller
{
    public function index(Request $request, TaskService $taskService): JsonResponse
    {
        $groupedTasks = $taskService->getTasksByGroups($request);
        foreach ($groupedTasks as &$group) {
            $group['tasks'] = MainResource::collection($group['tasks']);
        }

        return response()->json([
            'groupedTasks' => $groupedTasks,
            'totalTasks' => Task::count()
        ]);
    }

    public function store(StoreRequest $request): JsonResponse
    {
        $data = $request->validated();
        $task = Task::create($data);

        SendEmailJob::dispatch($task->executor->email, $task);
        $task->refresh();

        return response()->json([
            'message' => 'Задача успешно создана!',
            'task' => MainResource::make($task)->resolve()
        ], 201);
    }

    public function update(UpdateRequest $request, Task $task): JsonResponse
    {
        $response = Gate::inspect('update', $task);

        if ($response->denied()) {
            return response()->json([
                'error' => $response->message()
            ], 403);
        }

        $data = $request->validated();
        $task->update($data);
        $task->load(['producer', 'executor']);

        return response()->json([
            'message' => "Задача $task->name успешно обновлена!",
            'task' => MainResource::make($task)->resolve()
        ]);
    }

    public function delete(Task $task): JsonResponse
    {
        $response = Gate::inspect('delete', $task);

        if ($response->denied()) {
            return response()->json([
                'error' => $response->message()
            ], 403);
        }

        $name = $task->name;
        $task->delete();

        return response()->json([
            'message' => "Задача $name успешно удалена!"
        ]);
    }

    public function nextStatus(Task $task, TaskService $taskService): JsonResponse
    {
        $response = Gate::inspect('changeStatus', $task);

        if ($response->denied()) {
            return response()->json([
                'error' => $response->message()
            ], 403);
        }

        $isUpdated = $taskService->changeNextStatus($task);

        if (!$isUpdated) {
            return response()->json([
                'error' => "Невозможно изменить статус. Задача уже имеет конечный статус"
            ], 422);
        }

        $task->load(['producer', 'executor']);

        return response()->json([
            'message' => "Статус успешно изменен",
            'task' => MainResource::make($task)->resolve()
        ]);
    }

    public function changeStatus(Task $task, TaskService $taskService): JsonResponse
    {
        $response = Gate::inspect('changeStatus', $task);

        if ($response->denied()) {
            return response()->json([
                'error' => $response->message()
            ], 403);
        }

        $data = request()->only('status', 'tasks');
        $isUpdated = $taskService->changeStatus($task, $data);

        if (!$isUpdated) {
            return response()->json([
                'error' => "Невозможно изменить статус. Ошибка при сохранении."
            ], 422);
        }

        $task->load(['producer', 'executor']);

        return response()->json([
            'message' => "Статус успешно изменен.",
            'task' => MainResource::make($task)->resolve()
        ]);
    }

    public function toArchive(Task $task, TaskService $taskService): JsonResponse
    {
        $this->authorize('toArchive', $task);
        $task->in_archive = !$task->in_archive;
        $task->save();
        $task->load(['producer', 'executor']);

        return response()->json([
            'message' => "Успешно архивировано.",
            'in_archive' => $task->in_archive,
            'task' => MainResource::make($task)->resolve()
        ]);
    }
}
