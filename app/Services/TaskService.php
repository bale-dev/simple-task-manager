<?php

namespace App\Services;

use App\Contracts\TaskServiceInterface;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskService implements TaskServiceInterface
{
    public function create(array $data): Task
    {
        return Task::create([
            'name' => $data['name'],
            'project_id' => $data['project_id'],
            'priority' => Task::withoutGlobalScope('ordered')->max('priority') + 1,
        ]);
    }

    public function reorder(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Task::withoutGlobalScope('ordered')
                    ->where('id', $id)
                    ->update(['priority' => $index + 1]);
            }
        });
    }
}
