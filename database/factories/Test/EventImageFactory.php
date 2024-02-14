<?php

namespace Database\Factories\Test;


use App\Models\Event;
use App\Models\EventImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventImageFactory extends Factory
{
    protected $model = EventImage::class;

    public function definition()
    {
        return [
            'event_id' => Event::factory(),
            'image_id' => $this->faker->imageUrl(),
            'is_main' => $this->faker->boolean,
        ];
    }
}
