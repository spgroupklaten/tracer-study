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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('nik', 16)->nullable()->unique();
            $table->string('tempat_lahir', 100);
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->string('foto')->nullable();
            $table->string('nim', 20)->unique();
            $table->string('fakultas', 150)->nullable();
            $table->string('program_studi', 150);
            $table->unsignedSmallInteger('angkatan');
            $table->unsignedSmallInteger('tahun_masuk');
            $table->unsignedSmallInteger('tahun_lulus')->nullable();
            $table->date('tanggal_lulus')->nullable();
            $table->decimal('ipk', 3, 2)->nullable();
            $table->enum('academic_status', ['aktif', 'lulus'])->default('aktif');
            $table->string('no_hp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kabupaten', 100)->nullable();
            $table->string('provinsi', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->timestamps();

            $table->index('academic_status');
            $table->index('program_studi');
            $table->index('tahun_lulus');
            $table->index('jenis_kelamin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
