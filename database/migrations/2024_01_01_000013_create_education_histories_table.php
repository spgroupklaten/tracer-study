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
        Schema::create('education_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->cascadeOnDelete();
            $table->string('jenjang', 20);
            $table->string('nama_institusi', 255);
            $table->string('jurusan', 255)->nullable();
            $table->string('kota', 100)->nullable();
            $table->year('tahun_masuk');
            $table->year('tahun_lulus')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index('profile_id');
            $table->index('jenjang');
            $table->index('tahun_lulus');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('education_histories');
    }
};
