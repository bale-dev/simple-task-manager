<?php

use App\Models\Project;
use App\Models\Task;

// store

it('creates a project and redirects to tasks index', function () {
    $this->post(route('projects.store'), ['name' => 'Alpha'])
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Project created.');

    $this->assertDatabaseHas('projects', ['name' => 'Alpha']);
});

it('rejects a store request with a missing name', function () {
    $this->post(route('projects.store'), ['name' => ''])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('projects', 0);
});

it('rejects a store request with a duplicate name', function () {
    Project::factory()->create(['name' => 'Alpha']);

    $this->post(route('projects.store'), ['name' => 'Alpha'])
        ->assertSessionHasErrors('name');

    $this->assertDatabaseCount('projects', 1);
});

it('rejects a store request when name exceeds 255 characters', function () {
    $this->post(route('projects.store'), ['name' => str_repeat('a', 256)])
        ->assertSessionHasErrors('name');
});

// destroy

it('deletes a project and redirects to tasks index', function () {
    $project = Project::factory()->create();

    $this->delete(route('projects.destroy', $project))
        ->assertRedirect(route('tasks.index'))
        ->assertSessionHas('success', 'Project deleted.');

    $this->assertDatabaseMissing('projects', ['id' => $project->id]);
});

it('nullifies project_id on tasks when the project is deleted', function () {
    $project = Project::factory()->create();
    $task = Task::factory()->create(['project_id' => $project->id]);

    $this->delete(route('projects.destroy', $project));

    expect($task->fresh()->project_id)->toBeNull();
});
