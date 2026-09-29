<?php

namespace Database\Factories;

use App\Enums\QuestionType;
use App\Models\TracerQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TracerQuestion>
 */
class TracerQuestionFactory extends Factory
{
    protected $model = TracerQuestion::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'Q' . $this->faker->unique()->numberBetween(1, 9999),
            'question_text' => $this->faker->sentence() . '?',
            'type' => $this->faker->randomElement(QuestionType::cases()),
            'is_required' => $this->faker->boolean(70),
            'is_active' => true,
            'order' => $this->faker->numberBetween(1, 100),
            'options' => null,
            'validation_rules' => null,
        ];
    }

    /**
     * Indicate that the question is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the question is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Indicate that the question is required.
     */
    public function required(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_required' => true,
        ]);
    }

    /**
     * Set specific question type.
     */
    public function ofType(QuestionType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }
}
