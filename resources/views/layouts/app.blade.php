<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Tasks'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen">

    <nav class="bg-white border-b border-gray-200 px-6 py-4">
        <div class="max-w-4xl mx-auto flex items-center gap-6">
            <a href="{{ route('tasks.index') }}"
               class="font-semibold text-gray-900 hover:text-gray-600">
                {{ config('app.name', 'Tasks') }}
            </a>
            <a href="{{ route('tasks.index') }}"
               class="text-sm text-gray-500 hover:text-gray-900">All Tasks</a>
            <a href="{{ route('tasks.create') }}"
               class="text-sm text-gray-500 hover:text-gray-900">New Task</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto px-6 py-8">

        @if (session('success'))
            <div class="mb-6 rounded-md bg-green-50 border border-green-200 px-4 py-3 text-green-800 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')

    </main>

</body>
</html>
