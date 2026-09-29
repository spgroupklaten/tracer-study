<?php

namespace Database\Factories;

use App\Enums\AcademicStatus;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     * Default state is "aktif" (active mahasiswa).
     * Use ->state('lulus') or ->lulus() for alumni.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $tahunMasuk = fake()->numberBetween(2015, now()->year - 1);

        return [
            'user_id' => null,
            'nik' => fake()->unique()->numerify('################'),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-30 years', '-20 years'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'foto' => null,
            'nim' => fake()->unique()->numerify('########'),
            'fakultas' => fake()->randomElement(['Fakultas Teknik', 'Fakultas Ekonomi', 'Fakultas Hukum']),
            'program_studi' => fake()->randomElement([
                'Teknik Informatika',
                'Sistem Informasi',
                'Teknik Komputer',
                'Manajemen Informatika',
            ]),
            'angkatan' => $tahunMasuk,
            'tahun_masuk' => $tahunMasuk,
            'tahun_lulus' => null,
            'tanggal_lulus' => null,
            'ipk' => null,
            'academic_status' => AcademicStatus::Aktif,
            'no_hp' => fake()->phoneNumber(),
            'alamat' => fake()->address(),
            'kelurahan' => fake()->city(),
            'kecamatan' => fake()->city(),
            'kabupaten' => fake()->city(),
            'provinsi' => fake()->state(),
            'kode_pos' => fake()->postcode(),
        ];
    }

    /**
     * Indicate that the profile belongs to an active mahasiswa.
     */
    public function aktif(): static
    {
        return $this->state(fn (array $attributes) => [
            'academic_status' => AcademicStatus::Aktif,
            'tahun_lulus' => null,
            'tanggal_lulus' => null,
            'ipk' => null,
        ]);
    }

    /**
     * Indicate that the profile belongs to a graduated alumni.
     */
    public function lulus(): static
    {
        return $this->state(function (array $attributes) {
            $tahunMasuk = $attributes['tahun_masuk'] ?? now()->year - 4;
            $tahunLulus = is_numeric($tahunMasuk) ? ((int) $tahunMasuk + 4) : (int) date('Y');
            return [
                'academic_status' => AcademicStatus::Lulus,
                'tahun_lulus' => $tahunLulus,
                'tanggal_lulus' => $tahunLulus . '-08-01',
                'ipk' => fake()->randomFloat(2, 2.5, 4.0),
            ];
        });
    }
}
