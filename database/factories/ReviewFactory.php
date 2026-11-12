<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'source_url' => 'https://yandex.ru/maps/org/test/123/',
            'author_name' => fake()->name(),
            'text' => fake()->sentence(12),
            'rating' => fake()->numberBetween(1, 5),
            'published_at' => fake()->dateTimeBetween('-90 days'),
            'avatar_url' => null,
        ];
    }
}
