<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Event;
use App\Models\Session;
use Illuminate\Database\Eloquent\Factories\Factory;

class SessionFactory extends Factory
{
    protected $model = Session::class;

    public function definition()
    {
        static $sessionCounter = 0;

        $dateTimes = [
            "2024-02-01 10:00:00", "2024-02-01 12:00:00", "2024-02-01 14:00:00",
            "2024-02-02 10:00:00", "2024-02-02 12:00:00", "2024-02-02 14:00:00",
            "2024-02-03 10:00:00", "2024-02-03 12:00:00", "2024-02-03 14:00:00",
            "2024-02-04 10:00:00", "2024-02-04 12:00:00", "2024-02-04 14:00:00",
            "2024-02-05 10:00:00", "2024-02-05 12:00:00", "2024-02-05 14:00:00",
            "2024-02-06 10:00:00", "2024-02-06 12:00:00", "2024-02-06 14:00:00",
            "2024-02-07 10:00:00", "2024-02-07 12:00:00", "2024-02-07 14:00:00",
            "2024-02-08 10:00:00", "2024-02-08 12:00:00", "2024-02-08 14:00:00",
            "2024-03-29 10:00:00", "2024-03-29 12:00:00", "2024-03-29 14:00:00",
            "2024-03-30 10:00:00", "2024-03-30 12:00:00", "2024-03-30 14:00:00"
        ];


        $sessionData = [
            'event_id' => Event::inRandomOrder()->first()->id,
            'date_time' => $this->faker->dateTimeBetween(Carbon::tomorrow(), Carbon::today()->addYear())->format('Y-m-d H:i:s'),
            'max_capacity' => $this->faker->numberBetween(10, 20) 
        ];

        $sessionCounter++;
        return $sessionData;
    }
}
