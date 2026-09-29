<?php

namespace App\Http\Controllers;

use App\Models\Alumni;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the home page.
     */
    public function index()
    {
        $totalAlumni = Alumni::count();
        $alumniLaki = Alumni::where('jenis_kelamin', 'L')->count();
        $alumniPerempuan = Alumni::where('jenis_kelamin', 'P')->count();

        // Dummy berita data
        $berita = [
            [
                'title' => 'Pengisian Tracer Study Alumni Tahun 2026',
                'date' => '22 Agustus 2026',
                'excerpt' => 'Tracer Study Alumni tahun 2026 telah dibuka. Alumni diharapkan mengisi data tracer study untuk membantu evaluasi dan pengembangan program pendidikan.',
                'image' => 'https://images.unsplash.com/photo-1434030216411-0b793f4b4173?w=800&q=80',
            ],
            [
                'title' => 'Informasi Pendataan Alumni',
                'date' => '15 Agustus 2026',
                'excerpt' => 'Pendataan alumni dilakukan untuk memperbarui informasi dan mendukung kebutuhan evaluasi institusi.',
                'image' => 'https://images.unsplash.com/photo-1529400971008-f566de0e6dfc?w=800&q=80',
            ],
            [
                'title' => 'Workshop Karier untuk Alumni',
                'date' => '10 Agustus 2026',
                'excerpt' => 'Workshop pengembangan karier akan diadakan untuk membantu alumni dalam mempersiapkan diri menghadapi dunia kerja yang kompetitif.',
                'image' => 'https://images.unsplash.com/photo-1552664730-d307ca884978?w=800&q=80',
            ],
        ];

        // Dummy pengumuman data
        $pengumuman = [
            [
                'title' => 'Batas Pengisian Tracer Study 2026',
                'date' => '20 Agustus 2026',
                'excerpt' => 'Pengisian tracer study tahun 2026 akan ditutup pada tanggal 30 September 2026. Pastikan Anda telah mengisi data sebelum batas waktu.',
            ],
            [
                'title' => 'Verifikasi Data Alumni',
                'date' => '18 Agustus 2026',
                'excerpt' => 'Alumni yang telah mengisi tracer study diharapkan melakukan verifikasi data sebelum batas waktu yang ditentukan.',
            ],
        ];

        return view('home', compact(
            'totalAlumni',
            'alumniLaki',
            'alumniPerempuan',
            'berita',
            'pengumuman'
        ));
    }
}
