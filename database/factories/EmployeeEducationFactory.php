<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\EmployeeEducation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EmployeeEducation>
 */
class EmployeeEducationFactory extends Factory
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
            'education_level' => fake()->randomElement(['SMA', 'D3', 'S1', 'S2']),
            'major' => fake()->randomElement(['Teknik Informatika', 'Sistem Informasi', 'Manajemen', 'Akuntansi', 'Ilmu Hukum']),
            'institution_name' => fake()->randomElement(['Universitas Indonesia', 'Institut Teknologi Bandung', 'Universitas Gadjah Mada', 'Universitas Padjadjaran']),
            'graduation_year' => fake()->numberBetween(2010, 2024),
            'is_recognized' => true,
        ];
    }
}
