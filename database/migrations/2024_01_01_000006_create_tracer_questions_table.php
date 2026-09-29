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
        Schema::create('tracer_questions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('question_text', 500);
            $table->enum('type', ['text', 'textarea', 'number', 'radio', 'select', 'checkbox', 'date']);
            $table->boolean('is_required')->default(false);
            $table->integer('order')->default(0);
            $table->json('options')->nullable();
            $table->string('validation_rules', 500)->nullable();
            $table->timestamps();

            $table->index('order');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracer_questions');
    }
};
