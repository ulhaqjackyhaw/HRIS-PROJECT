<?php

namespace App\Http\Requests;

use App\Models\Position;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePositionRequest extends FormRequest
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
        /** @var Position $position */
        $position = $this->route('position');

        return [
            'department_id' => ['nullable', 'exists:departments,id'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('positions', 'code')->ignore($position->id),
            ],
            'title' => ['required', 'string', 'max:150'],
            'level' => ['nullable', 'string', 'max:50'],
            'career_path' => ['nullable', 'string', 'max:50'],
            'job_function' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_active' => $this->boolean('is_active'),
        ]);
    }
}
