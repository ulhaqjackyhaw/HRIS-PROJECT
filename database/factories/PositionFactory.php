<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'department_id' => Department::factory(),
            'code' => 'POS-'.fake()->unique()->numerify('####'),
            'title' => fake()->jobTitle(),
            'level' => fake()->randomElement(['Staff', 'Senior Staff', 'Supervisor', 'Assistant Manager', 'Manager']),
            'career_path' => fake()->randomElement(['Struktural', 'Fungsional', 'Spesialis']),
            'job_function' => fake()->randomElement(['Human Capital', 'Finance & Accounting', 'Information Technology', 'Operations']),
            'is_active' => true,
        ];
    }
}
