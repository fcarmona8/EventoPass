<?php

namespace Database\Factories\Test;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'event_date' => $this->faker->dateTimeBetween(Carbon::tomorrow(), Carbon::today()->addYear())->format('Y-m-d H:i:s'),
            'video_link' => $this->faker->url,
            'hidden' => $this->faker->boolean,
        ];
    }
}
