<?php

namespace Database\Factories\Test;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition()
    {
        return [
            'event_id' => Event::factory(),
            'date_time' => $this->faker->dateTimeBetween(Carbon::tomorrow(), Carbon::today()->addYear())->format('Y-m-d H:i:s'),
            'max_capacity' => $this->faker->numberBetween(50, 200),
            'online_sale_end_time' => $this->faker->dateTimeBetween('+1 day', '+2 days'),
            'ticket_quantity' => $this->faker->numberBetween(50, 200),
            'named_tickets' => $this->faker->boolean,
        ];
    }
}
