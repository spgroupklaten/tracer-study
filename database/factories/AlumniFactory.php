<?php

namespace Database\Factories;

use App\Models\Alumni;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Alumni>
 */
class AlumniFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nim' => fake()->unique()->numerify('########'),
            'nama_lengkap' => fake()->name(),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'tempat_lahir' => fake()->city(),
            'tanggal_lahir' => fake()->dateTimeBetween('-30 years', '-20 years'),
            'email' => fake()->unique()->safeEmail(),
            'nomor_hp' => fake()->phoneNumber(),
            'tahun_masuk' => fake()->numberBetween(2015, 2020),
            'tahun_lulus' => fake()->numberBetween(2019, 2024),
            'program_studi' => fake()->randomElement([
                'Teknik Informatika',
                'Sistem Informasi',
                'Teknik Komputer',
                'Manajemen Informatika'
            ]),
            'pekerjaan' => fake()->jobTitle(),
            'status_pekerjaan' => fake()->randomElement([
                'Bekerja',
                'Belum Bekerja',
                'Wiraswasta',
                'Studi Lanjut',
                'Lainnya'
            ]),
            'alamat' => fake()->address(),
            'keterangan' => fake()->sentence(),
        ];
    }
}
