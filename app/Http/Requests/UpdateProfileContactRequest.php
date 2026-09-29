<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileContactRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by route middleware and controller
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'whatsapp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'website' => ['sometimes', 'nullable', 'url', 'max:255'],
            'linkedin' => ['sometimes', 'nullable', 'url', 'max:255'],
            'instagram' => ['sometimes', 'nullable', 'url', 'max:255'],
            'facebook' => ['sometimes', 'nullable', 'url', 'max:255'],
            'twitter' => ['sometimes', 'nullable', 'url', 'max:255'],
            'github' => ['sometimes', 'nullable', 'url', 'max:255'],
            'youtube' => ['sometimes', 'nullable', 'url', 'max:255'],
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
            'whatsapp.max' => 'Nomor WhatsApp maksimal 20 karakter.',
            'website.url' => 'Format URL website tidak valid.',
            'linkedin.url' => 'Format URL LinkedIn tidak valid.',
            'instagram.url' => 'Format URL Instagram tidak valid.',
            'facebook.url' => 'Format URL Facebook tidak valid.',
            'twitter.url' => 'Format URL Twitter/X tidak valid.',
            'github.url' => 'Format URL GitHub tidak valid.',
            'youtube.url' => 'Format URL YouTube tidak valid.',
        ];
    }
}
