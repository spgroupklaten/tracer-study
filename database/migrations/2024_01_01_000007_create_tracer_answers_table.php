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
        Schema::create('tracer_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_study_id')
                ->constrained('tracer_studies')
                ->onDelete('cascade');
            $table->foreignId('question_id')
                ->constrained('tracer_questions')
                ->onDelete('cascade');
            $table->text('answer_value')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['tracer_study_id', 'question_id']);

            // Unique constraint - one answer per question per tracer study
            $table->unique(['tracer_study_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_answers');
    }
};
