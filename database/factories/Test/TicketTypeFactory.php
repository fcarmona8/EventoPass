<?php

namespace Database\Factories\Test;

use App\Models\TicketType;
use Illuminate\Database\Eloquent\Factories\Factory;

class TicketTypeFactory extends Factory
{
    protected $model = TicketType::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'price' => $this->faker->randomFloat(2, 20, 100),
            'available_tickets' => $this->faker->numberBetween(100, 500),
        ];
    }
}
