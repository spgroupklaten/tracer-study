<?php

namespace Database\Seeders;

use App\Models\TracerQuestion;
use App\Models\TracerQuestionOption;
use Illuminate\Database\Seeder;

class TracerQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $questions = [
            [
                'code' => 'status_saat_ini',
                'question_text' => 'Apa status Anda saat ini?',
                'type' => 'radio',
                'is_required' => true,
                'is_active' => true,
                'order' => 1,
                'options' => [
                    ['label' => 'Bekerja', 'value' => 'bekerja'],
                    ['label' => 'Wiraswasta', 'value' => 'wiraswasta'],
                    ['label' => 'Studi Lanjut', 'value' => 'studi_lanjut'],
                    ['label' => 'Belum Bekerja', 'value' => 'belum_bekerja'],
                    ['label' => 'Lainnya', 'value' => 'lainnya'],
                ],
            ],
            [
                'code' => 'nama_perusahaan',
                'question_text' => 'Nama Perusahaan/Institusi tempat Anda bekerja?',
                'type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'order' => 2,
                'options' => [],
            ],
            [
                'code' => 'jabatan',
                'question_text' => 'Jabatan/Posisi Anda saat ini?',
                'type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'order' => 3,
                'options' => [],
            ],
            [
                'code' => 'alamat_perusahaan',
                'question_text' => 'Alamat Perusahaan/Institusi?',
                'type' => 'textarea',
                'is_required' => false,
                'is_active' => true,
                'order' => 4,
                'options' => [],
            ],
            [
                'code' => 'masa_tunggu',
                'question_text' => 'Berapa lama waktu tunggu untuk mendapatkan pekerjaan pertama setelah lulus? (dalam bulan)',
                'type' => 'number',
                'is_required' => false,
                'is_active' => true,
                'order' => 5,
                'options' => [],
            ],
            [
                'code' => 'gaji_pertama',
                'question_text' => 'Berapa rata-rata gaji yang Anda terima saat pertama kali bekerja? (dalam juta rupiah per bulan)',
                'type' => 'number',
                'is_required' => false,
                'is_active' => true,
                'order' => 6,
                'options' => [],
            ],
            [
                'code' => 'relevansi_pendidikan',
                'question_text' => 'Bagaimana tingkat relevansi pendidikan yang Anda terima dengan pekerjaan saat ini?',
                'type' => 'radio',
                'is_required' => true,
                'is_active' => true,
                'order' => 7,
                'options' => [
                    ['label' => 'Sangat Relevan', 'value' => 'sangat_relevan'],
                    ['label' => 'Relevan', 'value' => 'relevan'],
                    ['label' => 'Cukup Relevan', 'value' => 'cukup_relevan'],
                    ['label' => 'Kurang Relevan', 'value' => 'kurang_relevan'],
                    ['label' => 'Tidak Relevan', 'value' => 'tidak_relevan'],
                ],
            ],
            [
                'code' => 'jenjang_studi',
                'question_text' => 'Jenjang studi yang Anda tempuh setelah lulus?',
                'type' => 'select',
                'is_required' => false,
                'is_active' => true,
                'order' => 8,
                'options' => [
                    ['label' => 'S1', 'value' => 's1'],
                    ['label' => 'S2', 'value' => 's2'],
                    ['label' => 'S3', 'value' => 's3'],
                    ['label' => 'D4', 'value' => 'd4'],
                    ['label' => 'D3', 'value' => 'd3'],
                    ['label' => 'D2', 'value' => 'd2'],
                    ['label' => 'D1', 'value' => 'd1'],
                    ['label' => 'Profesi', 'value' => 'profesi'],
                    ['label' => 'Lainnya', 'value' => 'lainnya'],
                ],
            ],
            [
                'code' => 'nama_perguruan_tinggi',
                'question_text' => 'Nama Universitas/Perguruan Tinggi tempat Anda melanjutkan studi?',
                'type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'order' => 9,
                'options' => [],
            ],
            [
                'code' => 'jurusan_studi_lanjut',
                'question_text' => 'Jurusan/Program Studi yang Anda tempuh?',
                'type' => 'text',
                'is_required' => false,
                'is_active' => true,
                'order' => 10,
                'options' => [],
            ],
            [
                'code' => 'saran',
                'question_text' => 'Saran untuk peningkatan kualitas pembelajaran di kampus?',
                'type' => 'textarea',
                'is_required' => false,
                'is_active' => true,
                'order' => 11,
                'options' => [],
            ],
            [
                'code' => 'kompetensi',
                'question_text' => 'Kompetensi apa saja yang Anda kuasai? (bisa pilih lebih dari satu)',
                'type' => 'checkbox',
                'is_required' => false,
                'is_active' => true,
                'order' => 12,
                'options' => [
                    ['label' => 'Komunikasi', 'value' => 'komunikasi'],
                    ['label' => 'Bahasa Inggris', 'value' => 'bahasa_inggris'],
                    ['label' => 'Teknologi Informasi', 'value' => 'teknologi_informasi'],
                    ['label' => 'Manajemen', 'value' => 'manajemen'],
                    ['label' => 'Kewirausahaan', 'value' => 'kewirausahaan'],
                ],
            ],
        ];

        foreach ($questions as $questionData) {
            $options = $questionData['options'];
            unset($questionData['options']);

            $question = TracerQuestion::updateOrCreate(
                ['code' => $questionData['code']],
                $questionData
            );

            // Sync options
            if (!empty($options)) {
                // Delete existing options
                $question->questionOptions()->delete();

                // Create new options
                foreach ($options as $order => $option) {
                    $question->questionOptions()->create([
                        'label' => $option['label'],
                        'value' => $option['value'],
                        'order' => $order,
                        'is_active' => true,
                    ]);
                }
            }
        }
    }
}
