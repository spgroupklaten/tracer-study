<?php

namespace App\Http\Requests;

use App\Enums\SkillLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProfileSkillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled in controller via profile ownership
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'skill_id' => [
                'required',
                'integer',
                'exists:skills,id',
                function ($attribute, $value, $fail) {
                    $profile = $this->user()->profile;
                    if ($profile && $profile->skills()->where('skill_id', $value)->exists()) {
                        $fail('Skill ini sudah ditambahkan sebelumnya.');
                    }
                },
            ],
            'level' => [
                'required',
                'string',
                Rule::in(SkillLevel::values()),
            ],
            'description' => [
                'nullable',
                'string',
                'max:500',
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
            'skill_id.required' => 'Pilih skill yang ingin ditambahkan.',
            'skill_id.exists' => 'Skill yang dipilih tidak valid.',
            'level.required' => 'Pilih level keahlian.',
            'level.in' => 'Level keahlian tidak valid.',
        ];
    }
}
