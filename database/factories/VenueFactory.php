<?php

namespace Database\Factories;

use App\Enums\Region;
use App\Models\Venue;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Venue::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'postal_code' => fake()->postcode(),
            'region' => $this->faker->randomElement( array: Region::class),
        ];
    }
}
