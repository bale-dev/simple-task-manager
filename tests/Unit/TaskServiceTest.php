<?php

use App\Models\Project;
use App\Models\Task;
use App\Services\TaskService;

beforeEach(function () {
    $this->service = new TaskService;
});

// create

it('persists a task with the given name and project', function () {
    $project = Project::factory()->create();

    $task = $this->service->create(['name' => 'My Task', 'project_id' => $project->id]);

    expect($task)->toBeInstanceOf(Task::class);
    $this->assertDatabaseHas('tasks', ['name' => 'My Task', 'project_id' => $project->id]);
});

it('assigns priority 1 when no tasks exist yet', function () {
    $task = $this->service->create(['name' => 'First', 'project_id' => null]);

    expect($task->priority)->toBe(1);
});

it('assigns the next priority after the current maximum', function () {
    Task::factory()->create(['priority' => 1]);
    Task::factory()->create(['priority' => 2]);

    $task = $this->service->create(['name' => 'Third', 'project_id' => null]);

    expect($task->priority)->toBe(3);
});

// reorder

it('updates each task priority to match the given array position', function () {
    $taskA = Task::factory()->create(['priority' => 1]);
    $taskB = Task::factory()->create(['priority' => 2]);
    $taskC = Task::factory()->create(['priority' => 3]);

    $this->service->reorder([$taskC->id, $taskB->id, $taskA->id]);

    expect($taskC->fresh()->priority)->toBe(1)
        ->and($taskB->fresh()->priority)->toBe(2)
        ->and($taskA->fresh()->priority)->toBe(3);
});
