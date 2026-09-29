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
        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alumni_id')
                ->nullable()
                ->constrained('alumni')
                ->onDelete('cascade');
            $table->enum('status', ['draft', 'submitted'])->default('draft');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('status');
            $table->index(['alumni_id', 'status']);
            $table->index(['profile_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_studies');
    }
};
