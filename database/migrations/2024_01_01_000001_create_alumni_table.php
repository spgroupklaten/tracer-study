<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->string('nim', 20)->unique();
            $table->string('nama_lengkap', 100);
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('tempat_lahir', 50);
            $table->date('tanggal_lahir');
            $table->string('email', 100)->unique();
            $table->string('nomor_hp', 20)->nullable();
            $table->year('tahun_masuk');
            $table->year('tahun_lulus');
            $table->string('program_studi', 100);
            $table->string('pekerjaan', 100)->nullable();
            $table->enum('status_pekerjaan', ['Bekerja', 'Belum Bekerja', 'Wiraswasta', 'Studi Lanjut', 'Lainnya'])->default('Belum Bekerja');
            $table->text('alamat')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('nama_lengkap');
            $table->index('program_studi');
            $table->index('tahun_lulus');
            $table->index('status_pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};
