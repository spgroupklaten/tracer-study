<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAlumniRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $currentYear = (int) date('Y');
        
        return [
            'nim' => 'required|string|max:20|unique:alumni,nim',
            'nama_lengkap' => 'required|string|max:100',
            'jenis_kelamin' => 'required|in:L,P',
            'tempat_lahir' => 'required|string|max:50',
            'tanggal_lahir' => 'required|date|before:today',
            'email' => 'required|email|max:100|unique:alumni,email',
            'nomor_hp' => 'nullable|string|max:20',
            'tahun_masuk' => 'required|integer|min:1990|max:' . $currentYear,
            'tahun_lulus' => [
                'required',
                'integer',
                'min:1990',
                'max:' . $currentYear,
                function ($attribute, $value, $fail) use ($currentYear) {
                    $tahunMasuk = (int) $this->input('tahun_masuk');
                    $tahunLulus = (int) $value;
                    
                    // Validasi tahun lulus tidak boleh lebih besar dari tahun sekarang
                    if ($tahunLulus > $currentYear) {
                        $fail('Tahun lulus tidak boleh lebih besar dari tahun sekarang (' . $currentYear . ').');
                    }
                    
                    // Validasi tahun lulus tidak boleh sebelum tahun masuk
                    if ($tahunMasuk && $tahunLulus < $tahunMasuk) {
                        $fail('Tahun lulus tidak boleh sebelum tahun masuk.');
                    }
                    
                    // Validasi selisih tahun masuk dan lulus tidak lebih dari 10 tahun
                    if ($tahunMasuk && ($tahunLulus - $tahunMasuk) > 10) {
                        $fail('Selisih tahun masuk dan tahun lulus tidak boleh lebih dari 10 tahun.');
                    }
                },
            ],
            'program_studi' => 'required|string|max:100',
            'pekerjaan' => 'nullable|string|max:100',
            'status_pekerjaan' => 'nullable|in:Bekerja,Belum Bekerja,Wiraswasta,Studi Lanjut,Lainnya',
            'alamat' => 'nullable|string|max:500',
            'keterangan' => 'nullable|string|max:1000',
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
            'nim.required' => 'NIM wajib diisi.',
            'nim.unique' => 'NIM sudah terdaftar.',
            'nim.max' => 'NIM maksimal 20 karakter.',
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L atau P.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.before' => 'Tanggal lahir harus sebelum hari ini.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',
            'tahun_masuk.required' => 'Tahun masuk wajib diisi.',
            'tahun_masuk.integer' => 'Tahun masuk harus berupa angka.',
            'tahun_lulus.required' => 'Tahun lulus wajib diisi.',
            'tahun_lulus.integer' => 'Tahun lulus harus berupa angka.',
            'program_studi.required' => 'Program studi wajib diisi.',
        ];
    }
}
