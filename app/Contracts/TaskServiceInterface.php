<?php

namespace App\Contracts;

use App\Models\Task;

interface TaskServiceInterface
{
    public function create(array $data): Task;

    public function reorder(array $ids): void;
}
