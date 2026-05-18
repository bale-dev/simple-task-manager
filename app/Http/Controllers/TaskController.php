<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TaskController extends Controller
{
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
        Task::create([
            'name' => $request->validated('name'),
            'project_id' => $request->validated('project_id'),
            'priority' => Task::withoutGlobalScope('ordered')->max('priority') + 1,
        ]);

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

        DB::transaction(function () use ($request) {
            foreach ($request->input('ids') as $index => $id) {
                Task::withoutGlobalScope('ordered')
                    ->where('id', $id)
                    ->update(['priority' => $index + 1]);
            }
        });

        return response()->json(['status' => 'ok']);
    }
}
