<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeContract;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeContract>
 */
class EmployeeContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 years', 'now');
        $endDate = (clone $startDate)->modify('+1 year');

        return [
            'employee_id' => Employee::factory(),
            'contract_number' => 'CTR/'.fake()->unique()->numerify('####/PKWT/Y'),
            'contract_type' => fake()->randomElement(['PKWT-1', 'PKWT-2', 'PKWTT', 'PROBATION']),
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'document_path' => null,
            'notes' => fake()->sentence(),
        ];
    }
}
