<?php

namespace App\Http\Controllers;

use App\Contracts\TaskServiceInterface;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(private TaskServiceInterface $tasks) {}

    public function index(Request $request): View
    {
        $projects = Project::orderBy('name')->get();

        $tasks = Task::with('project')
            ->when($request->filled('project_id'), function ($query) use ($request) {
                $query->where('project_id', $request->integer('project_id'));
            })
            ->get();

        $selectedProjectId = $request->integer('project_id') ?: null;

        return view('tasks.index', compact('tasks', 'projects', 'selectedProjectId'));
    }

    public function create(): View
    {
        $projects = Project::orderBy('name')->get();

        return view('tasks.create', compact('projects'));
    }

    public function store(StoreTaskRequest $request): RedirectResponse
    {
        $this->tasks->create($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task created.');
    }

    public function edit(Task $task): View
    {
        $projects = Project::orderBy('name')->get();

        return view('tasks.edit', compact('task', 'projects'));
    }

    public function update(UpdateTaskRequest $request, Task $task): RedirectResponse
    {
        $task->update($request->validated());

        return redirect()->route('tasks.index')
            ->with('success', 'Task updated.');
    }

    public function destroy(Task $task): RedirectResponse
    {
        $task->delete();

        return redirect()->route('tasks.index')
            ->with('success', 'Task deleted.');
    }

    public function reorder(Request $request): JsonResponse
    {
        $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:tasks,id'],
        ]);

        $this->tasks->reorder($request->input('ids'));

        return response()->json(['status' => 'ok']);
    }
}
