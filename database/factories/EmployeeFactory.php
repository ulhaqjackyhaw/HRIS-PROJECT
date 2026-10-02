<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $gender = fake()->randomElement(['MALE', 'FEMALE']);

        return [
            'user_id' => User::factory(),
            'department_id' => Department::factory(),
            'position_id' => Position::factory(),
            'manager_id' => null,

            // Identitas Perusahaan
            'nik' => fake()->unique()->numerify('EMP######'),
            'employment_status' => fake()->randomElement(['PKWT', 'PKWTT', 'MAGANG']),
            'join_date' => fake()->dateTimeBetween('-5 years', '-1 year')->format('Y-m-d'),
            'end_date' => null,
            'current_contract_no' => 'CTR/'.fake()->unique()->numerify('####/HC/Y'),
            'work_location' => fake()->randomElement(['Head Office Jakarta', 'Cabang Bandung', 'Cabang Surabaya']),
            'is_active' => true,

            // Identitas Personal & KTP
            'ktp_number' => fake()->unique()->numerify('3201##############'),
            'full_name' => fake()->name($gender === 'MALE' ? 'male' : 'female'),
            'gender' => $gender,
            'birth_date' => fake()->dateTimeBetween('-45 years', '-22 years')->format('Y-m-d'),
            'religion' => fake()->randomElement(['ISLAM', 'KRISTEN', 'KATOLIK', 'HINDU', 'BUDDHA']),
            'marital_status' => fake()->randomElement(['SINGLE', 'MARRIED', 'DIVORCED']),
            'ktp_address' => fake()->address(),
            'current_address' => fake()->address(),

            // Kontak
            'email' => fake()->unique()->safeEmail(),
            'phone_number' => fake()->phoneNumber(),

            // Pajak & Penggajian
            'npwp' => fake()->numerify('##.###.###.#-###.###'),
            'ptkp_status' => fake()->randomElement(['TK/0', 'TK/1', 'K/0', 'K/1', 'K/2', 'K/3']),
            'bpjs_ketenagakerjaan_no' => fake()->numerify('##########'),
            'bpjs_kesehatan_no' => fake()->numerify('#############'),
            'bank_name' => fake()->randomElement(['BCA', 'Bank Mandiri', 'BRI', 'BNI']),
            'bank_account_number' => fake()->bankAccountNumber(),
            'bank_account_holder' => fake()->name(),

            // Custom fields
            'custom_fields' => [
                'blood_type' => fake()->randomElement(['A', 'B', 'AB', 'O']),
                'uniform_size' => fake()->randomElement(['S', 'M', 'L', 'XL', 'XXL']),
            ],
        ];
    }
}
