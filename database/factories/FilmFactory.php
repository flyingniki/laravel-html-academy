<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'status' => Film::STATUS_READY,
            'description' => $this->faker->sentences(2, true),
            'director' => $this->faker->name(),
            'starring' => [$this->faker->name(), $this->faker->name(), $this->faker->name()],
            'run_time' => random_int(60, 240),
            'released' => $this->faker->year(),
            'imdb_id' => 'tt00' . random_int(1, 9999),
        ];
    }

    public function pending()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Film::STATUS_PENDING,
            ];
        });
    }
}
