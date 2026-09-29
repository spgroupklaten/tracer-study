<?php

namespace App\Http\Requests;

use App\Models\Profile;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMahasiswaProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by route middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $profile = $this->user()?->profile;

        return [
            // User fields - name only
            'name' => ['sometimes', 'required', 'string', 'max:255'],

            // Profile fields - personal data (editable by mahasiswa)
            'nik' => [
                'sometimes',
                'nullable',
                'string',
                'max:16',
            ],
            'jenis_kelamin' => ['sometimes', 'nullable', 'string', Rule::in(['L', 'P'])],
            'domisili' => ['sometimes', 'nullable', 'string', 'max:150'],
            'tempat_lahir' => ['sometimes', 'nullable', 'string', 'max:100'],
            'tanggal_lahir' => ['sometimes', 'nullable', 'date', 'before:today'],
            'status_pernikahan' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(['belum_menikah', 'menikah', 'cerai_hidup', 'cerai_mati']),
            ],
            'agama' => [
                'sometimes',
                'nullable',
                'string',
                Rule::in(['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu', 'lainnya']),
            ],
            'alamat_ktp' => ['sometimes', 'nullable', 'string'],
            'alamat_tinggal' => ['sometimes', 'nullable', 'string'],
            'hobi' => ['sometimes', 'nullable', 'string', 'max:255'],
            'no_hp' => ['sometimes', 'nullable', 'string', 'max:20'],
            'alamat' => ['sometimes', 'nullable', 'string'],
            'kelurahan' => ['sometimes', 'nullable', 'string', 'max:100'],
            'kecamatan' => ['sometimes', 'nullable', 'string', 'max:100'],
            'kabupaten' => ['sometimes', 'nullable', 'string', 'max:100'],
            'provinsi' => ['sometimes', 'nullable', 'string', 'max:100'],
            'kode_pos' => ['sometimes', 'nullable', 'string', 'max:10'],

            // Photo upload
            'foto' => ['sometimes', 'nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Custom NIK unique validation
            $nik = $this->input('nik');
            if ($nik) {
                $profile = $this->user()?->profile;
                $existingProfile = \App\Models\Profile::where('nik', $nik)
                    ->when($profile, fn($q) => $q->where('id', '!=', $profile->id))
                    ->first();

                if ($existingProfile) {
                    $validator->errors()->add('nik', 'NIK sudah digunakan oleh profile lain.');
                }
            }
        });
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 255 karakter.',
            'nik.max' => 'NIK maksimal 16 karakter.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'foto.image' => 'File foto harus berupa gambar.',
            'foto.mimes' => 'Format foto harus jpg, jpeg, png, atau webp.',
            'foto.max' => 'Ukuran foto maksimal 2MB.',
            'nik.unique' => 'NIK sudah digunakan oleh profile lain.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator): void
    {
        throw new \Illuminate\Validation\ValidationException($validator);
    }
}
