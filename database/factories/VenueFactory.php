<?php

namespace Database\Factories;

use App\Models\Venue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition()
    {
        return [
            'venue_name' => $this->faker->company,
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'capacity' => $this->faker->randomNumber(3),
            'user_id' => User::inRandomOrder()->first()->id
        ];
    }
}
