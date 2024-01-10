<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\Category;
use App\Models\Venue;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'venue_id' => Venue::factory(),
            'main_image' => $this->faker->imageUrl(640, 480, 'events'),
            'event_date' => $this->faker->date()
        ];
    }
}
