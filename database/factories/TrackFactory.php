<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Track>
 */
class TrackFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => 'trk-' . Str::uuid(),
            'title' => fake()->sentence(3),
            'artist' => fake()->name(),
            'image' => 'tracks/images/test.jpg',
            'audio' => 'tracks/musics/test.mp3',
            'is_visible' => fake()->boolean(90),
            'play_count' => fake()->numberBetween(0, 500),
        ];
    }
}
