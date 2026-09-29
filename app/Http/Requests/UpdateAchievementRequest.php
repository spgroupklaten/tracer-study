<?php

namespace App\Http\Requests;

use App\Enums\AchievementLevel;
use App\Enums\AchievementType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAchievementRequest extends FormRequest
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
            'judul' => [
                'required',
                'string',
                'max:255',
            ],
            'jenis' => [
                'required',
                'string',
                Rule::in(AchievementType::values()),
            ],
            'tingkat' => [
                'required',
                'string',
                Rule::in(AchievementLevel::values()),
            ],
            'penyelenggara' => [
                'nullable',
                'string',
                'max:255',
            ],
            'tanggal' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'tahun' => [
                'nullable',
                'integer',
                'min:1900',
                'max:' . (date('Y') + 5),
            ],
            'deskripsi' => [
                'nullable',
                'string',
                'max:1000',
            ],
            'certificate' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120', // 5MB
            ],
            'remove_certificate' => [
                'nullable',
                'string',
                'in:1',
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
            'judul.required' => 'Judul prestasi wajib diisi.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            'jenis.required' => 'Jenis prestasi wajib dipilih.',
            'jenis.in' => 'Jenis prestasi tidak valid.',
            'tingkat.required' => 'Tingkat prestasi wajib dipilih.',
            'tingkat.in' => 'Tingkat prestasi tidak valid.',
            'certificate.mimes' => 'Sertifikat harus berupa file PDF, JPG, JPEG, PNG, atau WEBP.',
            'certificate.max' => 'Ukuran sertifikat maksimal 5MB.',
        ];
    }
}
