<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Organisasi & Akun
            'user_id' => ['nullable', 'exists:users,id'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'position_id' => ['nullable', 'exists:positions,id'],
            'manager_id' => ['nullable', 'exists:employees,id'],

            // Kepegawaian
            'nik' => ['required', 'string', 'max:50', 'unique:employees,nik'],
            'employment_status' => ['required', 'string', 'in:PKWT,PKWTT,MAGANG'],
            'lifecycle_stage' => ['nullable', 'string', 'in:ONBOARDING,ACTIVE,SUSPENDED,OFFBOARDING,TERMINATED'],
            'join_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:join_date'],
            'current_contract_no' => ['nullable', 'string', 'max:100'],
            'work_location' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],

            // Identitas Personal
            'ktp_number' => ['required', 'string', 'max:20', 'unique:employees,ktp_number'],
            'full_name' => ['required', 'string', 'max:150'],
            'gender' => ['required', 'in:MALE,FEMALE'],
            'birth_date' => ['required', 'date', 'before:today'],
            'religion' => ['nullable', 'string', 'max:30'],
            'marital_status' => ['nullable', 'string', 'max:30'],
            'ktp_address' => ['nullable', 'string'],
            'current_address' => ['nullable', 'string'],

            // Kontak
            'email' => ['required', 'email', 'max:255', 'unique:employees,email'],
            'phone_number' => ['nullable', 'string', 'max:25'],

            // Pajak & Payroll
            'npwp' => ['nullable', 'string', 'max:30'],
            'ptkp_status' => ['required', 'string', 'max:10'],
            'bpjs_ketenagakerjaan_no' => ['nullable', 'string', 'max:50'],
            'bpjs_kesehatan_no' => ['nullable', 'string', 'max:50'],
            'bank_name' => ['nullable', 'string', 'max:50'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:150'],

            // Dynamic Custom Fields
            'custom_fields' => ['nullable', 'array'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active', true),
        ]);
    }
}
