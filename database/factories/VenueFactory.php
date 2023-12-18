<?php

namespace Database\Factories;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Tower', 'Building', 'Plaza', 'Center', 'Complex']),
            'location' => $this->faker->city,
        ];
    }
}
