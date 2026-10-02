<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeCareerHistoryRequest extends FormRequest
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
            'transition_type' => ['required', 'string', 'in:JOIN,PROMOTION,DEMOTION,MUTATION,RESIGN,TERMINATE'],
            'new_department_id' => ['nullable', 'exists:departments,id'],
            'new_position_id' => ['nullable', 'exists:positions,id'],
            'effective_date' => ['required', 'date'],
            'reference_doc_no' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'update_employee_profile' => ['nullable', 'boolean'],
            'lifecycle_stage' => ['nullable', 'string', 'in:ONBOARDING,ACTIVE,SUSPENDED,OFFBOARDING,TERMINATED'],
        ];
    }
}
