<?php

namespace Database\Seeders;

use App\Enums\AcademicStatus;
use App\Enums\Role;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user (no profile needed)
        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => Role::Admin,
            ]
        );

        // Create mahasiswa user with aktif profile
        $mahasiswa = User::updateOrCreate(
            ['email' => 'mahasiswa@test.com'],
            [
                'name' => 'Mahasiswa Test',
                'password' => Hash::make('password'),
                'role' => Role::Mahasiswa,
            ]
        );

        // Create profile for mahasiswa (aktif)
        Profile::updateOrCreate(
            ['user_id' => $mahasiswa->id],
            [
                'nik' => '1234567890123456',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '2002-05-15',
                'jenis_kelamin' => 'L',
                'nim' => '20210001',
                'fakultas' => 'Fakultas Teknik',
                'program_studi' => 'Teknik Informatika',
                'angkatan' => 2021,
                'tahun_masuk' => 2021,
                'tahun_lulus' => null,
                'tanggal_lulus' => null,
                'ipk' => null,
                'academic_status' => AcademicStatus::Aktif,
                'no_hp' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 1, Jakarta',
                'kelurahan' => 'Gambir',
                'kecamatan' => 'Gambir',
                'kabupaten' => 'Jakarta Pusat',
                'provinsi' => 'DKI Jakarta',
                'kode_pos' => '10110',
            ]
        );

        // Seed tracer questions
        $this->call(TracerQuestionSeeder::class);

        // Seed master skills
        $this->call(SkillSeeder::class);
    }
}
