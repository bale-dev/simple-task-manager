@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')

<div class="max-w-lg">

    <h1 class="text-2xl font-semibold mb-6">Edit Task</h1>

    <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-5">
        @csrf
        @method('PATCH')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                Task name <span class="text-red-500">*</span>
            </label>
            <input type="text" id="name" name="name"
                   value="{{ old('name', $task->name) }}"
                   class="w-full border rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-300
                          {{ $errors->has('name') ? 'border-red-400' : 'border-gray-300' }}"
                   required maxlength="255" autofocus>
            @error('name')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="project_id" class="block text-sm font-medium text-gray-700 mb-1">
                Project <span class="text-gray-400 font-normal">(optional)</span>
            </label>
            <select id="project_id" name="project_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm bg-white focus:outline-none focus:ring-2 focus:ring-blue-300">
                <option value="">— No project —</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}"
                        {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                        {{ $project->name }}
                    </option>
                @endforeach
            </select>
            @error('project_id')
                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-3 pt-1">
            <button type="submit"
                    class="bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                Update Task
            </button>
            <a href="{{ route('tasks.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700">Cancel</a>
        </div>

    </form>
</div>

@endsection
