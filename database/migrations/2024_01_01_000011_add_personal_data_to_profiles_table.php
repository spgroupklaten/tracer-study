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
        Schema::table('profiles', function (Blueprint $table) {
            $table->string('domisili', 150)->nullable()->after('kode_pos');
            $table->enum('status_pernikahan', ['belum_menikah', 'menikah', 'cerai_hidup', 'cerai_mati'])->nullable()->after('domisili');
            $table->enum('agama', ['islam', 'kristen', 'katolik', 'hindu', 'buddha', 'konghucu', 'lainnya'])->nullable()->after('status_pernikahan');
            $table->text('alamat_ktp')->nullable()->after('agama');
            $table->text('alamat_tinggal')->nullable()->after('alamat_ktp');
            $table->string('hobi', 255)->nullable()->after('alamat_tinggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('profiles', function (Blueprint $table) {
            $table->dropColumn([
                'domisili',
                'status_pernikahan',
                'agama',
                'alamat_ktp',
                'alamat_tinggal',
                'hobi',
            ]);
        });
    }
};
