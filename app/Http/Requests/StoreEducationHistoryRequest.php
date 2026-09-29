<?php

namespace App\Http\Requests;

use App\Enums\EducationLevel;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEducationHistoryRequest extends FormRequest
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
            'jenjang' => ['required', 'string', Rule::in(EducationLevel::values())],
            'nama_institusi' => ['required', 'string', 'max:255'],
            'jurusan' => ['sometimes', 'nullable', 'string', 'max:255'],
            'kota' => ['sometimes', 'nullable', 'string', 'max:100'],
            'tahun_masuk' => ['required', 'integer', 'min:1900', 'max:' . (date('Y') + 5)],
            'tahun_lulus' => ['sometimes', 'nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 5)],
            'keterangan' => ['sometimes', 'nullable', 'string'],
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $tahunMasuk = $this->input('tahun_masuk');
            $tahunLulus = $this->input('tahun_lulus');

            if ($tahunMasuk && $tahunLulus && $tahunLulus < $tahunMasuk) {
                $validator->errors()->add('tahun_lulus', 'Tahun lulus tidak boleh lebih kecil dari tahun masuk.');
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
            'jenjang.required' => 'Jenjang pendidikan wajib dipilih.',
            'jenjang.in' => 'Jenjang pendidikan tidak valid.',
            'nama_institusi.required' => 'Nama institusi wajib diisi.',
            'nama_institusi.max' => 'Nama institusi maksimal 255 karakter.',
            'tahun_masuk.required' => 'Tahun masuk wajib diisi.',
            'tahun_masuk.integer' => 'Tahun masuk harus berupa angka.',
            'tahun_masuk.min' => 'Tahun masuk tidak valid.',
            'tahun_masuk.max' => 'Tahun masuk tidak valid.',
            'tahun_lulus.integer' => 'Tahun lulus harus berupa angka.',
            'tahun_lulus.min' => 'Tahun lulus tidak valid.',
            'tahun_lulus.max' => 'Tahun lulus tidak valid.',
        ];
    }
}
