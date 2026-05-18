<?php

use App\Contracts\TaskServiceInterface;
use App\Models\Project;
use App\Models\Task;

// index

it('renders the task index with tasks and projects', function () {
    $project = Project::factory()->create();
    Task::factory()->create(['project_id' => $project->id]);

    $this->get(route('tasks.index'))
        ->assertOk()
        ->assertViewIs('tasks.index')
        ->assertViewHas('tasks')
        ->assertViewHas('projects')
        ->assertViewHas('selectedProjectId', null);
});

it('filters tasks by project_id on index', function () {
    $projectA = Project::factory()->create();
    $projectB = Project::factory()->create();
    Task::factory()->create(['project_id' => $projectA->id, 'name' => 'Task A']);
    Task::factory()->create(['project_id' => $projectB->id, 'name' => 'Task B']);

    $response = $this->get(route('tasks.index', ['project_id' => $projectA->id]));

    $response->assertOk()->assertViewHas('selectedProjectId', $projectA->id);

    expect($response->viewData('tasks'))
        ->toHaveCount(1)
        ->first()->name->toBe('Task A');
});

// create

it('renders the task create form with projects', function () {
    Project::factory()->count(3)->create();

    $this->get(route('tasks.create'))
        ->assertOk()
        ->assertViewIs('tasks.create')
        ->assertViewHas('projects');
});

// store

it('creates a task and redirects to index', function () {
    $project = Project::factory()->create();

    $this->mock(TaskServiceInterface::class)
        ->shouldReceive('create')
        ->once()
        ->with(['name' => 'New Task', 'project_id' => (string) $project->id]);

    $this->post(route('tasks.store'), ['name' => 'New Task', 'project_id' => $project->id])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Task created.');
});

it('rejects a store request with a missing name', function () {
    $this->post(route('tasks.store'), ['name' => ''])
        ->assertSessionHasErrors('name');
});

it('rejects a store request with a non-existent project_id', function () {
    $this->post(route('tasks.store'), ['name' => 'Task', 'project_id' => 9999])
        ->assertSessionHasErrors('project_id');
});

// edit

it('renders the task edit form with the task and projects', function () {
    $task = Task::factory()->create();

    $this->get(route('tasks.edit', $task))
        ->assertOk()
        ->assertViewIs('tasks.edit')
        ->assertViewHas('task', $task)
        ->assertViewHas('projects');
});

// update

it('updates a task and redirects to index', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create(['name' => 'Old Name', 'project_id' => $project->id]);

    $this->patch(route('tasks.update', $task), ['name' => 'New Name', 'project_id' => $project->id])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Task updated.');

    expect($task->fresh()->name)->toBe('New Name');
});

it('rejects an update request with a missing name', function () {
    $task = Task::factory()->create();

    $this->patch(route('tasks.update', $task), ['name' => ''])
        ->assertSessionHasErrors('name');
});

// destroy

it('deletes a task and redirects to index', function () {
    $task = Task::factory()->create();

    $this->delete(route('tasks.destroy', $task))
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Task deleted.');

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

// reorder

it('reorders tasks and returns an ok JSON response', function () {
    $tasks = Task::factory()->count(3)->create();
    $ids = $tasks->pluck('id')->reverse()->values()->all();

    $this->mock(TaskServiceInterface::class)
        ->shouldReceive('reorder')
        ->once()
        ->with($ids);

    $this->patchJson(route('tasks.reorder'), ['ids' => $ids])
        ->assertOk()
        ->assertJson(['status' => 'ok']);
});

it('rejects a reorder request without ids', function () {
    $this->patchJson(route('tasks.reorder'), [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('ids');
});

it('rejects a reorder request with non-existent task ids', function () {
    $this->patchJson(route('tasks.reorder'), ['ids' => [9999]])
        ->assertUnprocessable()
        ->assertJsonValidationErrors('ids.0');
});
