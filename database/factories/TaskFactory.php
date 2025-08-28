<?php

namespace Database\Factories;

use App\Enums\TaskStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(TaskStatus::class);
        $actual_date_of_execution = null;

        if($status->value === TaskStatus::COMPLETED->value){
            $actual_date_of_execution = fake()->dateTime;
        }

        return [
            'name' => fake()->word(),
            'description' => fake()->text(),
            'producer_id' => User::query()->inRandomOrder()->first()->id,
            'executor_id' => User::query()->inRandomOrder()->first()->id,
            'status' => $status,
            'due_date' => fake()->dateTime,
            'actual_date_of_execution' => $actual_date_of_execution,
        ];
    }
}
