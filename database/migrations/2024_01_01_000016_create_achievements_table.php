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
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->string('judul', 255);
            $table->string('jenis', 20); // akademik, non_akademik
            $table->string('tingkat', 20); // internal, kabupaten, provinsi, nasional, internasional
            $table->string('penyelenggara', 255)->nullable();
            $table->date('tanggal')->nullable();
            $table->year('tahun')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('certificate_path', 500)->nullable();
            $table->string('certificate_original_name', 255)->nullable();
            $table->timestamps();

            $table->index('profile_id');
            $table->index('jenis');
            $table->index('tingkat');
            $table->index('tahun');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('achievements');
    }
};
