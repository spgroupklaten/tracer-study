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
        Schema::create('profile_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('whatsapp', 20)->nullable();
            $table->string('website', 255)->nullable();
            $table->string('linkedin', 255)->nullable();
            $table->string('instagram', 100)->nullable();
            $table->string('facebook', 255)->nullable();
            $table->string('twitter', 100)->nullable();
            $table->string('github', 100)->nullable();
            $table->string('youtube', 255)->nullable();
            $table->timestamps();

            $table->index('profile_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_contacts');
    }
};
