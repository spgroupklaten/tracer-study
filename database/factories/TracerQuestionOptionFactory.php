<?php

namespace Database\Factories;

use App\Models\TracerQuestion;
use App\Models\TracerQuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TracerQuestionOption>
 */
class TracerQuestionOptionFactory extends Factory
{
    protected $model = TracerQuestionOption::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tracer_question_id' => TracerQuestion::factory(),
            'label' => $this->faker->words(2, true),
            'value' => $this->faker->unique()->slug(2),
            'order' => $this->faker->numberBetween(1, 10),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the option is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the option is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set the order for this option.
     */
    public function ordered(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'order' => $order,
        ]);
    }
}
