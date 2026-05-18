<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(4),
            'project_id' => Project::factory(),
            'priority' => $this->faker->numberBetween(1, 100),
        ];
    }
}
