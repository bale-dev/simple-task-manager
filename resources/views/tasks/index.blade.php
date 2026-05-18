@extends('layouts.app')

@section('title', 'Tasks')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Tasks</h1>
    <a href="{{ route('tasks.create') }}"
       class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
        + New Task
    </a>
</div>

{{-- Toolbar: project filter + inline project creation --}}
<div class="flex flex-wrap items-end gap-4 mb-6 p-4 bg-white border border-gray-200 rounded-lg">

    <form method="GET" action="{{ route('tasks.index') }}" class="flex items-center gap-2">
        <label for="project_id" class="text-sm font-medium text-gray-700 whitespace-nowrap">
            Filter by project
        </label>
        <select name="project_id" id="project_id"
                onchange="this.form.submit()"
                class="text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-300 bg-white">
            <option value="">All projects</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}"
                    {{ $selectedProjectId == $project->id ? 'selected' : '' }}>
                    {{ $project->name }}
                </option>
            @endforeach
        </select>
        @if ($selectedProjectId)
            <a href="{{ route('tasks.index') }}"
               class="text-sm text-gray-400 hover:text-gray-600">&#x2715; Clear</a>
        @endif
    </form>

    <div class="w-px h-6 bg-gray-200 hidden sm:block"></div>

    <form method="POST" action="{{ route('projects.store') }}" class="flex items-center gap-2">
        @csrf
        <input type="text" name="name" placeholder="New project name…"
               class="text-sm border border-gray-300 rounded-md px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-300 w-44"
               required maxlength="255">
        <button type="submit"
                class="text-sm bg-gray-100 hover:bg-gray-200 px-3 py-1.5 rounded-md transition-colors">
            Add Project
        </button>
    </form>

</div>

{{-- Task list --}}
@if ($tasks->isEmpty())
    <p class="text-gray-400 text-sm">
        No tasks yet.
        <a href="{{ route('tasks.create') }}" class="text-blue-600 hover:underline">Create one.</a>
    </p>
@else
    <ul id="task-list" class="space-y-2">
        @foreach ($tasks as $task)
            <li data-task-id="{{ $task->id }}"
                class="flex items-center gap-3 bg-white border border-gray-200 rounded-lg px-4 py-3 select-none">

                {{-- Drag handle --}}
                <span class="drag-handle cursor-grab text-gray-300 hover:text-gray-500 shrink-0 text-lg leading-none"
                      title="Drag to reorder">
                    &#9776;
                </span>

                {{-- Priority badge --}}
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold shrink-0 priority-badge">
                    {{ $task->priority }}
                </span>

                {{-- Name + project --}}
                <div class="flex-1 min-w-0">
                    <span class="font-medium text-gray-900">{{ $task->name }}</span>
                    @if ($task->project)
                        <span class="ml-2 inline-block text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                            {{ $task->project->name }}
                        </span>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-3 shrink-0">
                    <a href="{{ route('tasks.edit', $task) }}"
                       class="text-sm text-blue-600 hover:underline">Edit</a>

                    <form method="POST" action="{{ route('tasks.destroy', $task) }}"
                          onsubmit="return confirm('Delete this task?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-sm text-red-500 hover:underline">
                            Delete
                        </button>
                    </form>
                </div>

            </li>
        @endforeach
    </ul>
@endif

@endsection
