<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $skills = [
            [
                'name' => 'PHP',
                'description' => 'Bahasa pemrograman server-side untuk pengembangan web',
            ],
            [
                'name' => 'Laravel',
                'description' => 'Framework PHP untuk pengembangan aplikasi web modern',
            ],
            [
                'name' => 'JavaScript',
                'description' => 'Bahasa pemrograman untuk pengembangan web interaktif',
            ],
            [
                'name' => 'TypeScript',
                'description' => 'Superset JavaScript dengan tipe data statis',
            ],
            [
                'name' => 'Python',
                'description' => 'Bahasa pemrograman serbaguna untuk berbagai keperluan',
            ],
            [
                'name' => 'HTML & CSS',
                'description' => 'Markup dan styling untuk halaman web',
            ],
            [
                'name' => 'React',
                'description' => 'Library JavaScript untuk membangun user interface',
            ],
            [
                'name' => 'Vue.js',
                'description' => 'Framework JavaScript progresif untuk membangun UI',
            ],
            [
                'name' => 'Node.js',
                'description' => 'Runtime JavaScript untuk server-side programming',
            ],
            [
                'name' => 'Database',
                'description' => 'Pengelolaan database SQL dan NoSQL',
            ],
            [
                'name' => 'MySQL',
                'description' => 'Sistem manajemen database relasional',
            ],
            [
                'name' => 'PostgreSQL',
                'description' => 'Sistem manajemen database relasional objek',
            ],
            [
                'name' => 'MongoDB',
                'description' => 'Database NoSQL berbasis dokumen',
            ],
            [
                'name' => 'Git',
                'description' => 'Sistem kontrol versi untuk manajemen kode sumber',
            ],
            [
                'name' => 'Docker',
                'description' => 'Platform kontainerisasi untuk deployment aplikasi',
            ],
            [
                'name' => 'UI/UX Design',
                'description' => 'Desain antarmuka dan pengalaman pengguna',
            ],
            [
                'name' => 'Figma',
                'description' => 'Alat desain kolaboratif untuk UI/UX',
            ],
            [
                'name' => 'Networking',
                'description' => 'Konfigurasi dan pengelolaan jaringan komputer',
            ],
            [
                'name' => 'MikroTik',
                'description' => 'Konfigurasi router dan jaringan MikroTik',
            ],
            [
                'name' => 'Cisco',
                'description' => 'Konfigurasi jaringan dan perangkat Cisco',
            ],
            [
                'name' => 'Linux',
                'description' => 'Administrasi sistem operasi Linux',
            ],
            [
                'name' => 'Cloud Computing',
                'description' => 'Layanan komputasi awan (AWS, GCP, Azure)',
            ],
            [
                'name' => 'API Development',
                'description' => 'Pengembangan Application Programming Interface',
            ],
            [
                'name' => 'REST API',
                'description' => 'Desain dan implementasi RESTful API',
            ],
            [
                'name' => 'GraphQL',
                'description' => 'Bahasa query untuk API dan runtime',
            ],
            [
                'name' => 'Mobile Development',
                'description' => 'Pengembangan aplikasi mobile (Android/iOS)',
            ],
            [
                'name' => 'Flutter',
                'description' => 'Framework cross-platform untuk mobile development',
            ],
            [
                'name' => 'React Native',
                'description' => 'Framework untuk membangun aplikasi mobile dengan React',
            ],
            [
                'name' => 'Digital Marketing',
                'description' => 'Strategi pemasaran digital dan media sosial',
            ],
            [
                'name' => 'SEO',
                'description' => 'Search Engine Optimization',
            ],
            [
                'name' => 'Public Speaking',
                'description' => 'Kemampuan berbicara di depan umum',
            ],
            [
                'name' => 'Project Management',
                'description' => 'Pengelolaan proyek dan metodologi Agile/Scrum',
            ],
            [
                'name' => 'Data Science',
                'description' => 'Analisis data dan machine learning',
            ],
            [
                'name' => 'Cybersecurity',
                'description' => 'Keamanan siber dan perlindungan data',
            ],
            [
                'name' => 'Graphic Design',
                'description' => 'Desain grafis untuk media visual',
            ],
            [
                'name' => 'WordPress',
                'description' => 'Content Management System berbasis PHP',
            ],
        ];

        foreach ($skills as $skill) {
            Skill::updateOrCreate(
                ['slug' => Str::slug($skill['name'])],
                [
                    'name' => $skill['name'],
                    'description' => $skill['description'],
                ]
            );
        }
    }
}
