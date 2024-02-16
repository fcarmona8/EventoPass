<?php

namespace Database\Factories\Test;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'main_image_id' => $this->faker->imageUrl(),
            'category_id' => function () {
                return \App\Models\Category::factory()->create()->id;
            },
            'venue_id' => function () {
                return \App\Models\Venue::factory()->create()->id;
            },
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'event_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'video_link' => $this->faker->url,
            'hidden' => $this->faker->boolean,
        ];
    }
}
