<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\JobPosting;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<JobPosting>
 */
class JobPostingFactory extends Factory
{
    protected $model = JobPosting::class;

    public function definition(): array
    {
        $title = fake()->jobTitle();

        return [
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numberBetween(100, 999),
            'department_id' => Department::factory(),
            'employment_type' => fake()->randomElement(['FULL_TIME', 'CONTRACT', 'INTERNSHIP', 'PART_TIME']),
            'work_model' => fake()->randomElement(['ON_SITE', 'HYBRID', 'REMOTE']),
            'location' => fake()->city(),
            'experience_level' => fake()->randomElement(['Entry Level', 'Mid Level', 'Senior / Lead', 'Managerial']),
            'min_salary' => 10000000,
            'max_salary' => 20000000,
            'salary_currency' => 'IDR',
            'is_salary_visible' => true,
            'description' => fake()->paragraphs(3, true),
            'requirements' => fake()->paragraphs(2, true),
            'benefits' => fake()->paragraphs(2, true),
            'quota' => 1,
            'deadline' => now()->addDays(30),
            'status' => 'PUBLISHED',
            'views_count' => 0,
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'DRAFT',
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'CLOSED',
        ]);
    }

    public function remote(): static
    {
        return $this->state(fn (array $attributes) => [
            'work_model' => 'REMOTE',
        ]);
    }
}
