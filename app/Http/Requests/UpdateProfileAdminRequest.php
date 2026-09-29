<?php

namespace App\Http\Requests;

use App\Enums\AcademicStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileAdminRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Admin authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $profile = $this->route('profile');

        return [
            // User fields
            'name' => 'sometimes|nullable|string|max:255',
            'email' => [
                'sometimes',
                'nullable',
                'email',
                Rule::unique('users', 'email')->ignore($profile?->user_id),
            ],

            // Profile academic fields
            'nim' => [
                'sometimes',
                'nullable',
                'string',
                'max:20',
                Rule::unique('profiles', 'nim')->ignore($profile?->id),
            ],
            'fakultas' => 'sometimes|nullable|string|max:255',
            'program_studi' => 'sometimes|nullable|string|max:255',
            'tahun_masuk' => 'sometimes|nullable|integer|min:1990|max:' . (date('Y') + 5),
            'tahun_lulus' => 'sometimes|nullable|integer|min:1990|max:' . (date('Y') + 5),
            'tanggal_lulus' => 'sometimes|nullable|date|before_or_equal:today',
            'ipk' => 'sometimes|nullable|numeric|min:0|max:4',
            'academic_status' => [
                'sometimes',
                'nullable',
                Rule::in(array_column(AcademicStatus::cases(), 'value')),
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nim.unique' => 'NIM sudah digunakan oleh alumni lain.',
            'email.unique' => 'Email sudah digunakan oleh user lain.',
            'ipk.max' => 'IPK maksimal adalah 4.00.',
        ];
    }
}
