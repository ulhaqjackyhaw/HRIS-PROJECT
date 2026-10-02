<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\EmployeeCareerHistory;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeCareerHistory>
 */
class EmployeeCareerHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employee_id' => Employee::factory(),
            'transition_type' => fake()->randomElement(['JOIN', 'PROMOTION', 'MUTATION']),
            'old_department_id' => null,
            'new_department_id' => Department::factory(),
            'old_position_id' => null,
            'new_position_id' => Position::factory(),
            'effective_date' => fake()->date(),
            'reference_doc_no' => 'SK/'.fake()->unique()->numerify('###/DIR/Y'),
            'notes' => fake()->sentence(),
        ];
    }
}
