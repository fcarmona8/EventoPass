<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition()
    {
        return [
            'event_id' => Event::inRandomOrder()->first()->id,
            'date_time' => $this->faker->dateTime,
        ];
    }
}
