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
        Schema::create('tracer_question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tracer_question_id')
                ->constrained('tracer_questions')
                ->onDelete('cascade');
            $table->string('label', 255);
            $table->string('value', 100);
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['tracer_question_id', 'order']);
            $table->unique(['tracer_question_id', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfConstraints('tracer_question_options');
        Schema::dropIfExists('tracer_question_options');
    }
};
