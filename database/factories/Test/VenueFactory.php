<?php

namespace Database\Factories\Test;

use App\Models\Venue;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class VenueFactory extends Factory
{
    protected $model = Venue::class;

    public function definition()
    {
        static $venueCounter = 0;

        $venueNames = ["Centro de Eventos", "Teatro Principal", "Auditorio Ciudad", "Palacio de Congresos", "Sala de Fiestas", "Estadio Municipal", "Arena Deportiva", "Club Nocturno", "Parque de Exposiciones", "Recinto Ferial"];

        $user_id = User::inRandomOrder()->first()->id ?? User::factory()->create()->id;

        $venueData = [
            'venue_name' => $venueNames[$venueCounter % count($venueNames)],
            'city' => $this->faker->city,
            'province' => $this->faker->state,
            'postal_code' => $this->faker->postcode,
            'capacity' => $this->faker->randomNumber(3),
            'user_id' => $user_id
        ];

        $venueCounter++;
        return $venueData;
    }
}
