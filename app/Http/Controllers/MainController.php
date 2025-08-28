<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Http\Requests\Task\StoreRequest;
use App\Http\Requests\Task\UpdateRequest;
use App\Jobs\SendEmailJob;
use App\Models\Task;
use App\Models\User;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MainController extends Controller
{
    public function index(Request $request, TaskService $taskService): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('main', [
            'groupedTasks' => $taskService->getTasksByGroups($request),
            'totalTasks' => Task::count(),
            'filters' => $request->all(),
            'users' => User::all()
        ]);
    }

    public function create(): \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('tasks.create', [
            'users' => User::all()
        ]);
    }

    public function store(StoreRequest $request): \Illuminate\Http\RedirectResponse
    {
        $data = $request->validated();
        $task = Task::query()->create($data);
        SendEmailJob::dispatch($task->executor->email, $task);
        return redirect()->route('home')->with('success', 'Задача успешно создана!');
    }

    public function edit(Task $task): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        return view('tasks.edit', [
            'task' => $task,
            'users' => User::all()
        ]);
    }

    public function update(UpdateRequest $request, Task $task): \Illuminate\Http\RedirectResponse
    {
        $response = Gate::inspect('update', $task);

        if ($response->denied()) {
            return redirect()->route('home')->with('error', $response->message());
        }

        $data = $request->validated();
        $task->update($data);
        return redirect()->route('home')->with('success', "Задача  <b>$task->name</b>  успешно обновлена!");
    }

    public function delete(Task $task): \Illuminate\Http\RedirectResponse
    {
        $response = Gate::inspect('delete', $task);

        if ($response->denied()) {
            return back()->with('error', $response->message());
        }

        $name = $task->name;
        $task->delete();
        return back()->with('success', "Задача <b>$name</b> успешно удалена!");
    }

    public function nextStatus(Task $task, TaskService $taskService): \Illuminate\Http\RedirectResponse
    {
        $response = Gate::inspect('changeStatus', $task);

        if ($response->denied()) {
            return back()->with('error', $response->message());
        }

        $isUpdated = $taskService->changeNextStatus($task);
        if (!$isUpdated) {
            return back()->with('error', "Невозможно изменить статус. Задача уже имеет конечный статус");
        }
        return back()->with('success', "Статус успешно изменен");
    }

    public function changeStatus(Task $task, TaskService $taskService): \Illuminate\Http\JsonResponse
    {
        $response = Gate::inspect('changeStatus', $task);

        if ($response->denied()) {
            return response()->json([
                'data' => [
                    'error' => $response->message(),
                ]
            ]);
        }

        $data = request()->only('status', 'tasks');
        $isUpdated = $taskService->changeStatus($task, $data);
        if (!$isUpdated) {
            return response()->json([
                'data' => [
                    'error' => "Невозможно изменить статус. Ошибка при сохранении."
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'success' => "Статус успешно изменен."
            ]
        ]);
    }

    public function toArchive(Task $task, TaskService $taskService): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('toArchive', $task);
        $task->in_archive = !$task->in_archive;
        $task->save();
        return back()->with('success', "Успешно архивировано.");
    }
}
